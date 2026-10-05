<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-10">
        <div>
          <h1 class="font-serif text-3xl font-bold text-stone-900">My Wishlist</h1>
          <p class="text-stone-500 mt-1 text-sm">
            {{ wishlistStore.count }} {{ wishlistStore.count === 1 ? 'item' : 'items' }} saved
          </p>
        </div>
        <button
          v-if="wishlistStore.count > 0"
          @click="wishlistStore.clear()"
          class="text-sm text-stone-400 hover:text-red-500 transition-colors flex items-center gap-2"
        >
          <Trash2 class="w-4 h-4" />
          Clear All
        </button>
      </div>

      <!-- Empty State -->
      <div
        v-if="wishlistStore.count === 0"
        class="text-center py-24 bg-white rounded-2xl border border-stone-100 shadow-soft"
      >
        <Heart class="w-16 h-16 text-stone-200 mx-auto mb-5" />
        <h2 class="font-serif text-2xl font-semibold text-stone-700 mb-3">Your wishlist is empty</h2>
        <p class="text-stone-400 text-sm max-w-sm mx-auto mb-8">
          Save products you love to your wishlist. They'll stay here until you're ready to buy.
        </p>
        <router-link
          to="/catalog"
          class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-sm transition-all duration-300 shadow-glow"
        >
          <ShoppingBag class="w-4 h-4" />
          Explore Products
        </router-link>
      </div>

      <!-- Wishlist Grid -->
      <div
        v-else
        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6"
      >
        <div
          v-for="item in wishlistStore.items"
          :key="item.product_id || item.id"
          class="group bg-white rounded-2xl sm:rounded-3xl border border-stone-200/80 shadow-soft overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col justify-between"
        >
          <!-- Product Image -->
          <div class="relative aspect-square overflow-hidden bg-stone-50">
            <img
              :src="item.thumbnail_url || item.product?.thumbnail_url || item.product?.image_url || item.image_url || 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=400&q=80'"
              :alt="item.name || item.product?.name || 'Product'"
              @error="handleImgError"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <!-- Remove from wishlist -->
            <button
              @click="wishlistStore.remove(item.product_id || item.id)"
              class="absolute top-2 sm:top-3 right-2 sm:right-3 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-rose-500 hover:bg-rose-50 hover:scale-110 transition-all shadow"
            >
              <Heart class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-current" />
            </button>
          </div>

          <!-- Product Info -->
          <div class="p-3 sm:p-4 space-y-2 sm:space-y-3 flex-1 flex flex-col justify-between">
            <div>
              <p class="text-[9px] sm:text-xs text-brand-600 font-semibold uppercase tracking-wide mb-0.5">
                {{ item.brand?.name || item.product?.brand?.name || 'BeautyVault' }}
              </p>
              <router-link
                :to="`/products/${item.slug || item.product?.slug || item.product_id || item.id}`"
                class="font-serif font-bold text-stone-900 hover:text-brand-600 transition-colors text-xs sm:text-sm leading-snug line-clamp-1 sm:line-clamp-2"
              >
                {{ item.name || item.product?.name || 'Product Name' }}
              </router-link>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-sm sm:text-base font-bold text-stone-900">
                ${{ Number(item.base_price || item.product?.base_price || 0).toFixed(2) }}
              </span>
              <span
                v-if="item.is_vegan || item.product?.is_vegan"
                class="text-[9px] sm:text-xs text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full font-medium"
              >
                Vegan
              </span>
            </div>

            <div class="pt-1">
              <button
                @click="addToCart(item)"
                class="w-full py-1.5 sm:py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs sm:text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-1.5 shadow-sm"
              >
                <ShoppingBag class="w-3.5 h-3.5" />
                <span>Add to Cart</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Heart, ShoppingBag, Trash2 } from 'lucide-vue-next';
import { useWishlistStore } from '../stores/wishlist';
import { useCartStore } from '../stores/cart';

const router = useRouter();
const wishlistStore = useWishlistStore();
const cartStore = useCartStore();

onMounted(async () => {
  await wishlistStore.fetchServerWishlist();
});

async function addToCart(item) {
  const prod = item.product || item || {};
  const variantId = prod.default_variant_id || (prod.variants && prod.variants[0]?.id) || item.variant_id;
  if (!variantId) {
    const slug = item.slug || prod.slug || item.product_id || item.id;
    router.push(`/products/${slug}`);
    return;
  }
  try {
    await cartStore.addToCart(variantId, 1);
  } catch (e) {
    console.error(e);
  }
}

const handleImgError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80';
};
</script>
