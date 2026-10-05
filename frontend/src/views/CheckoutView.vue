<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- State 1: Order Confirmed Success Screen -->
    <div v-if="confirmedOrder" class="max-w-2xl mx-auto text-center space-y-6 py-12">
      <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-md">
        <CheckCircle2 class="w-8 h-8" />
      </div>

      <div class="space-y-2">
        <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">Payment & Reservation Complete</span>
        <h1 class="font-serif text-3xl font-bold text-stone-900">
          Thank you for your order!
        </h1>
        <p class="text-xs sm:text-sm text-stone-500">
          Order Number: <strong class="text-stone-900">{{ confirmedOrder.order_number }}</strong>
        </p>
      </div>

      <div class="bg-white p-6 rounded-3xl border border-stone-200 text-left text-xs space-y-3 shadow-sm">
        <div class="flex justify-between pb-3 border-b border-stone-100">
          <span class="text-stone-500">Total Paid:</span>
          <span class="font-bold text-stone-900 text-sm">${{ confirmedOrder.total_amount }}</span>
        </div>
        <div class="flex justify-between pb-3 border-b border-stone-100">
          <span class="text-stone-500">Payment Method:</span>
          <span class="font-bold uppercase text-stone-900">{{ confirmedOrder.payment_method }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-stone-500">Shipping To:</span>
          <span class="font-medium text-stone-900 text-right">
            {{ confirmedOrder.shipping_address?.street }}, {{ confirmedOrder.shipping_address?.city }}
          </span>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
        <router-link
          :to="`/track-order?order_number=${confirmedOrder.order_number}&email=${confirmedOrder.customer_email}`"
          class="w-full sm:w-auto px-7 py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs transition-colors shadow-md flex items-center justify-center gap-2"
        >
          <span>Track Order in Real-Time</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>

        <router-link
          to="/catalog"
          class="w-full sm:w-auto px-7 py-3 rounded-full border border-stone-300 text-stone-700 font-semibold text-xs hover:bg-stone-50"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>

    <!-- State 2: Checkout Form -->
    <div v-else class="space-y-8">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-stone-200">
        <div>
          <h1 class="font-serif text-3xl font-bold text-stone-900">Secure Checkout</h1>
          <p class="text-xs text-stone-500 mt-0.5">Stock allocated via real-time FIFO lot batches.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Guest Checkout Supported · No Login Required</span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        
        <!-- Left: Form -->
        <form @submit.prevent="handleCheckout" class="lg:col-span-2 space-y-8">
          
          <!-- Contact Info -->
          <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="font-serif font-bold text-lg text-stone-900 flex items-center gap-2">
              <Mail class="w-4 h-4 text-brand-600" />
              <span>Customer Contact Information</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div>
                <label class="block font-semibold text-stone-700 mb-1">Full Name</label>
                <input
                  v-model="form.customer_name"
                  type="text"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-stone-700 mb-1">Email Address</label>
                <input
                  v-model="form.customer_email"
                  type="email"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Phone Number (For Delivery SMS)</label>
                <input
                  v-model="form.customer_phone"
                  type="text"
                  placeholder="+1 (555) 000-0000"
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="font-serif font-bold text-lg text-stone-900 flex items-center gap-2">
              <MapPin class="w-4 h-4 text-brand-600" />
              <span>Shipping Address</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div class="sm:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Street Address</label>
                <input
                  v-model="form.shipping_address.street"
                  type="text"
                  required
                  placeholder="e.g. 742 Evergreen Terrace, Apt 4B"
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-stone-700 mb-1">City</label>
                <input
                  v-model="form.shipping_address.city"
                  type="text"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-stone-700 mb-1">State / Province</label>
                <input
                  v-model="form.shipping_address.state"
                  type="text"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-stone-700 mb-1">Postal / ZIP Code</label>
                <input
                  v-model="form.shipping_address.postal_code"
                  type="text"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-stone-700 mb-1">Country</label>
                <input
                  v-model="form.shipping_address.country"
                  type="text"
                  required
                  class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
                />
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="font-serif font-bold text-lg text-stone-900 flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-brand-600" />
              <span>Payment Method</span>
            </h2>

            <div class="grid grid-cols-2 gap-4 text-xs">
              <label
                class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition-all"
                :class="form.payment_method === 'stripe' ? 'border-brand-600 bg-brand-50/50 ring-1 ring-brand-400' : 'border-stone-200'"
              >
                <div class="flex items-center justify-between">
                  <span class="font-bold text-stone-900">Stripe Credit Card</span>
                  <input type="radio" value="stripe" v-model="form.payment_method" class="accent-brand-600" />
                </div>
                <p class="text-[11px] text-stone-500 mt-2">Instant payment intent verification</p>
              </label>

              <label
                class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition-all"
                :class="form.payment_method === 'cod' ? 'border-brand-600 bg-brand-50/50 ring-1 ring-brand-400' : 'border-stone-200'"
              >
                <div class="flex items-center justify-between">
                  <span class="font-bold text-stone-900">Cash on Delivery</span>
                  <input type="radio" value="cod" v-model="form.payment_method" class="accent-brand-600" />
                </div>
                <p class="text-[11px] text-stone-500 mt-2">Pay upon courier delivery</p>
              </label>
            </div>

            <!-- Card Simulation details if Stripe -->
            <div v-if="form.payment_method === 'stripe'" class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-3 text-xs">
              <p class="font-semibold text-stone-600">Simulated Stripe Card Test Element:</p>
              <input
                type="text"
                readonly
                value="•••• •••• •••• 4242   Exp: 12/28   CVC: 123"
                class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-stone-700 font-mono"
              />
              <span class="text-[10px] text-emerald-700 font-medium">✓ Stripe 3D Secure verified sandbox intent ready</span>
            </div>
          </div>

          <div v-if="errorMessage" class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-semibold">
            {{ errorMessage }}
          </div>

          <button
            type="submit"
            :disabled="loading || cartStore.items.length === 0"
            class="w-full py-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <span v-if="loading">Processing Atomic Order & Stock Allocation...</span>
            <span v-else>Complete Purchase (${{ cartStore.total.toFixed(2) }})</span>
          </button>
        </form>

        <!-- Right: Order Summary -->
        <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-4">
          <h3 class="font-serif font-bold text-base text-stone-900">Order Summary ({{ cartStore.itemCount }})</h3>

          <div class="space-y-3 max-h-64 overflow-y-auto divide-y divide-stone-100">
            <div v-for="item in cartStore.items" :key="item.id" class="pt-3 first:pt-0 flex gap-3 text-xs">
              <img :src="item.variant?.image_url" class="w-12 h-14 object-cover rounded-lg border border-stone-200" />
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-stone-900 truncate">{{ item.variant?.product?.name }}</p>
                <div v-if="item.variant?.shade_hex" class="flex items-center gap-1 mt-0.5">
                  <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: item.variant.shade_hex }"></span>
                  <span class="text-[11px] text-stone-500 truncate">{{ item.variant.shade_name }}</span>
                </div>
                <p class="text-stone-400 text-[10px]">Qty: {{ item.quantity }}</p>
              </div>
              <span class="font-bold text-stone-900">${{ (item.variant?.price * item.quantity).toFixed(2) }}</span>
            </div>
          </div>

          <div class="pt-3 border-t border-stone-100 space-y-1.5 text-xs text-stone-600">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span>${{ cartStore.subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="cartStore.discount > 0" class="flex justify-between text-emerald-600 font-semibold">
              <span>Discount ({{ cartStore.coupon?.code }})</span>
              <span>-${{ cartStore.discount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Shipping</span>
              <span :class="cartStore.shipping === 0 ? 'text-emerald-600 font-semibold' : ''">
                {{ cartStore.shipping === 0 ? 'FREE' : `$${cartStore.shipping.toFixed(2)}` }}
              </span>
            </div>
            <div class="flex justify-between">
              <span>Sales Tax</span>
              <span>${{ cartStore.tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-stone-900 pt-2 border-t border-stone-100">
              <span>Total Amount</span>
              <span>${{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import confetti from 'canvas-confetti';
import {
  CheckCircle2, ArrowRight, Mail, MapPin, CreditCard
} from 'lucide-vue-next';

const cartStore = useCartStore();
const authStore = useAuthStore();

const loading = ref(false);
const errorMessage = ref('');
const confirmedOrder = ref(null);

const form = reactive({
  customer_name: authStore.user?.name || 'Sophia Chen',
  customer_email: authStore.user?.email || 'customer@beautyvault.com',
  customer_phone: authStore.user?.phone || '+1 (555) 987-6543',
  shipping_address: {
    recipient_name: authStore.user?.name || 'Sophia Chen',
    street: '742 Evergreen Terrace, Apt 4B',
    city: 'Beverly Hills',
    state: 'CA',
    postal_code: '90210',
    country: 'US',
  },
  payment_method: 'stripe',
  coupon_code: '',
  items: [],
});

onMounted(async () => {
  await cartStore.fetchCart();
  if (cartStore.coupon) {
    form.coupon_code = cartStore.coupon.code;
  }
});

const handleCheckout = async () => {
  if (cartStore.items.length === 0) return;
  loading.value = true;
  errorMessage.value = '';

  form.items = cartStore.items.map(item => ({
    product_variant_id: item.product_variant_id,
    quantity: item.quantity,
  }));

  form.coupon_code = cartStore.coupon?.code || '';

  try {
    const res = await api.post('/checkout', form);
    confirmedOrder.value = res.data.order;
    cartStore.fetchCart();

    // Persist order details to localStorage so Track Order auto-fills automatically!
    try {
      localStorage.setItem('beautyvault_last_order', JSON.stringify({
        order_number: confirmedOrder.value.order_number,
        email: confirmedOrder.value.customer_email || form.customer_email,
        placed_at: new Date().toISOString()
      }));
    } catch (e) {
      console.warn('Could not save to localStorage', e);
    }

    // Trigger celebration confetti
    try {
      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 }
      });
    } catch (e) {}

  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Checkout failed. Please check inventory.';
  } finally {
    loading.value = false;
  }
};
</script>
