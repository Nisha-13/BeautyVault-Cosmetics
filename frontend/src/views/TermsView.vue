<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

      <!-- Header -->
      <div class="space-y-3 pb-8 border-b border-stone-200">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-100 text-stone-700 border border-stone-200">
          <FileText class="w-3.5 h-3.5" />
          <span>Terms & Conditions</span>
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
          Terms of Service
        </h1>
        <p class="text-xs sm:text-sm text-stone-500">
          Effective Date: September 2026 • BeautyVault E-Commerce Platform
        </p>
      </div>

      <!-- Legal Content -->
      <div class="prose prose-stone max-w-none text-xs sm:text-sm text-stone-600 leading-relaxed space-y-8">
        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">1. Acceptance of Agreement</h2>
          <p>
            By accessing or ordering from the BeautyVault website, you agree to abide by these Terms of Service and all applicable federal and state cosmetics dispensing standards. If you disagree with any terms, please discontinue use of the platform.
          </p>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">2. Product Formulations & Clinical Disclaimers</h2>
          <p>
            The products, ingredient analyses, and diagnostic algorithms available on BeautyVault are designed for cosmetic and skincare nourishment purposes. They are not intended to diagnose, cure, mitigate, or treat clinical dermatological diseases. Always perform a 24-hour patch test prior to full facial application.
          </p>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">3. Orders, FIFO Batches & Pricing</h2>
          <p>
            All orders placed through our checkout pipeline are allocated based on real-time FIFO (First-In, First-Out) warehouse lot numbers. While we make every effort to display accurate pricing, stock availability, and shade descriptions, we reserve the right to correct typographical inaccuracies or cancel orders where pricing errors occurred.
          </p>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">4. 30-Day Zero-Risk Shade Guarantee</h2>
          <p>
            If a foundation, concealer, or tinted moisturizer shade purchased on BeautyVault does not blend seamlessly with your skin tone, you may exchange it for an alternative shade within thirty (30) calendar days of delivery, subject to our standard return policy guidelines.
          </p>
        </section>
      </div>

    </div>
  </div>
</template>

<script setup>
import { FileText } from 'lucide-vue-next';
</script>
