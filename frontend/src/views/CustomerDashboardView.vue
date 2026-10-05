<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- User Header Profile Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <!-- Avatar with upload overlay -->
        <div class="relative group shrink-0">
          <img
            :src="avatarPreview || authStore.userAvatar"
            @error="onAvatarImgError"
            alt="Customer avatar"
            class="w-16 h-16 rounded-full object-cover border-2 border-brand-200 shadow-sm"
          />
          <label
            for="dashboard-avatar-upload"
            class="absolute inset-0 rounded-full bg-stone-900/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
            title="Change avatar photo"
          >
            <Camera class="w-5 h-5" />
          </label>
          <input
            id="dashboard-avatar-upload"
            type="file"
            accept="image/jpeg,image/png,image/gif,image/webp"
            class="hidden"
            @change="handleDashboardAvatarUpload"
          />
        </div>

        <div class="space-y-1">
          <!-- Editable Name -->
          <div class="flex items-center gap-2">
            <template v-if="isEditingName">
              <input
                v-model="editNameValue"
                type="text"
                class="font-serif text-xl font-bold text-stone-900 bg-stone-50 border border-stone-200 rounded-xl px-3 py-1 focus:outline-none focus:border-brand-500 w-48"
                @keyup.enter="saveProfileName"
              />
              <button @click="saveProfileName" class="text-emerald-600 hover:text-emerald-700 text-xs font-bold">Save</button>
              <button @click="isEditingName = false" class="text-stone-400 hover:text-stone-600 text-xs">Cancel</button>
            </template>
            <template v-else>
              <h1 class="font-serif text-2xl font-bold text-stone-900">{{ authStore.user?.name }}</h1>
              <button @click="startEditName" class="text-stone-400 hover:text-brand-600 transition-colors" title="Quick edit name">
                <Pencil class="w-3.5 h-3.5" />
              </button>
            </template>
          </div>

          <p class="text-xs text-stone-500">{{ authStore.user?.email }} • Member since 2026</p>
          <div class="flex items-center gap-2 mt-1">
            <span v-if="authStore.skinProfile?.skin_type" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700">
              {{ authStore.skinProfile.skin_type }} Skin • {{ authStore.skinProfile.undertone }} Undertone
            </span>
          </div>

          <!-- Upload feedback -->
          <p v-if="profileSaving" class="text-[11px] text-brand-600 font-semibold animate-pulse mt-1">Updating profile...</p>
          <p v-if="profileSaved" class="text-[11px] text-emerald-600 font-semibold mt-1">✓ Profile updated successfully!</p>
          <p v-if="profileError" class="text-[11px] text-rose-600 font-semibold mt-1">{{ profileError }}</p>
        </div>
      </div>

      <button
        @click="handleLogout"
        class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-stone-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
      >
        <LogOut class="w-4 h-4" />
        <span>Sign Out</span>
      </button>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-stone-200 gap-4 sm:gap-6 text-xs font-semibold text-stone-500 overflow-x-auto no-scrollbar whitespace-nowrap">
      <button
        @click="activeTab = 'profile'"
        class="pb-3 border-b-2 transition-colors"
        :class="activeTab === 'profile' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Profile & Skin Chemistry
      </button>
      <button
        @click="activeTab = 'orders'"
        class="pb-3 border-b-2 transition-colors"
        :class="activeTab === 'orders' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Orders & Tracking
      </button>
      <button
        @click="activeTab = 'returns'"
        class="pb-3 border-b-2 transition-colors"
        :class="activeTab === 'returns' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Returns & Exchanges
      </button>
    </div>

    <!-- Tab 1: Profile & Skin Chemistry -->
    <div v-if="activeTab === 'profile'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Card 1: Customer Profile & Avatar Management -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6">
        <div class="space-y-1 pb-4 border-b border-stone-100">
          <h3 class="font-serif font-bold text-lg text-stone-900">Personal Information & Avatar</h3>
          <p class="text-xs text-stone-500">Update your photo, display name, and contact details.</p>
        </div>

        <!-- Avatar Change Section -->
        <div class="space-y-4">
          <label class="block font-semibold text-xs text-stone-700">Profile Photo</label>
          <div class="flex items-center gap-5">
            <div class="relative group shrink-0">
              <img
                :src="avatarPreview || authStore.userAvatar"
                @error="onAvatarImgError"
                alt="Avatar"
                class="w-20 h-20 rounded-full object-cover border-2 border-brand-200 shadow-sm"
              />
              <label
                for="tab-avatar-upload"
                class="absolute inset-0 rounded-full bg-stone-900/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                title="Change photo"
              >
                <Camera class="w-5 h-5" />
              </label>
            </div>

            <div class="space-y-2">
              <div class="flex flex-wrap items-center gap-2">
                <label
                  for="tab-avatar-upload"
                  class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-brand-50 text-brand-700 hover:bg-brand-100 text-xs font-semibold border border-brand-200 transition-colors"
                >
                  <Upload class="w-3.5 h-3.5" />
                  <span>Upload Image</span>
                </label>
                <input
                  id="tab-avatar-upload"
                  type="file"
                  accept="image/jpeg,image/png,image/gif,image/webp"
                  class="hidden"
                  @change="handleDashboardAvatarUpload"
                />
              </div>
              <p class="text-[11px] text-stone-400">JPG, PNG, GIF or WebP. Max 2MB.</p>
            </div>
          </div>

          <!-- Preset Avatars Selector -->
          <div class="pt-2">
            <p class="text-[11px] text-stone-500 font-medium mb-2">Or choose a preset portrait avatar:</p>
            <div class="flex items-center gap-3">
              <button
                v-for="(avatar, idx) in presetAvatars"
                :key="idx"
                type="button"
                @click="selectPresetAvatar(avatar)"
                class="relative rounded-full transition-all focus:outline-none"
                :class="authStore.userAvatar === avatar ? 'ring-2 ring-brand-600 ring-offset-2 scale-110 shadow-sm' : 'opacity-70 hover:opacity-100 hover:scale-105'"
                :title="'Select preset ' + (idx + 1)"
              >
                <img :src="avatar" class="w-10 h-10 rounded-full object-cover border border-stone-200" alt="Preset Avatar" />
                <span
                  v-if="authStore.userAvatar === avatar"
                  class="absolute -top-1 -right-1 w-4 h-4 bg-brand-600 rounded-full text-white flex items-center justify-center text-[9px] font-bold"
                >
                  ✓
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Name & Phone Fields -->
        <div class="space-y-4 pt-2 text-xs">
          <div>
            <label class="block font-semibold text-stone-700 mb-1">Full Name</label>
            <input
              v-model="personalForm.name"
              type="text"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500"
              placeholder="Your full name"
            />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Phone Number</label>
            <input
              v-model="personalForm.phone"
              type="text"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500"
              placeholder="e.g. +1 (555) 234-5678"
            />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Email Address</label>
            <div class="flex items-center justify-between bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-stone-600">
              <span>{{ authStore.user?.email }}</span>
              <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Verified</span>
            </div>
          </div>

          <button
            @click="savePersonalInfo"
            :disabled="profileSaving"
            class="px-5 py-2.5 rounded-full bg-stone-900 hover:bg-stone-800 text-white font-semibold transition-colors text-xs flex items-center gap-2 disabled:opacity-50 cursor-pointer"
          >
            <span v-if="profileSaving">Saving details...</span>
            <span v-else>Save Personal Info</span>
          </button>
        </div>
      </div>

      <!-- Card 2: Skin Diagnostic Metrics -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6">
        <div class="space-y-1 pb-4 border-b border-stone-100">
          <h3 class="font-serif font-bold text-lg text-stone-900">Personalized Skin Chemistry</h3>
          <p class="text-xs text-stone-500">Values that calibrate the clinical Compatibility Score across catalog items.</p>
        </div>

        <form @submit.prevent="saveSkinProfile" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-stone-700 mb-1">Skin Type</label>
            <select v-model="profileForm.skin_type" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500">
              <option value="normal">Normal (Balanced hydration &amp; oil)</option>
              <option value="dry">Dry (Flaky / Barrier depleted)</option>
              <option value="oily">Oily (Excess sebum &amp; pore congestion)</option>
              <option value="combination">Combination (T-Zone oiliness, normal cheeks)</option>
              <option value="sensitive">Sensitive (Easily reactive &amp; redness-prone)</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Natural Undertone</label>
            <select v-model="profileForm.undertone" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500">
              <option value="warm">Warm (Golden / Peachy)</option>
              <option value="cool">Cool (Pink / Rosy)</option>
              <option value="neutral">Neutral (Balanced)</option>
              <option value="olive">Olive (Green / Muted tones)</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Known Sensitivities / Allergens (Comma separated)</label>
            <input
              v-model="allergiesInput"
              type="text"
              placeholder="e.g. fragrance, coconut oil, parabens, essential oils"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800 focus:outline-none focus:border-brand-500"
            />
          </div>

          <div class="pt-2">
            <button
              type="submit"
              :disabled="profileLoading"
              class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold transition-colors shadow-sm disabled:opacity-50 cursor-pointer"
            >
              <span v-if="profileLoading">Saving Chemistry Profile...</span>
              <span v-else>Update Skin Diagnostic</span>
            </button>
          </div>
        </form>

        <!-- Feedback Messages inside Tab -->
        <div v-if="tabFeedbackMessage" class="p-3.5 rounded-2xl text-xs flex items-center gap-2" :class="tabFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
          <CheckCircle2 v-if="tabFeedbackType === 'success'" class="w-4 h-4 text-emerald-600 shrink-0" />
          <AlertCircle v-else class="w-4 h-4 text-rose-600 shrink-0" />
          <span>{{ tabFeedbackMessage }}</span>
        </div>
      </div>

    </div>

    <!-- Tab 2: Orders History -->
    <div v-if="activeTab === 'orders'" class="space-y-4">
      <div v-if="ordersLoading" class="p-8 text-center text-xs text-stone-500">Loading order history...</div>
      <div v-else-if="orders.length === 0" class="bg-white p-12 text-center rounded-3xl border border-stone-200">
        <p class="text-xs text-stone-500">No orders placed yet.</p>
        <router-link to="/catalog" class="mt-3 inline-block px-5 py-2 bg-brand-600 text-white text-xs font-semibold rounded-full">
          Browse Cult Bestsellers
        </router-link>
      </div>

      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-4"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-stone-100 text-xs">
          <div>
            <span class="font-bold text-stone-900 text-sm">{{ order.order_number }}</span>
            <p class="text-stone-400">{{ new Date(order.created_at).toLocaleDateString() }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span
              class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase"
              :class="{
                'bg-emerald-50 text-emerald-700': order.status === 'delivered',
                'bg-rose-50 text-rose-700': order.status === 'cancelled',
                'bg-amber-50 text-amber-800': order.status === 'dispatched',
                'bg-brand-50 text-brand-700': !['delivered', 'cancelled', 'dispatched'].includes(order.status)
              }"
            >
              {{ order.status }}
            </span>
            <span class="font-bold text-stone-900 text-sm">${{ order.total_amount }}</span>
          </div>
        </div>

        <div class="space-y-2">
          <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between text-xs">
            <div>
              <p class="font-semibold text-stone-900">{{ item.product_name }}</p>
              <p class="text-stone-400 text-[11px]">{{ item.variant_title }} • Qty: {{ item.quantity }}</p>
            </div>
            <span class="font-bold text-stone-800">${{ item.subtotal }}</span>
          </div>
        </div>

        <div class="pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-2 text-xs">
          <router-link
            :to="`/track-order?order_number=${order.order_number}&email=${order.customer_email}`"
            class="text-brand-600 font-semibold hover:underline"
          >
            Live Tracking →
          </router-link>

          <!-- Return Actions / Status -->
          <div class="flex items-center gap-2">
            <!-- If return already requested for this order -->
            <span
              v-if="getOrderReturn(order.id)"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200"
            >
              Return {{ getOrderReturn(order.id).status }}
            </span>

            <!-- If order is delivered and within 30 days window -->
            <button
              v-else-if="canRequestReturn(order)"
              @click="openReturnModal(order)"
              class="px-3.5 py-1.5 bg-stone-900 hover:bg-stone-800 text-white font-medium rounded-full transition-colors text-xs shadow-sm"
            >
              Request Return / Exchange
            </button>

            <!-- If delivered but 30 days have passed -->
            <span
              v-else-if="order.status === 'delivered'"
              class="text-stone-400 text-[11px] italic"
            >
              Return window expired (>30 days)
            </span>

            <!-- If order is cancelled -->
            <span
              v-else-if="order.status === 'cancelled'"
              class="text-stone-400 text-[11px] italic"
            >
              Order cancelled
            </span>

            <!-- If order is not delivered yet (pending, confirmed, preparing, dispatched) -->
            <span
              v-else
              class="text-stone-400 text-[11px] bg-stone-50 px-2.5 py-1 rounded-full border border-stone-100"
            >
              Return eligible after delivery
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 3: Returns History -->
    <div v-if="activeTab === 'returns'" class="space-y-4">
      <div v-if="returnsLoading" class="p-8 text-center text-xs text-stone-500">Loading returns...</div>
      <div v-else-if="returns.length === 0" class="bg-white p-12 text-center rounded-3xl border border-stone-200">
        <p class="text-xs text-stone-500">No return requests found.</p>
      </div>

      <div
        v-for="ret in returns"
        :key="ret.id"
        class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-2 text-xs"
      >
        <div class="flex items-center justify-between pb-2 border-b border-stone-100">
          <span class="font-bold text-stone-900">{{ ret.return_number }}</span>
          <span
            class="px-2.5 py-0.5 rounded-full font-bold uppercase text-[10px] border"
            :class="{
              'bg-amber-50 text-amber-800 border-amber-200': ret.status === 'requested',
              'bg-emerald-50 text-emerald-800 border-emerald-200': ret.status === 'refunded',
              'bg-rose-50 text-rose-800 border-rose-200': ret.status === 'rejected',
              'bg-blue-50 text-blue-800 border-blue-200': ret.status === 'approved'
            }"
          >
            {{ ret.status }}
          </span>
        </div>
        <p class="text-stone-600">Reason: <strong class="capitalize">{{ ret.reason_code?.replace('_', ' ') }}</strong></p>
        <p class="text-stone-500">{{ ret.explanation }}</p>
        <p class="font-bold text-stone-900">Refund Amount: ${{ ret.refund_amount }}</p>
        <div v-if="ret.admin_notes" class="mt-2 p-2.5 bg-stone-50 border border-stone-100 rounded-xl text-stone-600 text-[11px]">
          <span class="font-semibold text-stone-800">Team Update:</span> {{ ret.admin_notes }}
        </div>
      </div>
    </div>

    <!-- Return Modal -->
    <div v-if="returnModalOrder" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-4 text-xs shadow-2xl">
        <h3 class="font-serif font-bold text-lg text-stone-900">Request Return for #{{ returnModalOrder.order_number }}</h3>
        
        <div>
          <label class="block font-semibold text-stone-700 mb-1">Reason for Return</label>
          <select v-model="returnForm.reason_code" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2">
            <option value="wrong_shade">Wrong Shade (Shade guarantee replacement)</option>
            <option value="skin_reaction">Skin Reaction / Sensitivity</option>
            <option value="damaged_in_transit">Damaged in Transit</option>
            <option value="defective_packaging">Defective Packaging</option>
            <option value="changed_mind">Changed Mind</option>
          </select>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Explanation <span class="text-stone-400 font-normal">(min. 5 characters)</span></label>
          <textarea
            v-model="returnForm.explanation"
            rows="3"
            placeholder="Describe the issue or reason for return..."
            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800"
          ></textarea>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Refund Method</label>
          <select v-model="returnForm.refund_method" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2">
            <option value="original_payment">Refund to Original Payment Method</option>
            <option value="store_credit">BeautyVault Store Credit</option>
          </select>
        </div>

        <!-- Error Message -->
        <div v-if="returnError" class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 font-medium">
          {{ returnError }}
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="returnModalOrder = null; returnError = ''" class="px-4 py-2 rounded-full border border-stone-200">Cancel</button>
          <button 
            @click="submitReturn" 
            :disabled="returnSubmitting"
            class="px-5 py-2 rounded-full bg-brand-600 text-white font-semibold disabled:opacity-50"
          >
            {{ returnSubmitting ? 'Submitting...' : 'Submit Return Request' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { LogOut, Camera, Pencil, Upload, CheckCircle2, AlertCircle } from 'lucide-vue-next';
import api from '../services/api';

const router = useRouter();
const authStore = useAuthStore();
const activeTab = ref('profile');

// 5 Curated luxury portrait avatars
const presetAvatars = [
  'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
  'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80'
];

// Profile & Personal editing state
const avatarPreview = ref('');
const isEditingName = ref(false);
const editNameValue = ref('');
const profileSaving = ref(false);
const profileSaved = ref(false);
const profileError = ref('');
const tabFeedbackMessage = ref('');
const tabFeedbackType = ref('success');

const personalForm = reactive({
  name: authStore.user?.name || '',
  phone: authStore.user?.phone || '',
});

const onAvatarImgError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80';
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/');
};

const startEditName = () => {
  editNameValue.value = authStore.user?.name || '';
  isEditingName.value = true;
};

const saveProfileName = async () => {
  if (!editNameValue.value.trim()) return;
  profileSaving.value = true;
  profileSaved.value = false;
  profileError.value = '';
  try {
    await authStore.updateProfile({ name: editNameValue.value.trim() });
    personalForm.name = editNameValue.value.trim();
    isEditingName.value = false;
    profileSaved.value = true;
    setTimeout(() => { profileSaved.value = false; }, 2500);
  } catch (err) {
    profileError.value = err.response?.data?.message || 'Failed to update name.';
  } finally {
    profileSaving.value = false;
  }
};

const savePersonalInfo = async () => {
  if (!personalForm.name.trim()) {
    tabFeedbackMessage.value = 'Name cannot be empty.';
    tabFeedbackType.value = 'error';
    return;
  }
  profileSaving.value = true;
  profileSaved.value = false;
  profileError.value = '';
  tabFeedbackMessage.value = '';
  try {
    await authStore.updateProfile({
      name: personalForm.name.trim(),
      phone: personalForm.phone.trim(),
    });
    editNameValue.value = personalForm.name.trim();
    profileSaved.value = true;
    tabFeedbackMessage.value = 'Personal information saved successfully!';
    tabFeedbackType.value = 'success';
    setTimeout(() => { profileSaved.value = false; }, 3000);
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to update personal details.';
    profileError.value = msg;
    tabFeedbackMessage.value = msg;
    tabFeedbackType.value = 'error';
  } finally {
    profileSaving.value = false;
  }
};

const selectPresetAvatar = async (url) => {
  profileSaving.value = true;
  profileSaved.value = false;
  profileError.value = '';
  avatarPreview.value = url;
  tabFeedbackMessage.value = '';
  try {
    await authStore.updateProfile({ avatar_url: url });
    profileSaved.value = true;
    avatarPreview.value = '';
    tabFeedbackMessage.value = 'Profile photo updated to selected portrait!';
    tabFeedbackType.value = 'success';
    setTimeout(() => { profileSaved.value = false; }, 3000);
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to update avatar.';
    profileError.value = msg;
    tabFeedbackMessage.value = msg;
    tabFeedbackType.value = 'error';
    avatarPreview.value = '';
  } finally {
    profileSaving.value = false;
  }
};

const handleDashboardAvatarUpload = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    profileError.value = 'Image must be smaller than 2 MB.';
    tabFeedbackMessage.value = 'Image must be smaller than 2 MB.';
    tabFeedbackType.value = 'error';
    return;
  }
  // Show local preview immediately
  const reader = new FileReader();
  reader.onload = (e) => { avatarPreview.value = e.target.result; };
  reader.readAsDataURL(file);

  // Upload to server
  profileSaving.value = true;
  profileSaved.value = false;
  profileError.value = '';
  tabFeedbackMessage.value = '';
  try {
    await authStore.updateProfile({ avatarFile: file });
    profileSaved.value = true;
    avatarPreview.value = ''; // Use fresh server URL now
    tabFeedbackMessage.value = 'Profile photo uploaded and updated!';
    tabFeedbackType.value = 'success';
    setTimeout(() => { profileSaved.value = false; }, 3000);
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to upload avatar.';
    profileError.value = msg;
    tabFeedbackMessage.value = msg;
    tabFeedbackType.value = 'error';
    avatarPreview.value = '';
  } finally {
    profileSaving.value = false;
  }
};

const profileForm = reactive({
  skin_type: authStore.skinProfile?.skin_type || 'combination',
  undertone: authStore.skinProfile?.undertone || 'warm',
  shade_depth: authStore.skinProfile?.shade_depth || 4,
});
const allergiesInput = ref((authStore.skinProfile?.allergies || []).join(', '));
const profileLoading = ref(false);

const orders = ref([]);
const ordersLoading = ref(false);
const returns = ref([]);
const returnsLoading = ref(false);

const returnModalOrder = ref(null);
const returnError = ref('');
const returnSubmitting = ref(false);
const returnForm = reactive({
  reason_code: 'wrong_shade',
  explanation: '',
  refund_method: 'original_payment',
});

const saveSkinProfile = async () => {
  profileLoading.value = true;
  tabFeedbackMessage.value = '';
  try {
    const allergies = allergiesInput.value
      .split(',')
      .map(s => s.trim().toLowerCase())
      .filter(Boolean);

    await authStore.updateSkinProfile({
      ...profileForm,
      allergies,
    });
    tabFeedbackMessage.value = 'Skin diagnostic profile updated! Compatibility scores recalculated.';
    tabFeedbackType.value = 'success';
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to update skin profile';
    tabFeedbackMessage.value = msg;
    tabFeedbackType.value = 'error';
  } finally {
    profileLoading.value = false;
  }
};

const fetchOrders = async () => {
  ordersLoading.value = true;
  try {
    const res = await api.get('/orders');
    orders.value = res.data.data;
  } catch (err) {
    console.error('Failed to load orders:', err);
  } finally {
    ordersLoading.value = false;
  }
};

const fetchReturns = async () => {
  returnsLoading.value = true;
  try {
    const res = await api.get('/returns');
    returns.value = res.data.data;
  } catch (err) {
    console.error('Failed to load returns:', err);
  } finally {
    returnsLoading.value = false;
  }
};

const getOrderReturn = (orderId) => {
  return returns.value.find(r => r.order_id === orderId);
};

const canRequestReturn = (order) => {
  if (order.status !== 'delivered') return false;
  const deliveredDate = order.delivered_at ? new Date(order.delivered_at) : new Date(order.updated_at);
  const diffDays = (Date.now() - deliveredDate.getTime()) / (1000 * 60 * 60 * 24);
  return diffDays <= 30;
};

const openReturnModal = (order) => {
  returnModalOrder.value = order;
};

const submitReturn = async () => {
  returnError.value = '';
  if (!returnForm.explanation.trim() || returnForm.explanation.trim().length < 5) {
    returnError.value = 'Please write an explanation (at least 5 characters).';
    return;
  }
  returnSubmitting.value = true;
  try {
    const items = returnModalOrder.value.items.map(item => ({
      order_item_id: item.id,
      quantity: item.quantity,
      condition: 'opened_tested',
    }));

    await api.post('/returns', {
      order_id: returnModalOrder.value.id,
      reason_code: returnForm.reason_code,
      explanation: returnForm.explanation.trim(),
      refund_method: returnForm.refund_method,
      items,
    });

    returnModalOrder.value = null;
    returnError.value = '';
    returnForm.explanation = '';
    returnForm.reason_code = 'wrong_shade';
    returnForm.refund_method = 'original_payment';
    alert('✅ Return request submitted! Our team will review within 24 hours.');
    fetchReturns();
    activeTab.value = 'returns';
  } catch (err) {
    // Show the real server error message to the user
    const msg = err.response?.data?.message
      || Object.values(err.response?.data?.errors || {})[0]?.[0]
      || 'Failed to submit return. Please try again.';
    returnError.value = msg;
  } finally {
    returnSubmitting.value = false;
  }
};

onMounted(async () => {
  if (authStore.token) {
    try {
      await authStore.fetchUser();
    } catch (_) {}
  }
  if (authStore.user) {
    personalForm.name = authStore.user.name || '';
    personalForm.phone = authStore.user.phone || '';
    editNameValue.value = authStore.user.name || '';
  }
  if (authStore.skinProfile) {
    profileForm.skin_type = authStore.skinProfile.skin_type || 'combination';
    profileForm.undertone = authStore.skinProfile.undertone || 'warm';
    profileForm.shade_depth = authStore.skinProfile.shade_depth || 4;
    allergiesInput.value = (authStore.skinProfile.allergies || []).join(', ');
  }
  fetchOrders();
  fetchReturns();
});
</script>
