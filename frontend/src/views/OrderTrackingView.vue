<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Header -->
    <div class="text-center max-w-xl mx-auto space-y-2">
      <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Real-Time WebSocket Fulfillment</span>
      <h1 class="font-serif text-3xl font-bold text-stone-900">Track Your Cosmetic Order</h1>
      <p class="text-xs text-stone-500">
        Enter your order reference code and email address to view live fulfillment stages.
      </p>
    </div>

    <!-- Search Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft max-w-2xl mx-auto">
      
      <!-- Auto-fill indicator badge -->
      <div v-if="autoFilledNotice" class="mb-5 p-3.5 bg-brand-50/80 border border-brand-200 rounded-2xl flex items-center justify-between text-xs text-brand-900 animate-fade-in">
        <div class="flex items-center gap-2">
          <Sparkles class="w-4 h-4 text-brand-600 shrink-0" />
          <span>{{ autoFilledNotice }}</span>
        </div>
        <button
          type="button"
          @click="clearAutoFill"
          class="text-stone-500 hover:text-stone-900 underline text-[11px] font-medium"
        >
          Track different order
        </button>
      </div>

      <form @submit.prevent="trackOrder" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <label class="block font-semibold text-stone-700 mb-1">Order Number</label>
          <input
            v-model="orderNumber"
            type="text"
            required
            placeholder="e.g. BV-2026-98124"
            class="w-full uppercase font-mono bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
          />
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Email Address</label>
          <input
            v-model="email"
            type="email"
            required
            placeholder="e.g. customer@beautyvault.com"
            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3.5 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
          />
        </div>

        <div class="sm:col-span-2 flex items-center justify-between pt-2">
          <button
            type="button"
            @click="fillDemoOrder"
            class="text-[11px] text-brand-600 hover:underline font-semibold"
          >
            ⚡ Click to track sample seeded order
          </button>

          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2.5 rounded-full bg-stone-900 hover:bg-stone-800 text-white font-semibold shadow-sm disabled:opacity-50"
          >
            <span v-if="loading">Locating Order...</span>
            <span v-else>Track Shipment</span>
          </button>
        </div>
      </form>
      <p v-if="error" class="mt-3 text-xs text-rose-600 font-medium">{{ error }}</p>
    </div>

    <!-- Live Tracking Results & WebSocket Stream -->
    <div v-if="orderData" class="space-y-8 animate-fade-in">
      
      <!-- WebSocket Live Badge -->
      <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-xs text-emerald-800">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
          <span><strong>WebSocket Streaming Active:</strong> Subscribed to live updates on channel <code>orders.{{ orderData.id }}</code></span>
        </div>
        <span class="text-[10px] font-mono text-emerald-600">Reverb / TCP</span>
      </div>

      <!-- Live Notification Banner when updated via WebSockets -->
      <div v-if="liveUpdateNotice" class="p-4 bg-brand-50 border border-brand-300 rounded-2xl text-xs text-brand-900 font-semibold flex items-center gap-2 animate-bounce">
        <Sparkles class="w-4 h-4 text-brand-600" />
        <span>{{ liveUpdateNotice }}</span>
      </div>

      <!-- Tracking Stepper -->
      <div class="bg-white p-6 sm:p-10 rounded-3xl border border-stone-200 shadow-sm space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Order Reference</span>
            <h2 class="font-serif text-2xl font-bold text-stone-900">{{ orderData.order_number }}</h2>
          </div>
          <div class="text-right">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Current Status</span>
            <p class="font-bold text-base text-brand-600 uppercase">{{ orderData.status }}</p>
          </div>
        </div>

        <!-- 4-Stage Stepper -->
        <div class="grid grid-cols-4 gap-2 relative">
          <div
            v-for="(step, idx) in stages"
            :key="step.key"
            class="text-center relative z-10 space-y-2"
          >
            <div
              class="w-10 h-10 rounded-full mx-auto flex items-center justify-center border-2 transition-all font-bold text-xs"
              :class="isStageComplete(step.key) ? 'bg-brand-600 border-brand-600 text-white shadow-md' : 'bg-stone-50 border-stone-200 text-stone-400'"
            >
              <Check v-if="isStageComplete(step.key)" class="w-4 h-4" />
              <span v-else>{{ idx + 1 }}</span>
            </div>
            <div>
              <p class="font-bold text-xs text-stone-900">{{ step.title }}</p>
              <p class="text-[10px] text-stone-400 hidden sm:block">{{ step.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Carrier & Tracking Info -->
        <div v-if="orderData.tracking_number" class="p-4 bg-stone-50 rounded-2xl border border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
          <div>
            <span class="text-stone-400 font-semibold">Shipping Carrier:</span>
            <span class="font-bold text-stone-800 ml-1">{{ orderData.shipping_carrier || 'DHL Express' }}</span>
          </div>
          <div>
            <span class="text-stone-400 font-semibold">Tracking Number:</span>
            <span class="font-mono font-bold text-stone-800 ml-1">{{ orderData.tracking_number }}</span>
          </div>
        </div>

        <!-- Ordered Items in this Shipment -->
        <div v-if="orderData.items && orderData.items.length > 0" class="space-y-3 pt-4 border-t border-stone-100">
          <div class="flex items-center justify-between">
            <h4 class="font-serif font-bold text-sm text-stone-900 flex items-center gap-1.5">
              <Package class="w-4 h-4 text-brand-600" />
              <span>Purchased Items in this Shipment</span>
            </h4>
            <span class="text-[11px] font-semibold text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded-full">
              {{ orderData.items.length }} {{ orderData.items.length === 1 ? 'item' : 'items' }}
            </span>
          </div>

          <div class="divide-y divide-stone-100 bg-stone-50/70 rounded-2xl border border-stone-200/80 p-3 sm:p-4">
            <div
              v-for="item in orderData.items"
              :key="item.id"
              class="flex items-center gap-3.5 py-3 first:pt-0 last:pb-0"
            >
              <div class="w-14 h-14 rounded-xl overflow-hidden bg-white border border-stone-200 shrink-0">
                <img
                  :src="item.product?.thumbnail_url || 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=200&q=80'"
                  :alt="item.product?.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider">
                  {{ item.product?.brand?.name || 'BeautyVault' }}
                </p>
                <p class="font-serif font-bold text-xs text-stone-900 truncate">
                  {{ item.product?.name || 'Cosmetic Item' }}
                </p>
                <p v-if="item.variant?.shade_name || item.variant?.title" class="text-[11px] text-stone-500">
                  Shade / Option: <span class="font-medium text-stone-700">{{ item.variant?.shade_name || item.variant?.title }}</span>
                </p>
              </div>

              <div class="text-right shrink-0">
                <p class="text-xs font-bold text-stone-900">
                  ${{ Number(item.unit_price || 0).toFixed(2) }}
                </p>
                <p class="text-[11px] text-stone-500">
                  Qty: <span class="font-semibold text-stone-800">{{ item.quantity }}</span>
                </p>
              </div>
            </div>

            <!-- Order Total Summary -->
            <div class="mt-3 pt-3 border-t border-stone-200/60 flex items-center justify-between text-xs">
              <span class="font-medium text-stone-600">Total Order Value:</span>
              <span class="font-bold text-sm text-stone-900 font-mono">${{ Number(orderData.total_amount || 0).toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <!-- Status History Timeline -->
        <div class="space-y-4 pt-4 border-t border-stone-100">
          <h4 class="font-serif font-bold text-sm text-stone-900">Fulfillment Audit Timeline</h4>
          <div class="space-y-3">
            <div
              v-for="h in orderData.status_histories"
              :key="h.id"
              class="flex gap-4 items-start text-xs"
            >
              <div class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 flex-shrink-0"></div>
              <div>
                <p class="font-bold text-stone-800 capitalize">{{ h.status }}: <span class="font-normal text-stone-600">{{ h.comment }}</span></p>
                <p class="text-[10px] text-stone-400">{{ new Date(h.created_at).toLocaleString() }}</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../services/api';
import { getEcho } from '../services/echo';
import { Check, Sparkles, Package } from 'lucide-vue-next';

const route = useRoute();

const orderNumber = ref('');
const email = ref('');
const loading = ref(false);
const error = ref('');
const orderData = ref(null);
const liveUpdateNotice = ref('');
const autoFilledNotice = ref('');

const stages = [
  { key: 'confirmed', title: 'Confirmed', desc: 'Payment verified' },
  { key: 'preparing', title: 'Preparing', desc: 'FIFO lot packed' },
  { key: 'dispatched', title: 'Dispatched', desc: 'Handed to carrier' },
  { key: 'delivered', title: 'Delivered', desc: 'Package arrived' },
];

const stageOrder = ['pending', 'confirmed', 'preparing', 'dispatched', 'delivered'];

const isStageComplete = (stageKey) => {
  const currentIdx = stageOrder.indexOf(orderData.value?.status || 'pending');
  const stageIdx = stageOrder.indexOf(stageKey);
  return currentIdx >= stageIdx;
};

const fillDemoOrder = () => {
  orderNumber.value = 'BV-2026-98124';
  email.value = 'customer@beautyvault.com';
  trackOrder();
};

const trackOrder = async () => {
  if (!orderNumber.value || !email.value) return;
  loading.value = true;
  error.value = '';
  liveUpdateNotice.value = '';

  try {
    const res = await api.post('/orders/track', {
      order_number: orderNumber.value.trim(),
      customer_email: email.value.trim(),
    });
    orderData.value = res.data.order;
    subscribeToWebSockets(orderData.value.id);
  } catch (err) {
    error.value = err.response?.data?.message || 'Order not found. Please verify details.';
    orderData.value = null;
  } finally {
    loading.value = false;
  }
};

const subscribeToWebSockets = (orderId) => {
  const echo = getEcho();
  if (echo) {
    echo.channel(`orders.${orderId}`)
      .listen('.OrderStatusUpdated', (e) => {
        orderData.value.status = e.status;
        if (e.tracking_number) orderData.value.tracking_number = e.tracking_number;
        liveUpdateNotice.value = `Live Update: Order status transitioned to "${e.status.toUpperCase()}"!`;
        if (orderData.value.status_histories) {
          orderData.value.status_histories.unshift({
            id: Date.now(),
            status: e.status,
            comment: e.comment || `Status updated via Reverb WebSockets`,
            created_at: new Date().toISOString(),
          });
        }
      });
  }
};

const clearAutoFill = () => {
  autoFilledNotice.value = '';
  orderNumber.value = '';
  email.value = '';
  orderData.value = null;
  error.value = '';
};

onMounted(async () => {
  // 1. Check URL query params first
  if (route.query.order_number && route.query.email) {
    orderNumber.value = route.query.order_number;
    email.value = route.query.email;
    autoFilledNotice.value = `Auto-filled from checkout: ${orderNumber.value}`;
    trackOrder();
    return;
  }

  // 2. Check localStorage for last placed order (even guest checkout!)
  try {
    const savedOrder = JSON.parse(localStorage.getItem('beautyvault_last_order') || 'null');
    if (savedOrder?.order_number && savedOrder?.email) {
      orderNumber.value = savedOrder.order_number;
      email.value = savedOrder.email;
      autoFilledNotice.value = `Auto-detected your recent order: ${savedOrder.order_number} (${savedOrder.email})`;
      trackOrder();
      return;
    }
  } catch (e) {}

  // 3. Check logged-in user profile & fetch latest customer order
  try {
    const savedUser = JSON.parse(localStorage.getItem('beautyvault_user') || 'null');
    if (savedUser?.email) {
      email.value = savedUser.email;
      const res = await api.get('/orders');
      const latest = res.data?.data?.[0];
      if (latest?.order_number) {
        orderNumber.value = latest.order_number;
        autoFilledNotice.value = `Auto-filled latest order from your account: ${latest.order_number}`;
        trackOrder();
      }
    }
  } catch (e) {}
});

onUnmounted(() => {
  const echo = getEcho();
  if (echo && orderData.value) {
    echo.leaveChannel(`orders.${orderData.value.id}`);
  }
});
</script>
