<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      
      <!-- Backdrop -->
      <div @click="close" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"></div>

      <!-- Modal Card -->
      <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 text-left shadow-2xl border border-stone-100 transform transition-all my-8 z-10">
        
        <!-- Close Button -->
        <button
          @click="close"
          class="absolute top-5 right-5 p-2 text-stone-400 hover:text-stone-700 rounded-full hover:bg-stone-100 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>

        <!-- Dynamic Header -->
        <div class="text-center mb-5">
          <div
            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2.5 transition-colors"
            :class="{
              'bg-brand-50 text-brand-600': mode === 'login' || mode === 'register',
              'bg-amber-50 text-amber-600': mode === 'forgot',
              'bg-emerald-50 text-emerald-600': mode === 'reset',
            }"
          >
            <Sparkles v-if="mode === 'login' || mode === 'register'" class="w-5 h-5" />
            <KeyRound v-else-if="mode === 'forgot'" class="w-5 h-5" />
            <ShieldCheck v-else class="w-5 h-5" />
          </div>

          <h3 class="font-serif text-2xl font-bold text-stone-900">
            <template v-if="mode === 'login'">Welcome to BeautyVault</template>
            <template v-else-if="mode === 'register'">Create Your Beauty Profile</template>
            <template v-else-if="mode === 'forgot'">Reset Your Password</template>
            <template v-else-if="mode === 'reset'">Set New Password</template>
          </h3>

          <p class="text-xs text-stone-500 mt-1">
            <template v-if="mode === 'login'">Sign in to access your custom shade history &amp; orders.</template>
            <template v-else-if="mode === 'register'">Pick an avatar, save shade matches, and track orders.</template>
            <template v-else-if="mode === 'forgot'">Enter your account email to receive a 6-digit verification code.</template>
            <template v-else-if="mode === 'reset'">Enter the verification code and set your new account password.</template>
          </p>
        </div>

        <!-- Admin Notice if opened for admin access -->
        <div v-if="route.query.auth === 'admin_login' && mode === 'login'" class="mb-5 p-3.5 bg-stone-50 border border-stone-200 rounded-2xl flex items-center gap-3 text-xs text-stone-700">
          <div class="w-8 h-8 rounded-full bg-stone-900 text-white flex items-center justify-center shrink-0">
            <ShieldAlert class="w-4 h-4 text-amber-400" />
          </div>
          <div>
            <p class="font-bold text-stone-900">Admin Portal Access</p>
            <p class="text-[11px] text-stone-500">Sign in with your administrative credentials to continue.</p>
          </div>
        </div>

        <!-- Success Feedback Banner -->
        <div v-if="successMessage" class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-2.5 text-xs text-emerald-800">
          <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
          <span class="font-medium">{{ successMessage }}</span>
        </div>

        <!-- Error Feedback Banner -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
          {{ errorMessage }}
        </div>

        <!-- MODE 1: Login / Register Form -->
        <form v-if="mode === 'login' || mode === 'register'" @submit.prevent="handleSubmit" class="space-y-4">
          
          <!-- Registration: Avatar Picker -->
          <div v-if="mode === 'register'" class="space-y-2 pb-1">
            <label class="block text-xs font-semibold text-stone-700">Profile Photo</label>
            <div class="flex flex-col gap-2.5">

              <!-- Upload own photo -->
              <div class="flex items-center gap-3">
                <!-- Preview circle -->
                <div class="relative shrink-0">
                  <img
                    :src="avatarPreview || form.avatar_url"
                    alt="Avatar preview"
                    class="w-14 h-14 rounded-full object-cover border-2 border-brand-300 shadow-sm"
                  />
                  <label
                    for="avatar-upload"
                    class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-brand-600 hover:bg-brand-700 text-white flex items-center justify-center cursor-pointer shadow-sm transition-colors"
                    title="Upload photo"
                  >
                    <Camera class="w-2.5 h-2.5" />
                  </label>
                  <input
                    id="avatar-upload"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    class="hidden"
                    @change="handleAvatarUpload"
                  />
                </div>
                <div class="flex-1">
                  <p class="text-[11px] font-semibold text-stone-700">Upload your photo</p>
                  <p class="text-[10px] text-stone-400">JPG, PNG, GIF or WebP · max 2 MB</p>
                  <label
                    v-if="!avatarPreview"
                    for="avatar-upload"
                    class="inline-flex items-center gap-1 mt-1 text-[11px] text-brand-600 hover:underline font-semibold cursor-pointer"
                  >
                    <Camera class="w-3 h-3" /> Choose file
                  </label>
                  <button
                    v-else
                    type="button"
                    @click="clearAvatarUpload"
                    class="mt-1 text-[11px] text-rose-500 hover:underline font-semibold"
                  >
                    ✕ Remove & use preset
                  </button>
                </div>
              </div>

              <!-- Preset avatars (shown when no file uploaded) -->
              <div v-if="!avatarPreview">
                <p class="text-[10px] text-stone-400 mb-1.5">Or pick a preset avatar:</p>
                <div class="flex items-center gap-1.5 flex-wrap">
                  <button
                    v-for="(avatar, idx) in availableAvatars"
                    :key="idx"
                    type="button"
                    @click="form.avatar_url = avatar"
                    class="relative rounded-full p-0.5 transition-all focus:outline-none"
                    :class="form.avatar_url === avatar ? 'ring-2 ring-brand-600 scale-110 shadow-sm' : 'opacity-60 hover:opacity-100 hover:scale-105'"
                  >
                    <img :src="avatar" alt="Avatar option" class="w-9 h-9 rounded-full object-cover" />
                    <span
                      v-if="form.avatar_url === avatar"
                      class="absolute -top-1 -right-1 w-4 h-4 bg-brand-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow-sm"
                    >✓</span>
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Registration: Full Name -->
          <div v-if="mode === 'register'">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Full Name</label>
            <input
              v-model="form.name"
              @input="clearFieldError('name')"
              type="text"
              placeholder="e.g. Sophia Chen"
              :class="inputClass(fieldErrors.name)"
            />
            <p v-if="fieldErrors.name" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.name }}
            </p>
          </div>

          <!-- Email Field -->
          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Email Address</label>
            <input
              v-model="form.email"
              @input="clearFieldError('email')"
              type="email"
              placeholder="you@example.com"
              :class="inputClass(fieldErrors.email)"
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.email }}
            </p>
          </div>

          <!-- Password Field -->
          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                @input="clearFieldError('password')"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :class="inputClass(fieldErrors.password) + ' pr-11'"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-700 transition-colors rounded-full focus:outline-none"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
              >
                <EyeOff v-if="showPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.password }}
            </p>
          </div>

          <!-- Register: Confirm Password -->
          <div v-if="mode === 'register'">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Confirm Password</label>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                @input="clearFieldError('password_confirmation')"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :class="inputClass(fieldErrors.password_confirmation) + ' pr-11'"
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-700 transition-colors rounded-full focus:outline-none"
                :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
              >
                <EyeOff v-if="showConfirmPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="fieldErrors.password_confirmation" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.password_confirmation }}
            </p>
          </div>

          <!-- Remember me & Forgot Password Row -->
          <div v-if="mode === 'login'" class="flex items-center justify-between text-xs pt-0.5 pb-1">
            <label class="flex items-center gap-2 text-stone-600 cursor-pointer select-none">
              <input
                type="checkbox"
                v-model="rememberMe"
                class="rounded border-stone-300 text-brand-600 focus:ring-brand-500 cursor-pointer"
              />
              <span>Remember me</span>
            </label>
            <button
              type="button"
              @click="openForgotPassword"
              class="text-brand-600 hover:text-brand-700 font-semibold hover:underline"
            >
              Forgot password?
            </button>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow disabled:opacity-50"
          >
            <span v-if="loading">Verifying...</span>
            <span v-else>{{ mode === 'register' ? 'Create Profile & Sign In' : 'Sign In to BeautyVault' }}</span>
          </button>
        </form>

        <!-- MODE 2: Forgot Password Form (Request OTP) -->
        <form v-else-if="mode === 'forgot'" @submit.prevent="handleForgotPassword" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Registered Account Email</label>
            <input
              v-model="forgotEmail"
              @input="clearFieldError('forgotEmail')"
              type="email"
              placeholder="e.g. customer@beautyvault.com"
              :class="inputClass(fieldErrors.forgotEmail)"
            />
            <p v-if="fieldErrors.forgotEmail" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.forgotEmail }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow disabled:opacity-50"
          >
            <span v-if="loading">Sending Code...</span>
            <span v-else>Send Verification Code</span>
          </button>
        </form>

        <!-- MODE 3: Reset Password Form (Enter Code & New Password) -->
        <form v-else-if="mode === 'reset'" @submit.prevent="handleResetPassword" class="space-y-4">
          
          <!-- Code hint for immediate test convenience -->
          <div v-if="dispatchedCode" class="p-3 bg-stone-50 border border-stone-200 rounded-2xl text-xs text-stone-700 space-y-1">
            <p class="font-semibold text-stone-900">Your 6-Digit Code:</p>
            <div class="flex items-center gap-2">
              <span class="font-mono text-base font-bold tracking-widest text-brand-700 bg-white px-3 py-1 rounded-xl border border-stone-200">
                {{ dispatchedCode }}
              </span>
              <button
                type="button"
                @click="resetForm.code = dispatchedCode"
                class="text-[11px] text-brand-600 hover:underline font-semibold"
              >
                Click to Auto-fill
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">6-Digit Verification Code</label>
            <input
              v-model="resetForm.code"
              @input="clearFieldError('code')"
              type="text"
              maxlength="6"
              placeholder="e.g. 123456"
              :class="inputClass(fieldErrors.code) + ' font-mono tracking-widest'"
            />
            <p v-if="fieldErrors.code" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.code }}
            </p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">New Password (min. 8 characters)</label>
            <div class="relative">
              <input
                v-model="resetForm.password"
                @input="clearFieldError('resetPassword')"
                :type="showNewPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :class="inputClass(fieldErrors.resetPassword) + ' pr-11'"
              />
              <button
                type="button"
                @click="showNewPassword = !showNewPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-700 transition-colors rounded-full focus:outline-none"
              >
                <EyeOff v-if="showNewPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="fieldErrors.resetPassword" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.resetPassword }}
            </p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Confirm New Password</label>
            <div class="relative">
              <input
                v-model="resetForm.password_confirmation"
                @input="clearFieldError('resetPasswordConfirm')"
                :type="showConfirmNewPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :class="inputClass(fieldErrors.resetPasswordConfirm) + ' pr-11'"
              />
              <button
                type="button"
                @click="showConfirmNewPassword = !showConfirmNewPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-700 transition-colors rounded-full focus:outline-none"
                :aria-label="showConfirmNewPassword ? 'Hide password' : 'Show password'"
              >
                <EyeOff v-if="showConfirmNewPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="fieldErrors.resetPasswordConfirm" class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
              <AlertCircle class="w-3 h-3 shrink-0" /> {{ fieldErrors.resetPasswordConfirm }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-glow disabled:opacity-50"
          >
            <span v-if="loading">Resetting Password...</span>
            <span v-else>Update Password &amp; Sign In</span>
          </button>
        </form>

        <!-- Toggle Links -->
        <div class="mt-5 text-center text-xs text-stone-500">
          <template v-if="mode === 'login'">
            <span>Don't have an account yet? </span>
            <button
              @click="switchMode('register')"
              class="text-brand-600 font-bold hover:underline ml-1"
            >
              Create Free Account
            </button>
          </template>
          
          <template v-else-if="mode === 'register'">
            <span>Already have an account? </span>
            <button
              @click="switchMode('login')"
              class="text-brand-600 font-bold hover:underline ml-1"
            >
              Sign In
            </button>
          </template>

          <template v-else>
            <button
              type="button"
              @click="switchMode('login')"
              class="inline-flex items-center gap-1.5 text-stone-600 hover:text-stone-900 font-semibold"
            >
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>Back to Sign In</span>
            </button>
          </template>
        </div>

        <!-- Professional Security & Terms Footer -->
        <div class="mt-5 pt-3.5 border-t border-stone-100 text-center">
          <p class="text-[11px] text-stone-400">
            🔒 256-Bit SSL Encrypted • <router-link to="/privacy-policy" @click="close" class="hover:underline">Privacy Policy</router-link> &amp; <router-link to="/terms" @click="close" class="hover:underline">Terms</router-link>
          </p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import { useCartStore } from '../stores/cart';
import api from '../services/api';
import { X, Sparkles, Eye, EyeOff, ShieldAlert, KeyRound, ShieldCheck, CheckCircle2, ArrowLeft, AlertCircle, Camera } from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
});

const emit = defineEmits(['close']);

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const wishlistStore = useWishlistStore();
const cartStore = useCartStore();

// View modes: 'login' | 'register' | 'forgot' | 'reset'
const mode = ref('login');
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmNewPassword = ref(false);
const rememberMe = ref(true);
const avatarFile = ref(null);
const avatarPreview = ref('');

const forgotEmail = ref('');
const dispatchedCode = ref('');

const resetForm = reactive({
  code: '',
  password: '',
  password_confirmation: '',
});

// Per-field validation errors
const fieldErrors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  forgotEmail: '',
  code: '',
  resetPassword: '',
  resetPasswordConfirm: '',
});

// 5 diverse beauty avatar options
const availableAvatars = [
  'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80'
];

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  avatar_url: availableAvatars[0],
});

const handleAvatarUpload = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    errorMessage.value = 'Image must be smaller than 2 MB.';
    return;
  }
  avatarFile.value = file;
  const reader = new FileReader();
  reader.onload = (e) => { avatarPreview.value = e.target.result; };
  reader.readAsDataURL(file);
};

const clearAvatarUpload = () => {
  avatarFile.value = null;
  avatarPreview.value = '';
};

// Helper: dynamic input class based on error state
const inputClass = (hasError) =>
  `w-full text-sm bg-stone-50 border rounded-xl px-4 py-2.5 text-stone-800 focus:outline-none transition-colors ${
    hasError
      ? 'border-rose-400 bg-rose-50/30 focus:border-rose-500'
      : 'border-stone-200 focus:border-brand-500'
  }`;

// Clear a single field error on input
const clearFieldError = (field) => {
  if (fieldErrors[field]) fieldErrors[field] = '';
  if (errorMessage.value) errorMessage.value = '';
};

// Clear all errors (on mode switch / form submit start)
const clearAllErrors = () => {
  Object.keys(fieldErrors).forEach((k) => (fieldErrors[k] = ''));
  errorMessage.value = '';
  successMessage.value = '';
};

// Map Laravel 422 errors object to per-field state
const mapBackendErrors = (errors) => {
  if (!errors || typeof errors !== 'object') return;
  const pick = (v) => (Array.isArray(v) ? v[0] : v);
  if (errors.name) fieldErrors.name = pick(errors.name);
  if (errors.email) fieldErrors.email = pick(errors.email);
  if (errors.password) fieldErrors.password = pick(errors.password);
  if (errors.password_confirmation) fieldErrors.password_confirmation = pick(errors.password_confirmation);
  if (errors.code) fieldErrors.code = pick(errors.code);
};

// Switch mode and clear all errors
const switchMode = (newMode) => {
  mode.value = newMode;
  clearAllErrors();
};

const close = () => {
  clearAllErrors();
  emit('close');
};

const openForgotPassword = () => {
  mode.value = 'forgot';
  clearAllErrors();
  forgotEmail.value = form.value.email || '';
};

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      mode.value = 'login';
      clearAllErrors();
      if (route.query.auth === 'admin_login' && !form.value.email) {
        form.value.email = 'admin@beautyvault.com';
      }
    }
  },
  { immediate: true }
);

// Sign In & Register Submission
const handleSubmit = async () => {
  clearAllErrors();
  loading.value = true;
  try {
    if (mode.value === 'register') {
      await authStore.register({ ...form.value, avatarFile: avatarFile.value });
    } else {
      await authStore.login(form.value.email, form.value.password);
    }
    close();
    await wishlistStore.fetchServerWishlist();
    await cartStore.fetchCart();
    if (authStore.isAdmin) {
      router.push('/admin');
    } else {
      router.push('/account');
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      mapBackendErrors(data.errors);
    } else if (data?.message) {
      const msg = data.message;
      if (msg.toLowerCase().includes('password') || msg.toLowerCase().includes('credential')) {
        fieldErrors.password = msg;
      } else if (msg.toLowerCase().includes('email') || msg.toLowerCase().includes('account')) {
        fieldErrors.email = msg;
      } else {
        errorMessage.value = msg;
      }
    } else {
      errorMessage.value = 'Authentication failed. Please check your credentials.';
    }
  } finally {
    loading.value = false;
  }
};

// Forgot Password: Request 6-digit Code
const handleForgotPassword = async () => {
  clearAllErrors();
  loading.value = true;
  try {
    const res = await api.post('/auth/forgot-password', {
      email: forgotEmail.value.trim(),
    });
    dispatchedCode.value = res.data.verification_code || '';
    successMessage.value = 'Verification code generated! Please enter it below to set a new password.';
    mode.value = 'reset';
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors?.email) {
      fieldErrors.forgotEmail = Array.isArray(data.errors.email) ? data.errors.email[0] : data.errors.email;
    } else {
      errorMessage.value = data?.message || 'Failed to request reset code. Please check your email.';
    }
  } finally {
    loading.value = false;
  }
};

// Reset Password: Enter Code & New Password
const handleResetPassword = async () => {
  clearAllErrors();

  // Client-side checks before hitting API
  if (resetForm.password.length < 8) {
    fieldErrors.resetPassword = 'Password must be at least 8 characters.';
    return;
  }
  if (resetForm.password !== resetForm.password_confirmation) {
    fieldErrors.resetPasswordConfirm = 'Passwords do not match.';
    return;
  }

  loading.value = true;
  try {
    const res = await api.post('/auth/reset-password', {
      email: forgotEmail.value.trim(),
      code: resetForm.code.trim(),
      password: resetForm.password,
      password_confirmation: resetForm.password_confirmation,
    });

    // Populate email in login form
    form.value.email = forgotEmail.value.trim();
    form.value.password = resetForm.password;

    // Reset temporary state
    resetForm.code = '';
    resetForm.password = '';
    resetForm.password_confirmation = '';
    dispatchedCode.value = '';

    successMessage.value = res.data.message || 'Password successfully updated! You can now sign in.';
    mode.value = 'login';
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      const pick = (v) => (Array.isArray(v) ? v[0] : v);
      if (data.errors.code) fieldErrors.code = pick(data.errors.code);
      if (data.errors.password) fieldErrors.resetPassword = pick(data.errors.password);
      if (data.errors.password_confirmation) fieldErrors.resetPasswordConfirm = pick(data.errors.password_confirmation);
    } else {
      errorMessage.value = data?.message || 'Failed to reset password. Please verify your code and try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
