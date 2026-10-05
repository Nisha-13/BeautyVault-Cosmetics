<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if ($request->wantsJson()) {
        return response()->json([
            'system'         => 'BeautyVault Clinical Cosmetics API & E-Commerce Platform',
            'version'        => '1.0.0-RELEASE',
            'status'         => 'operational',
            'database'       => 'MySQL (cosmetic_ecommerce)',
            'api_health'     => url('/api/v1/health'),
            'storefront_url' => 'http://localhost:5173',
        ]);
    }

    // Redirect browser visitors to the Vue storefront
    return redirect('http://localhost:5173');
});

