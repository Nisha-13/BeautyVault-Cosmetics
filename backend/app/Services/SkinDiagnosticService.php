<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserSkinProfile;
use Illuminate\Support\Collection;

class SkinDiagnosticService
{
    /**
     * Process diagnostic quiz submission and compute skin profile
     */
    public function evaluateQuiz(array $answers): array
    {
        // 1. Determine Skin Type
        $oiliness = $answers['oil_level'] ?? 'medium'; // dry, normal, oily, combo
        $sensitivity = $answers['reactivity'] ?? 'low'; // low, high

        $skinType = match ($oiliness) {
            'very_dry', 'tight' => 'dry',
            'shiny_everywhere'  => 'oily',
            'shiny_tzone_only'  => 'combination',
            default             => 'normal',
        };

        if ($sensitivity === 'high' || !empty($answers['has_rosacea'])) {
            $skinType = 'sensitive';
        }

        // 2. Determine Undertone via vein test & jewelry preference
        $veins = $answers['vein_color'] ?? 'blue_green'; // blue_purple (cool), green (warm), blue_green (neutral), olive (olive)
        $jewelry = $answers['jewelry_flattery'] ?? 'both'; // gold (warm), silver (cool), both (neutral)

        $undertone = match (true) {
            $veins === 'green' || $jewelry === 'gold' => 'warm',
            $veins === 'blue_purple' || $jewelry === 'silver' => 'cool',
            $veins === 'olive' => 'olive',
            default => 'neutral',
        };

        $depth = (int) ($answers['shade_depth'] ?? 4);
        $concerns = (array) ($answers['concerns'] ?? ['large_pores', 'dullness']);
        $allergies = (array) ($answers['allergies'] ?? []);

        return [
            'skin_type' => $skinType,
            'undertone' => $undertone,
            'shade_depth' => max(1, min(10, $depth)),
            'concerns' => $concerns,
            'allergies' => $allergies,
        ];
    }

    /**
     * Recommend complete personalized routine based on profile
     */
    public function buildCuratedRoutine(UserSkinProfile $profile): array
    {
        $products = Product::with(['brand', 'ingredients', 'variants'])->where('is_active', true)->get();

        $scoredProducts = $products->map(function (Product $product) use ($profile) {
            $compatibility = $product->calculateCompatibility($profile);
            return [
                'product' => $product,
                'score' => $compatibility['score'],
                'status' => $compatibility['status'],
                'reasons' => $compatibility['reasons'],
            ];
        })->sortByDesc('score');

        return [
            'profile_summary' => [
                'skin_type' => ucfirst($profile->skin_type ?? 'Normal'),
                'undertone' => ucfirst($profile->undertone ?? 'Neutral'),
                'concerns' => $profile->concerns ?? [],
            ],
            'morning_routine' => [
                'step_1_treatment' => $scoredProducts->first(fn($p) => in_array('pore_minimizing', $p['product']->ingredients->pluck('benefits')->flatten()->toArray()))['product'] ?? null,
                'step_2_sunscreen' => $scoredProducts->first(fn($p) => $p['product']->spf_rating > 0)['product'] ?? null,
            ],
            'evening_routine' => [
                'step_1_active' => $scoredProducts->first(fn($p) => in_array('acne_treatment', $p['product']->ingredients->pluck('benefits')->flatten()->toArray()))['product'] ?? null,
            ],
            'top_recommendations' => $scoredProducts->take(4)->values(),
        ];
    }
}
