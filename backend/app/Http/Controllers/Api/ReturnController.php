<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\ReturnItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $returns = OrderReturn::with(['order', 'items.orderItem'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json($returns);
    }

    public function requestReturn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason_code' => 'required|in:wrong_shade,skin_reaction,damaged_in_transit,defective_packaging,changed_mind',
            'explanation' => 'required|string|min:5|max:1000',
            'refund_method' => 'required|in:original_payment,store_credit',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|in:unopened,opened_tested,damaged',
        ]);

        $user = $request->user();
        $order = Order::with('items')
            ->where('id', $validated['order_id'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('customer_email', $user->email);
            })
            ->firstOrFail();

        if (!$order->canBeReturned()) {
            return response()->json([
                'message' => 'This order is not eligible for return (only delivered orders within 30 days are eligible).',
            ], 422);
        }

        $existingReturn = OrderReturn::where('order_id', $order->id)
            ->whereNotIn('status', ['rejected'])
            ->first();
        if ($existingReturn) {
            return response()->json([
                'message' => 'A return request has already been submitted for this order.',
            ], 422);
        }

        // Calculate refund amount
        $refundTotal = 0.0;
        foreach ($validated['items'] as $itemData) {
            $orderItem = $order->items->firstWhere('id', $itemData['order_item_id']);
            if ($orderItem) {
                $qty = min($orderItem->quantity, $itemData['quantity']);
                $refundTotal += (float) $orderItem->unit_price * $qty;
            }
        }

        $returnOrder = OrderReturn::create([
            'return_number' => 'RET-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'order_id' => $order->id,
            'user_id' => $user->id,
            'status' => 'requested',
            'reason_code' => $validated['reason_code'],
            'explanation' => $validated['explanation'],
            'refund_amount' => round($refundTotal, 2),
            'refund_method' => $validated['refund_method'],
        ]);

        foreach ($validated['items'] as $itemData) {
            ReturnItem::create([
                'return_id' => $returnOrder->id,
                'order_item_id' => $itemData['order_item_id'],
                'quantity' => $itemData['quantity'],
                'condition' => $itemData['condition'],
            ]);
        }

        return response()->json([
            'message' => 'Return request submitted successfully. Our beauty care team will review within 24 hours.',
            'return' => $returnOrder->load('items'),
        ], 201);
    }
}
