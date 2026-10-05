<template>
  <div class="min-h-screen bg-[#FDFBF9] py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

      <!-- Header -->
      <div class="text-center space-y-3 max-w-2xl mx-auto">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
          <HelpCircle class="w-3.5 h-3.5" />
          <span>Support & Intelligence</span>
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
          Frequently Asked Questions
        </h1>
        <p class="text-xs sm:text-sm text-stone-500">
          Everything you need to know about our clinical formulas, shade matching algorithms, and batch guarantees.
        </p>
      </div>

      <!-- Category Filter Pills -->
      <div class="flex items-center justify-center gap-2 flex-wrap">
        <button
          v-for="cat in faqCategories"
          :key="cat"
          @click="selectedCategory = cat"
          class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all"
          :class="selectedCategory === cat
            ? 'bg-stone-900 text-white shadow-sm'
            : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-50'"
        >
          {{ cat }}
        </button>
      </div>

      <!-- FAQ Accordion List -->
      <div class="space-y-3">
        <div
          v-for="(item, index) in filteredFaqs"
          :key="index"
          class="bg-white rounded-2xl border border-stone-200/80 shadow-soft overflow-hidden transition-all duration-200"
        >
          <button
            @click="toggleFaq(index)"
            class="w-full text-left p-5 sm:p-6 flex items-center justify-between gap-4 font-serif font-bold text-stone-900 text-sm sm:text-base hover:text-brand-600 transition-colors"
          >
            <span>{{ item.q }}</span>
            <ChevronDown
              class="w-4 h-4 text-stone-400 shrink-0 transition-transform duration-200"
              :class="{ 'rotate-180 text-brand-600': openIndices.includes(index) }"
            />
          </button>
          <div
            v-if="openIndices.includes(index)"
            class="px-5 sm:px-6 pb-5 sm:pb-6 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3"
          >
            {{ item.a }}
          </div>
        </div>
      </div>

      <!-- Still Have Questions Card -->
      <div class="bg-brand-50/70 border border-brand-200/70 rounded-3xl p-6 sm:p-8 text-center space-y-4">
        <h3 class="font-serif font-bold text-lg text-stone-900">Still have questions regarding your skin profile?</h3>
        <p class="text-xs text-stone-600 max-w-md mx-auto">
          Take our 60-second diagnostic or reach out to our licensed dermatological cosmetic concierges.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
          <router-link
            to="/skin-quiz"
            class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs transition-colors shadow-sm"
          >
            Take Skin Diagnostic
          </router-link>
          <router-link
            to="/shade-finder"
            class="px-6 py-2.5 rounded-full bg-white border border-stone-300 text-stone-800 font-semibold text-xs hover:bg-stone-50 transition-colors"
          >
            Find My Shade Match
          </router-link>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { HelpCircle, ChevronDown } from 'lucide-vue-next';

const selectedCategory = ref('All');
const faqCategories = ['All', 'Shade Matching', 'Formulation & Safety', 'Shipping & Returns', 'FIFO Batches'];

const faqs = [
  {
    category: 'Shade Matching',
    q: 'How does the AI Undertone Matrix calculate my shade?',
    a: 'Our algorithm cross-examines 3 variables: depth level (1–10 scale), undertone temperature (warm golden, cool rosy, neutral, or olive), and skin dryness type. This ensures that the foundation neither oxidizes darker over the course of the day nor leaves an ashy tone in natural daylight.'
  },
  {
    category: 'Shade Matching',
    q: 'What if the foundation shade I ordered doesn\'t match my skin?',
    a: 'Every foundation purchased on BeautyVault is backed by our 30-Day Zero-Risk Shade Guarantee. Simply request a shade exchange from your customer dashboard, and we will ship you your alternative match free of charge.'
  },
  {
    category: 'Formulation & Safety',
    q: 'What does the Comedogenic Rating (0/5) mean?',
    a: 'The comedogenic scale ranks ingredients on their likelihood to clog pores and cause micro-comedones or breakouts. A 0/5 rating signifies that the ingredient has zero known pore-clogging propensities, making it completely safe for acne-prone, reactive, and congested skin.'
  },
  {
    category: 'Formulation & Safety',
    q: 'Are all products 100% cruelty-free and vegan?',
    a: 'Over 90% of our catalog is certified 100% vegan. Every brand hosted on BeautyVault must strictly adhere to Leaping Bunny or PETA cruelty-free auditing standards. We never retail formulas tested on animals.'
  },
  {
    category: 'FIFO Batches',
    q: 'What is FIFO Freshness Lot Tracking?',
    a: 'FIFO stands for First-In, First-Out inventory logistics. Active compounds like pure Niacinamide, Retinoids, Vitamin C, and Sunscreen UV filters degrade with age. Our automated lot batching tracks manufacturing and expiry dates in real time so you never receive stock nearing end-of-shelf-life.'
  },
  {
    category: 'Shipping & Returns',
    q: 'How long does shipping take and what are the delivery costs?',
    a: 'We offer Complimentary 2-Day Priority Shipping on all orders exceeding $40. Standard delivery for orders under $40 is a flat $4.95 with full real-time carrier tracking provided via SMS and web link.'
  },
  {
    category: 'Shipping & Returns',
    q: 'How do I initiate a return or exchange?',
    a: 'Log into your customer dashboard, navigate to the "Orders & Tracking" tab, and select "Request Return / Exchange". You will receive an instant prepaid return label and QR code.'
  }
];

const openIndices = ref([0]);

const toggleFaq = (index) => {
  if (openIndices.value.includes(index)) {
    openIndices.value = openIndices.value.filter(i => i !== index);
  } else {
    openIndices.value.push(index);
  }
};

const filteredFaqs = computed(() => {
  if (selectedCategory.value === 'All') return faqs;
  return faqs.filter(f => f.category === selectedCategory.value);
});
</script>
