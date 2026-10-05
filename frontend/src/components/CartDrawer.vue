<template>
  <Transition
    enter-active-class="transition-opacity duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
  <div v-if="cartStore.isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div
      @click="cartStore.toggleCart(false)"
      class="absolute inset-0 bg-stone-900/60 backdrop-blur-sm"
    ></div>

    <div class="fixed inset-y-0 right-0 w-full sm:max-w-md flex flex-col h-full">
      <div class="w-full h-full bg-white shadow-2xl flex flex-col overflow-hidden transform translate-x-0 transition-transform duration-300">
        
        <!-- Drawer Header -->
        <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <ShoppingBag class="w-5 h-5 text-brand-600" />
            <h2 class="font-serif text-xl font-bold text-stone-900">Your Beauty Bag</h2>
            <span class="text-xs text-stone-400 font-medium">({{ cartStore.itemCount }} items)</span>
          </div>
          <button
            @click="cartStore.toggleCart(false)"
            class="p-2 text-stone-400 hover:text-stone-700 rounded-full hover:bg-stone-100 transition-colors"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Free Shipping Progress Bar -->
        <div class="px-6 py-3 bg-brand-50/70 border-b border-brand-100/60 text-xs">
          <div v-if="cartStore.isFreeShipping" class="flex items-center gap-1.5 text-emerald-700 font-semibold">
            <CheckCircle2 class="w-4 h-4 text-emerald-600" />
            <span>You unlocked <strong>Free 2-Day Express Shipping!</strong></span>
          </div>
          <div v-else>
            <p class="text-stone-700 mb-1.5 font-medium">
              Add <span class="font-bold text-brand-700">${{ cartStore.amountToFreeShipping.toFixed(2) }}</span> more for <strong>Free Express Shipping</strong>
            </p>
            <div class="w-full bg-brand-200/50 rounded-full h-1.5 overflow-hidden">
              <div
                class="bg-brand-600 h-1.5 rounded-full transition-all duration-500"
                :style="{ width: `${Math.min(100, (cartStore.subtotal / cartStore.freeShippingThreshold) * 100)}%` }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 divide-y divide-stone-100">
          <div v-if="cartStore.items.length === 0" class="py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-400 flex items-center justify-center mx-auto mb-4">
              <ShoppingBag class="w-8 h-8 stroke-[1.5]" />
            </div>
            <h3 class="font-serif text-lg font-semibold text-stone-800">Your bag is empty</h3>
            <p class="text-xs text-stone-500 mt-1 max-w-xs mx-auto">
              Explore our shade-matched foundations, serums with clinical actives, and clean formulas.
            </p>
            <button
              @click="goToCatalog"
              class="mt-6 px-6 py-2.5 rounded-full bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 transition-colors shadow-sm"
            >
              Explore Best Sellers
            </button>
          </div>

          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="pt-4 first:pt-0 flex gap-4 items-start group"
          >
            <!-- Thumbnail -->
            <img
              :src="item.variant?.image_url || 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=300&q=80'"
              :alt="item.variant?.title"
              class="w-16 h-20 object-cover rounded-xl border border-stone-200 bg-stone-50 flex-shrink-0"
            />

            <!-- Item Info -->
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider">
                {{ item.variant?.product?.brand?.name }}
              </p>
              <h4 class="text-sm font-semibold text-stone-900 truncate">
                {{ item.variant?.product?.name }}
              </h4>
              
              <!-- Shade Swatch Tag if foundation/makeup -->
              <div v-if="item.variant?.shade_hex" class="flex items-center gap-1.5 mt-1">
                <span
                  class="w-3.5 h-3.5 rounded-full border border-black/10 shadow-inner flex-shrink-0"
                  :style="{ backgroundColor: item.variant.shade_hex }"
                ></span>
                <span class="text-xs text-stone-600 font-medium truncate">
                  {{ item.variant.shade_name }} ({{ item.variant.undertone }} undertone)
                </span>
              </div>
              <p v-else class="text-xs text-stone-500 mt-0.5">
                {{ item.variant?.volume_or_weight || item.variant?.title }}
              </p>

              <!-- Price & Quantity Stepper -->
              <div class="flex items-center justify-between mt-3">
                <div class="flex items-center border border-stone-200 rounded-lg overflow-hidden bg-stone-50">
                  <button
                    @click="cartStore.updateQuantity(item.id, item.quantity - 1)"
                    class="p-1 hover:bg-stone-200 text-stone-600 transition-colors"
                  >
                    <Minus class="w-3.5 h-3.5" />
                  </button>
                  <span class="w-8 text-center text-xs font-semibold text-stone-800">
                    {{ item.quantity }}
                  </span>
                  <button
                    @click="cartStore.updateQuantity(item.id, item.quantity + 1)"
                    class="p-1 hover:bg-stone-200 text-stone-600 transition-colors"
                  >
                    <Plus class="w-3.5 h-3.5" />
                  </button>
                </div>

                <div class="text-right">
                  <span class="text-sm font-bold text-stone-900">
                    ${{ (item.variant?.price * item.quantity).toFixed(2) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Remove Button -->
            <button
              @click="cartStore.removeItem(item.id)"
              class="text-stone-400 hover:text-rose-600 transition-colors p-1"
              aria-label="Remove item"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Promo Code Section -->
        <div v-if="cartStore.items.length > 0" class="px-6 py-3 bg-stone-50/80 border-t border-stone-100">
          <div v-if="cartStore.coupon" class="flex items-center justify-between text-xs bg-emerald-50 text-emerald-800 px-3 py-2 rounded-xl border border-emerald-200">
            <div class="flex items-center gap-1.5">
              <Tag class="w-3.5 h-3.5 text-emerald-600" />
              <span>Promo <strong>{{ cartStore.coupon.code }}</strong> applied (-${{ cartStore.discount.toFixed(2) }})</span>
            </div>
            <button @click="cartStore.removeCoupon" class="text-emerald-700 hover:text-rose-600 font-bold ml-2">
              <X class="w-3.5 h-3.5" />
            </button>
          </div>

          <div v-else class="space-y-1.5">
            <div class="flex gap-2">
              <input
                v-model="couponCode"
                @keyup.enter="handleApplyCoupon"
                type="text"
                placeholder="Promo code (e.g. GLOW20)"
                class="flex-1 text-xs uppercase bg-white border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500"
              />
              <button
                @click="handleApplyCoupon"
                :disabled="couponLoading || !couponCode.trim()"
                class="px-4 py-2 bg-stone-900 text-white text-xs font-semibold rounded-xl hover:bg-stone-800 transition-colors disabled:opacity-40"
              >
                Apply
              </button>
            </div>
            <!-- Quick Demo Code Chip -->
            <button
              @click="quickApplyDemoCoupon"
              class="text-[11px] text-brand-600 hover:underline flex items-center gap-1 font-medium"
            >
              <span>Click to apply demo promo: <strong>GLOW20</strong></span>
            </button>
            <p v-if="couponError" class="text-xs text-rose-600 mt-1 font-medium">{{ couponError }}</p>
          </div>
        </div>

        <!-- Drawer Footer & Totals -->
        <div v-if="cartStore.items.length > 0" class="px-6 py-5 bg-white border-t border-stone-200 space-y-3">
          <div class="space-y-1.5 text-xs text-stone-600">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span class="font-medium text-stone-900">${{ cartStore.subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="cartStore.discount > 0" class="flex justify-between text-emerald-600 font-semibold">
              <span>Discount</span>
              <span>-${{ cartStore.discount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Shipping</span>
              <span class="font-medium" :class="cartStore.shipping === 0 ? 'text-emerald-600 font-semibold' : 'text-stone-900'">
                {{ cartStore.shipping === 0 ? 'FREE' : `$${cartStore.shipping.toFixed(2)}` }}
              </span>
            </div>
            <div class="flex justify-between">
              <span>Estimated Tax (8%)</span>
              <span class="font-medium text-stone-900">${{ cartStore.tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-stone-900 pt-2 border-t border-stone-100">
              <span>Total</span>
              <span>${{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <button
            @click="goToCheckout"
            class="w-full py-3.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow flex items-center justify-center gap-2"
          >
            <span>Proceed to Secure Checkout</span>
            <ArrowRight class="w-4 h-4" />
          </button>
          
          <div class="flex justify-center items-center gap-4 text-[10px] text-stone-400">
            <span class="flex items-center gap-1"><Lock class="w-3 h-3 text-stone-400" /> 256-Bit SSL Encrypted</span>
            <span>•</span>
            <span>30-Day Shade Guarantee</span>
          </div>
        </div>

      </div>
    </div>
  </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cart';
import {
  ShoppingBag, X, CheckCircle2, Minus, Plus, Trash2, Tag, ArrowRight, Lock
} from 'lucide-vue-next';

const router = useRouter();
const cartStore = useCartStore();

const couponCode = ref('');
const couponLoading = ref(false);
const couponError = ref('');

const handleApplyCoupon = async () => {
  if (!couponCode.value.trim()) return;
  couponLoading.value = true;
  couponError.value = '';
  try {
    await cartStore.applyCoupon(couponCode.value.trim());
    couponCode.value = '';
  } catch (err) {
    couponError.value = err.response?.data?.message || 'Invalid coupon code';
  } finally {
    couponLoading.value = false;
  }
};

const quickApplyDemoCoupon = () => {
  couponCode.value = 'GLOW20';
  handleApplyCoupon();
};

const goToCatalog = () => {
  cartStore.toggleCart(false);
  router.push('/catalog');
};

const goToCheckout = () => {
  cartStore.toggleCart(false);
  router.push('/checkout');
};
</script>
