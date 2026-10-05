<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'title',
        'shade_name',
        'shade_hex',
        'depth_level',
        'undertone',
        'volume_or_weight',
        'price',
        'compare_at_price',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'image_url',
    ];

    protected $casts = [
        'depth_level' => 'integer',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * FIFO Batches: Earliest unexpired active batch first
     */
    public function fifoBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class)
            ->where('status', 'active')
            ->where('current_quantity', '>', 0)
            ->where('expires_at', '>', now()->toDateString())
            ->orderBy('expires_at', 'asc');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
