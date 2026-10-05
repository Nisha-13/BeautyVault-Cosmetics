<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    /**
     * Submit checkout order and deduct stock atomically
     */
    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'shipping_address' => 'required|array',
            'shipping_address.recipient_name' => 'required|string',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'billing_address' => 'nullable|array',
            'payment_method' => 'required|in:stripe,cod',
            'stripe_payment_intent_id' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'customer_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $user = $request->user('sanctum') ?? User::where('email', $validated['customer_email'])->first();
            $order = $this->checkoutService->processCheckout($validated, $user);

            // Clear Cart if user had a cart
            if ($user) {
                Cart::where('user_id', $user->id)->delete();
            }

            return response()->json([
                'message' => 'Order created and stock reserved successfully!',
                'order' => $order,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
