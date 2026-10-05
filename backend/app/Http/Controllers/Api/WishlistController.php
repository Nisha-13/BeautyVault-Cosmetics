<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::with(['product.brand', 'product.category', 'variant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $items,
            'count' => $items->count(),
        ]);
    }

    /**
     * Toggle product in user's wishlist
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'in_wishlist' => false,
                'message' => 'Removed from wishlist.',
            ]);
        }

        $wishlistItem = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'product_variant_id' => $validated['product_variant_id'] ?? null,
        ]);

        return response()->json([
            'in_wishlist' => true,
            'message' => 'Added to wishlist.',
            'item' => $wishlistItem->load('product'),
        ], 201);
    }

    /**
     * Remove item from wishlist
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'message' => 'Removed from wishlist.',
        ]);
    }
}
