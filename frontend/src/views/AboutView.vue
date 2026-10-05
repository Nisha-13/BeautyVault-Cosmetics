<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12 sm:py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

      <!-- Breadcrumbs & Header -->
      <div class="space-y-4 text-center max-w-2xl mx-auto">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
          <Sparkles class="w-3.5 h-3.5" />
          <span>The BeautyVault Philosophy</span>
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-stone-900 leading-tight">
          Where Clinical Chemistry Meets Inclusive Luxury.
        </h1>
        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
          Founded on radical transparency, BeautyVault eliminates shade guesswork and comedogenic irritants through science-backed formulation indexing and FIFO freshness tracking.
        </p>
      </div>

      <!-- Hero Visual -->
      <div class="relative rounded-3xl overflow-hidden aspect-[21/9] shadow-xl border border-stone-200/80">
        <img
          src="https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=1400&q=85"
          alt="BeautyVault Laboratory"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
        <div class="absolute bottom-6 left-6 right-6 text-white flex flex-col sm:flex-row sm:items-end justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-rose-300 uppercase tracking-widest">Our Standard</p>
            <p class="font-serif text-xl sm:text-2xl font-bold">100% Transparent Ingredient Indexing</p>
          </div>
          <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold border border-white/30 shrink-0">
            Dermatologist Formulated
          </span>
        </div>
      </div>

      <!-- Core Pillars Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft space-y-3">
          <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-lg">
            🎨
          </div>
          <h3 class="font-serif font-bold text-lg text-stone-900">Inclusive Undertone Matrix</h3>
          <p class="text-xs text-stone-600 leading-relaxed">
            Every skin tone has unique red, yellow, neutral, or olive chroma. Our 50+ shade foundation system calibrates undertone depth so your coverage never oxidizes orange or leaves an ashy cast.
          </p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft space-y-3">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
            🧪
          </div>
          <h3 class="font-serif font-bold text-lg text-stone-900">Zero Comedogenic Guesswork</h3>
          <p class="text-xs text-stone-600 leading-relaxed">
            We test and score every active against clinical pore-clogging scales (0 to 5 rating). If a product triggers acne or sensitivity, our Skin Diagnostic algorithms flag it immediately.
          </p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-soft space-y-3">
          <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
            📦
          </div>
          <h3 class="font-serif font-bold text-lg text-stone-900">Strict FIFO Batch Freshness</h3>
          <p class="text-xs text-stone-600 leading-relaxed">
            Active ingredients like Vitamin C, Niacinamide, and Retinol degrade over time. Our warehouse operates on automated First-In, First-Out lot batch tracking to guarantee maximum chemical potency.
          </p>
        </div>
      </div>

      <!-- Stats Bar (Dynamically Computed from Database) -->
      <div class="bg-stone-900 text-white rounded-3xl p-8 sm:p-10 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
        <div>
          <p class="font-serif text-3xl sm:text-4xl font-bold text-brand-300">{{ storeStats.inclusive_undertones }}</p>
          <p class="text-xs text-stone-400 mt-1">Calibrated Undertones</p>
        </div>
        <div>
          <p class="font-serif text-3xl sm:text-4xl font-bold text-brand-300">{{ storeStats.cruelty_free_percentage }}</p>
          <p class="text-xs text-stone-400 mt-1">Cruelty-Free Certified</p>
        </div>
        <div>
          <p class="font-serif text-3xl sm:text-4xl font-bold text-brand-300">{{ storeStats.guarantee_days }}</p>
          <p class="text-xs text-stone-400 mt-1">Shade Match Guarantee</p>
        </div>
        <div>
          <p class="font-serif text-3xl sm:text-4xl font-bold text-brand-300">{{ storeStats.verified_reviews }}</p>
          <p class="text-xs text-stone-400 mt-1">Verified Customers</p>
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center pt-4">
        <router-link
          to="/catalog"
          class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs sm:text-sm transition-all shadow-glow hover:scale-105"
        >
          <span>Explore Verified Formulations</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import { Sparkles, ArrowRight } from 'lucide-vue-next';

const storeStats = ref({
  inclusive_undertones: '50+',
  cruelty_free_percentage: '100%',
  guarantee_days: '30-Day',
  verified_reviews: '18.4K+',
});

onMounted(async () => {
  try {
    const res = await api.get('/metadata');
    if (res.data?.stats) {
      storeStats.value = res.data.stats;
    }
  } catch (err) {
    console.error('Failed to load store stats:', err);
  }
});
</script>
