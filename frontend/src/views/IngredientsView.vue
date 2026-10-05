<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Header -->
    <div class="max-w-2xl space-y-2">
      <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Formulation Transparency</span>
      <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">
        Clinical INCI Ingredient Glossary
      </h1>
      <p class="text-xs sm:text-sm text-stone-500 leading-relaxed">
        We believe in 100% cosmetic transparency. Explore our non-comedogenic active ingredient database, EWG toxicity scores, and clinical skincare functions.
      </p>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="relative w-full md:w-80">
        <input
          v-model="search"
          @input="fetchIngredients"
          type="text"
          placeholder="Search Niacinamide, Salicylic, Retinol..."
          class="w-full text-xs bg-stone-50 border border-stone-200 rounded-full pl-9 pr-4 py-2.5 text-stone-800 focus:outline-none focus:border-brand-500"
        />
        <Search class="w-4 h-4 text-stone-400 absolute left-3 top-3 pointer-events-none" />
      </div>

      <!-- Filter Chips -->
      <div class="flex flex-wrap items-center gap-2 text-xs">
        <button
          @click="activeOnly = !activeOnly; fetchIngredients()"
          class="px-3.5 py-1.5 rounded-full border transition-all flex items-center gap-1.5"
          :class="activeOnly ? 'bg-brand-600 text-white border-brand-600 font-semibold shadow-sm' : 'bg-stone-50 text-stone-600 border-stone-200 hover:bg-stone-100'"
        >
          <span>🔬 Key Actives Only</span>
          <span v-if="activeOnly" class="w-1.5 h-1.5 rounded-full bg-white"></span>
        </button>
        <button
          @click="nonComedogenicOnly = !nonComedogenicOnly; fetchIngredients()"
          class="px-3.5 py-1.5 rounded-full border transition-all flex items-center gap-1.5"
          :class="nonComedogenicOnly ? 'bg-emerald-600 text-white border-emerald-600 font-semibold shadow-sm' : 'bg-stone-50 text-stone-600 border-stone-200 hover:bg-stone-100'"
        >
          <span>✨ Non-Comedogenic (Rating 0)</span>
          <span v-if="nonComedogenicOnly" class="w-1.5 h-1.5 rounded-full bg-white"></span>
        </button>
        <button
          @click="showGuide = !showGuide"
          class="px-3 py-1.5 rounded-full bg-stone-100 text-stone-600 hover:text-stone-900 border border-stone-200 transition-colors text-xs font-medium"
        >
          {{ showGuide ? 'Hide Clinical Guide' : 'ℹ️ Science & Rating Guide' }}
        </button>
      </div>
    </div>

    <!-- Interactive Clinical Science Guide Box -->
    <div v-if="showGuide" class="p-6 bg-stone-900 text-white rounded-3xl space-y-4 animate-fade-in shadow-lg">
      <div class="flex items-center justify-between pb-3 border-b border-stone-800">
        <h3 class="font-serif font-bold text-lg text-amber-300 flex items-center gap-2">
          <span>🧪 Cosmetic Chemistry & Diagnostic Guide</span>
        </h3>
        <button @click="showGuide = false" class="text-stone-400 hover:text-white text-xs">✕ Close</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-stone-300">
        <div class="space-y-1.5 p-4 bg-stone-850 rounded-2xl border border-stone-800">
          <p class="font-bold text-sm text-brand-300 flex items-center gap-1.5">
            <span>🔬 Key Actives Only</span>
          </p>
          <p class="leading-relaxed text-stone-300">
            <strong>What it means:</strong> Skincare products consist of active ingredients (therapeutic chemical agents) and inactive bases (water, emulsifiers, stabilizers).
          </p>
          <p class="leading-relaxed text-stone-400">
            <strong>How it works:</strong> Activating this filter isolates clinically validated molecules like <em>Niacinamide (Vitamin B3)</em>, <em>Retinol</em>, <em>Salicylic Acid (BHA)</em>, <em>Peptides</em>, and <em>Ceramides</em> that directly alter cellular activity, synthesize collagen, and dissolve pores.
          </p>
        </div>

        <div class="space-y-1.5 p-4 bg-stone-850 rounded-2xl border border-stone-800">
          <p class="font-bold text-sm text-emerald-400 flex items-center gap-1.5">
            <span>✨ Non-Comedogenic (Rating 0)</span>
          </p>
          <p class="leading-relaxed text-stone-300">
            <strong>What it means:</strong> The Comedogenic Scale ranges from <strong>0 (Will NOT clog pores)</strong> to <strong>5 (High likelihood of clogging pores & acne)</strong>.
          </p>
          <p class="leading-relaxed text-stone-400">
            <strong>How it works:</strong> Activating this filter strictly displays ingredients rated <strong>0/5</strong>. These are completely safe for acne-prone, congested, oily, and sensitive skin types, ensuring zero microcomedones or breakout triggers.
          </p>
        </div>
      </div>
    </div>

    <!-- Active Filter Notification Pill -->
    <div v-if="activeOnly || nonComedogenicOnly" class="p-3 bg-brand-50 border border-brand-200 rounded-2xl flex items-center justify-between text-xs text-brand-900">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
        <span>
          <strong>Active Filter Applied:</strong>
          <span v-if="activeOnly"> Isolating therapeutic actives only.</span>
          <span v-if="nonComedogenicOnly"> Restricting strictly to pore-safe rating 0 (anti-comedogenic).</span>
        </span>
      </div>
      <button
        @click="activeOnly = false; nonComedogenicOnly = false; fetchIngredients()"
        class="text-brand-700 hover:text-brand-900 font-bold underline text-[11px]"
      >
        Reset Filters
      </button>
    </div>

    <!-- Ingredients Grid -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 6" :key="n" class="h-48 bg-stone-100 rounded-3xl animate-pulse"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="ing in ingredients"
        :key="ing.id"
        class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md hover:border-brand-300 transition-all"
      >
        <div>
          <div class="flex items-center justify-between text-xs mb-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="ing.is_active_ingredient ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-stone-100 text-stone-600'">
              {{ ing.function_type || 'Cosmetic Botanical' }}
            </span>
            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="ing.comedogenic_rating === 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
            >
              Comedogenic: {{ ing.comedogenic_rating }}/5
            </span>
          </div>

          <h3 class="font-serif font-bold text-lg text-stone-900">{{ ing.name }}</h3>
          <p class="text-[11px] font-mono text-stone-400 mt-0.5">{{ ing.inci_name }}</p>
          <p class="text-xs text-stone-600 mt-2 leading-relaxed">{{ ing.description }}</p>

          <div v-if="ing.benefits?.length > 0" class="flex flex-wrap gap-1 mt-3">
            <span
              v-for="(b, idx) in ing.benefits"
              :key="idx"
              class="px-2 py-0.5 bg-stone-100 text-stone-600 text-[10px] font-medium rounded-full capitalize"
            >
              {{ b.replace('_', ' ') }}
            </span>
          </div>
        </div>

        <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
          <span>EWG Hazard: <strong class="text-stone-800">{{ ing.ewg_safety_score }}/10 (Low)</strong></span>
          <span v-if="ing.safe_for_fungal_acne" class="text-emerald-700 font-semibold">✓ Fungal-Acne Safe</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import { Search } from 'lucide-vue-next';

const ingredients = ref([]);
const loading = ref(true);
const search = ref('');
const activeOnly = ref(false);
const nonComedogenicOnly = ref(false);
const showGuide = ref(false);

const fetchIngredients = async () => {
  loading.value = true;
  try {
    const params = {
      search: search.value,
      active_only: activeOnly.value ? 1 : 0,
      max_comedogenic: nonComedogenicOnly.value ? 0 : 5,
    };
    const res = await api.get('/ingredients', { params });
    ingredients.value = res.data.data;
  } catch (err) {
    console.error('Failed to load ingredients:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchIngredients();
});
</script>
