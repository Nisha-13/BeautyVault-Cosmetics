<template>
  <div class="min-h-screen flex flex-col bg-[#FDFBF9] text-[#1E1B18] antialiased">
    <!-- Navbar -->
    <Navbar @open-auth="openAuthModal" />

    <!-- Main Content Routed View -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Cart Slide-Over Drawer -->
    <CartDrawer />

    <!-- Authentication Modal (Sign In / Register) -->
    <AuthModal
      :isOpen="isAuthModalOpen"
      @close="closeAuthModal"
    />

    <!-- Luxury Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from './components/Navbar.vue';
import CartDrawer from './components/CartDrawer.vue';
import AuthModal from './components/AuthModal.vue';
import Footer from './components/Footer.vue';
import { useAuthStore } from './stores/auth';
import { useCartStore } from './stores/cart';
import { useWishlistStore } from './stores/wishlist';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();

const isAuthModalOpen = ref(false);

const openAuthModal = (targetMode) => {
  if (targetMode === 'admin_login') {
    router.replace({ query: { ...route.query, auth: 'admin_login' } });
  }
  isAuthModalOpen.value = true;
};

const closeAuthModal = () => {
  isAuthModalOpen.value = false;
  if (route.query.auth) {
    const q = { ...route.query };
    delete q.auth;
    router.replace({ query: q });
  }
};

const handleCustomOpenAuth = (e) => {
  openAuthModal(e.detail?.mode);
};

// Reactively open auth modal whenever query param ?auth is present
watch(
  () => route.query.auth,
  (newAuth) => {
    if (newAuth) {
      isAuthModalOpen.value = true;
    }
  }
);

// Lock background page scroll when cart drawer is open
watch(
  () => cartStore.isOpen,
  (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
  }
);

onMounted(async () => {
  window.addEventListener('beautyvault:open-auth', handleCustomOpenAuth);

  // Restore user session if token exists
  if (authStore.token) {
    await authStore.fetchUser();
    // Load this user's wishlist from server (never from shared localStorage)
    await wishlistStore.fetchServerWishlist();
  }
  // Initialize server-synced cart (per user_id when logged in, session otherwise)
  await cartStore.fetchCart();

  // Open auth modal if redirected with query
  if (route.query.auth) {
    isAuthModalOpen.value = true;
  }
});

onUnmounted(() => {
  window.removeEventListener('beautyvault:open-auth', handleCustomOpenAuth);
});
</script>

