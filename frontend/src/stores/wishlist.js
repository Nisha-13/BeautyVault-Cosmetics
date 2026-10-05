import { defineStore } from 'pinia';
import api from '../services/api';

export const useWishlistStore = defineStore('wishlist', {
  state: () => {
    let initialItems = [];
    try {
      const stored = localStorage.getItem('beautyvault_wishlist');
      if (stored) {
        const parsed = JSON.parse(stored);
        if (Array.isArray(parsed)) {
          initialItems = parsed.map(item => {
            const prod = item.product || item || {};
            return {
              id: Number(item.product_id || prod.id || item.id),
              product_id: Number(item.product_id || prod.id || item.id),
              product: prod,
              thumbnail_url: prod.thumbnail_url || prod.image_url || item.thumbnail_url || item.image_url || '',
              name: prod.name || item.name || '',
              brand: prod.brand || item.brand || null,
              base_price: prod.base_price || item.base_price || 0,
              slug: prod.slug || item.slug || '',
              is_vegan: prod.is_vegan || item.is_vegan || false,
            };
          });
        }
      }
    } catch (e) {
      initialItems = [];
    }
    return {
      items: initialItems,
      loading: false,
    };
  },

  getters: {
    count: (state) => state.items.length,
    // Use Number() coercion — product.id can be string or number depending on source
    isInWishlist: (state) => (productId) =>
      state.items.some(i => Number(i.product_id) === Number(productId) || Number(i.id) === Number(productId)),
  },

  actions: {
    async fetchServerWishlist() {
      try {
        const token = localStorage.getItem('beautyvault_token');
        if (!token) return;
        const res = await api.get('/wishlist');
        if (res.data?.data) {
          // Replace entire list — no merging, no duplicates, fully normalized
          this.items = res.data.data.map(item => {
            const prod = item.product || item || {};
            return {
              id: Number(item.product_id || prod.id || item.id),
              product_id: Number(item.product_id || prod.id || item.id),
              product: prod,
              thumbnail_url: prod.thumbnail_url || prod.image_url || item.thumbnail_url || item.image_url || '',
              name: prod.name || item.name || '',
              brand: prod.brand || item.brand || null,
              base_price: prod.base_price || item.base_price || 0,
              slug: prod.slug || item.slug || '',
              is_vegan: prod.is_vegan || item.is_vegan || false,
            };
          });
          this._persist();
        }
      } catch (err) {
        console.warn('Failed to fetch wishlist from server:', err);
      }
    },

    async toggle(product) {
      if (!product) return;
      const pid = Number(product.id || product.product_id);
      if (!pid) return;

      const exists = this.isInWishlist(pid);
      if (exists) {
        // Remove by numeric comparison
        this.items = this.items.filter(i => Number(i.product_id || i.id) !== pid);
      } else {
        // Guard: only push if not already present (prevents double-click duplicates)
        const alreadyInList = this.items.some(i => Number(i.product_id || i.id) === pid);
        if (!alreadyInList) {
          const prod = product.product || product || {};
          this.items.push({
            id: pid,
            product_id: pid,
            product: prod,
            thumbnail_url: prod.thumbnail_url || prod.image_url || product.thumbnail_url || product.image_url || '',
            name: prod.name || product.name || '',
            brand: prod.brand || product.brand || null,
            base_price: prod.base_price || product.base_price || 0,
            slug: prod.slug || product.slug || '',
            is_vegan: prod.is_vegan || product.is_vegan || false,
          });
        }
      }
      this._persist();

      // If user is authenticated, sync with backend
      const token = localStorage.getItem('beautyvault_token');
      if (token) {
        try {
          await api.post('/wishlist/toggle', { product_id: pid });
        } catch (err) {
          console.warn('Could not sync wishlist with server:', err);
        }
      }
    },
    _persist() {
      localStorage.setItem('beautyvault_wishlist', JSON.stringify(this.items));
    },
    async remove(productId) {
      const pid = Number(productId);
      this.items = this.items.filter(i => Number(i.product_id || i.id) !== pid);
      this._persist();

      const token = localStorage.getItem('beautyvault_token');
      if (token) {
        try {
          await api.delete(`/wishlist/${pid}`);
        } catch (err) {
          console.warn('Could not remove wishlist item on server:', err);
        }
      }
    },
    clear() {
      this.items = [];
      this._persist();
    },
  },
});
