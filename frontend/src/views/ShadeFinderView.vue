<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    
    <!-- Hero / Intro -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold">
        <Sparkles class="w-3.5 h-3.5 text-brand-600" />
        <span>Multi-Brand Shade Matching Engine</span>
      </div>
      <h1 class="font-serif text-3xl sm:text-5xl font-bold text-stone-900">
        Discover Your Exact Foundation Match
      </h1>
      <p class="text-xs sm:text-sm text-stone-500 leading-relaxed">
        Select your skin depth and natural undertone. Our algorithm calculates undertone harmony and color distance across 50+ inclusive shades from Fenty Beauty, Rare Beauty, and more.
      </p>
    </div>

    <!-- Interactive Match Selector Panel -->
    <div class="bg-white rounded-3xl border border-stone-200 p-8 shadow-soft max-w-4xl mx-auto space-y-8">
      
      <!-- Step 1: Depth Slider (1 - 10) -->
      <div class="space-y-4">
        <div class="flex items-center justify-between text-xs">
          <label class="font-bold text-stone-900 uppercase tracking-wider">
            Step 1: Select Skin Depth Level
          </label>
          <span class="font-bold text-brand-600 text-sm">
            Level {{ depthLevel }} of 10 ({{ getDepthLabel(depthLevel) }})
          </span>
        </div>

        <input
          type="range"
          min="1"
          max="10"
          step="1"
          v-model.number="depthLevel"
          @change="calculateMatches"
          class="w-full accent-brand-600 h-2 bg-stone-200 rounded-lg cursor-pointer"
        />

        <!-- Visual Swatches Gradient Scale for Depth -->
        <div class="grid grid-cols-10 gap-1.5 pt-2">
          <div
            v-for="d in 10"
            :key="d"
            @click="setDepth(d)"
            class="cursor-pointer text-center group"
          >
            <div
              class="w-full aspect-square rounded-xl border-2 transition-all"
              :class="depthLevel === d ? 'border-brand-600 scale-110 shadow-md ring-2 ring-brand-300' : 'border-transparent opacity-80 group-hover:opacity-100'"
              :style="{ backgroundColor: getDepthColor(d) }"
            ></div>
            <span class="text-[10px] text-stone-400 mt-1 block font-semibold group-hover:text-stone-800">
              {{ d }}
            </span>
          </div>
        </div>
      </div>

      <!-- Step 2: Undertone Selector -->
      <div class="space-y-4 pt-4 border-t border-stone-100">
        <div class="flex items-center justify-between text-xs">
          <label class="font-bold text-stone-900 uppercase tracking-wider">
            Step 2: Select Natural Undertone
          </label>
          <span class="text-stone-500 font-medium">Vein color / jewelry guide</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
          <button
            v-for="ut in undertones"
            :key="ut.id"
            type="button"
            @click="setUndertone(ut.id)"
            class="p-4 rounded-2xl border text-left transition-all relative overflow-hidden group"
            :class="selectedUndertone === ut.id ? 'border-brand-600 bg-brand-50/50 shadow-sm ring-1 ring-brand-400' : 'border-stone-200 hover:border-stone-300 bg-white'"
          >
            <div class="w-4 h-4 rounded-full mb-2 border border-black/10" :style="{ backgroundColor: ut.color }"></div>
            <p class="font-bold text-xs text-stone-900">{{ ut.name }}</p>
            <p class="text-[10px] text-stone-500 mt-0.5 leading-tight">{{ ut.desc }}</p>
          </button>
        </div>
      </div>

    </div>

    <!-- Match Results Grid -->
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Algorithm Results</span>
          <h2 class="font-serif text-2xl font-bold text-stone-900 mt-0.5">
            Your Recommended Foundation Matches ({{ matches.length }})
          </h2>
        </div>
        <span class="text-xs text-stone-500 font-medium">Sorted by Match Score</span>
      </div>

      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="n in 3" :key="n" class="h-64 bg-stone-100 rounded-3xl animate-pulse"></div>
      </div>

      <div v-else-if="matches.length === 0" class="bg-white p-12 text-center rounded-3xl border border-stone-200">
        <p class="text-xs text-stone-500">No shades matched these criteria. Try adjusting the depth slider.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="m in matches"
          :key="m.variant_id"
          class="bg-white rounded-3xl border border-stone-200 p-6 flex flex-col justify-between space-y-4 hover:shadow-xl transition-all duration-300 group"
          :class="m.match_score >= 90 ? 'ring-2 ring-emerald-400/40' : ''"
        >
          <div>
            <!-- Match Header Tag -->
            <div class="flex items-center justify-between mb-3">
              <span
                class="px-3 py-1 rounded-full text-[11px] font-bold"
                :class="m.match_score >= 90 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-brand-50 text-brand-800 border border-brand-200'"
              >
                {{ m.match_score }}% {{ m.match_quality }}
              </span>
              <span class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">
                {{ m.brand_name }}
              </span>
            </div>

            <!-- Shade Color Box -->
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-stone-50 border border-stone-100">
              <div
                class="w-14 h-14 rounded-2xl border border-black/15 shadow-inner flex-shrink-0"
                :style="{ backgroundColor: m.shade_hex }"
              ></div>
              <div>
                <h4 class="font-serif font-bold text-base text-stone-900 group-hover:text-brand-600 transition-colors">
                  {{ m.shade_name }}
                </h4>
                <p class="text-xs text-stone-500 mt-0.5">
                  Depth Level: <strong>{{ m.depth_level }}/10</strong> • {{ m.undertone }} undertone
                </p>
              </div>
            </div>

            <!-- Alignment Explanation -->
            <div class="mt-3 text-xs text-stone-600 space-y-1">
              <p class="font-semibold text-stone-800">{{ m.product_name }}</p>
              <p class="text-[11px] text-stone-500 leading-relaxed">{{ m.explanation }}</p>
            </div>
          </div>

          <!-- Bottom Action -->
          <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
            <span class="font-bold text-base text-stone-900">${{ m.price.toFixed(2) }}</span>
            <button
              @click="addMatchedShade(m.variant_id)"
              class="px-5 py-2.5 rounded-full bg-stone-900 group-hover:bg-brand-600 text-white text-xs font-semibold shadow-sm transition-colors"
            >
              Add Matched Shade
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import { useCartStore } from '../stores/cart';
import { Sparkles } from 'lucide-vue-next';

const cartStore = useCartStore();

const depthLevel = ref(4);
const selectedUndertone = ref('warm');
const matches = ref([]);
const loading = ref(false);

const undertones = [
  { id: 'cool', name: 'Cool (Pink)', desc: 'Burns easily, blue veins', color: '#f7c4b8' },
  { id: 'warm', name: 'Warm (Golden)', desc: 'Tans easily, green veins', color: '#e8aa78' },
  { id: 'neutral', name: 'Neutral', desc: 'Balanced pink & gold', color: '#eac3a5' },
  { id: 'olive', name: 'Olive', desc: 'Slightly green/grey hue', color: '#c99468' },
  { id: 'peach', name: 'Peach', desc: 'Warm peachy undertone', color: '#d88b63' },
];

const depthScaleColors = [
  '#fdf0e2', '#f9dfc5', '#f2cca7', '#e8b88d', '#d9a273',
  '#c68b59', '#b27443', '#965a30', '#734120', '#422413'
];

const getDepthColor = (level) => depthScaleColors[level - 1] || '#d9a273';

const getDepthLabel = (level) => {
  if (level <= 2) return 'Fair / Porcelain';
  if (level <= 4) return 'Light Medium';
  if (level <= 6) return 'Medium';
  if (level <= 8) return 'Tan / Deep Bronze';
  return 'Rich Deep Espresso';
};

const setDepth = (lvl) => {
  depthLevel.value = lvl;
  calculateMatches();
};

const setUndertone = (ut) => {
  selectedUndertone.value = ut;
  calculateMatches();
};

const calculateMatches = async () => {
  loading.value = true;
  try {
    const res = await api.post('/match-shades', {
      depth_level: depthLevel.value,
      undertone: selectedUndertone.value,
    });
    matches.value = res.data.matches;
  } catch (err) {
    console.error('Shade match failed:', err);
  } finally {
    loading.value = false;
  }
};

const addMatchedShade = async (variantId) => {
  await cartStore.addToCart(variantId, 1);
};

onMounted(() => {
  calculateMatches();
});
</script>
