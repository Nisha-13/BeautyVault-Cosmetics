<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSkinProfile;
use App\Services\SkinDiagnosticService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkinDiagnosticController extends Controller
{
    public function __construct(protected SkinDiagnosticService $diagnosticService)
    {
    }

    /**
     * Questions schema for the Interactive Beauty Quiz
     */
    public function getQuizQuestions(): JsonResponse
    {
        $questions = [
            [
                'id' => 'oil_level',
                'title' => 'How does your skin feel midday without powder or blotters?',
                'options' => [
                    ['value' => 'tight', 'label' => 'Tight, flaky, or dry across entire face', 'desc' => 'Needs deep hydration and ceramides'],
                    ['value' => 'normal', 'label' => 'Comfortable, neither greasy nor dry', 'desc' => 'Balanced lipid barrier'],
                    ['value' => 'shiny_tzone_only', 'label' => 'Oily only on forehead, nose, and chin', 'desc' => 'Classic combination skin'],
                    ['value' => 'shiny_everywhere', 'label' => 'Noticeable shine and excess oil all over', 'desc' => 'Active sebum production'],
                ],
            ],
            [
                'id' => 'vein_color',
                'title' => 'Look at the veins on the inside of your wrist under natural sunlight:',
                'options' => [
                    ['value' => 'blue_purple', 'label' => 'Blue or purplish veins', 'desc' => 'Indicates Cool pink undertone'],
                    ['value' => 'green', 'label' => 'Green or olive veins', 'desc' => 'Indicates Warm golden undertone'],
                    ['value' => 'blue_green', 'label' => 'A mix of blue and green', 'desc' => 'Indicates Neutral undertone'],
                    ['value' => 'olive', 'label' => 'Slightly grey/greenish tone against white paper', 'desc' => 'Indicates Olive undertone'],
                ],
            ],
            [
                'id' => 'shade_depth',
                'title' => 'Select your foundation depth range (1 = Porcelain/Fair, 10 = Deep Espresso):',
                'type' => 'slider',
                'min' => 1,
                'max' => 10,
                'default' => 4,
            ],
            [
                'id' => 'concerns',
                'title' => 'What are your top 2 skincare concerns right now?',
                'is_multiple' => true,
                'max_select' => 2,
                'options' => [
                    ['value' => 'acne', 'label' => 'Active Acne & Blemishes'],
                    ['value' => 'large_pores', 'label' => 'Enlarged Pores & Blackheads'],
                    ['value' => 'dullness', 'label' => 'Dullness & Uneven Skin Tone'],
                    ['value' => 'wrinkles', 'label' => 'Fine Lines & Loss of Elasticity'],
                    ['value' => 'redness', 'label' => 'Sensitivity & Persistent Redness'],
                    ['value' => 'sun_damage', 'label' => 'Hyperpigmentation & Sun Spots'],
                ],
            ],
            [
                'id' => 'reactivity',
                'title' => 'Does your skin easily sting or turn red when testing new products?',
                'options' => [
                    ['value' => 'low', 'label' => 'Rarely, my skin is resilient'],
                    ['value' => 'high', 'label' => 'Yes, easily sensitized by fragrances or actives'],
                ],
            ],
        ];

        return response()->json(['questions' => $questions]);
    }

    /**
     * Submit quiz responses and compute diagnosis
     */
    public function submitQuiz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'oil_level' => 'required|string',
            'vein_color' => 'required|string',
            'shade_depth' => 'required|integer|between:1,10',
            'concerns' => 'required|array',
            'reactivity' => 'required|string',
            'allergies' => 'nullable|array',
        ]);

        $evaluation = $this->diagnosticService->evaluateQuiz($validated);

        // If authenticated, persist to user's profile
        $user = $request->user('sanctum');
        if ($user) {
            UserSkinProfile::updateOrCreate(
                ['user_id' => $user->id],
                $evaluation
            );
        }

        // Mock profile object for routine generation if guest
        $profile = new UserSkinProfile($evaluation);
        $routine = $this->diagnosticService->buildCuratedRoutine($profile);

        return response()->json([
            'diagnosis' => $evaluation,
            'routine' => $routine,
        ]);
    }

    /**
     * Get routine for authenticated user
     */
    public function getMyRoutine(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->skinProfile;

        if (!$profile) {
            return response()->json([
                'has_profile' => false,
                'message' => 'Please take the Skin Diagnostic Quiz to generate your personalized routine.',
            ]);
        }

        $routine = $this->diagnosticService->buildCuratedRoutine($profile);

        return response()->json([
            'has_profile' => true,
            'routine' => $routine,
        ]);
    }
}
