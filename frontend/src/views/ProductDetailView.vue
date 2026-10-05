<template>
  <div v-if="loading" class="max-w-7xl mx-auto px-4 py-20 text-center">
    <div class="w-12 h-12 border-4 border-brand-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
    <p class="text-xs text-stone-500 font-semibold tracking-wider uppercase">Loading clinical formulation...</p>
  </div>

  <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 pb-24 lg:pb-12 space-y-12 sm:space-y-16">
    
    <!-- Top Product Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
      
      <!-- Gallery Column -->
      <div class="space-y-4">
        <div class="aspect-square rounded-3xl overflow-hidden bg-stone-100 border border-stone-200/80 shadow-md">
          <img
            :src="selectedImage || product.thumbnail_url"
            :alt="product.name"
            @error="handleImgError"
            class="w-full h-full object-cover transition-all duration-300"
          />
        </div>

        <!-- Thumbnails -->
        <div v-if="product.gallery_images?.length > 1" class="flex gap-3">
          <button
            v-for="(img, idx) in product.gallery_images"
            :key="idx"
            @click="selectedImage = img"
            class="w-20 h-20 rounded-2xl overflow-hidden border-2 transition-all"
            :class="selectedImage === img ? 'border-brand-600 shadow-md scale-105' : 'border-stone-200 opacity-70 hover:opacity-100'"
          >
            <img :src="img" :alt="`Thumb ${idx}`" @error="handleImgError" class="w-full h-full object-cover" />
          </button>
        </div>
      </div>

      <!-- Purchasing Details Column -->
      <div class="space-y-6">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">
            {{ product.brand?.name }}
          </span>
          <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900 mt-1">
            {{ product.name }}
          </h1>
          <p class="text-xs sm:text-sm text-stone-500 mt-1 leading-relaxed">
            {{ product.subtitle }}
          </p>

          <!-- Rating -->
          <div class="flex items-center gap-2 mt-3 text-xs">
            <div class="flex items-center text-amber-400">
              <Star v-for="n in 5" :key="n" class="w-4 h-4 fill-amber-400 text-amber-400" />
            </div>
            <span class="font-bold text-stone-900">{{ product.rating_avg }}</span>
            <span class="text-stone-400">({{ product.reviews_count }} verified reviews)</span>
          </div>
        </div>

        <!-- Price -->
        <div class="flex items-baseline gap-3 pb-4 border-b border-stone-100">
          <span class="text-3xl font-bold text-stone-900">
            ${{ parseFloat(selectedVariant?.price || product.discount_price || product.base_price).toFixed(2) }}
          </span>
          <span
            v-if="product.discount_price && product.discount_price < product.base_price"
            class="text-base text-stone-400 line-through"
          >
            ${{ parseFloat(product.base_price).toFixed(2) }}
          </span>
          <span
            v-if="selectedVariant?.stock_quantity <= 5 && selectedVariant?.stock_quantity > 0"
            class="ml-auto text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 animate-pulse"
          >
            ⚡ Only {{ selectedVariant.stock_quantity }} left in stock!
          </span>
        </div>

        <!-- Personal Skin Compatibility Scorecard -->
        <div
          v-if="compatibility && compatibility.score"
          class="p-4 rounded-2xl border"
          :class="compatibility.score >= 80 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-brand-50/60 border-brand-200'"
        >
          <div class="flex items-center justify-between text-xs mb-2">
            <span class="font-bold text-stone-900 flex items-center gap-1.5">
              <Sparkles class="w-4 h-4 text-brand-600" />
              <span>Skin Profile Match Compatibility</span>
            </span>
            <span class="font-bold text-sm" :class="compatibility.score >= 80 ? 'text-emerald-700' : 'text-brand-700'">
              {{ compatibility.score }}% {{ compatibility.status }}
            </span>
          </div>
          <!-- Progress bar -->
          <div class="w-full bg-black/10 rounded-full h-2 overflow-hidden mb-2">
            <div
              class="h-2 rounded-full transition-all duration-700"
              :class="compatibility.score >= 80 ? 'bg-emerald-500' : 'bg-brand-500'"
              :style="{ width: `${compatibility.score}%` }"
            ></div>
          </div>
          <ul class="text-[11px] text-stone-600 space-y-0.5">
            <li v-for="(reason, idx) in compatibility.reasons" :key="idx" class="flex items-start gap-1">
              <span>•</span>
              <span>{{ reason }}</span>
            </li>
          </ul>
        </div>

        <!-- Shade Selector (If shades exist) -->
        <div v-if="product.variants?.length > 0" class="space-y-3">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-stone-900">
              Selected Shade:
              <span class="text-brand-600 font-semibold ml-1">
                {{ selectedVariant?.shade_name || selectedVariant?.title }}
              </span>
            </span>
            <router-link to="/shade-finder" class="text-brand-600 font-semibold hover:underline">
              Need help? Find Your Shade
            </router-link>
          </div>

          <!-- Swatches Flex -->
          <div class="flex flex-wrap gap-2.5">
            <button
              v-for="v in product.variants"
              :key="v.id"
              @click="selectVariant(v)"
              type="button"
              class="relative p-1 rounded-full border-2 transition-all flex items-center justify-center group"
              :class="selectedVariant?.id === v.id ? 'border-stone-900 scale-110 shadow-md' : 'border-transparent hover:scale-105'"
            >
              <span
                class="w-7 h-7 rounded-full border border-black/15 shadow-inner block"
                :style="{ backgroundColor: v.shade_hex || '#e2b393' }"
              ></span>
            </button>
          </div>
          <p v-if="selectedVariant?.undertone" class="text-xs text-stone-500">
            Depth Level: <strong>{{ selectedVariant.depth_level }}/10</strong> • Undertone: <strong>{{ selectedVariant.undertone }}</strong>
          </p>
        </div>

        <!-- Quantity & Add to Cart -->
        <div class="flex items-center gap-4 pt-4">
          <div class="flex items-center border border-stone-200 rounded-full bg-stone-50 px-3 py-1.5">
            <button @click="quantity = Math.max(1, quantity - 1)" class="p-1 hover:text-brand-600">
              <Minus class="w-3.5 h-3.5" />
            </button>
            <span class="w-10 text-center font-bold text-xs text-stone-800">{{ quantity }}</span>
            <button @click="quantity = quantity + 1" class="p-1 hover:text-brand-600">
              <Plus class="w-3.5 h-3.5" />
            </button>
          </div>

          <button
            @click="handleAddToCart"
            :disabled="cartLoading || !selectedVariant || selectedVariant.stock_quantity === 0"
            class="flex-1 py-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <ShoppingBag class="w-4 h-4" />
            <span v-if="cartLoading">Adding to Bag...</span>
            <span v-else-if="selectedVariant && selectedVariant.stock_quantity === 0">Out of Stock</span>
            <span v-else>Add to Beauty Bag</span>
          </button>
        </div>

        <!-- Transparency Guarantees -->
        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-stone-100 text-[11px] text-stone-500">
          <div class="flex items-center gap-2">
            <ShieldCheck class="w-4 h-4 text-emerald-600" />
            <span>FIFO Batch Freshness Guarantee</span>
          </div>
          <div class="flex items-center gap-2">
            <Sparkles class="w-4 h-4 text-brand-600" />
            <span>Non-Comedogenic Verified</span>
          </div>
        </div>

      </div>
    </div>

    <!-- Formula Breakdown & Ingredients Tab Section -->
    <div class="border-t border-stone-200 pt-12 space-y-8">
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Clinical Formulation Breakdown</span>
        <h2 class="font-serif text-2xl font-bold text-stone-900 mt-1">Active Science & Safety Index</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Key Actives -->
        <div class="bg-white p-6 rounded-3xl border border-stone-200 space-y-3">
          <h3 class="font-serif font-bold text-base text-stone-900 flex items-center gap-2">
            <FlaskConical class="w-4 h-4 text-brand-600" />
            <span>Key Active Ingredients</span>
          </h3>
          <div v-if="product.ingredients?.length > 0" class="space-y-3 pt-2 text-xs">
            <div
              v-for="ing in product.ingredients"
              :key="ing.id"
              class="p-3 rounded-2xl bg-stone-50 border border-stone-100"
            >
              <div class="flex justify-between items-start">
                <span class="font-bold text-stone-900">{{ ing.name }}</span>
                <span v-if="ing.pivot?.concentration_percentage" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800">
                  {{ ing.pivot.concentration_percentage }}% Concentration
                </span>
              </div>
              <p class="text-[11px] text-stone-500 mt-1">{{ ing.description }}</p>
              <div class="flex items-center gap-3 mt-2 text-[10px] text-stone-400">
                <span>Comedogenic: <strong>{{ ing.comedogenic_rating }}/5</strong></span>
                <span>•</span>
                <span>EWG Safety: <strong>{{ ing.ewg_safety_score }}/10</strong></span>
              </div>
            </div>
          </div>
          <p v-else class="text-xs text-stone-500">Pure dermatological base formulation.</p>
        </div>

        <!-- How To Use -->
        <div class="bg-white p-6 rounded-3xl border border-stone-200 space-y-3">
          <h3 class="font-serif font-bold text-base text-stone-900 flex items-center gap-2">
            <CheckCircle2 class="w-4 h-4 text-emerald-600" />
            <span>Dermatologist Application Protocol</span>
          </h3>
          <p class="text-xs text-stone-600 leading-relaxed pt-2">
            {{ product.how_to_use || 'Apply directly to cleansed skin morning and night before heavier occlusive creams.' }}
          </p>
          <div class="pt-4 border-t border-stone-100 text-xs text-stone-500 space-y-1">
            <p>• Period After Opening (PAO): <strong>{{ product.pao_months || 12 }} Months</strong></p>
            <p>• Finish: <strong class="capitalize">{{ product.finish_type || 'Natural' }}</strong></p>
          </div>
        </div>

        <!-- Full INCI List -->
        <div class="bg-white p-6 rounded-3xl border border-stone-200 space-y-3">
          <h3 class="font-serif font-bold text-base text-stone-900 flex items-center gap-2">
            <FileText class="w-4 h-4 text-stone-600" />
            <span>Full Transparent INCI List</span>
          </h3>
          <p class="text-[11px] text-stone-500 leading-relaxed font-mono bg-stone-50 p-3 rounded-2xl border border-stone-100 max-h-48 overflow-y-auto">
            {{ product.full_ingredients_text || 'Aqua, Glycerin, Niacinamide, Sodium Hyaluronate, Phenoxyethanol.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Verified Customer Reviews Section -->
    <div class="border-t border-stone-200 pt-12 space-y-8">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Community Social Proof</span>
          <h2 class="font-serif text-2xl font-bold text-stone-900 mt-1">Customer Reviews ({{ product.reviews_count }})</h2>
        </div>
        <button
          @click="showReviewModal = true"
          class="px-5 py-2.5 rounded-full bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold shadow-sm"
        >
          Write a Verified Review
        </button>
      </div>

      <!-- Reviews Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="rev in product.approved_reviews"
          :key="rev.id"
          class="bg-white p-6 rounded-3xl border border-stone-200 space-y-3 shadow-sm"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-1 text-amber-400">
              <Star v-for="n in rev.rating" :key="n" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
            </div>
            <span class="text-[10px] text-stone-400">{{ new Date(rev.created_at).toLocaleDateString() }}</span>
          </div>

          <h4 class="font-serif font-bold text-sm text-stone-900">{{ rev.title }}</h4>
          <p class="text-xs text-stone-600 leading-relaxed">{{ rev.review_text }}</p>

          <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
            <div>
              <p class="font-bold text-stone-900">{{ rev.user?.name || 'Verified Beauty Enthusiast' }}</p>
              <p class="text-[10px] text-stone-400">
                Skin: {{ rev.reviewer_skin_type || 'Combination' }} • Undertone: {{ rev.reviewer_undertone || 'Neutral' }}
              </p>
            </div>
            <span v-if="rev.purchased_shade" class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-50 text-brand-700">
              Shade: {{ rev.purchased_shade }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Sticky Purchase Bar (Native App Feel on Smartphones) -->
    <div
      v-if="product"
      class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-stone-200/90 px-4 py-3 shadow-[0_-8px_20px_rgba(0,0,0,0.08)] flex items-center justify-between gap-3"
    >
      <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2">
          <span class="font-bold text-base text-stone-900">
            ${{ parseFloat(selectedVariant?.price || product.discount_price || product.base_price).toFixed(2) }}
          </span>
          <span
            v-if="product.discount_price && product.discount_price < product.base_price"
            class="text-xs text-stone-400 line-through"
          >
            ${{ parseFloat(product.base_price).toFixed(2) }}
          </span>
        </div>
        <p class="text-[10px] text-stone-500 truncate">
          {{ selectedVariant?.shade_name || selectedVariant?.title || 'Default formulation' }}
        </p>
      </div>

      <button
        @click="handleAddToCart"
        :disabled="cartLoading || !selectedVariant || selectedVariant.stock_quantity === 0"
        class="py-3 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs transition-all shadow-md flex items-center justify-center gap-1.5 shrink-0 disabled:opacity-50"
      >
        <ShoppingBag class="w-3.5 h-3.5" />
        <span v-if="cartLoading">Adding...</span>
        <span v-else-if="selectedVariant && selectedVariant.stock_quantity === 0">Out of Stock</span>
        <span v-else>Add to Bag</span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../services/api';
import { useCartStore } from '../stores/cart';
import {
  Star, Sparkles, Minus, Plus, ShoppingBag, ShieldCheck,
  FlaskConical, CheckCircle2, FileText
} from 'lucide-vue-next';

const route = useRoute();
const cartStore = useCartStore();

const product = ref(null);
const compatibility = ref(null);
const selectedVariant = ref(null);
const selectedImage = ref(null);
const quantity = ref(1);
const loading = ref(true);
const cartLoading = ref(false);
const showReviewModal = ref(false);

const selectVariant = (v) => {
  selectedVariant.value = v;
  if (v.image_url) {
    selectedImage.value = v.image_url;
  }
};

const handleAddToCart = async () => {
  if (!selectedVariant.value) return;
  cartLoading.value = true;
  try {
    await cartStore.addToCart(selectedVariant.value.id, quantity.value);
  } catch (err) {
    alert(err.response?.data?.message || 'Error adding to cart');
  } finally {
    cartLoading.value = false;
  }
};

onMounted(async () => {
  try {
    const slug = route.params.slug;
    const res = await api.get(`/products/${slug}`);
    product.value = res.data.product;
    compatibility.value = res.data.compatibility;

    if (product.value.variants?.length > 0) {
      selectedVariant.value = product.value.variants[0];
    }
  } catch (err) {
    console.error('Failed to load product:', err);
  } finally {
    loading.value = false;
  }
});

const handleImgError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80';
};
</script>
