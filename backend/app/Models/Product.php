<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'slug',
        'subtitle',
        'description',
        'how_to_use',
        'full_ingredients_text',
        'target_skin_types',
        'target_concerns',
        'finish_type',
        'coverage_level',
        'spf_rating',
        'pao_months',
        'base_price',
        'discount_price',
        'is_featured',
        'is_bestseller',
        'is_active',
        'rating_avg',
        'reviews_count',
        'thumbnail_url',
        'gallery_images',
    ];

    protected $casts = [
        'target_skin_types' => 'array',
        'target_concerns' => 'array',
        'gallery_images' => 'array',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_active' => 'boolean',
        'spf_rating' => 'integer',
        'pao_months' => 'integer',
        'reviews_count' => 'integer',
    ];

    /**
     * Meilisearch / Scout Searchable schema
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'description' => strip_tags($this->description ?? ''),
            'brand' => $this->brand?->name ?? '',
            'category' => $this->category?->name ?? '',
            'base_price' => (float) $this->base_price,
            'discount_price' => (float) ($this->discount_price ?? $this->base_price),
            'rating_avg' => (float) $this->rating_avg,
            'target_skin_types' => $this->target_skin_types ?? [],
            'target_concerns' => $this->target_concerns ?? [],
            'finish_type' => $this->finish_type,
            'coverage_level' => $this->coverage_level,
            'spf_rating' => $this->spf_rating,
            'is_bestseller' => (bool) $this->is_bestseller,
            'is_featured' => (bool) $this->is_featured,
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot('concentration_percentage', 'is_key_active')
            ->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestsellers(Builder $query): Builder
    {
        return $query->where('is_bestseller', true);
    }

    /**
     * Calculate Skin Compatibility Score (0 - 100%)
     */
    public function calculateCompatibility(?UserSkinProfile $profile): array
    {
        if (!$profile) {
            return [
                'score' => null,
                'status' => 'Unknown',
                'reasons' => ['Take the Skin Diagnostic Quiz to see your compatibility score.']
            ];
        }

        $score = 70; // baseline
        $reasons = [];

        // 1. Check Skin Type match
        $skinTypes = $this->target_skin_types ?? [];
        if ($profile->skin_type && in_array(strtolower($profile->skin_type), array_map('strtolower', $skinTypes))) {
            $score += 15;
            $reasons[] = "Formulated specifically for " . ucfirst($profile->skin_type) . " skin.";
        }

        // 2. Check Concerns match
        $targetConcerns = $this->target_concerns ?? [];
        $userConcerns = $profile->concerns ?? [];
        $matchedConcerns = array_intersect(
            array_map('strtolower', $userConcerns),
            array_map('strtolower', $targetConcerns)
        );

        if (!empty($matchedConcerns)) {
            $score += min(15, count($matchedConcerns) * 7);
            $reasons[] = "Targets your key concerns: " . implode(', ', array_map('ucfirst', $matchedConcerns)) . ".";
        }

        // 3. Comedogenic check for acne-prone skin
        if (in_array('acne', array_map('strtolower', $userConcerns)) || $profile->skin_type === 'oily') {
            $highComedogenic = $this->ingredients->first(fn($ing) => $ing->comedogenic_rating >= 3);
            if ($highComedogenic) {
                $score -= 25;
                $reasons[] = "Warning: Contains " . $highComedogenic->name . " (Comedogenic Rating " . $highComedogenic->comedogenic_rating . "/5) which may clog pores.";
            } else {
                $score += 5;
                $reasons[] = "Non-comedogenic formula safe for acne-prone skin.";
            }
        }

        // 4. Allergy check
        $allergies = $profile->allergies ?? [];
        foreach ($allergies as $allergy) {
            $allergyLower = strtolower($allergy);
            $foundAllergen = $this->ingredients->first(function ($ing) use ($allergyLower) {
                return str_contains(strtolower($ing->name), $allergyLower) ||
                       str_contains(strtolower($ing->inci_name ?? ''), $allergyLower);
            });

            if ($foundAllergen) {
                $score = max(10, $score - 40);
                $reasons[] = "Alert: Contains " . $foundAllergen->name . " matching your allergen profile (" . ucfirst($allergy) . ").";
            }
        }

        $score = max(10, min(99, $score));

        $status = match (true) {
            $score >= 85 => 'Perfect Match',
            $score >= 70 => 'Good Match',
            $score >= 50 => 'Moderate Match',
            default => 'Not Recommended',
        };

        return [
            'score' => $score,
            'status' => $status,
            'reasons' => $reasons,
        ];
    }
}
