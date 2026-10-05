import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/catalog',
    name: 'catalog',
    component: () => import('../views/CatalogView.vue'),
  },
  {
    path: '/products/:slug',
    name: 'product-detail',
    component: () => import('../views/ProductDetailView.vue'),
  },
  {
    path: '/shade-finder',
    name: 'shade-finder',
    component: () => import('../views/ShadeFinderView.vue'),
  },
  {
    path: '/skin-quiz',
    name: 'skin-quiz',
    component: () => import('../views/SkinQuizView.vue'),
  },
  {
    path: '/ingredients',
    name: 'ingredients',
    component: () => import('../views/IngredientsView.vue'),
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('../views/CheckoutView.vue'),
  },
  {
    path: '/track-order',
    name: 'track-order',
    component: () => import('../views/OrderTrackingView.vue'),
  },
  {
    path: '/wishlist',
    name: 'wishlist',
    component: () => import('../views/WishlistView.vue'),
  },
  {
    path: '/account',
    name: 'account',
    component: () => import('../views/CustomerDashboardView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../views/AdminDashboardView.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue'),
  },
  {
    path: '/faq',
    name: 'faq',
    component: () => import('../views/FaqView.vue'),
  },
  {
    path: '/privacy-policy',
    name: 'privacy-policy',
    component: () => import('../views/PrivacyPolicyView.vue'),
  },
  {
    path: '/terms-of-service',
    name: 'terms-of-service',
    component: () => import('../views/TermsView.vue'),
  },
  {
    path: '/shipping-and-returns',
    name: 'shipping-and-returns',
    component: () => import('../views/ShippingReturnsView.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('beautyvault_token');
  const user = JSON.parse(localStorage.getItem('beautyvault_user') || 'null');

  if (to.meta.requiresAuth && !token) {
    return next({ name: 'home', query: { auth: 'login' } });
  }

  if (to.meta.requiresAdmin) {
    if (!token || (user?.role !== 'admin' && user?.role !== 'manager')) {
      window.dispatchEvent(new CustomEvent('beautyvault:open-auth', { detail: { mode: 'admin_login' } }));
      if (from.name) {
        return next(false);
      } else {
        return next({ name: 'home', query: { auth: 'admin_login' } });
      }
    }
  }

  next();
});

export default router;
