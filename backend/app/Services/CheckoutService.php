<?php

namespace App\Services;

use App\Events\OrderPlacedEvent;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(protected InventoryService $inventoryService)
    {
    }

    /**
     * Process complete order checkout atomically
     */
    public function processCheckout(array $data, ?User $user = null): Order
    {
        return DB::transaction(function () use ($data, $user) {
            $itemsData = $data['items'] ?? [];
            if (empty($itemsData)) {
                throw new Exception("Cannot process checkout with an empty cart.");
            }

            // 1. Calculate raw subtotal and load product variants
            $subtotal = 0.0;
            $preparedItems = [];

            foreach ($itemsData as $item) {
                $variantId = $item['product_variant_id'];
                $quantity = (int) $item['quantity'];

                /** @var ProductVariant $variant */
                $variant = ProductVariant::with(['product'])->findOrFail($variantId);

                if (!$variant->is_active || !$variant->product->is_active) {
                    throw new Exception("Product {$variant->product->name} ({$variant->title}) is currently unavailable.");
                }

                if ($variant->stock_quantity < $quantity) {
                    throw new Exception("Sorry, only {$variant->stock_quantity} units left in stock for {$variant->product->name} ({$variant->title}).");
                }

                $itemSubtotal = round((float) $variant->price * $quantity, 2);
                $subtotal += $itemSubtotal;

                $preparedItems[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => (float) $variant->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // 2. Validate Coupon and calculate discount
            $discountAmount = 0.0;
            $appliedCoupon = null;

            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper(trim($data['coupon_code'])))->first();
                if ($coupon) {
                    $validation = $coupon->isValidForAmount($subtotal, $user?->id);
                    if ($validation['valid']) {
                        $discountAmount = $validation['discount'];
                        $appliedCoupon = $coupon;
                    }
                }
            }

            $shippingCost = $subtotal >= 40.00 ? 0.00 : 5.99; // Free shipping over $40
            $taxAmount = round(($subtotal - $discountAmount) * 0.08, 2); // 8% sales tax
            $grandTotal = max(0.00, round(($subtotal - $discountAmount) + $shippingCost + $taxAmount, 2));

            // 3. Create the Order
            $orderNumber = 'BV-' . date('Y') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user?->id,
                'customer_name' => $data['customer_name'] ?? ($user ? $user->name : 'Valued Customer'),
                'customer_email' => $data['customer_email'] ?? ($user ? $user->email : 'guest@example.com'),
                'customer_phone' => $data['customer_phone'] ?? $user?->phone,
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
                'coupon_id' => $appliedCoupon?->id,
                'coupon_code' => $appliedCoupon?->code,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'total_amount' => $grandTotal,
                'payment_method' => $data['payment_method'] ?? 'stripe',
                'payment_status' => ($data['payment_method'] ?? 'stripe') === 'stripe' ? 'paid' : 'pending',
                'stripe_payment_intent_id' => $data['stripe_payment_intent_id'] ?? ('pi_simulated_' . Str::random(18)),
                'status' => 'confirmed',
                'customer_notes' => $data['customer_notes'] ?? null,
                'paid_at' => ($data['payment_method'] ?? 'stripe') === 'stripe' ? now() : null,
            ]);

            // 4. Create Order Items & Deduct Stock with FIFO Lots
            foreach ($preparedItems as $item) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_title' => $variant->title,
                    'shade_name' => $variant->shade_name,
                    'shade_hex' => $variant->shade_hex,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // FIFO Batch Allocation & Row-level locked stock reduction
                $this->inventoryService->allocateAndDeductStock(
                    variant: $variant,
                    qtyRequested: $item['quantity'],
                    orderItem: $orderItem,
                    user: $user,
                    reason: "Order #{$order->order_number} checkout fulfillment",
                    referenceType: 'Order',
                    referenceId: $order->id
                );
            }

            // 5. Increment coupon usage
            if ($appliedCoupon) {
                $appliedCoupon->increment('usage_count');
                CouponUsage::create([
                    'coupon_id' => $appliedCoupon->id,
                    'user_id' => $user?->id,
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // 6. Record Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'confirmed',
                'comment' => 'Order successfully placed and verified. Stock allocated via FIFO lot batches.',
                'created_by_user_id' => $user?->id,
            ]);

            // 7. Fire WebSocket broadcast to real-time subscribers
            try {
                broadcast(new OrderPlacedEvent($order));
            } catch (\Throwable $e) {
                // Ignore WebSocket connection errors if daemon is offline during local test
            }

            return $order->fresh(['items', 'statusHistories']);
        });
    }
}
