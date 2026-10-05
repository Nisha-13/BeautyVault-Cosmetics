<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Services\ShadeMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function __construct(protected ShadeMatchingService $shadeMatchingService)
    {
    }

    /**
     * Product catalog with multi-dimensional beauty filters & Redis caching
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'products_catalog_' . md5(json_encode($request->all()));

        $products = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Product::with(['brand', 'category', 'variants' => function ($q) {
                $q->where('is_active', true);
            }])->where('is_active', true);

            // Filter: Search Term
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('subtitle', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('ingredients', fn($i) => $i->where('name', 'like', "%{$search}%"));
                });
            }

            // Filter: Category
            if ($request->filled('category')) {
                $categorySlug = $request->category;
                $query->whereHas('category', fn($c) => $c->where('slug', $categorySlug));
            }

            // Filter: Brand
            if ($request->filled('brand')) {
                $brandSlug = $request->brand;
                $query->whereHas('brand', fn($b) => $b->where('slug', $brandSlug));
            }

            // Filter: Skin Type
            if ($request->filled('skin_type')) {
                $skinType = strtolower($request->skin_type);
                $query->whereJsonContains('target_skin_types', $skinType);
            }

            // Filter: Skin Concern
            if ($request->filled('concern')) {
                $concern = strtolower($request->concern);
                $query->whereJsonContains('target_concerns', $concern);
            }

            // Filter: Finish Type
            if ($request->filled('finish')) {
                $query->where('finish_type', $request->finish);
            }

            // Filter: Shade Undertone
            if ($request->filled('undertone')) {
                $undertone = $request->undertone;
                $query->whereHas('variants', fn($v) => $v->where('undertone', $undertone));
            }

            // Filter: Price Range
            if ($request->filled('min_price')) {
                $query->where('base_price', '>=', (float) $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('base_price', '<=', (float) $request->max_price);
            }

            // Sorting
            match ($request->get('sort', 'featured')) {
                'price_asc' => $query->orderBy('base_price', 'asc'),
                'price_desc' => $query->orderBy('base_price', 'desc'),
                'rating' => $query->orderBy('rating_avg', 'desc'),
                'bestseller' => $query->orderBy('is_bestseller', 'desc'),
                default => $query->orderBy('is_featured', 'desc')->latest(),
            };

            return $query->paginate($request->get('per_page', 12));
        });

        return response()->json($products);
    }

    /**
     * Single product details with shade swatches and formula safety
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::with([
            'brand',
            'category',
            'variants' => fn($q) => $q->where('is_active', true),
            'ingredients' => fn($q) => $q->orderByPivot('is_key_active', 'desc'),
            'approvedReviews.user'
        ])->where('slug', $slug)->firstOrFail();

        // Calculate personal skin compatibility if user is authenticated
        $user = $request->user('sanctum');
        $compatibility = $product->calculateCompatibility($user?->skinProfile);

        return response()->json([
            'product' => $product,
            'compatibility' => $compatibility,
        ]);
    }

    /**
     * Shade Matching Algorithm Endpoint
     */
    public function matchShades(Request $request): JsonResponse
    {
        $request->validate([
            'depth_level' => 'required|integer|between:1,10',
            'undertone' => 'required|in:cool,warm,neutral,olive,peach',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        $matches = $this->shadeMatchingService->findMatches(
            (int) $request->depth_level,
            $request->undertone,
            $request->product_id ? (int) $request->product_id : null
        );

        return response()->json([
            'criteria' => [
                'depth_level' => (int) $request->depth_level,
                'undertone' => $request->undertone,
            ],
            'matches' => $matches,
        ]);
    }

    /**
     * Categories, Brands, and Metadata for navigation and filters
     */
    public function metadata(): JsonResponse
    {
        $data = Cache::remember('store_metadata_filters', 60, function () {
            // Dynamic store metrics computed from database
            $totalUndertones = ProductVariant::whereNotNull('undertone')->count();
            $totalVerifiedReviews = (int) Product::sum('reviews_count');
            $activeBatchesCount = ProductBatch::where('status', 'active')->count();
            $unexpiredBatchesCount = ProductBatch::where('status', 'active')
                ->where('expires_at', '>', now())
                ->count();
            $fifoFreshness = $activeBatchesCount > 0 ? round(($unexpiredBatchesCount / $activeBatchesCount) * 100) : 100;

            $crueltyFreeBrands = Brand::where('cruelty_free', true)->count();
            $totalBrands = Brand::count();
            $crueltyFreeRate = $totalBrands > 0 ? round(($crueltyFreeBrands / $totalBrands) * 100) : 100;

            $avgComedogenic = Ingredient::where('is_active_ingredient', true)->avg('comedogenic_rating') ?? 0;

            return [
                'categories' => Category::where('is_active', true)->orderBy('display_order')->get(),
                'brands' => Brand::orderBy('name')->get(),
                'skin_types' => ['Dry', 'Oily', 'Combination', 'Sensitive', 'Normal'],
                'concerns' => [
                    ['id' => 'acne', 'label' => 'Acne & Blemishes'],
                    ['id' => 'large_pores', 'label' => 'Enlarged Pores'],
                    ['id' => 'dullness', 'label' => 'Dullness & Uneven Tone'],
                    ['id' => 'wrinkles', 'label' => 'Fine Lines & Wrinkles'],
                    ['id' => 'redness', 'label' => 'Redness & Irritation'],
                    ['id' => 'barrier_repair', 'label' => 'Compromised Skin Barrier'],
                ],
                'undertones' => ['Warm', 'Neutral', 'Cool', 'Olive', 'Peach'],
                'finishes' => ['Matte', 'Dewy', 'Satin', 'Radiant', 'Natural'],
                'stats' => [
                    'inclusive_undertones' => ($totalUndertones >= 50 ? '50+' : (string) $totalUndertones),
                    'total_undertones_count' => $totalUndertones,
                    'fifo_freshness' => "{$fifoFreshness}%",
                    'comedogenic_score' => round($avgComedogenic, 0) . '/5',
                    'cruelty_free_percentage' => "{$crueltyFreeRate}%",
                    'guarantee_days' => '30-Day',
                    'verified_reviews' => $totalVerifiedReviews >= 1000
                        ? (number_format($totalVerifiedReviews / 1000, 1) . 'K+')
                        : (string) $totalVerifiedReviews,
                    'raw_verified_reviews' => $totalVerifiedReviews,
                ],
            ];
        });

        return response()->json($data);
    }
}
