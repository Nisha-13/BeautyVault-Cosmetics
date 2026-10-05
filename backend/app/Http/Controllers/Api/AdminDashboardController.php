<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderStatusUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct(protected InventoryService $inventoryService)
    {
    }

    /**
     * Executive Overview & Real-Time Analytics
     */
    public function analytics(): JsonResponse
    {
        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'preparing'])->count();

        // Expiring batches in next 60 days
        $expiringBatchesCount = ProductBatch::expiringSoon(60)->count();

        // Low stock SKUs
        $lowStockCount = ProductVariant::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        // Top 5 Bestselling Products
        $topProducts = Product::with('brand')
            ->orderBy('reviews_count', 'desc')
            ->take(5)
            ->get(['id', 'name', 'brand_id', 'base_price', 'rating_avg', 'reviews_count', 'thumbnail_url']);

        // Recent 5 Orders
        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        // Expiring Batches Preview
        $expiringBatches = ProductBatch::with(['variant.product.brand'])
            ->expiringSoon(60)
            ->orderBy('expires_at', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'revenue' => round($totalRevenue, 2),
            'orders_count' => $totalOrders,
            'metrics' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'expiring_batches_count' => $expiringBatchesCount,
                'low_stock_count' => $lowStockCount,
            ],
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
            'expiring_batches' => $expiringBatches,
        ]);
    }

    /**
     * Orders list for management
     */
    public function orders(Request $request): JsonResponse
    {
        $query = Order::with(['items.product', 'items.variant', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Update order status & trigger real-time WebSocket broadcast
     */
    /**
     * One-way status transition map. Key = current status, value = allowed next statuses.
     */
    private const ALLOWED_TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['preparing', 'cancelled'],
        'preparing'  => ['dispatched', 'cancelled'],
        'dispatched' => ['delivered'],
        'delivered'  => [],   // Terminal — cannot change
        'cancelled'  => [],   // Terminal — cannot change
    ];

    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,dispatched,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'shipping_carrier' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        // Enforce irreversible terminal states
        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? [];
        if (empty($allowed)) {
            return response()->json([
                'message' => "Order #{$order->order_number} is in a terminal state ('{$order->status}') and cannot be changed.",
            ], 422);
        }

        if (!in_array($validated['status'], $allowed)) {
            return response()->json([
                'message' => "Invalid transition: '{$order->status}' → '{$validated['status']}'. Allowed next statuses: " . implode(', ', $allowed),
            ], 422);
        }

        $order->status = $validated['status'];

        if (!empty($validated['tracking_number'])) {
            $order->tracking_number = $validated['tracking_number'];
        }
        if (!empty($validated['shipping_carrier'])) {
            $order->shipping_carrier = $validated['shipping_carrier'];
        }

        if ($validated['status'] === 'dispatched' && !$order->dispatched_at) {
            $order->dispatched_at = now();
        }
        if ($validated['status'] === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        // Audit state transition in history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'comment' => $validated['comment'] ?? "Status updated to " . ucfirst($validated['status']),
            'created_by_user_id' => $request->user()->id,
        ]);

        // Broadcast real-time WebSocket event
        try {
            broadcast(new OrderStatusUpdatedEvent($order, $validated['comment'] ?? null));
        } catch (\Throwable $e) {
            // gracefully catch if websocket server offline
        }

        return response()->json([
            'message' => "Order #{$order->order_number} status updated to " . ucfirst($order->status),
            'order' => $order->fresh(['statusHistories', 'items']),
        ]);
    }

    /**
     * Inventory list with low stock warnings
     */
    public function inventory(): JsonResponse
    {
        $variants = ProductVariant::with(['product.brand', 'fifoBatches'])
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);

        return response()->json($variants);
    }

    /**
     * Batches list with expiry countdown
     */
    public function batches(): JsonResponse
    {
        $batches = ProductBatch::with(['variant.product.brand'])
            ->orderBy('expires_at', 'asc')
            ->paginate(20);

        return response()->json($batches);
    }

    /**
     * Restock a variant with a new batch lot
     */
    public function restock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'batch_number' => 'required|string',
            'supplier_name' => 'nullable|string',
            'expires_at' => 'required|date|after:today',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'warehouse_location' => 'nullable|string',
        ]);

        $variant = ProductVariant::findOrFail($validated['product_variant_id']);

        $batch = $this->inventoryService->restockBatch($variant, $validated, $request->user());

        return response()->json([
            'message' => "Batch {$batch->batch_number} received and stocked ({$validated['quantity']} units added).",
            'batch' => $batch->load('variant'),
        ], 201);
    }

    /**
     * Returns management
     */
    public function returns(): JsonResponse
    {
        $returns = OrderReturn::with(['order', 'user', 'items.orderItem.product'])->latest()->paginate(15);
        return response()->json($returns);
    }

    public function updateReturnStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:requested,approved,rejected,in_transit,received,refunded',
            'admin_notes' => 'nullable|string',
            'refund_method' => 'nullable|in:original_payment,store_credit',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $returnOrder = OrderReturn::findOrFail($id);
        $returnOrder->status = $validated['status'];
        if (!empty($validated['admin_notes'])) {
            $returnOrder->admin_notes = $validated['admin_notes'];
        }
        if (!empty($validated['refund_method'])) {
            $returnOrder->refund_method = $validated['refund_method'];
        }
        if (isset($validated['refund_amount']) && $validated['refund_amount'] > 0) {
            $returnOrder->refund_amount = $validated['refund_amount'];
        }
        if (in_array($validated['status'], ['refunded', 'rejected'])) {
            $returnOrder->resolved_at = now();
        }
        $returnOrder->save();

        return response()->json([
            'message' => "Return #{$returnOrder->return_number} status updated to " . ucfirst($validated['status']),
            'return' => $returnOrder,
        ]);
    }

    /**
     * System Health Check for DevOps Monitoring
     */
    public function healthCheck(): JsonResponse
    {
        $dbStatus = 'OK';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        $cacheStatus = 'OK';
        try {
            Cache::put('health_test', 'val', 5);
            $cacheStatus = Cache::get('health_test') === 'val' ? 'OK' : 'Fail';
        } catch (\Throwable $e) {
            $cacheStatus = 'Error: ' . $e->getMessage();
        }

        return response()->json([
            'status' => ($dbStatus === 'OK' && $cacheStatus === 'OK') ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'database' => $dbStatus,
            'cache' => $cacheStatus,
            'environment' => config('app.env'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    }
}
