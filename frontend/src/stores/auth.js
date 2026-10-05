import { defineStore } from 'pinia';
import api from '../services/api';
import { useWishlistStore } from './wishlist';
import { useCartStore } from './cart';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('beautyvault_user') || 'null'),
    token: localStorage.getItem('beautyvault_token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin' || state.user?.role === 'manager',
    skinProfile: (state) => state.user?.skin_profile || null,
    userAvatar: (state) => {
      const url = state.user?.avatar_url;
      if (!url) return 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80';
      if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) {
        return url;
      }
      return `http://localhost:8000/${url.replace(/^\/+/, '')}`;
    },
  },

  actions: {
    async login(email, password) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.post('/auth/login', { email, password });
        this.token = res.data.token;
        this.user = res.data.user;
        localStorage.setItem('beautyvault_token', this.token);
        localStorage.setItem('beautyvault_user', JSON.stringify(this.user));
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async register(data) {
      this.loading = true;
      this.error = null;
      try {
        // Build FormData to support optional avatar file upload
        const formData = new FormData();
        formData.append('name', data.name);
        formData.append('email', data.email);
        formData.append('password', data.password);
        formData.append('password_confirmation', data.password_confirmation);
        if (data.avatarFile) {
          formData.append('avatar', data.avatarFile);
        } else if (data.avatar_url) {
          formData.append('avatar_url', data.avatar_url);
        }

        const res = await api.post('/auth/register', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        this.token = res.data.token;
        this.user = res.data.user;
        localStorage.setItem('beautyvault_token', this.token);
        localStorage.setItem('beautyvault_user', JSON.stringify(this.user));
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Registration failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      if (!this.token) return;
      try {
        const res = await api.get('/auth/me');
        this.user = res.data.user;
        localStorage.setItem('beautyvault_user', JSON.stringify(this.user));
      } catch (err) {
        if (err.response?.status === 401) {
          this.logout();
        }
      }
    },

    async updateSkinProfile(profileData) {
      try {
        const res = await api.put('/auth/skin-profile', profileData);
        if (this.user) {
          this.user.skin_profile = res.data.profile;
          localStorage.setItem('beautyvault_user', JSON.stringify(this.user));
        }
        return res.data;
      } catch (err) {
        throw err;
      }
    },

    async updateProfile({ name, phone, avatarFile, avatar_url }) {
      const formData = new FormData();
      if (name !== undefined) formData.append('name', name);
      if (phone !== undefined) formData.append('phone', phone);
      if (avatarFile) formData.append('avatar', avatarFile);
      if (avatar_url) formData.append('avatar_url', avatar_url);

      const res = await api.post('/auth/profile', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      this.user = res.data.user;
      localStorage.setItem('beautyvault_user', JSON.stringify(this.user));
      return res.data;
    },

    async logout() {
      if (this.token) {
        try {
          await api.post('/auth/logout');
        } catch (err) {
          console.warn('Backend logout error:', err);
        }
      }
      // Clear user-specific stores so next user doesn't see stale data
      try {
        const wishlistStore = useWishlistStore();
        wishlistStore.clear();
      } catch (_) {}
      try {
        const cartStore = useCartStore();
        cartStore.items = [];
        cartStore.subtotal = 0;
        cartStore.total = 0;
      } catch (_) {}

      this.user = null;
      this.token = null;
      this.error = null;
      localStorage.removeItem('beautyvault_token');
      localStorage.removeItem('beautyvault_user');
    },
  },
});
