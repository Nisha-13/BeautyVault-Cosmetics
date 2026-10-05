<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Authenticated customer order history
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = Order::with(['items.product', 'items.variant'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('customer_email', $user->email);
            })
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Detailed single order view
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::with([
            'items.product.brand',
            'items.variant',
            'items.batchAllocations.batch',
            'statusHistories',
            'coupon',
        ])
        ->where('id', $id)
        ->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
        })
        ->firstOrFail();

        return response()->json($order);
    }

    /**
     * Public order tracking by Order Number & Email
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'order_number' => 'required|string',
            'customer_email' => 'required|email',
        ]);

        $order = Order::with([
            'items.product.brand',
            'items.variant',
            'statusHistories',
        ])
        ->where('order_number', strtoupper(trim($request->order_number)))
        ->where('customer_email', strtolower(trim($request->customer_email)))
        ->first();

        if (!$order) {
            return response()->json(['message' => 'No order found with these credentials.'], 404);
        }

        return response()->json([
            'order' => $order,
            'current_status' => $order->status,
            'tracking_number' => $order->tracking_number,
            'shipping_carrier' => $order->shipping_carrier,
            'estimated_delivery' => $order->dispatched_at ? $order->dispatched_at->addDays(3)->toFormattedDateString() : 'Pending Dispatch',
        ]);
    }
}
