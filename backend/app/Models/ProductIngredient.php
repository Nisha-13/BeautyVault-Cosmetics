<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductIngredient extends Pivot
{
    protected $table = 'product_ingredients';

    protected $casts = [
        'concentration_percentage' => 'decimal:2',
        'is_key_active' => 'boolean',
    ];
}
