<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit_total',
        'usage_limit_per_user',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit_total' => 'integer',
        'usage_limit_per_user' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidForAmount(float $subtotal, ?int $userId = null): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Coupon is no longer active.'];
        }

        if ($this->starts_at && Carbon::parse($this->starts_at)->isFuture()) {
            return ['valid' => false, 'message' => 'Coupon promotion has not started yet.'];
        }

        if ($this->expires_at && Carbon::parse($this->expires_at)->isPast()) {
            return ['valid' => false, 'message' => 'Coupon has expired.'];
        }

        if ($this->usage_limit_total && $this->usage_count >= $this->usage_limit_total) {
            return ['valid' => false, 'message' => 'Coupon maximum usage limit reached.'];
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return ['valid' => false, 'message' => "Order minimum is \${$this->min_order_amount} to use this coupon."];
        }

        if ($userId && $this->usage_limit_per_user) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'You have already redeemed this coupon the maximum allowed times.'];
            }
        }

        // Calculate discount
        $discount = 0.0;
        if ($this->discount_type === 'percentage') {
            $discount = round(($subtotal * (float) $this->discount_value) / 100, 2);
            if ($this->max_discount_amount && $discount > (float) $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }
        } else {
            $discount = min($subtotal, (float) $this->discount_value);
        }

        return [
            'valid' => true,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!',
        ];
    }
}
