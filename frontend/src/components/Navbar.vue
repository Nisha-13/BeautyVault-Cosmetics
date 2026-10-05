<template>
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-stone-200/80 transition-all w-full">

    <!-- Top Announcement Bar -->
    <div class="bg-stone-950 text-stone-300 text-[11px] sm:text-xs py-1.5 px-4 text-center font-medium tracking-wide flex justify-center items-center gap-2">
      <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
      <span class="line-clamp-1">Complimentary 2-Day Priority Shipping &amp; Shade Samples on orders over $40</span>
      <span class="opacity-50 hidden md:inline">|</span>
      <span class="hidden md:inline text-rose-400 font-semibold">Use code <strong>GLOW20</strong> for 20% off</span>
    </div>

    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4">

        <!-- Brand Logo -->
        <router-link to="/" class="flex items-center gap-2 group shrink-0">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-brand-600 to-rosewood flex items-center justify-center text-white font-serif font-bold text-base sm:text-xl shadow-glow group-hover:scale-105 transition-transform">
            B
          </div>
          <div class="flex flex-col">
            <span class="font-serif text-lg sm:text-2xl font-bold tracking-tight text-stone-900 group-hover:text-brand-600 transition-colors leading-none">
              BEAUTY<span class="text-brand-600 font-light">VAULT</span>
            </span>
            <span class="hidden sm:block text-[8px] sm:text-[9px] uppercase tracking-[0.2em] sm:tracking-[0.25em] text-stone-400 font-semibold mt-0.5">
              Advanced Clinical Care
            </span>
          </div>
        </router-link>

        <!-- Desktop Navigation Links -->
        <nav class="hidden lg:flex items-center gap-5 xl:gap-6 text-sm font-medium text-stone-700">
          <router-link to="/catalog" class="hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            Catalog
          </router-link>
          <router-link to="/shade-finder" class="flex items-center gap-1.5 hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
            Shade Matcher
          </router-link>
          <router-link to="/skin-quiz" class="flex items-center gap-1.5 hover:text-brand-600 transition-colors py-2 text-brand-600 font-semibold" active-class="text-brand-700">
            <Sparkles class="w-4 h-4 text-brand-500 animate-spin-slow shrink-0" />
            Skin Diagnostic
          </router-link>
          <router-link to="/ingredients" class="hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            Ingredients
          </router-link>
          <router-link to="/about" class="hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            About
          </router-link>
          <router-link to="/faq" class="hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            FAQ
          </router-link>
          <router-link to="/track-order" class="hover:text-brand-600 transition-colors py-2" active-class="text-brand-600 font-semibold">
            Track Order
          </router-link>
        </nav>

        <!-- Right Actions Group -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">

          <!-- Desktop Search Input -->
          <div class="relative hidden md:block w-36 lg:w-52 xl:w-60">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Search shades..."
              class="w-full bg-stone-100/80 border border-stone-200 text-xs rounded-full pl-8 pr-3 py-2 text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
            />
            <Search class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2.5 pointer-events-none" />
          </div>



          <!-- Customer Dashboard Link (desktop) -->
          <router-link
            v-if="authStore.isAuthenticated && !authStore.isAdmin"
            to="/account"
            class="hidden sm:flex px-3 py-1.5 bg-brand-50 text-brand-700 hover:bg-brand-100 rounded-full text-xs font-semibold transition-colors items-center gap-1.5 border border-brand-200"
          >
            <User class="w-3.5 h-3.5 text-brand-600 shrink-0" />
            <span>Dashboard</span>
          </router-link>

          <!-- User Avatar / Sign In Wrapper -->
          <div class="relative" ref="userMenuContainer">
            <!-- 1. Logged In State: Avatar with reliably working click -->
            <button
              v-if="authStore.isAuthenticated"
              @click.stop="toggleUserMenu"
              class="flex items-center gap-1.5 p-1 rounded-full hover:bg-stone-100 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-400"
              aria-label="User account menu"
            >
              <img
                v-if="!avatarImgError && authStore.userAvatar"
                :src="authStore.userAvatar"
                @error="avatarImgError = true"
                alt="User Profile"
                class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover border-2 border-brand-300 shadow-sm"
              />
              <div
                v-else
                class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 text-white font-bold text-xs flex items-center justify-center border-2 border-white shadow-sm"
              >
                {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
              </div>
            </button>

            <!-- 2. Logged Out State: Desktop text pill & Mobile compact user button -->
            <template v-else>
              <button
                @click="$emit('open-auth')"
                class="hidden sm:inline-flex px-3.5 sm:px-4 py-2 text-xs font-semibold rounded-full bg-brand-600 text-white hover:bg-brand-700 transition-colors shadow-sm"
              >
                Sign In
              </button>
              <button
                @click="$emit('open-auth')"
                class="sm:hidden p-2 rounded-full text-stone-600 hover:text-brand-600 hover:bg-stone-100 transition-colors"
                aria-label="Sign In"
                title="Sign In"
              >
                <User class="w-4 h-4" />
              </button>
            </template>

            <!-- Dropdown Menu for Authenticated User -->
            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 scale-95 -translate-y-1"
              enter-to-class="opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95 -translate-y-1"
            >
              <div
                v-if="isUserMenuOpen"
                class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-2xl border border-stone-100 py-2 z-50 text-sm origin-top-right"
              >
                <div class="px-4 py-2.5 border-b border-stone-100">
                  <p class="font-semibold text-stone-900 truncate text-sm">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-stone-500 truncate">{{ authStore.user?.email }}</p>
                  <span v-if="authStore.skinProfile?.skin_type" class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                    {{ authStore.skinProfile.skin_type }} · {{ authStore.skinProfile.undertone }}
                  </span>
                </div>
                <router-link to="/account" @click="closeUserMenu" class="flex items-center gap-2 px-4 py-2.5 text-stone-700 hover:bg-stone-50 hover:text-brand-600 text-xs">
                  <User class="w-4 h-4 shrink-0" /> My Profile &amp; Orders
                </router-link>
                <router-link to="/skin-quiz" @click="closeUserMenu" class="flex items-center gap-2 px-4 py-2.5 text-stone-700 hover:bg-stone-50 hover:text-brand-600 text-xs">
                  <Sparkles class="w-4 h-4 shrink-0" /> Skin Diagnostic
                </router-link>

                <button
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2.5 text-rose-600 hover:bg-rose-50 flex items-center gap-2 text-xs font-medium border-t border-stone-100 transition-colors"
                >
                  <LogOut class="w-4 h-4 text-rose-500 shrink-0" />
                  <span>Sign Out</span>
                </button>
              </div>
            </Transition>
          </div>

          <!-- Wishlist Button -->
          <router-link
            to="/wishlist"
            class="relative p-2 sm:p-2.5 text-stone-600 hover:text-rose-500 hover:bg-stone-100 rounded-full transition-colors shrink-0"
            aria-label="Wishlist"
          >
            <Heart class="w-4 h-4 sm:w-5 sm:h-5" :class="{'fill-rose-500 text-rose-500': wishlistStore.count > 0}" />
            <span
              v-if="wishlistStore.count > 0"
              class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white font-bold text-[9px] min-w-[18px] h-[18px] px-0.5 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
            >
              {{ wishlistStore.count }}
            </span>
          </router-link>

          <!-- Cart Button -->
          <button
            @click="cartStore.toggleCart(true)"
            class="relative p-2 sm:p-2.5 text-stone-600 hover:text-brand-600 hover:bg-stone-100 rounded-full transition-colors shrink-0"
            aria-label="Cart"
          >
            <ShoppingBag class="w-4 h-4 sm:w-5 sm:h-5" />
            <span
              v-if="cartStore.itemCount > 0"
              class="absolute -top-0.5 -right-0.5 bg-brand-600 text-white font-bold text-[9px] min-w-[18px] h-[18px] px-0.5 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
            >
              {{ cartStore.itemCount }}
            </span>
          </button>

          <!-- Mobile Hamburger Menu Toggle (Guaranteed Visible & Sized) -->
          <button
            @click.stop="toggleMobileMenu"
            class="lg:hidden p-2 rounded-full hover:bg-stone-100 transition-colors text-stone-700 shrink-0 z-20"
            :aria-label="isMobileMenuOpen ? 'Close menu' : 'Open menu'"
          >
            <X v-if="isMobileMenuOpen" class="w-5 h-5" />
            <Menu v-else class="w-5 h-5" />
          </button>

        </div>

      </div>
    </div>

    <!-- ── MOBILE MENU DRAWER ── -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="isMobileMenuOpen" class="lg:hidden bg-white border-t border-stone-100 shadow-2xl overflow-y-auto max-h-[calc(100vh-64px)]">
        <!-- Mobile Search -->
        <div class="px-4 pt-4 pb-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearchMobile"
              type="text"
              placeholder="Search shades, ingredients..."
              class="w-full bg-stone-100 border border-stone-200 text-sm rounded-full pl-9 pr-4 py-2.5 text-stone-800 placeholder-stone-400 focus:outline-none focus:border-brand-500 transition-all"
            />
            <Search class="w-4 h-4 text-stone-400 absolute left-3 top-3 pointer-events-none" />
          </div>
        </div>

        <!-- Mobile Nav Links -->
        <nav class="px-4 py-2 space-y-0.5">
          <router-link
            v-for="link in mobileNavLinks"
            :key="link.to"
            :to="link.to"
            @click="closeMobileMenu"
            class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-stone-700 hover:bg-stone-50 hover:text-brand-600 transition-colors"
            active-class="bg-brand-50 text-brand-600 font-semibold"
          >
            <component :is="link.icon" class="w-4 h-4 shrink-0" />
            {{ link.label }}
          </router-link>
        </nav>

        <!-- Mobile Auth / Account Section -->
        <div class="px-4 pb-5 pt-2 border-t border-stone-100 mt-2 space-y-2">
          <template v-if="authStore.isAuthenticated">
            <div class="flex items-center gap-3 px-3 py-3 bg-stone-50 rounded-xl">
              <img
                v-if="!avatarImgError && authStore.userAvatar"
                :src="authStore.userAvatar"
                @error="avatarImgError = true"
                alt="Avatar"
                class="w-9 h-9 rounded-full object-cover border-2 border-brand-200"
              />
              <div
                v-else
                class="w-9 h-9 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center border-2 border-white"
              >
                {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-stone-900 text-sm truncate">{{ authStore.user?.name }}</p>
                <p class="text-xs text-stone-400 truncate">{{ authStore.user?.email }}</p>
              </div>
            </div>
            <router-link
              to="/account"
              @click="closeMobileMenu"
              class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-brand-50 text-brand-700 text-sm font-semibold border border-brand-200"
            >
              <User class="w-4 h-4 shrink-0" />
              <span>My Dashboard</span>
            </router-link>
            <button
              @click="handleLogout"
              class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 text-sm font-medium transition-colors"
            >
              <LogOut class="w-4 h-4 shrink-0" />
              <span>Sign Out</span>
            </button>
          </template>
          <template v-else>
            <button
              @click="openAuthAndClose"
              class="w-full py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition-colors shadow-sm flex items-center justify-center gap-2"
            >
              <User class="w-4 h-4" />
              <span>Sign In to BeautyVault</span>
            </button>
          </template>
        </div>
      </div>
    </Transition>

  </header>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useWishlistStore } from '../stores/wishlist';
import {
  Search, ShoppingBag, Sparkles, Heart, LogOut,
  User, Menu, X, Home, BookOpen, Layers, MapPin, FlaskConical, HelpCircle,
} from 'lucide-vue-next';

const emit = defineEmits(['open-auth']);

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();

const searchQuery = ref('');
const isUserMenuOpen = ref(false);
const isMobileMenuOpen = ref(false);
const userMenuContainer = ref(null);
const avatarImgError = ref(false);

watch(() => authStore.userAvatar, () => {
  avatarImgError.value = false;
});

const mobileNavLinks = [
  { to: '/',             label: 'Home',               icon: Home },
  { to: '/catalog',      label: 'Catalog',            icon: Layers },
  { to: '/shade-finder', label: 'Shade Matcher',      icon: BookOpen },
  { to: '/skin-quiz',    label: 'Skin Diagnostic',    icon: Sparkles },
  { to: '/ingredients',  label: 'Ingredients (INCI)', icon: FlaskConical },
  { to: '/about',        label: 'About BeautyVault',  icon: BookOpen },
  { to: '/faq',          label: 'Help & FAQ',         icon: HelpCircle },
  { to: '/track-order',  label: 'Track Order',        icon: MapPin },
];

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
  isUserMenuOpen.value = false;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

const handleOutsideClick = (event) => {
  if (userMenuContainer.value && !userMenuContainer.value.contains(event.target)) {
    closeUserMenu();
  }
};

onMounted(() => {
  document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/catalog', query: { search: searchQuery.value.trim() } });
    searchQuery.value = '';
  }
};

const handleSearchMobile = () => {
  handleSearch();
  closeMobileMenu();
};

const handleLogout = async () => {
  closeUserMenu();
  closeMobileMenu();
  await authStore.logout();
  router.push('/');
};

const openAuthAndClose = () => {
  closeMobileMenu();
  emit('open-auth');
};
</script>
