<template>
  <footer class="bg-stone-950 text-stone-300 pt-16 pb-12 border-t border-stone-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Top Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-stone-800/80">
        
        <!-- Brand Info -->
        <div class="lg:col-span-2 space-y-4">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white font-serif font-bold text-lg">
              B
            </div>
            <span class="font-serif text-2xl font-bold tracking-tight text-white">
              BEAUTY<span class="text-brand-500 font-light">VAULT</span>
            </span>
          </div>
          <p class="text-xs text-stone-400 leading-relaxed max-w-sm">
            Engineered cosmetics, clinical-grade skincare, and AI-driven shade matching algorithms.
            Every formula is verified non-comedogenic, cruelty-free, and batch-tracked with FIFO transparency.
          </p>
          <div class="flex items-center gap-3 pt-2">
            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-stone-900 border border-stone-800 text-stone-300">
              🐰 Leaping Bunny Certified
            </span>
            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-stone-900 border border-stone-800 text-stone-300">
              🧪 Dermatologist Tested
            </span>
          </div>
        </div>

        <!-- Col 1: Shop -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-white">Clinical Formulations</h4>
          <ul class="space-y-2 text-xs text-stone-400">
            <li><router-link to="/catalog?category=skincare" class="hover:text-brand-400 transition-colors">Skincare &amp; Clinical Serums</router-link></li>
            <li><router-link to="/catalog?category=complexion" class="hover:text-brand-400 transition-colors">Complexion &amp; Inclusive Foundations</router-link></li>
            <li><router-link to="/catalog?category=cheeks-glow" class="hover:text-brand-400 transition-colors">Liquid Blushes &amp; Glow</router-link></li>
            <li><router-link to="/catalog?category=sun-protection" class="hover:text-brand-400 transition-colors">Mineral SPF 50 Shields</router-link></li>
          </ul>
        </div>

        <!-- Col 2: Diagnostic Tools -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-white">Beauty Intelligence</h4>
          <ul class="space-y-2 text-xs text-stone-400">
            <li><router-link to="/shade-finder" class="hover:text-brand-400 transition-colors">Undertone Shade Matcher</router-link></li>
            <li><router-link to="/skin-quiz" class="hover:text-brand-400 transition-colors">Skin Diagnostic Routine Quiz</router-link></li>
            <li><router-link to="/ingredients" class="hover:text-brand-400 transition-colors">Comedogenic INCI Glossary</router-link></li>
            <li><router-link to="/track-order" class="hover:text-brand-400 transition-colors">Live Shipment Tracking</router-link></li>
          </ul>
        </div>

        <!-- Col 3: Customer Care & Transparency -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-white">Trust & Company</h4>
          <ul class="space-y-2 text-xs text-stone-400">
            <li><router-link to="/about" class="hover:text-brand-400 transition-colors">About BeautyVault</router-link></li>
            <li><router-link to="/shipping-and-returns" class="hover:text-brand-400 transition-colors">30-Day Shade Exchange &amp; Shipping</router-link></li>
            <li><router-link to="/faq" class="hover:text-brand-400 transition-colors">Frequently Asked Questions</router-link></li>
            <li><router-link to="/admin" class="text-amber-400 hover:text-amber-300 transition-colors font-medium">Admin Management Portal</router-link></li>
          </ul>
        </div>

      </div>

      <!-- Bottom Bar -->
      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-4">
        <p>© 2026 BeautyVault Labs Inc. Built with Laravel 12 REST API, Redis, Meilisearch &amp; Vue 3.</p>
        <div class="flex items-center gap-6">
          <router-link to="/privacy-policy" class="hover:text-stone-300 transition-colors">Privacy Policy</router-link>
          <router-link to="/terms-of-service" class="hover:text-stone-300 transition-colors">Terms of Service</router-link>
          <router-link to="/shipping-and-returns" class="hover:text-stone-300 transition-colors">Shipping &amp; Returns</router-link>
        </div>
      </div>

    </div>
  </footer>
</template>
