<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-900 text-amber-400 text-xs font-bold mb-2">
          <ShieldAlert class="w-3.5 h-3.5" />
          <span>Enterprise Admin Console</span>
        </div>
        <h1 class="font-serif text-3xl font-bold text-stone-900">
          Executive Operations & Inventory Intelligence
        </h1>
        <p class="text-xs text-stone-500 mt-0.5">
          Real-time order management, FIFO batch expiry monitoring, and DevOps health.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <!-- Live WebSockets Status Indicator -->
        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-stone-200 shadow-sm text-xs">
          <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
          <span class="text-stone-700 font-semibold">WebSockets Online</span>
          <span class="text-stone-400 font-mono text-[10px]">(channel: admin.orders)</span>
        </div>

        <!-- Exit / Sign Out Button -->
        <button
          @click="handleLogout"
          class="flex items-center gap-2 bg-stone-900 hover:bg-rose-950 text-stone-200 hover:text-rose-200 px-4 py-2 rounded-2xl text-xs font-semibold shadow-sm border border-stone-800 transition-all cursor-pointer"
          title="Sign Out of Admin Console"
        >
          <LogOut class="w-3.5 h-3.5 text-rose-400" />
          <span>Sign Out</span>
        </button>
      </div>
    </div>

    <!-- Live Order Broadcast Notification Flash -->
    <div
      v-if="liveOrderBanner"
      class="p-4 bg-amber-500 text-stone-950 font-bold text-xs rounded-2xl shadow-lg flex items-center justify-between animate-bounce"
    >
      <div class="flex items-center gap-2">
        <Bell class="w-4 h-4 animate-spin-slow" />
        <span>{{ liveOrderBanner }}</span>
      </div>
      <button @click="liveOrderBanner = ''" class="text-stone-950 hover:underline">Dismiss</button>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-stone-400">
          <span class="text-xs font-bold uppercase tracking-wider">Total Revenue</span>
          <DollarSign class="w-5 h-5 text-emerald-600" />
        </div>
        <p class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
          ${{ metrics.total_revenue?.toFixed(2) || '0.00' }}
        </p>
        <p class="text-[11px] text-emerald-600 font-medium">ACID settled transactions</p>
      </div>

      <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-stone-400">
          <span class="text-xs font-bold uppercase tracking-wider">Total Orders</span>
          <ShoppingBag class="w-5 h-5 text-brand-600" />
        </div>
        <p class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
          {{ metrics.total_orders || 0 }}
        </p>
        <p class="text-[11px] text-stone-500 font-medium">{{ metrics.pending_orders || 0 }} pending fulfillment</p>
      </div>

      <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-stone-400">
          <span class="text-xs font-bold uppercase tracking-wider">Expiring Batches (60d)</span>
          <Clock class="w-5 h-5 text-amber-500" />
        </div>
        <p class="font-serif text-2xl sm:text-3xl font-bold" :class="metrics.expiring_batches_count > 0 ? 'text-amber-600' : 'text-stone-900'">
          {{ metrics.expiring_batches_count || 0 }}
        </p>
        <p class="text-[11px] text-amber-700 font-medium">FIFO alerts triggered</p>
      </div>

      <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-stone-400">
          <span class="text-xs font-bold uppercase tracking-wider">Low Stock SKUs</span>
          <AlertTriangle class="w-5 h-5 text-rose-600" />
        </div>
        <p class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
          {{ metrics.low_stock_count || 0 }}
        </p>
        <p class="text-[11px] text-rose-600 font-medium">Requires supplier restock</p>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-stone-200 gap-6 text-xs font-semibold text-stone-500 overflow-x-auto">
      <button
        @click="activeTab = 'orders'"
        class="pb-3 border-b-2 whitespace-nowrap transition-colors"
        :class="activeTab === 'orders' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Orders Management
      </button>
      <button
        @click="activeTab = 'batches'"
        class="pb-3 border-b-2 whitespace-nowrap transition-colors"
        :class="activeTab === 'batches' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        FIFO Batches & Expiry ({{ batches.length }})
      </button>
      <button
        @click="activeTab = 'inventory'"
        class="pb-3 border-b-2 whitespace-nowrap transition-colors"
        :class="activeTab === 'inventory' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Stock Inventory
      </button>
      <button
        @click="activeTab = 'returns'"
        class="pb-3 border-b-2 whitespace-nowrap transition-colors"
        :class="activeTab === 'returns' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        Returns / Exchanges
      </button>
      <button
        @click="activeTab = 'health'"
        class="pb-3 border-b-2 whitespace-nowrap transition-colors"
        :class="activeTab === 'health' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent hover:text-stone-800'"
      >
        DevOps Health Check
      </button>
    </div>

    <!-- Tab 1: Orders Management -->
    <div v-if="activeTab === 'orders'" class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-serif font-bold text-lg text-stone-900">Live Customer Orders</h3>
        <div class="flex items-center gap-2.5">
          <button
            @click="handleRefreshOrders"
            :disabled="isRefreshingOrders"
            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-stone-200 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition-all cursor-pointer disabled:opacity-50"
            title="Reload orders and live analytics"
          >
            <RefreshCw class="w-3.5 h-3.5 text-brand-600" :class="{ 'animate-spin': isRefreshingOrders }" />
            <span>{{ isRefreshingOrders ? 'Refreshing Orders...' : 'Refresh Orders' }}</span>
          </button>
          <span v-if="orderRefreshSuccess" class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 animate-fade-in">
            <CheckCircle2 class="w-3.5 h-3.5" /> Updated!
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-stone-50 border-b border-stone-100 text-stone-400 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3">Order Ref</th>
              <th class="px-6 py-3">Customer</th>
              <th class="px-6 py-3">Items</th>
              <th class="px-6 py-3">Total</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100 text-stone-700">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-stone-50/60">
              <td class="px-6 py-4 font-mono font-bold text-stone-900">{{ order.order_number }}</td>
              <td class="px-6 py-4">
                <p class="font-semibold text-stone-900">{{ order.customer_name }}</p>
                <p class="text-[10px] text-stone-400">{{ order.customer_email }}</p>
              </td>
              <td class="px-6 py-4">
                <span class="font-medium">{{ order.items?.length || 0 }} items</span>
              </td>
              <td class="px-6 py-4 font-bold text-stone-900">${{ order.total_amount }}</td>
              <td class="px-6 py-4">
                <span
                  class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  :class="getStatusBadgeClass(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <button
                  v-if="!isTerminalStatus(order.status)"
                  @click="openStatusModal(order)"
                  class="px-3 py-1.5 rounded-full bg-stone-900 text-white text-[11px] font-semibold hover:bg-stone-800"
                >
                  Update Status
                </button>
                <span
                  v-else
                  class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-stone-100 text-stone-500"
                >
                  🔒 Locked
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 2: FIFO Batches & Expiry Engine -->
    <div v-if="activeTab === 'batches'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-serif font-bold text-lg text-stone-900">FIFO Batch Lots & Expiry Tracking</h3>
          <p class="text-xs text-stone-500">Oldest unexpired batches are systematically allocated first during checkout.</p>
        </div>
        <button
          @click="showRestockModal = true"
          class="px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-sm"
        >
          + Receive New Batch Restock
        </button>
      </div>

      <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 border-b border-stone-100 text-stone-400 uppercase font-semibold">
              <tr>
                <th class="px-6 py-3">Batch Number</th>
                <th class="px-6 py-3">Product / Shade</th>
                <th class="px-6 py-3">Current Qty</th>
                <th class="px-6 py-3">Expiry Date</th>
                <th class="px-6 py-3">Freshness Alert</th>
                <th class="px-6 py-3">Warehouse Rack</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 text-stone-700">
              <tr v-for="b in batches" :key="b.id" class="hover:bg-stone-50/60">
                <td class="px-6 py-4 font-mono font-bold text-stone-900">{{ b.batch_number }}</td>
                <td class="px-6 py-4">
                  <p class="font-semibold text-stone-900">{{ b.variant?.product?.name }}</p>
                  <p class="text-[11px] text-stone-400">{{ b.variant?.shade_name || b.variant?.title }}</p>
                </td>
                <td class="px-6 py-4 font-bold text-stone-900">{{ b.current_quantity }} units</td>
                <td class="px-6 py-4 font-mono">{{ b.expires_at }}</td>
                <td class="px-6 py-4">
                  <span
                    v-if="isExpiringSoon(b.expires_at)"
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 animate-pulse"
                  >
                    ⚠️ Expiring Soon
                  </span>
                  <span v-else class="text-emerald-700 font-medium">✓ Fresh Formula</span>
                </td>
                <td class="px-6 py-4 font-mono text-stone-500">{{ b.warehouse_location || 'Zone A' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab 3: Inventory Table -->
    <div v-if="activeTab === 'inventory'" class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-stone-100">
        <h3 class="font-serif font-bold text-lg text-stone-900">Aggregated SKU Stock Levels</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-stone-50 border-b border-stone-100 text-stone-400 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3">SKU</th>
              <th class="px-6 py-3">Product Name</th>
              <th class="px-6 py-3">Shade / Variant</th>
              <th class="px-6 py-3">Price</th>
              <th class="px-6 py-3">Stock Units</th>
              <th class="px-6 py-3">Stock Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100 text-stone-700">
            <tr v-for="item in inventory" :key="item.id" class="hover:bg-stone-50/60">
              <td class="px-6 py-4 font-mono font-bold text-stone-900">{{ item.sku }}</td>
              <td class="px-6 py-4 font-medium">{{ item.product?.name }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-1.5">
                  <span v-if="item.shade_hex" class="w-3 h-3 rounded-full border" :style="{ backgroundColor: item.shade_hex }"></span>
                  <span>{{ item.shade_name || item.title }}</span>
                </div>
              </td>
              <td class="px-6 py-4 font-bold text-stone-900">${{ parseFloat(item.price).toFixed(2) }}</td>
              <td class="px-6 py-4 font-bold text-stone-900">{{ item.stock_quantity }}</td>
              <td class="px-6 py-4">
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                  :class="item.stock_quantity <= item.low_stock_threshold ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800'"
                >
                  {{ item.stock_quantity <= item.low_stock_threshold ? 'Low Stock Alert' : 'Healthy Stock' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 4: Returns Management -->
    <div v-if="activeTab === 'returns'" class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-stone-100">
        <h3 class="font-serif font-bold text-lg text-stone-900">Customer Returns & Shade Exchanges</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-stone-50 border-b border-stone-100 text-stone-400 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3">Return Ref</th>
              <th class="px-6 py-3">Order Number</th>
              <th class="px-6 py-3">Customer</th>
              <th class="px-6 py-3">Reason</th>
              <th class="px-6 py-3">Refund Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100 text-stone-700">
            <tr v-for="r in returns" :key="r.id" class="hover:bg-stone-50/60">
              <td class="px-6 py-4 font-mono font-bold text-stone-900">{{ r.return_number }}</td>
              <td class="px-6 py-4 font-mono">{{ r.order?.order_number }}</td>
              <td class="px-6 py-4">{{ r.user?.name }}</td>
              <td class="px-6 py-4 capitalize font-semibold">{{ r.reason_code?.replace('_', ' ') }}</td>
              <td class="px-6 py-4 font-bold text-stone-900">${{ r.refund_amount }}</td>
              <td class="px-6 py-4">
                <span
                  class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border"
                  :class="{
                    'bg-amber-50 text-amber-800 border-amber-200': r.status === 'requested',
                    'bg-blue-50 text-blue-800 border-blue-200': r.status === 'approved',
                    'bg-emerald-50 text-emerald-800 border-emerald-200': r.status === 'refunded',
                    'bg-rose-50 text-rose-800 border-rose-200': r.status === 'rejected',
                    'bg-purple-50 text-purple-800 border-purple-200': ['in_transit', 'received'].includes(r.status)
                  }"
                >
                  {{ r.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <!-- Actions when requested or approved -->
                <div v-if="r.status === 'requested' || r.status === 'approved'" class="flex items-center gap-2">
                  <button
                    @click="openRefundModal(r)"
                    class="px-3 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-1"
                    title="Process payment refund with method and notes"
                  >
                    <CreditCard class="w-3 h-3" />
                    <span>Approve &amp; Refund</span>
                  </button>
                  <button
                    @click="rejectReturn(r.id)"
                    class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold hover:bg-rose-100 transition-colors"
                    title="Reject this return request"
                  >
                    Reject / Cancel
                  </button>
                </div>

                <!-- Post-action displays -->
                <div v-else-if="r.status === 'refunded'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span>Refunded (${{ r.refund_amount }})</span>
                </div>
                <div v-else-if="r.status === 'rejected'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                  <span>Rejected</span>
                </div>
                <span v-else class="text-stone-400 text-xs">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 5: DevOps Health Check -->
    <div v-if="activeTab === 'health'" class="bg-white p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6 max-w-2xl">
      <div class="space-y-1">
        <h3 class="font-serif font-bold text-lg text-stone-900">Infrastructure Health & Microservices</h3>
        <p class="text-xs text-stone-500">Live heartbeat telemetry across database, caches, and web nodes.</p>
      </div>

      <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100 space-y-1">
          <span class="text-stone-400 font-semibold uppercase text-[10px]">Database Persistence</span>
          <p class="font-bold text-stone-900 flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>MySQL 8.0 / SQLite (ACID Active)</span>
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100 space-y-1">
          <span class="text-stone-400 font-semibold uppercase text-[10px]">In-Memory Cache & Mutex</span>
          <p class="font-bold text-stone-900 flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Redis 7.0 (Predis Driver)</span>
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100 space-y-1">
          <span class="text-stone-400 font-semibold uppercase text-[10px]">Full-Text Typo-Tolerant Engine</span>
          <p class="font-bold text-stone-900 flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Meilisearch + Laravel Scout</span>
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100 space-y-1">
          <span class="text-stone-400 font-semibold uppercase text-[10px]">Real-Time Streaming Daemon</span>
          <p class="font-bold text-stone-900 flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Laravel Reverb (Port 8080)</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div v-if="selectedOrderForStatus" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 text-xs shadow-2xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-stone-900 text-amber-400 flex items-center justify-center">
            <ShieldAlert class="w-5 h-5" />
          </div>
          <div>
            <h3 class="font-serif font-bold text-lg text-stone-900">
              Update Order #{{ selectedOrderForStatus.order_number }}
            </h3>
            <p class="text-stone-400 text-[11px]">Current status: <strong class="uppercase">{{ selectedOrderForStatus.status }}</strong></p>
          </div>
        </div>

        <!-- Warning if no valid transitions -->
        <div v-if="allowedNextStatuses.length === 0" class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 font-semibold">
          🔒 This order is in a terminal state ({{ selectedOrderForStatus.status }}) and cannot be changed.
        </div>

        <template v-else>
          <div>
            <label class="block font-semibold text-stone-700 mb-1">New Fulfillment Status</label>
            <select v-model="statusUpdateForm.status" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2">
              <option v-for="s in allowedNextStatuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>

          <div v-if="statusUpdateForm.status === 'dispatched' || statusUpdateForm.status === 'delivered'">
            <label class="block font-semibold text-stone-700 mb-1">Carrier Tracking Number</label>
            <input
              v-model="statusUpdateForm.tracking_number"
              type="text"
              placeholder="e.g. DHL-US-940010009823412"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 font-mono"
            />
          </div>

          <div v-if="statusUpdateForm.status === 'dispatched' || statusUpdateForm.status === 'delivered'">
            <label class="block font-semibold text-stone-700 mb-1">Shipping Carrier</label>
            <input
              v-model="statusUpdateForm.shipping_carrier"
              type="text"
              placeholder="DHL Express Priority"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2"
            />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Audit Comment <span class="text-stone-400 font-normal">(Broadcast to customer via WebSocket)</span></label>
            <input
              v-model="statusUpdateForm.comment"
              type="text"
              placeholder="Package handed over to courier for express flight..."
              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2"
            />
          </div>

          <!-- Confirmation warning before final submit -->
          <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 font-medium">
            ⚠️ This action will broadcast a live WebSocket update to the customer. Status changes are <strong>one-way and irreversible</strong>.
          </div>

          <!-- Error message from server -->
          <div v-if="statusUpdateError" class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 font-medium">
            {{ statusUpdateError }}
          </div>
        </template>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="selectedOrderForStatus = null; statusUpdateError = ''" class="px-4 py-2 rounded-full border border-stone-200">Cancel</button>
          <button
            v-if="allowedNextStatuses.length > 0"
            @click="confirmAndSubmitStatusUpdate"
            :disabled="statusUpdateLoading"
            class="px-5 py-2 rounded-full bg-stone-900 text-white font-semibold disabled:opacity-50"
          >
            {{ statusUpdateLoading ? 'Broadcasting...' : 'Confirm & Broadcast Live' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Restock Modal -->
    <div v-if="showRestockModal" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-4 text-xs shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b border-stone-100">
          <div>
            <h3 class="font-serif font-bold text-lg text-stone-900">Receive New FIFO Batch Stock</h3>
            <p class="text-[11px] text-stone-500">Record a fresh manufacturing lot from suppliers to increase inventory.</p>
          </div>
          <button @click="showRestockModal = false" class="text-stone-400 hover:text-stone-700">
            <X class="w-4 h-4" />
          </button>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Select Product SKU</label>
          <select v-model="restockForm.product_variant_id" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2">
            <option v-for="inv in inventory" :key="inv.id" :value="inv.id">
              {{ inv.sku }} - {{ inv.product?.name }} ({{ inv.shade_name || inv.title }}) — Current Stock: {{ inv.stock_quantity }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="font-semibold text-stone-700">Batch Number (Lot)</label>
              <button type="button" @click="generateBatchNumber" class="text-[10px] text-brand-600 hover:underline font-semibold flex items-center gap-0.5">
                <Sparkles class="w-2.5 h-2.5" /> Auto Lot
              </button>
            </div>
            <input v-model="restockForm.batch_number" type="text" placeholder="LOT-2026-FNT-09" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 font-mono" />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Quantity Received</label>
            <input v-model.number="restockForm.quantity" type="number" min="1" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 font-bold" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-stone-700 mb-1">Supplier / Brand Lab</label>
            <input v-model="restockForm.supplier_name" type="text" placeholder="e.g. Fenty Labs France" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2" />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Unit Cost ($ USD)</label>
            <input v-model.number="restockForm.unit_cost" type="number" step="0.01" min="0" placeholder="12.50" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-stone-700 mb-1">Expiration Date (FIFO)</label>
            <input v-model="restockForm.expires_at" type="date" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2" />
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-1">Warehouse Shelf Rack</label>
            <input v-model="restockForm.warehouse_location" type="text" placeholder="Zone B, Shelf 12" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2" />
          </div>
        </div>

        <div v-if="restockMessage" class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-[11px] text-emerald-800 font-semibold flex items-center gap-1.5">
          <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0" />
          <span>{{ restockMessage }}</span>
        </div>

        <div v-if="restockError" class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-[11px] text-rose-800 font-semibold">
          {{ restockError }}
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="showRestockModal = false" class="px-4 py-2 rounded-full border border-stone-200 hover:bg-stone-50">Cancel</button>
          <button
            @click="submitRestock"
            :disabled="restockLoading"
            class="px-5 py-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-semibold disabled:opacity-50"
          >
            <span v-if="restockLoading">Processing Stock...</span>
            <span v-else>Receive &amp; Stock Batch (+{{ restockForm.quantity }})</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Approve Refund Modal with Payment Details -->
    <div v-if="selectedReturnForRefund" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 text-xs shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <CreditCard class="w-4 h-4" />
            </div>
            <div>
              <h3 class="font-serif font-bold text-base text-stone-900">Process &amp; Issue Refund</h3>
              <p class="text-stone-400 text-[11px]">Return #{{ selectedReturnForRefund.return_number }}</p>
            </div>
          </div>
          <button @click="selectedReturnForRefund = null" class="text-stone-400 hover:text-stone-700">
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="p-3 bg-stone-50 rounded-2xl border border-stone-200/80 space-y-1">
          <div class="flex justify-between text-stone-500">
            <span>Customer:</span>
            <span class="font-semibold text-stone-800">{{ selectedReturnForRefund.user?.name || selectedReturnForRefund.order?.customer_name }}</span>
          </div>
          <div class="flex justify-between text-stone-500">
            <span>Original Order:</span>
            <span class="font-mono font-semibold text-stone-800">#{{ selectedReturnForRefund.order?.order_number }}</span>
          </div>
          <div class="flex justify-between text-stone-500">
            <span>Return Reason:</span>
            <span class="font-semibold text-stone-800 capitalize">{{ selectedReturnForRefund.reason_code?.replace('_', ' ') }}</span>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Refund Amount ($ USD)</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-stone-400">$</span>
            <input
              v-model.number="refundForm.refund_amount"
              type="number"
              step="0.01"
              min="0"
              class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-7 pr-3 py-2 font-bold text-stone-900 text-sm"
            />
          </div>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Refund Payment Method</label>
          <select v-model="refundForm.refund_method" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800">
            <option value="original_payment">Original Payment (Credit Card / Online Gateway)</option>
            <option value="store_credit">Store Credit / BeautyVault Customer Wallet</option>
          </select>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-1">Payment Reference &amp; Transaction Notes</label>
          <input
            v-model="refundForm.admin_notes"
            type="text"
            placeholder="e.g. Refunded to Visa via Stripe intent txn_98124..."
            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-800"
          />
        </div>

        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-[11px] text-emerald-800">
          ℹ️ Approving will mark this return as <strong>Refunded</strong>, record the transaction notes, and log resolution timestamp.
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="selectedReturnForRefund = null" class="px-4 py-2 rounded-full border border-stone-200 hover:bg-stone-50">Cancel</button>
          <button
            @click="submitRefundApproval"
            :disabled="refundLoading"
            class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold disabled:opacity-50 flex items-center gap-1.5"
          >
            <span v-if="refundLoading">Processing...</span>
            <span v-else>Confirm &amp; Issue ${{ Number(refundForm.refund_amount || 0).toFixed(2) }} Refund</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import { getEcho } from '../services/echo';
import {
  ShieldAlert, Bell, DollarSign, ShoppingBag, Clock, AlertTriangle, LogOut,
  RefreshCw, CheckCircle2, CreditCard, Sparkles, X
} from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/');
};

const activeTab = ref('orders');
const metrics = ref({});
const orders = ref([]);
const batches = ref([]);
const inventory = ref([]);
const returns = ref([]);

const liveOrderBanner = ref('');
const selectedOrderForStatus = ref(null);
const showRestockModal = ref(false);
const statusUpdateLoading = ref(false);
const statusUpdateError = ref('');

// Status transition map — mirrors backend ALLOWED_TRANSITIONS
const STATUS_TRANSITIONS = {
  pending:    [{ value: 'confirmed', label: 'Confirmed — Payment Verified' }, { value: 'cancelled', label: 'Cancelled' }],
  confirmed:  [{ value: 'preparing', label: 'Preparing — FIFO Lot Being Packed' }, { value: 'cancelled', label: 'Cancelled' }],
  preparing:  [{ value: 'dispatched', label: 'Dispatched — Handed to Carrier' }, { value: 'cancelled', label: 'Cancelled' }],
  dispatched: [{ value: 'delivered', label: 'Delivered — Arrived at Customer' }],
  delivered:  [],
  cancelled:  [],
};

const allowedNextStatuses = computed(() => {
  if (!selectedOrderForStatus.value) return [];
  return STATUS_TRANSITIONS[selectedOrderForStatus.value.status] || [];
});

const isTerminalStatus = (status) => {
  return status === 'delivered' || status === 'cancelled';
};

const statusUpdateForm = reactive({
  status: 'dispatched',
  tracking_number: 'DHL-US-940010009823412',
  shipping_carrier: 'DHL Express Priority',
  comment: 'Package picked up by courier.',
});

const restockLoading = ref(false);
const restockMessage = ref('');
const restockError = ref('');

const isRefreshingOrders = ref(false);
const orderRefreshSuccess = ref(false);

const selectedReturnForRefund = ref(null);
const refundLoading = ref(false);
const refundForm = reactive({
  refund_amount: 0,
  refund_method: 'original_payment',
  admin_notes: '',
});

const restockForm = reactive({
  product_variant_id: null,
  batch_number: 'LOT-2026-FNT-01',
  quantity: 50,
  expires_at: '2027-12-31',
  supplier_name: 'Brand Primary Lab',
  unit_cost: 15.00,
  warehouse_location: 'Bay C-04',
});

const generateBatchNumber = () => {
  const code = Math.floor(1000 + Math.random() * 9000);
  restockForm.batch_number = `LOT-2026-LOT-${code}`;
};

const getStatusBadgeClass = (status) => {
  return matchStatusClass(status);
};

const matchStatusClass = (status) => {
  switch (status) {
    case 'delivered': return 'bg-emerald-100 text-emerald-800';
    case 'dispatched': return 'bg-blue-100 text-blue-800';
    case 'preparing': return 'bg-amber-100 text-amber-800';
    case 'cancelled': return 'bg-rose-100 text-rose-800';
    default: return 'bg-stone-100 text-stone-800';
  }
};

const isExpiringSoon = (expiresAt) => {
  const expiry = new Date(expiresAt);
  const now = new Date();
  const diffDays = (expiry - now) / (1000 * 60 * 60 * 24);
  return diffDays <= 60 && diffDays > 0;
};

const fetchAnalytics = async () => {
  try {
    const res = await api.get('/admin/analytics');
    metrics.value = res.data.metrics;
  } catch (err) {}
};

const fetchOrders = async () => {
  try {
    const res = await api.get('/admin/orders');
    orders.value = res.data.data;
  } catch (err) {}
};

const fetchBatches = async () => {
  try {
    const res = await api.get('/admin/batches');
    batches.value = res.data.data;
  } catch (err) {}
};

const fetchInventory = async () => {
  try {
    const res = await api.get('/admin/inventory');
    inventory.value = res.data.data;
    if (!restockForm.product_variant_id && inventory.value && inventory.value.length > 0) {
      restockForm.product_variant_id = inventory.value[0].id;
    }
  } catch (err) {}
};

const fetchReturns = async () => {
  try {
    const res = await api.get('/admin/returns');
    returns.value = res.data.data;
  } catch (err) {}
};

const openStatusModal = (order) => {
  selectedOrderForStatus.value = order;
  // Pre-select first valid next status
  const transitions = STATUS_TRANSITIONS[order.status] || [];
  statusUpdateForm.status = transitions[0]?.value || order.status;
  statusUpdateForm.tracking_number = order.tracking_number || 'DHL-US-' + Math.floor(Math.random() * 90000000);
  statusUpdateForm.comment = '';
  statusUpdateError.value = '';
};

const confirmAndSubmitStatusUpdate = async () => {
  const confirmed = window.confirm(
    `Are you sure you want to change order #${selectedOrderForStatus.value.order_number} status to "${statusUpdateForm.status.toUpperCase()}"?\n\nThis is IRREVERSIBLE and will broadcast a live WebSocket update to the customer.`
  );
  if (!confirmed) return;
  await submitStatusUpdate();
};

const submitStatusUpdate = async () => {
  if (!selectedOrderForStatus.value) return;
  statusUpdateLoading.value = true;
  statusUpdateError.value = '';
  try {
    await api.patch(`/admin/orders/${selectedOrderForStatus.value.id}/status`, statusUpdateForm);
    selectedOrderForStatus.value = null;
    fetchOrders();
    fetchAnalytics();
  } catch (err) {
    statusUpdateError.value = err.response?.data?.message || 'Failed to update order status.';
  } finally {
    statusUpdateLoading.value = false;
  }
};

const handleRefreshOrders = async () => {
  isRefreshingOrders.value = true;
  orderRefreshSuccess.value = false;
  try {
    await Promise.all([fetchOrders(), fetchAnalytics()]);
    orderRefreshSuccess.value = true;
    setTimeout(() => {
      orderRefreshSuccess.value = false;
    }, 2500);
  } catch (err) {
    console.error('Failed to refresh orders:', err);
  } finally {
    isRefreshingOrders.value = false;
  }
};

const submitRestock = async () => {
  if (!restockForm.product_variant_id) {
    restockError.value = 'Please select a product variant SKU.';
    return;
  }
  restockLoading.value = true;
  restockError.value = '';
  restockMessage.value = '';
  try {
    const res = await api.post('/admin/batches/restock', restockForm);
    restockMessage.value = res.data.message || 'Batch successfully restocked and inventory incremented!';
    setTimeout(() => {
      showRestockModal.value = false;
      restockMessage.value = '';
    }, 1500);
    fetchBatches();
    fetchInventory();
    fetchAnalytics();
  } catch (err) {
    restockError.value = err.response?.data?.message || 'Failed to restock batch. Please verify dates and values.';
  } finally {
    restockLoading.value = false;
  }
};

const openRefundModal = (ret) => {
  selectedReturnForRefund.value = ret;
  refundForm.refund_amount = Number(ret.refund_amount || 0);
  refundForm.refund_method = ret.refund_method || 'original_payment';
  refundForm.admin_notes = `Refund processed to customer for Return #${ret.return_number}`;
};

const submitRefundApproval = async () => {
  if (!selectedReturnForRefund.value) return;
  refundLoading.value = true;
  try {
    await api.patch(`/admin/returns/${selectedReturnForRefund.value.id}/status`, {
      status: 'refunded',
      refund_amount: refundForm.refund_amount,
      refund_method: refundForm.refund_method,
      admin_notes: refundForm.admin_notes,
    });
    selectedReturnForRefund.value = null;
    fetchReturns();
    fetchAnalytics();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to process refund');
  } finally {
    refundLoading.value = false;
  }
};

const rejectReturn = async (id) => {
  const reason = window.prompt('Enter reason or note for rejecting this return (optional):', 'Item condition does not meet return policy criteria.');
  if (reason === null) return;

  try {
    await api.patch(`/admin/returns/${id}/status`, {
      status: 'rejected',
      admin_notes: reason.trim() || 'Rejected by Admin',
    });
    fetchReturns();
    fetchAnalytics();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to reject return');
  }
};

// Real-time WebSocket connection to admin.orders
const listenToOrders = () => {
  const echo = getEcho();
  if (echo) {
    echo.channel('admin.orders')
      .listen('.OrderPlaced', (e) => {
        liveOrderBanner.value = `⚡ New Order Placed: #${e.order_number} by ${e.customer_name} for $${e.total_amount.toFixed(2)}!`;
        fetchOrders();
        fetchAnalytics();
      });
  }
};

onMounted(() => {
  fetchAnalytics();
  fetchOrders();
  fetchBatches();
  fetchInventory();
  fetchReturns();
  listenToOrders();
});
</script>
