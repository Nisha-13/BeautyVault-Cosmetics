<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

      <!-- Header -->
      <div class="space-y-3 pb-8 border-b border-stone-200 text-center max-w-2xl mx-auto">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
          <Truck class="w-3.5 h-3.5" />
          <span>Fulfillment & Delivery Assurance</span>
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
          Shipping, Exchanges &amp; Returns
        </h1>
        <p class="text-xs sm:text-sm text-stone-500">
          Climate-controlled parcel handling, 2-day priority dispatch, and hassle-free 30-day shade swaps.
        </p>
      </div>

      <!-- Quick Highlights Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-soft text-center space-y-2">
          <span class="text-3xl">🚀</span>
          <h3 class="font-serif font-bold text-stone-900 text-sm">Free 2-Day Shipping</h3>
          <p class="text-xs text-stone-500">Complimentary on all domestic orders over $40.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-soft text-center space-y-2">
          <span class="text-3xl">🎨</span>
          <h3 class="font-serif font-bold text-stone-900 text-sm">30-Day Shade Swap</h3>
          <p class="text-xs text-stone-500">Not your exact undertone? Swap shades free.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-soft text-center space-y-2">
          <span class="text-3xl">📦</span>
          <h3 class="font-serif font-bold text-stone-900 text-sm">FIFO Batch Packed</h3>
          <p class="text-xs text-stone-500">Fresh formulations dispatched in thermo-safe boxes.</p>
        </div>
      </div>

      <!-- Policy Details -->
      <div class="space-y-6 text-xs sm:text-sm text-stone-600 leading-relaxed">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft space-y-3">
          <h2 class="font-serif font-bold text-lg text-stone-900">Domestic & International Rates</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="border-b border-stone-200 text-stone-800 font-bold">
                <tr>
                  <th class="py-2.5">Tier</th>
                  <th class="py-2.5">Delivery Time</th>
                  <th class="py-2.5">Order Threshold</th>
                  <th class="py-2.5">Cost</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr>
                  <td class="py-2.5 font-semibold text-stone-900">Standard Priority</td>
                  <td class="py-2.5">2–3 Business Days</td>
                  <td class="py-2.5">Orders over $40</td>
                  <td class="py-2.5 font-bold text-emerald-600">FREE</td>
                </tr>
                <tr>
                  <td class="py-2.5 font-semibold text-stone-900">Standard Ground</td>
                  <td class="py-2.5">3–5 Business Days</td>
                  <td class="py-2.5">Orders under $40</td>
                  <td class="py-2.5">$4.95</td>
                </tr>
                <tr>
                  <td class="py-2.5 font-semibold text-stone-900">Next-Day Overnight</td>
                  <td class="py-2.5">1 Business Day</td>
                  <td class="py-2.5">Any Order</td>
                  <td class="py-2.5">$14.95</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft space-y-3">
          <h2 class="font-serif font-bold text-lg text-stone-900">How to Return or Exchange</h2>
          <ol class="list-decimal pl-5 space-y-2 text-stone-600">
            <li>Visit your <router-link to="/account" class="text-brand-600 font-semibold underline">Customer Dashboard</router-link> and click "Orders &amp; Tracking".</li>
            <li>Select the order and tap <strong>"Request Return / Exchange"</strong>.</li>
            <li>Choose whether you want a different shade or full refund to original payment.</li>
            <li>Print the prepaid carrier label or show the digital QR code at any postal branch.</li>
          </ol>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { Truck } from 'lucide-vue-next';
</script>
