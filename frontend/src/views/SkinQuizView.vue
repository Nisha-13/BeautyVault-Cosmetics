<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- State 1: Taking the Quiz -->
    <div v-if="!results" class="space-y-8">
      
      <!-- Progress Bar -->
      <div class="space-y-2">
        <div class="flex items-center justify-between text-xs text-stone-500 font-semibold">
          <span>Question {{ currentStep + 1 }} of {{ questions.length }}</span>
          <span>{{ Math.round(((currentStep + 1) / questions.length) * 100) }}% Complete</span>
        </div>
        <div class="w-full bg-stone-200 h-2 rounded-full overflow-hidden">
          <div
            class="bg-brand-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${((currentStep + 1) / questions.length) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Question Card -->
      <div v-if="currentQuestion" class="bg-white rounded-3xl border border-stone-200 p-8 sm:p-12 shadow-soft space-y-6">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Clinical Skin Diagnostic</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900 mt-1">
            {{ currentQuestion.title }}
          </h2>
        </div>

        <!-- Slider Type -->
        <div v-if="currentQuestion.type === 'slider'" class="space-y-4 py-4">
          <div class="text-center">
            <span class="text-3xl font-bold text-brand-700 font-serif">Level {{ answers.shade_depth }}</span>
            <p class="text-xs text-stone-500 mt-1">1: Fair Porcelain to 10: Deep Rich</p>
          </div>
          <input
            type="range"
            :min="currentQuestion.min"
            :max="currentQuestion.max"
            v-model.number="answers.shade_depth"
            class="w-full accent-brand-600 h-2 bg-stone-200 rounded-lg cursor-pointer"
          />
        </div>

        <!-- Multiple Choice Options -->
        <div v-else class="space-y-3">
          <button
            v-for="opt in currentQuestion.options"
            :key="opt.value"
            type="button"
            @click="selectOption(opt.value)"
            class="w-full p-5 rounded-2xl border text-left transition-all flex items-center justify-between group"
            :class="isOptionSelected(opt.value) ? 'border-brand-600 bg-brand-50/60 shadow-sm ring-1 ring-brand-400' : 'border-stone-200 hover:border-stone-300 bg-white'"
          >
            <div>
              <p class="font-bold text-sm text-stone-900 group-hover:text-brand-600 transition-colors">{{ opt.label }}</p>
              <p v-if="opt.desc" class="text-xs text-stone-500 mt-0.5">{{ opt.desc }}</p>
            </div>
            <div
              class="w-5 h-5 rounded-full border flex items-center justify-center flex-shrink-0"
              :class="isOptionSelected(opt.value) ? 'border-brand-600 bg-brand-600 text-white' : 'border-stone-300'"
            >
              <Check v-if="isOptionSelected(opt.value)" class="w-3.5 h-3.5" />
            </div>
          </button>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-stone-100">
          <button
            v-if="currentStep > 0"
            @click="currentStep--"
            class="px-5 py-2.5 rounded-full border border-stone-200 text-xs font-semibold text-stone-600 hover:bg-stone-50"
          >
            Back
          </button>
          <div v-else></div>

          <button
            @click="nextStep"
            :disabled="!canProceed"
            class="px-7 py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-md disabled:opacity-40"
          >
            {{ currentStep === questions.length - 1 ? 'Analyze Skin Chemistry' : 'Continue' }}
          </button>
        </div>

      </div>

    </div>

    <!-- State 2: Diagnosis Results & Personalized Routine -->
    <div v-else class="space-y-12">
      
      <!-- Diagnosis Summary Header -->
      <div class="bg-gradient-to-r from-stone-950 via-stone-900 to-rosewood-dark text-white rounded-3xl p-8 sm:p-12 shadow-2xl space-y-4">
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-500/30 text-rose-300 border border-brand-400/40">
          Diagnosis Complete
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold">
          Your Skin Type: <span class="text-rose-300 capitalize">{{ results.diagnosis.skin_type }}</span> with <span class="capitalize">{{ results.diagnosis.undertone }}</span> Undertones
        </h1>
        <p class="text-xs sm:text-sm text-stone-300 max-w-2xl leading-relaxed">
          Based on your barrier lipid levels and target concerns, we have curated a synchronized Morning and Evening regimen formulated without pore-clogging comedogenic oils.
        </p>
      </div>

      <!-- Routines -->
      <div class="space-y-8">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Personalized Regimen</span>
          <h2 class="font-serif text-2xl font-bold text-stone-900 mt-1">Your Curated Daily Routine</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <!-- Morning Routine -->
          <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
              <Sun class="w-5 h-5 text-amber-500" />
              <h3 class="font-serif font-bold text-lg text-stone-900">Morning Barrier Defense</h3>
            </div>

            <div v-if="results.routine?.morning_routine?.step_1_treatment" class="flex gap-4 items-center p-3 rounded-2xl bg-stone-50 border border-stone-100">
              <img
                :src="results.routine.morning_routine.step_1_treatment.thumbnail_url"
                class="w-16 h-16 object-cover rounded-xl border border-stone-200"
              />
              <div class="flex-1 min-w-0 text-xs">
                <span class="text-[10px] uppercase font-bold text-brand-600">Step 1: Treatment</span>
                <p class="font-bold text-stone-900 truncate">{{ results.routine.morning_routine.step_1_treatment.name }}</p>
                <p class="text-stone-500">${{ parseFloat(results.routine.morning_routine.step_1_treatment.base_price).toFixed(2) }}</p>
              </div>
            </div>

            <div v-if="results.routine?.morning_routine?.step_2_sunscreen" class="flex gap-4 items-center p-3 rounded-2xl bg-stone-50 border border-stone-100">
              <img
                :src="results.routine.morning_routine.step_2_sunscreen.thumbnail_url"
                class="w-16 h-16 object-cover rounded-xl border border-stone-200"
              />
              <div class="flex-1 min-w-0 text-xs">
                <span class="text-[10px] uppercase font-bold text-brand-600">Step 2: Sun Protection</span>
                <p class="font-bold text-stone-900 truncate">{{ results.routine.morning_routine.step_2_sunscreen.name }}</p>
                <p class="text-stone-500">${{ parseFloat(results.routine.morning_routine.step_2_sunscreen.base_price).toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <!-- Evening Routine -->
          <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
              <Moon class="w-5 h-5 text-indigo-500" />
              <h3 class="font-serif font-bold text-lg text-stone-900">Evening Cellular Repair</h3>
            </div>

            <div v-if="results.routine?.evening_routine?.step_1_active" class="flex gap-4 items-center p-3 rounded-2xl bg-stone-50 border border-stone-100">
              <img
                :src="results.routine.evening_routine.step_1_active.thumbnail_url"
                class="w-16 h-16 object-cover rounded-xl border border-stone-200"
              />
              <div class="flex-1 min-w-0 text-xs">
                <span class="text-[10px] uppercase font-bold text-brand-600">Step 1: Cellular Exfoliation</span>
                <p class="font-bold text-stone-900 truncate">{{ results.routine.evening_routine.step_1_active.name }}</p>
                <p class="text-stone-500">${{ parseFloat(results.routine.evening_routine.step_1_active.base_price).toFixed(2) }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="text-center pt-4">
        <button
          @click="results = null; currentStep = 0"
          class="px-6 py-2.5 rounded-full border border-stone-300 text-xs font-semibold text-stone-700 hover:bg-stone-50"
        >
          Retake Skin Quiz
        </button>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import api from '../services/api';
import { Check, Sun, Moon } from 'lucide-vue-next';

const questions = ref([]);
const currentStep = ref(0);
const results = ref(null);

const answers = reactive({
  oil_level: 'shiny_tzone_only',
  vein_color: 'green',
  shade_depth: 4,
  concerns: ['large_pores', 'dullness'],
  reactivity: 'low',
});

const currentQuestion = computed(() => questions.value[currentStep.value]);

const isOptionSelected = (val) => {
  const qId = currentQuestion.value?.id;
  if (!qId) return false;
  if (currentQuestion.value.is_multiple) {
    return answers[qId]?.includes(val);
  }
  return answers[qId] === val;
};

const selectOption = (val) => {
  const q = currentQuestion.value;
  if (q.is_multiple) {
    const list = answers[q.id] || [];
    if (list.includes(val)) {
      answers[q.id] = list.filter(item => item !== val);
    } else {
      if (list.length < (q.max_select || 2)) {
        answers[q.id] = [...list, val];
      }
    }
  } else {
    answers[q.id] = val;
  }
};

const canProceed = computed(() => {
  const q = currentQuestion.value;
  if (!q) return false;
  if (q.type === 'slider') return true;
  if (q.is_multiple) {
    return (answers[q.id]?.length || 0) > 0;
  }
  return !!answers[q.id];
});

const nextStep = () => {
  if (currentStep.value < questions.value.length - 1) {
    currentStep.value++;
  } else {
    submitQuiz();
  }
};

const submitQuiz = async () => {
  try {
    const res = await api.post('/diagnostic/quiz', answers);
    results.value = res.data;
  } catch (err) {
    console.error('Quiz submit failed:', err);
  }
};

onMounted(async () => {
  try {
    const res = await api.get('/diagnostic/quiz');
    questions.value = res.data.questions;
  } catch (err) {
    console.error('Failed to load quiz:', err);
  }
});
</script>
