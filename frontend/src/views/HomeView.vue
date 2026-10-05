<template>
  <div class="w-full max-w-full overflow-x-hidden">

    <!-- ═══════════════════════════════════════════════════════
         HERO — Authentic High-End Editorial Cosmetic Showcase
    ════════════════════════════════════════════════════════════ -->
    <section class="relative bg-stone-950 text-white min-h-[90svh] flex items-center overflow-hidden w-full">

      <!-- Ambient Lighting Glows (Strictly contained within section) -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 left-1/4 w-96 sm:w-[550px] h-96 sm:h-[550px] bg-brand-600/15 rounded-full blur-[130px]"></div>
        <div class="absolute bottom-0 right-10 w-80 sm:w-[450px] h-80 sm:h-[450px] bg-rosewood/25 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.025)_1px,transparent_1px)] bg-[size:28px_28px] opacity-40"></div>
      </div>

      <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

          <!-- ═══════ LEFT COLUMN: Authentic, Luxury, High-Impact ═══════ -->
          <div class="space-y-6 sm:space-y-7 lg:col-span-7">

            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/15 border border-brand-400/30 text-rose-300 text-xs font-semibold tracking-wide backdrop-blur-md">
              <Sparkles class="w-3.5 h-3.5 text-brand-400 shrink-0" />
              <span>AI Clinical Shade &amp; Barrier Diagnostic</span>
            </div>

            <!-- Headline -->
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.12] text-white">
              Beauty Formulated<br />
              for Your
              <span class="relative inline-block mx-1.5">
                <span class="italic font-light text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-rose-300 to-amber-200">Exact</span>
                <span class="absolute -bottom-1 left-0 w-full h-[3px] bg-gradient-to-r from-brand-400 to-rose-300 rounded-full opacity-60"></span>
              </span>
              <br class="hidden sm:block" />
              Skin Chemistry.
            </h1>

            <!-- Editorial Subtitle -->
            <p class="text-stone-300 text-sm sm:text-base leading-relaxed max-w-xl font-normal">
              No more shade guesswork, oxidizing orange foundations, or acne-triggering comedogenic ingredients. Discover dermatological skincare and 50+ inclusive shades, calibrated with FIFO batch-tested freshness.
            </p>

            <!-- Primary CTAs -->
            <div class="flex flex-col sm:flex-row gap-3.5 pt-1">
              <router-link
                to="/shade-finder"
                class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs sm:text-sm transition-all duration-300 shadow-glow hover:scale-105 active:scale-95 text-center"
              >
                <Palette class="w-4 h-4 shrink-0" />
                <span>Find My Shade Match</span>
                <ArrowRight class="w-3.5 h-3.5 ml-1 hidden sm:inline" />
              </router-link>

              <router-link
                to="/skin-quiz"
                class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm backdrop-blur-md border border-white/20 transition-all hover:scale-105 active:scale-95 text-center"
              >
                <Sparkles class="w-4 h-4 text-rose-300 shrink-0" />
                <span>Take Skin Diagnostic</span>
              </router-link>
            </div>

            <!-- Social Proof Strip -->
            <div class="pt-3 flex flex-wrap items-center gap-4 sm:gap-6 text-xs text-stone-300 border-t border-white/10">
              <div class="flex items-center gap-3">
                <div class="flex -space-x-2 shrink-0">
                  <img
                    v-for="(avatar, i) in customerAvatars"
                    :key="i"
                    :src="avatar"
                    alt="Verified Customer"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 border-stone-900 object-cover"
                  />
                </div>
                <div>
                  <div class="flex items-center gap-1 text-amber-400">
                    <Star v-for="n in 5" :key="n" class="w-3 h-3 fill-current" />
                    <span class="text-xs font-bold text-white ml-1">4.96/5</span>
                  </div>
                  <p class="text-[10px] sm:text-[11px] text-stone-400">{{ storeStats.verified_reviews }} Verified Clinical Reviews</p>
                </div>
              </div>

              <div class="hidden sm:block h-6 w-px bg-white/15"></div>

              <div class="flex items-center gap-2 text-stone-300 text-[11px] sm:text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                <span>Dermatologist Approved &amp; Cruelty-Free</span>
              </div>
            </div>

            <!-- Key Trust Metrics (Clean 3-Col Bar - Dynamically Computed from Catalog Batches & Undertones) -->
            <div class="grid grid-cols-3 gap-3 sm:gap-6 max-w-lg pt-2">
              <div>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold font-serif text-white">{{ storeStats.inclusive_undertones }}</p>
                <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">Inclusive Undertones</p>
              </div>
              <div>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold font-serif text-white">{{ storeStats.fifo_freshness }}</p>
                <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">FIFO Freshness Lot</p>
              </div>
              <div>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold font-serif text-white">{{ storeStats.comedogenic_score }}</p>
                <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">Comedogenic Score</p>
              </div>
            </div>

            <!-- Mobile Showcase Preview (Shown on < lg screens so mobile users get an authentic visual) -->
            <div class="lg:hidden pt-2">
              <div class="relative rounded-2xl overflow-hidden bg-gradient-to-tr from-stone-900 to-stone-850 border border-white/15 p-4 shadow-xl">
                <div class="flex items-center gap-4">
                  <div class="relative w-20 h-20 rounded-xl overflow-hidden shrink-0 border border-white/10">
                    <img
                      src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=300&q=80"
                      @error="handleImgError"
                      alt="Cult Favorite Blush"
                      class="w-full h-full object-cover"
                    />
                    <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-brand-600 text-white uppercase">
                      Cult Pick
                    </span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-brand-300 font-bold uppercase tracking-wider">Rare Beauty · $23.00</p>
                    <p class="font-serif text-sm font-bold text-white truncate mt-0.5">Soft Pinch Liquid Blush</p>
                    <div class="flex items-center gap-1 mt-1 text-amber-400">
                      <Star v-for="n in 5" :key="n" class="w-3 h-3 fill-current" />
                      <span class="text-[10px] text-stone-300 ml-1">4.95 (310 verified reviews)</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-2">
                      <span class="w-3 h-3 rounded-full border border-white/40" style="background:#DE8B87"></span>
                      <span class="w-3 h-3 rounded-full border border-white/40" style="background:#EB8A74"></span>
                      <span class="w-3 h-3 rounded-full border border-white/40" style="background:#EA7899"></span>
                      <span class="text-[9px] text-stone-400 ml-1">4 Shades Available</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ═══════ RIGHT COLUMN: Refined Luxury Editorial Showcase (Desktop) ═══════ -->
          <div class="relative hidden lg:flex items-center justify-center lg:col-span-5">
            <div class="relative w-full max-w-[390px] mx-auto">

              <!-- Main Hero Product Frame -->
              <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/15 aspect-[4/5] bg-stone-900 group">
                <img
                  src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=85"
                  @error="handleImgError"
                  alt="Luxury Cosmetic Pigment"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-stone-950/80 via-transparent to-transparent"></div>

                <!-- Floating Glass Info Badge inside the frame -->
                <div class="absolute bottom-4 left-4 right-4 bg-stone-900/85 backdrop-blur-xl rounded-2xl p-4 border border-white/20 shadow-2xl">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-[10px] text-brand-300 font-semibold uppercase tracking-wider">Cult Bestseller</p>
                      <p class="text-sm font-bold text-white font-serif mt-0.5">Soft Pinch Liquid Blush</p>
                      <p class="text-[11px] text-rose-300 mt-0.5">Rare Beauty · $23.00</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                      <div class="flex gap-1">
                        <span class="w-3.5 h-3.5 rounded-full border border-white/30" style="background:#DE8B87"></span>
                        <span class="w-3.5 h-3.5 rounded-full border border-white/30" style="background:#EB8A74"></span>
                        <span class="w-3.5 h-3.5 rounded-full border border-white/30" style="background:#EA7899"></span>
                      </div>
                      <span class="text-[10px] text-stone-300">4 clinical shades</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-1 mt-2">
                    <Star v-for="n in 5" :key="n" class="w-3 h-3 fill-amber-400 text-amber-400" />
                    <span class="text-[10px] text-stone-300 ml-1">4.95 · 310 verified reviews</span>
                  </div>
                </div>
              </div>

              <!-- Top-Right Mini Active Card (Properly inside container margins) -->
              <div class="absolute -top-4 -right-4 w-40 bg-stone-900/90 backdrop-blur-xl rounded-2xl p-3 border border-white/15 shadow-xl">
                <img
                  src="https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&q=80"
                  @error="handleImgError"
                  alt="Niacinamide Serum"
                  class="w-full aspect-square object-cover rounded-xl mb-2"
                />
                <p class="text-[9px] text-stone-400 font-semibold uppercase tracking-wider">Barrier Science</p>
                <p class="text-xs font-bold text-white font-serif mt-0.5 truncate">Niacinamide 10%</p>
                <div class="flex items-center justify-between mt-1">
                  <span class="text-xs font-bold text-brand-300">$10.80</span>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[9px] font-bold">In Stock</span>
                </div>
              </div>

              <!-- Bottom-Left Clinical Batch Badge -->
              <div class="absolute -bottom-3 -left-4 bg-brand-600 rounded-2xl p-3.5 shadow-glow text-center w-28 border border-brand-400/30">
                <p class="text-xl font-bold font-serif text-white">22+</p>
                <p class="text-[9px] text-brand-100 leading-tight mt-0.5">Clinical<br>Formulations</p>
              </div>

            </div>
          </div>

        </div>
      </div>

    </section>

    <!-- ═══════════════════════════════════════════════════════
         MARQUEE — Scrolling Brand Partner Strip (Contained)
    ════════════════════════════════════════════════════════════ -->
    <div class="bg-stone-100 border-y border-stone-200 py-3.5 w-full overflow-hidden">
      <div class="flex gap-8 sm:gap-12 animate-marquee whitespace-nowrap">
        <span v-for="(brand, i) in [...marqueeItems, ...marqueeItems]" :key="i"
          class="text-xs font-semibold text-stone-500 uppercase tracking-widest shrink-0 flex items-center gap-2.5 sm:gap-3">
          <span class="w-1.5 h-1.5 rounded-full bg-brand-500 inline-block"></span>
          {{ brand }}
        </span>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CATEGORIES GRID (Every Image Distinct & Specific)
    ════════════════════════════════════════════════════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20 w-full">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Curated Categories</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900 mt-1">Shop by Clinical Concern</h2>
        </div>
        <router-link to="/catalog" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1 group shrink-0">
          <span>Explore All Categories</span>
          <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </router-link>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        <router-link
          v-for="cat in categories"
          :key="cat.slug"
          :to="`/catalog?category=${cat.slug}`"
          class="group relative rounded-2xl overflow-hidden bg-stone-100 shadow-soft hover:shadow-xl transition-all duration-300 aspect-[4/5]"
        >
          <img :src="cat.image" :alt="cat.name" @error="handleImgError" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></div>
          <div class="absolute bottom-3.5 left-3.5 right-3.5 text-white">
            <span class="inline-block px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-white/20 backdrop-blur-sm border border-white/25 mb-1.5">{{ cat.tag }}</span>
            <h3 class="font-serif text-sm sm:text-base lg:text-lg font-bold leading-tight">{{ cat.name }}</h3>
            <p class="text-[10px] sm:text-[11px] text-stone-300 mt-0.5 line-clamp-1 hidden sm:block">{{ cat.desc }}</p>
          </div>
        </router-link>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         BESTSELLERS GRID
    ════════════════════════════════════════════════════════════ -->
    <section class="bg-stone-50 py-12 sm:py-16 lg:py-20 border-y border-stone-200/60 w-full">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Formulation Highlights</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900 mt-1">Bestselling Cult Favorites</h2>
          </div>
          <router-link to="/catalog?sort=bestseller" class="text-xs font-semibold text-stone-600 hover:text-brand-600 shrink-0 flex items-center gap-1">
            <span>View All Bestsellers</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
          <div v-for="n in 4" :key="n" class="h-64 sm:h-96 bg-stone-200 rounded-3xl animate-pulse"></div>
        </div>

        <div v-else class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
          <div
            v-for="product in bestsellers"
            :key="product.id"
            class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200/80 p-3 sm:p-4 flex flex-col group hover:shadow-xl hover:border-brand-200 transition-all duration-300"
          >
            <!-- Image -->
            <div class="relative rounded-xl sm:rounded-2xl overflow-hidden aspect-square bg-stone-50 mb-3">
              <img :src="product.thumbnail_url" :alt="product.name" @error="handleImgError" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <span v-if="product.is_bestseller" class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-stone-900 text-white shadow-sm">
                Cult
              </span>
              <!-- Wishlist heart -->
              <button
                @click.prevent="wishlistStore.toggle(product)"
                class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/85 backdrop-blur-sm flex items-center justify-center border border-stone-100 shadow-sm transition-all hover:scale-110"
              >
                <Heart class="w-3.5 h-3.5 sm:w-4 sm:h-4" :class="wishlistStore.isInWishlist(product.id) ? 'fill-rose-500 text-rose-500' : 'text-stone-400'" />
              </button>
            </div>

            <!-- Details -->
            <div class="flex-1 flex flex-col justify-between">
              <div>
                <p class="text-[9px] sm:text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ product.brand?.name }}</p>
                <router-link :to="`/products/${product.slug}`" class="block mt-0.5 font-serif text-xs sm:text-base font-bold text-stone-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                  {{ product.name }}
                </router-link>
                <p class="text-[11px] text-stone-500 mt-1 line-clamp-2 leading-relaxed hidden sm:block">{{ product.subtitle }}</p>
              </div>

              <!-- Shade dots -->
              <div v-if="product.variants?.length > 1" class="flex items-center gap-1 my-2">
                <span v-for="v in product.variants.slice(0, 5)" :key="v.id"
                  class="w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full border border-black/10 shadow-sm"
                  :style="{ backgroundColor: v.shade_hex || '#D8A279' }"
                  :title="v.shade_name"
                ></span>
                <span v-if="product.variants.length > 5" class="text-[9px] sm:text-[10px] text-stone-500 font-semibold ml-0.5">+{{ product.variants.length - 5 }}</span>
              </div>

              <!-- Price & CTA -->
              <div class="pt-2 sm:pt-2.5 border-t border-stone-100 flex items-center justify-between mt-1 gap-2">
                <div>
                  <span class="text-xs sm:text-base font-bold text-stone-900">
                    ${{ parseFloat(product.discount_price || product.base_price).toFixed(2) }}
                  </span>
                  <span v-if="product.discount_price && product.discount_price < product.base_price" class="text-[10px] sm:text-[11px] text-stone-400 line-through ml-1 hidden sm:inline">
                    ${{ parseFloat(product.base_price).toFixed(2) }}
                  </span>
                </div>
                <router-link :to="`/products/${product.slug}`"
                  class="px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-full bg-stone-900 group-hover:bg-brand-600 text-white text-[10px] sm:text-xs font-semibold transition-colors shadow-sm shrink-0">
                  View
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         SHADE FINDER FEATURE BANNER
    ════════════════════════════════════════════════════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 w-full">
      <div class="relative rounded-3xl bg-gradient-to-br from-stone-900 via-rosewood-dark to-stone-950 text-white overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-80 h-80 bg-brand-600/20 rounded-full blur-[90px] pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center p-6 sm:p-10 lg:p-14">
          <!-- Text -->
          <div class="space-y-4">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
              Proprietary Undertone Matrix
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight">
              Stop Guessing Your Foundation Shade Online.
            </h2>
            <p class="text-xs sm:text-sm text-stone-300 leading-relaxed max-w-lg">
              Whether you have cool olive, golden warm, or true peach undertones, our algorithm matches your depth level across Fenty, Rare Beauty &amp; Charlotte Tilbury with 98% accuracy.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
              <router-link to="/shade-finder"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-white text-stone-900 hover:bg-stone-100 text-xs font-bold tracking-wide transition-all shadow-md hover:scale-105">
                <span>Launch Shade Matcher</span>
                <ArrowRight class="w-4 h-4" />
              </router-link>
              <router-link to="/skin-quiz"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold tracking-wide transition-all">
                <Sparkles class="w-4 h-4 text-rose-300" />
                <span>Take Skin Diagnostic</span>
              </router-link>
            </div>
          </div>

          <!-- Feature Cards Grid -->
          <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <div v-for="feat in features" :key="feat.label"
              class="p-4 rounded-2xl bg-white/8 backdrop-blur-md border border-white/10 space-y-1">
              <span class="text-xl sm:text-2xl">{{ feat.icon }}</span>
              <p class="text-xs sm:text-sm font-bold text-white mt-1">{{ feat.label }}</p>
              <p class="text-[10px] sm:text-xs text-stone-400 leading-snug">{{ feat.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         CLINICAL ROUTINE PROMISE (Unique Lab Image)
    ════════════════════════════════════════════════════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 w-full">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
        <div class="relative rounded-3xl overflow-hidden aspect-[4/3] shadow-xl border border-stone-200">
          <img
            src="https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=900&q=85"
            @error="handleImgError"
            alt="Clinical Laboratory Formulation"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md border border-white/25">FIFO Freshness Lot Guarantee</span>
            <p class="font-serif text-lg font-bold mt-2">Never Buy Stale or Degraded Active Formulas</p>
          </div>
        </div>

        <div class="space-y-6">
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">The BeautyVault Guarantee</span>
          <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 leading-tight">
            Clinical Freshness &amp; Shade Perfection, Backed by Science.
          </h2>
          <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
            Every cosmetic bottle in our warehouse is managed by strict First-In, First-Out (FIFO) batch lot tracking. You always receive product with minimum 12+ months PAO lifespan, stored under climate-controlled conditions.
          </p>

          <div class="space-y-3.5">
            <div class="flex items-start gap-3">
              <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
              <div>
                <p class="text-xs sm:text-sm font-bold text-stone-900">Zero-Risk Shade Guarantee</p>
                <p class="text-[11px] sm:text-xs text-stone-500">If your foundation doesn't blend seamlessly, exchange it free within 30 days.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
              <div>
                <p class="text-xs sm:text-sm font-bold text-stone-900">Comedogenic Score Indexing</p>
                <p class="text-[11px] sm:text-xs text-stone-500">Every single ingredient is mapped against clinical pore-clogging databases (0-5 scale).</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
              <div>
                <p class="text-xs sm:text-sm font-bold text-stone-900">Complimentary 2-Day Shipping</p>
                <p class="text-[11px] sm:text-xs text-stone-500">On all orders over $40 with luxury protective packaging &amp; free deluxe shade samples.</p>
              </div>
            </div>
          </div>

          <div class="pt-2">
            <router-link to="/catalog?category=skincare"
              class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-stone-900 hover:bg-brand-600 text-white text-xs font-bold tracking-wide transition-all shadow-md">
              <span>Start Exploring Formulations</span>
              <ArrowRight class="w-4 h-4" />
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         VERIFIED REVIEWS
    ════════════════════════════════════════════════════════════ -->
    <section class="bg-stone-50 py-12 sm:py-16 lg:py-20 border-t border-stone-200/60 w-full">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-10 sm:mb-12 space-y-2">
          <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Real Skin Stories</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">Clinical Results from Real Buyers</h2>
          <p class="text-xs sm:text-sm text-stone-500">Verified reviews cross-referenced by skin type and undertone profile.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
          <div
            v-for="(rev, idx) in reviews"
            :key="idx"
            class="bg-white rounded-3xl p-6 border border-stone-200/80 shadow-soft flex flex-col justify-between space-y-4"
          >
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1 text-amber-400">
                  <Star v-for="n in 5" :key="n" class="w-3.5 h-3.5 fill-current" />
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                  {{ rev.badge }}
                </span>
              </div>
              <h3 class="font-serif font-bold text-stone-900 text-sm sm:text-base leading-snug">"{{ rev.title }}"</h3>
              <p class="text-xs text-stone-600 leading-relaxed">{{ rev.body }}</p>
            </div>

            <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
              <div>
                <p class="font-bold text-stone-900">{{ rev.author }}</p>
                <p class="text-[10px] text-stone-400 mt-0.5">{{ rev.skinType }}</p>
              </div>
              <span class="text-stone-300 text-lg font-serif">"</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         NEWSLETTER / VIP ACCESS
    ════════════════════════════════════════════════════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 w-full">
      <div class="bg-stone-900 rounded-3xl p-6 sm:p-10 lg:p-12 flex flex-col sm:flex-row items-center justify-between gap-6 sm:gap-8 text-white shadow-2xl">
        <div class="text-center sm:text-left space-y-2">
          <span class="text-[10px] font-bold uppercase tracking-widest text-brand-300">VIP Access</span>
          <h3 class="font-serif text-2xl sm:text-3xl font-bold">Join the BeautyVault Circle.</h3>
          <p class="text-xs sm:text-sm text-stone-400 max-w-md">Get early access to new shade launches, clinical ingredient breakdowns, and exclusive member-only deals.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          <input
            type="email"
            placeholder="your@email.com"
            class="flex-1 sm:w-64 px-5 py-3 rounded-full bg-white/10 border border-white/20 text-white placeholder-stone-500 text-xs sm:text-sm focus:outline-none focus:border-brand-400 backdrop-blur-sm"
          />
          <button class="px-6 py-3 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs sm:text-sm transition-all hover:scale-105 shadow-glow whitespace-nowrap">
            Subscribe Free
          </button>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import { useWishlistStore } from '../stores/wishlist';
import { Sparkles, Palette, ArrowRight, Star, Heart } from 'lucide-vue-next';

const bestsellers = ref([]);
const loading = ref(true);
const wishlistStore = useWishlistStore();

// Live dynamic metrics fetched from backend metadata API
const storeStats = ref({
  inclusive_undertones: '50+',
  fifo_freshness: '100%',
  comedogenic_score: '0/5',
  cruelty_free_percentage: '100%',
  guarantee_days: '30-Day',
  verified_reviews: '18.4K+',
});

// Customer avatar social proof (4 distinct working portraits)
const customerAvatars = [
  'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
  'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80',
  'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
  'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80'
];

const marqueeItems = [
  'Fenty Beauty', 'Rare Beauty', 'The Ordinary', 'Paula\'s Choice',
  'CeraVe', 'Charlotte Tilbury', 'La Roche-Posay', 'COSRX', 'Rhode Skin', 'Summer Fridays',
];

// Distinct, verified, accurate cosmetic photos for every category (no duplicate photos)
const categories = [
  {
    name: 'Complexion & Foundation',
    slug: 'complexion',
    tag: '50+ Shades',
    desc: 'Climate-adaptive coverage',
    image: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?auto=format&fit=crop&w=600&q=80'
  },
  {
    name: 'Clinical Skincare',
    slug: 'skincare',
    tag: 'Actives',
    desc: 'Concentrated barrier formulas',
    image: 'https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=600&q=80'
  },
  {
    name: 'Cheeks & Liquid Glow',
    slug: 'cheeks-glow',
    tag: 'Weightless',
    desc: 'Longwear liquid pigment',
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=600&q=80'
  },
  {
    name: 'Mineral Sunscreen SPF',
    slug: 'sun-protection',
    tag: 'SPF 46–50+',
    desc: 'Zero white cast UV shield',
    image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=600&q=80'
  },
];

const features = [
  { icon: '🎨', label: 'Undertone Matrix', desc: 'Cool, warm, olive & neutral depth matching' },
  { icon: '🧬', label: 'INCI Analysis', desc: 'Comedogenic score per ingredient' },
  { icon: '📦', label: 'FIFO Freshness', desc: 'Real-time batch expiry tracking' },
  { icon: '✨', label: '98% Match Rate', desc: 'Verified against 18k+ skin profiles' },
];

const reviews = [
  {
    rating: '4.95',
    title: 'Finally a true neutral that doesn\'t oxidize orange!',
    body: 'I have combination skin with warm undertones and always struggled with foundations going pumpkin. Shade 230 was my exact match from the undertone matrix. Lasts 12 hours through heat without caking.',
    author: 'Sophia Chen',
    skinType: 'Combination · Warm Undertone',
    badge: 'Verified Purchase',
  },
  {
    rating: '4.90',
    title: 'Holy grail for sebaceous filaments and hormonal redness',
    body: 'Within 3 weeks of daily morning use, my pores appear visibly smaller and my skin barrier feels substantially stronger. At this price point, nothing competes with The Ordinary Niacinamide.',
    author: 'Maya Alvarez',
    skinType: 'Oily · Acne Prone',
    badge: 'Verified 60ml',
  },
  {
    rating: '5.00',
    title: 'SPF that doesn\'t feel like a brick on oily skin!',
    body: 'The La Roche-Posay Anthelios has completely changed my morning routine. Zero white cast, no pilling under makeup, and my skin stays matte through a full workday.',
    author: 'Aisha Rahman',
    skinType: 'Oily · Sensitive',
    badge: 'Verified SPF Purchase',
  },
];

onMounted(async () => {
  try {
    // 1. Fetch live store metrics (undertones, FIFO freshness, review totals)
    const metaRes = await api.get('/metadata');
    if (metaRes.data?.stats) {
      storeStats.value = metaRes.data.stats;
    }

    // 2. Fetch bestsellers
    const res = await api.get('/products', { params: { per_page: 4, sort: 'bestseller' } });
    bestsellers.value = res.data.data;
  } catch (err) {
    console.error('Failed to load home data:', err);
  } finally {
    loading.value = false;
  }
});

const handleImgError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80';
};
</script>

<style scoped>
@keyframes marquee {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}
.animate-marquee {
  animation: marquee 28s linear infinite;
}
</style>
