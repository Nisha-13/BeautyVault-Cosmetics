import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    coupon: null,
    couponMessage: null,
    subtotal: 0,
    discount: 0,
    shipping: 0,
    tax: 0,
    total: 0,
    freeShippingThreshold: 40,
    amountToFreeShipping: 40,
    isOpen: false,
    loading: false,
  }),

  getters: {
    itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    isFreeShipping: (state) => state.amountToFreeShipping <= 0,
  },

  actions: {
    async fetchCart() {
      try {
        const res = await api.get('/cart');
        this.updateStateFromResponse(res.data);
      } catch (err) {
        console.error('Failed to fetch cart:', err);
      }
    },

    async addToCart(variantId, quantity = 1) {
      this.loading = true;
      try {
        const res = await api.post('/cart/items', {
          product_variant_id: variantId,
          quantity,
        });
        this.updateStateFromResponse(res.data);
        this.isOpen = true; // Open drawer for instant feedback
        return true;
      } catch (err) {
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateQuantity(itemId, quantity) {
      try {
        const res = await api.patch(`/cart/items/${itemId}`, { quantity });
        this.updateStateFromResponse(res.data);
      } catch (err) {
        console.error('Failed to update quantity:', err);
        throw err;
      }
    },

    async removeItem(itemId) {
      try {
        const res = await api.delete(`/cart/items/${itemId}`);
        this.updateStateFromResponse(res.data);
      } catch (err) {
        console.error('Failed to remove item:', err);
      }
    },

    async applyCoupon(code) {
      try {
        const res = await api.post('/cart/coupon', { code });
        this.updateStateFromResponse(res.data);
        return res.data;
      } catch (err) {
        throw err;
      }
    },

    async removeCoupon() {
      try {
        const res = await api.delete('/cart/coupon');
        this.updateStateFromResponse(res.data);
      } catch (err) {
        console.error('Failed to remove coupon:', err);
      }
    },

    updateStateFromResponse(data) {
      this.items = data.items || [];
      this.coupon = data.coupon || null;
      this.couponMessage = data.coupon_message || null;
      this.subtotal = data.subtotal || 0;
      this.discount = data.discount || 0;
      this.shipping = data.shipping || 0;
      this.tax = data.tax || 0;
      this.total = data.total || 0;
      this.amountToFreeShipping = data.amount_to_free_shipping ?? 40;
    },

    toggleCart(val = null) {
      this.isOpen = val !== null ? val : !this.isOpen;
    },
  },
});
