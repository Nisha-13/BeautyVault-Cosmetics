<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, int $productId): JsonResponse
    {
        if (!$request->has('review_text') && $request->has('body')) {
            $request->merge(['review_text' => $request->body]);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:150',
            'review_text' => 'required|string|min:10|max:2000',
            'purchased_shade' => 'nullable|string|max:100',
            'photos' => 'nullable|array',
        ]);

        $product = Product::findOrFail($productId);
        $user = $request->user();
        $skinProfile = $user->skinProfile;

        // Check if verified purchase
        $hasPurchased = $user->orders()->whereHas('items', function ($q) use ($productId) {
            $q->where('product_id', $productId);
        })->where('status', '!=', 'cancelled')->exists();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'review_text' => $validated['review_text'],
            'verified_purchase' => $hasPurchased,
            'reviewer_skin_type' => $skinProfile?->skin_type,
            'reviewer_undertone' => $skinProfile?->undertone,
            'purchased_shade' => $validated['purchased_shade'] ?? null,
            'photos' => $validated['photos'] ?? null,
            'status' => 'approved',
        ]);

        // Recalculate average rating & count
        $approvedReviews = $product->reviews()->where('status', 'approved');
        $product->reviews_count = $approvedReviews->count();
        $product->rating_avg = round($approvedReviews->avg('rating') ?? 5.0, 2);
        $product->save();

        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully!',
            'review' => $review,
            'id' => $review->id,
            'rating' => $review->rating,
            'title' => $review->title,
            'user' => $review->user,
        ], 201);
    }

    public function upvote(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->increment('helpful_votes');

        return response()->json([
            'message' => 'Upvoted review',
            'helpful_votes' => $review->helpful_votes,
        ]);
    }
}
