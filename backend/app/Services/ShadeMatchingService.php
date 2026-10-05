<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ShadeMatchingService
{
    /**
     * Map undertone compatibility affinities
     */
    protected array $undertoneAffinities = [
        'neutral' => ['neutral' => 100, 'warm' => 70, 'cool' => 70, 'olive' => 60, 'peach' => 70],
        'warm'    => ['warm' => 100, 'peach' => 85, 'neutral' => 75, 'olive' => 65, 'cool' => 30],
        'cool'    => ['cool' => 100, 'neutral' => 75, 'peach' => 60, 'warm' => 30, 'olive' => 40],
        'olive'   => ['olive' => 100, 'neutral' => 80, 'warm' => 70, 'cool' => 50, 'peach' => 40],
        'peach'   => ['peach' => 100, 'warm' => 85, 'neutral' => 75, 'cool' => 60, 'olive' => 40],
    ];

    /**
     * Match shades based on User depth (1-10) and undertone
     */
    public function findMatches(int $userDepth, string $userUndertone, ?int $productId = null): Collection
    {
        $userUndertone = strtolower($userUndertone);

        $query = ProductVariant::with(['product.brand'])
            ->whereNotNull('shade_hex')
            ->whereNotNull('depth_level')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $variants = $query->get();

        return $variants->map(function (ProductVariant $variant) use ($userDepth, $userUndertone) {
            // Depth distance penalty (each depth unit difference deducts 18 points)
            $depthDiff = abs($variant->depth_level - $userDepth);
            $depthScore = max(0, 100 - ($depthDiff * 18));

            // Undertone compatibility
            $variantUndertone = strtolower($variant->undertone ?? 'neutral');
            $undertoneScore = $this->undertoneAffinities[$userUndertone][$variantUndertone] ?? 50;

            // Weighted Overall Shade Match Score (60% Depth accuracy + 40% Undertone resonance)
            $finalMatchScore = (int) round(($depthScore * 0.6) + ($undertoneScore * 0.4));

            $matchQuality = match (true) {
                $finalMatchScore >= 90 => 'Exact Match',
                $finalMatchScore >= 75 => 'Close Match',
                $finalMatchScore >= 60 => 'Alternative Option',
                default => 'Poor Match',
            };

            return [
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => $variant->product->name,
                'brand_name' => $variant->product->brand->name,
                'sku' => $variant->sku,
                'title' => $variant->title,
                'shade_name' => $variant->shade_name,
                'shade_hex' => $variant->shade_hex,
                'depth_level' => $variant->depth_level,
                'undertone' => $variant->undertone,
                'price' => (float) $variant->price,
                'stock_quantity' => $variant->stock_quantity,
                'match_score' => $finalMatchScore,
                'match_quality' => $matchQuality,
                'undertone_alignment' => $undertoneScore >= 80 ? 'Harmonious' : 'Contrasting',
                'explanation' => "Depth difference: {$depthDiff} shades. Undertone: {$variantUndertone} vs your {$userUndertone}.",
            ];
        })->sortByDesc('match_score')->values();
    }
}
