<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

    
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 text-xs text-stone-400 mb-2">
        <router-link to="/" class="hover:text-stone-700">Home</router-link>
        <span>/</span>
        <span class="text-stone-700 font-medium">Product Catalog</span>
      </div>
      <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
        Clinical Formulas & Inclusive Shades
      </h1>
      <p class="text-xs sm:text-sm text-stone-500 mt-1 max-w-2xl">
        Filter through our dermatologically-tested catalog by exact skin concern, pore-clogging safety index, or foundation undertone.
      </p>
    </div>

    <!-- Active Filters Chips -->
    <div v-if="hasActiveFilters" class="mb-6 flex flex-wrap items-center gap-2">
      <span class="text-xs text-stone-400 font-semibold mr-1">Active Filters:</span>
      <span
        v-if="filters.category"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200"
      >
        Category: {{ filters.category }}
        <button @click="clearFilter('category')"><X class="w-3 h-3 hover:text-brand-900" /></button>
      </span>
      <span
        v-if="filters.brand"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200"
      >
        Brand: {{ filters.brand }}
        <button @click="clearFilter('brand')"><X class="w-3 h-3 hover:text-brand-900" /></button>
      </span>
      <span
        v-if="filters.skin_type"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200"
      >
        Skin: {{ filters.skin_type }}
        <button @click="clearFilter('skin_type')"><X class="w-3 h-3 hover:text-brand-900" /></button>
      </span>
      <span
        v-if="filters.undertone"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200"
      >
        Undertone: {{ filters.undertone }}
        <button @click="clearFilter('undertone')"><X class="w-3 h-3 hover:text-brand-900" /></button>
      </span>
      <span
        v-if="filters.search"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200"
      >
        Search: "{{ filters.search }}"
        <button @click="clearFilter('search')"><X class="w-3 h-3 hover:text-brand-900" /></button>
      </span>
      <button
        @click="resetAllFilters"
        class="text-xs text-stone-500 hover:text-brand-600 underline font-medium ml-2"
      >
        Clear All
      </button>
    </div>

    <!-- Main Catalog Layout (Sidebar Filters + Products Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8 items-start">

      <!-- Left Filters Sidebar -->
      <aside class="bg-white rounded-3xl border border-stone-200 shadow-sm">
        <!-- Sidebar header with mobile toggle -->
        <button
          @click="filtersOpen = !filtersOpen"
          class="w-full flex items-center justify-between p-5 lg:p-6 border-b border-stone-100"
        >
          <h3 class="font-serif text-base lg:text-lg font-bold text-stone-900 flex items-center gap-2">
            <SlidersHorizontal class="w-4 h-4 text-brand-600" />
            <span>Refine By</span>
          </h3>
          <ChevronDown
            class="w-4 h-4 text-stone-400 transition-transform lg:hidden"
            :class="{ 'rotate-180': filtersOpen }"
          />
        </button>

        <div :class="['space-y-5 p-5 lg:p-6', { 'hidden lg:block': !filtersOpen }]">

        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Categories</label>
            <button v-if="filters.category" @click="filters.category = ''; fetchProducts()" class="text-[10px] text-brand-600 hover:underline">Reset</button>
          </div>
          <div class="space-y-1 text-xs">
            <label class="flex items-center gap-2 text-stone-600 hover:text-stone-900 cursor-pointer py-1 font-medium">
              <input
                type="radio"
                name="category_filter"
                value=""
                v-model="filters.category"
                @change="fetchProducts"
                class="accent-brand-600"
              />
              <span>All Categories</span>
            </label>
            <label
              v-for="cat in metadata.categories"
              :key="cat.slug"
              class="flex items-center gap-2 text-stone-600 hover:text-stone-900 cursor-pointer py-1"
            >
              <input
                type="radio"
                name="category_filter"
                :value="cat.slug"
                v-model="filters.category"
                @change="fetchProducts"
                class="accent-brand-600"
              />
              <span>{{ cat.name }}</span>
            </label>
          </div>
        </div>

        <!-- Filter: Brand -->
        <div class="space-y-2 pt-4 border-t border-stone-100">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Brand</label>
            <button v-if="filters.brand" @click="filters.brand = ''; fetchProducts()" class="text-[10px] text-brand-600 hover:underline">Reset</button>
          </div>
          <div class="space-y-1 text-xs">
            <label class="flex items-center gap-2 text-stone-600 hover:text-stone-900 cursor-pointer py-1 font-medium">
              <input
                type="radio"
                name="brand_filter"
                value=""
                v-model="filters.brand"
                @change="fetchProducts"
                class="accent-brand-600"
              />
              <span>All Brands</span>
            </label>
            <label
              v-for="b in metadata.brands"
              :key="b.slug"
              class="flex items-center gap-2 text-stone-600 hover:text-stone-900 cursor-pointer py-1"
            >
              <input
                type="radio"
                name="brand_filter"
                :value="b.slug"
                v-model="filters.brand"
                @change="fetchProducts"
                class="accent-brand-600"
              />
              <span>{{ b.name }}</span>
            </label>
          </div>
        </div>

        <!-- Filter: Skin Type -->
        <div class="space-y-2 pt-4 border-t border-stone-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Target Skin Type</label>
          <div class="flex flex-wrap gap-1.5">
            <button
              v-for="st in metadata.skin_types"
              :key="st"
              type="button"
              @click="toggleSkinType(st)"
              :class="filters.skin_type === st.toLowerCase() ? 'bg-brand-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'"
              class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors"
            >
              {{ st }}
            </button>
          </div>
        </div>

        <!-- Filter: Shade Undertone -->
        <div class="space-y-2 pt-4 border-t border-stone-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Shade Undertone</label>
          <div class="flex flex-wrap gap-1.5">
            <button
              v-for="ut in metadata.undertones"
              :key="ut"
              type="button"
              @click="toggleUndertone(ut)"
              :class="filters.undertone === ut.toLowerCase() ? 'bg-brand-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'"
              class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors"
            >
              {{ ut }}
            </button>
          </div>
        </div>

        <!-- Filter: Price Range -->
        <div class="space-y-2 pt-4 border-t border-stone-100 text-xs">
          <div class="flex justify-between font-bold text-stone-700">
            <span>Max Price</span>
            <span>${{ filters.max_price }}</span>
          </div>
          <input
            type="range"
            min="10"
            max="60"
            step="2"
            v-model="filters.max_price"
            @change="fetchProducts"
            class="w-full accent-brand-600"
          />
        </div>
        </div>
      </aside>

      <!-- Right Products Content Area -->
      <main class="lg:col-span-3 space-y-6">
        
        <!-- Controls Bar -->
        <div class="bg-white p-4 rounded-2xl border border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
          <span class="text-stone-500 font-medium">
            Showing <strong class="text-stone-900">{{ products.length }}</strong> products
          </span>

          <div class="flex items-center gap-3">
            <label class="text-stone-500 font-semibold">Sort By:</label>
            <select
              v-model="filters.sort"
              @change="fetchProducts"
              class="bg-stone-50 border border-stone-200 rounded-xl px-3 py-1.5 text-stone-800 focus:outline-none focus:border-brand-500"
            >
              <option value="featured">Featured Collection</option>
              <option value="bestseller">Best Selling Cult Products</option>
              <option value="price_asc">Price: Low to High</option>
              <option value="price_desc">Price: High to Low</option>
              <option value="rating">Highest Customer Rating</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="n in 6" :key="n" class="h-96 bg-stone-100 rounded-3xl animate-pulse"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="products.length === 0" class="bg-white rounded-3xl border border-stone-200 p-12 text-center">
          <SearchX class="w-12 h-12 text-stone-300 mx-auto mb-3" />
          <h3 class="font-serif text-lg font-bold text-stone-800">No matching formulas found</h3>
          <p class="text-xs text-stone-500 mt-1">Try widening your price range or clearing specific undertone filters.</p>
          <button
            @click="resetAllFilters"
            class="mt-4 px-5 py-2 rounded-full bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800"
          >
            Reset Filters
          </button>
        </div>

        <!-- Product Cards Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="product in products"
            :key="product.id"
            class="bg-white rounded-3xl border border-stone-200 p-4 flex flex-col justify-between group hover:shadow-xl hover:border-brand-300 transition-all duration-300"
          >
            <div>
              <!-- Image -->
              <div class="relative rounded-2xl overflow-hidden aspect-square bg-stone-50 mb-3">
                <img
                  :src="product.thumbnail_url"
                  :alt="product.name"
                  @error="handleImgError"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <span
                  v-if="product.spf_rating"
                  class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200"
                >
                  SPF {{ product.spf_rating }}
                </span>
                <!-- Wishlist Toggle -->
                <button
                  @click.prevent="wishlistStore.toggle(product)"
                  class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 backdrop-blur flex items-center justify-center hover:scale-110 transition-all shadow"
                  :class="wishlistStore.isInWishlist(product.id) ? 'text-rose-500' : 'text-stone-400'"
                >
                  <Heart class="w-3.5 h-3.5" :class="wishlistStore.isInWishlist(product.id) ? 'fill-current' : ''" />
                </button>
              </div>

              <!-- Brand & Title -->
              <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400">
                {{ product.brand?.name }}
              </p>
              <router-link
                :to="`/products/${product.slug}`"
                class="block font-serif text-base font-bold text-stone-900 group-hover:text-brand-600 transition-colors line-clamp-1 mt-0.5"
              >
                {{ product.name }}
              </router-link>
              <p class="text-xs text-stone-500 mt-1 line-clamp-2 leading-relaxed">
                {{ product.subtitle }}
              </p>

              <!-- Shade Swatches Preview -->
              <div v-if="product.variants?.length > 1" class="flex items-center gap-1.5 my-3">
                <span
                  v-for="v in product.variants.slice(0, 5)"
                  :key="v.id"
                  class="w-4 h-4 rounded-full border border-black/10 shadow-sm"
                  :style="{ backgroundColor: v.shade_hex || '#D8A279' }"
                  :title="v.shade_name"
                ></span>
                <span v-if="product.variants.length > 5" class="text-[10px] text-stone-400 font-semibold ml-1">
                  +{{ product.variants.length - 5 }}
                </span>
              </div>

              <!-- Rating Stars -->
              <div class="flex items-center gap-1 text-amber-400 text-xs my-2">
                <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                <span class="font-bold text-stone-800">{{ product.rating_avg }}</span>
                <span class="text-stone-400">({{ product.reviews_count }})</span>
              </div>
            </div>

            <!-- Price & Button -->
            <div class="pt-3 border-t border-stone-100 flex items-center justify-between mt-2">
              <div>
                <span class="text-base font-bold text-stone-900">
                  ${{ parseFloat(product.discount_price || product.base_price).toFixed(2) }}
                </span>
                <span
                  v-if="product.discount_price && product.discount_price < product.base_price"
                  class="text-xs text-stone-400 line-through ml-1"
                >
                  ${{ parseFloat(product.base_price).toFixed(2) }}
                </span>
              </div>

              <router-link
                :to="`/products/${product.slug}`"
                class="px-4 py-2 rounded-full bg-stone-900 group-hover:bg-brand-600 text-white text-xs font-semibold transition-colors shadow-sm"
              >
                {{ product.variants?.length > 1 ? 'Choose Shade' : 'View Product' }}
              </router-link>
            </div>
          </div>
        </div>

      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { useWishlistStore } from '../stores/wishlist';
import { SlidersHorizontal, X, SearchX, Star, Heart, ChevronDown } from 'lucide-vue-next';


const wishlistStore = useWishlistStore();
const route = useRoute();
const router = useRouter();

const products = ref([]);
const metadata = ref({ categories: [], brands: [], skin_types: [], undertones: [] });
const loading = ref(true);
const filtersOpen = ref(false);


const filters = reactive({
  category: route.query.category || '',
  brand: route.query.brand || '',
  skin_type: route.query.skin_type || '',
  undertone: route.query.undertone || '',
  search: route.query.search || '',
  max_price: 60,
  sort: route.query.sort || 'featured',
});

const hasActiveFilters = computed(() => {
  return !!(filters.category || filters.brand || filters.skin_type || filters.undertone || filters.search);
});

const toggleSkinType = (type) => {
  const lower = type.toLowerCase();
  filters.skin_type = filters.skin_type === lower ? '' : lower;
  fetchProducts();
};

const toggleUndertone = (ut) => {
  const lower = ut.toLowerCase();
  filters.undertone = filters.undertone === lower ? '' : lower;
  fetchProducts();
};

const clearFilter = (key) => {
  filters[key] = '';
  fetchProducts();
};

const resetAllFilters = () => {
  filters.category = '';
  filters.brand = '';
  filters.skin_type = '';
  filters.undertone = '';
  filters.search = '';
  filters.max_price = 60;
  filters.sort = 'featured';
  fetchProducts();
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = {
      ...filters,
      per_page: 15,
    };
    const res = await api.get('/products', { params });
    products.value = res.data.data;
  } catch (err) {
    console.error('Catalog fetch error:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  try {
    const metaRes = await api.get('/metadata');
    metadata.value = metaRes.data;
  } catch (err) {
    console.error('Metadata error:', err);
  }
  fetchProducts();
});

watch(() => route.query, (newQuery) => {
  filters.category = newQuery.category || '';
  filters.brand = newQuery.brand || '';
  filters.skin_type = newQuery.skin_type || '';
  filters.search = newQuery.search || '';
  filters.sort = newQuery.sort || 'featured';
  fetchProducts();
}, { deep: true });

const handleImgError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80';
};
</script>
