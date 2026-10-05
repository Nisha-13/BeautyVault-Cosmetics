<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'inci_name',
        'slug',
        'description',
        'comedogenic_rating',
        'ewg_safety_score',
        'function_type',
        'benefits',
        'is_active_ingredient',
        'is_potential_irritant',
        'safe_for_fungal_acne',
    ];

    protected $casts = [
        'benefits' => 'array',
        'comedogenic_rating' => 'integer',
        'ewg_safety_score' => 'integer',
        'is_active_ingredient' => 'boolean',
        'is_potential_irritant' => 'boolean',
        'safe_for_fungal_acne' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot('concentration_percentage', 'is_key_active')
            ->withTimestamps();
    }
}
