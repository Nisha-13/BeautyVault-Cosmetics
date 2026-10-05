<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_url',
        'banner_url',
        'origin_country',
        'is_featured',
        'cruelty_free',
        'vegan_certified',
        'clean_certified',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'cruelty_free' => 'boolean',
        'vegan_certified' => 'boolean',
        'clean_certified' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
