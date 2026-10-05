<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ingredient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('inci_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('max_comedogenic')) {
            $query->where('comedogenic_rating', '<=', (int) $request->max_comedogenic);
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active_ingredient', true);
        }

        if ($request->boolean('fungal_acne_safe')) {
            $query->where('safe_for_fungal_acne', true);
        }

        $ingredients = $query->orderBy('is_active_ingredient', 'desc')
                             ->orderBy('name', 'asc')
                             ->paginate(20);

        return response()->json($ingredients);
    }

    public function show(string $slug): JsonResponse
    {
        $ingredient = Ingredient::with(['products' => function ($q) {
            $q->where('is_active', true)->with(['brand', 'variants']);
        }])->where('slug', $slug)->firstOrFail();

        return response()->json($ingredient);
    }
}
