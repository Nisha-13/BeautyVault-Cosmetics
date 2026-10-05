<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'batch_number',
        'supplier_name',
        'manufactured_at',
        'expires_at',
        'initial_quantity',
        'current_quantity',
        'unit_cost',
        'status',
        'warehouse_location',
    ];

    protected $casts = [
        'manufactured_at' => 'date',
        'expires_at' => 'date',
        'initial_quantity' => 'integer',
        'current_quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function isExpired(): bool
    {
        return Carbon::parse($this->expires_at)->isPast();
    }

    public function daysUntilExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays(Carbon::parse($this->expires_at)->startOfDay(), false);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('current_quantity', '>', 0);
    }

    public function scopeExpiringSoon(Builder $query, int $days = 60): Builder
    {
        return $query->where('status', 'active')
            ->where('current_quantity', '>', 0)
            ->whereBetween('expires_at', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now()->toDateString());
    }
}
