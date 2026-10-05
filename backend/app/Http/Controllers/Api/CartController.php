<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected function getOrCreateCart(Request $request): Cart
    {
        $user = $request->user('sanctum');

        if ($user) {
            return Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => $request->header('X-Session-ID') ?? Str::random(32)]
            );
        }

        $sessionId = $request->header('X-Session-ID');
        if (!$sessionId) {
            $sessionId = (string) Str::uuid();
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function getCart(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request)->load([
            'items.variant.product.brand',
            'coupon',
        ]);

        $subtotal = 0.0;
        foreach ($cart->items as $item) {
            $subtotal += (float) $item->variant->price * $item->quantity;
        }

        $discount = 0.0;
        $couponMessage = null;
        if ($cart->coupon) {
            $validation = $cart->coupon->isValidForAmount($subtotal, $cart->user_id);
            if ($validation['valid']) {
                $discount = $validation['discount'];
                $couponMessage = $validation['message'];
            } else {
                $cart->coupon_id = null;
                $cart->save();
            }
        }

        $shipping = $subtotal >= 40.0 || $subtotal == 0 ? 0.0 : 5.99;
        $tax = round(($subtotal - $discount) * 0.08, 2);
        $total = max(0.0, round(($subtotal - $discount) + $shipping + $tax, 2));

        return response()->json([
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'items' => $cart->items,
            'coupon' => $cart->coupon,
            'coupon_message' => $couponMessage,
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => $total,
            'free_shipping_threshold' => 40.00,
            'amount_to_free_shipping' => max(0.00, round(40.00 - $subtotal, 2)),
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->getOrCreateCart($request);
        $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);

        if ($variant->stock_quantity < $validated['quantity']) {
            return response()->json([
                'message' => "Only {$variant->stock_quantity} units available in stock.",
            ], 422);
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $validated['quantity'];
            if ($variant->stock_quantity < $newQty) {
                return response()->json([
                    'message' => "Cannot add more. Maximum available stock reached ({$variant->stock_quantity}).",
                ], 422);
            }
            $item->quantity = $newQty;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        return $this->getCart($request);
    }

    public function updateQuantity(Request $request, int $itemId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:10']);
        $cart = $this->getOrCreateCart($request);

        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();

        if ($request->quantity <= 0) {
            $item->delete();
        } else {
            $variant = $item->variant;
            if ($variant->stock_quantity < $request->quantity) {
                return response()->json([
                    'message' => "Only {$variant->stock_quantity} units available in stock.",
                ], 422);
            }
            $item->quantity = $request->quantity;
            $item->save();
        }

        return $this->getCart($request);
    }

    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();

        return $this->getCart($request);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        $cart = $this->getOrCreateCart($request);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();
        if (!$coupon) {
            return response()->json(['message' => 'Invalid promotional code.'], 422);
        }

        $subtotal = 0.0;
        foreach ($cart->items as $item) {
            $subtotal += (float) $item->variant->price * $item->quantity;
        }

        $validation = $coupon->isValidForAmount($subtotal, $cart->user_id);
        if (!$validation['valid']) {
            return response()->json(['message' => $validation['message']], 422);
        }

        $cart->coupon_id = $coupon->id;
        $cart->save();

        return $this->getCart($request);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->coupon_id = null;
        $cart->save();

        return $this->getCart($request);
    }
}
