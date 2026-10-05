<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

      <!-- Header -->
      <div class="space-y-3 pb-8 border-b border-stone-200">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-100 text-stone-700 border border-stone-200">
          <Shield class="w-3.5 h-3.5" />
          <span>Legal & Privacy Transparency</span>
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
          Privacy Policy
        </h1>
        <p class="text-xs sm:text-sm text-stone-500">
          Last updated: September 2026 • BeautyVault Labs Inc.
        </p>
      </div>

      <!-- Legal Content Body -->
      <div class="prose prose-stone max-w-none text-xs sm:text-sm text-stone-600 leading-relaxed space-y-8">

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">1. Information We Collect</h2>
          <p>
            At BeautyVault, we value your privacy and skin chemistry data. We collect information you provide directly to us when creating an account, completing the <strong>Skin Diagnostic Quiz</strong>, using the <strong>Undertone Shade Matcher</strong>, or making a purchase.
          </p>
          <ul class="list-disc pl-5 space-y-1.5 text-stone-600">
            <li><strong>Personal Identity:</strong> Full name, email address, shipping address, and telephone number.</li>
            <li><strong>Dermatological & Skin Metrics:</strong> Self-reported skin type, undertone temperature, sensitivity allergens, and shade depth preferences.</li>
            <li><strong>Transactional Data:</strong> Order history, batch tracking lot allocations, payment method tokens (processed via PCI-DSS compliant gateways; we never store raw credit card numbers).</li>
          </ul>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">2. How We Use Your Diagnostic Data</h2>
          <p>
            Your skin diagnostic profile is strictly utilized to compute the real-time <strong>Skin Compatibility Score</strong> across formulations in our catalog and to recommend personalized undertone foundations. We do <em>not</em> sell or rent your biometric or dermal profile data to third-party data brokers.
          </p>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">3. Data Security & Storage</h2>
          <p>
            We implement industry-standard encryption protocols (TLS 1.3, AES-256 for persistent records) and tokenized authentication. Access to inventory lot records and customer account databases is restricted to authorized laboratory and customer-care personnel.
          </p>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">4. Your Consumer Rights (GDPR & CCPA)</h2>
          <p>
            You retain full ownership of your account and skin chemistry records. At any time, you may:
          </p>
          <ul class="list-disc pl-5 space-y-1.5 text-stone-600">
            <li>Request a copy of all personal skin profiles and order records associated with your email.</li>
            <li>Update or erase your dermatological preferences directly from your customer dashboard.</li>
            <li>Opt out of marketing communications via the one-click unsubscribe footer.</li>
          </ul>
        </section>

        <section class="space-y-3 bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft">
          <h2 class="font-serif font-bold text-lg text-stone-900">5. Contact Our Data Protection Chemist</h2>
          <p>
            For privacy inquiries or data erasure requests, please contact our legal compliance desk at:
            <br />
            <a href="mailto:privacy@beautyvault.com" class="text-brand-600 font-semibold hover:underline">privacy@beautyvault.com</a>
          </p>
        </section>

      </div>

    </div>
  </div>
</template>

<script setup>
import { Shield } from 'lucide-vue-next';
</script>
