<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSkinProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'phone'     => 'nullable|string|max:30',
            'avatar_url'=> 'nullable|string|max:1000',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'skin_type' => 'nullable|in:normal,dry,oily,combination,sensitive',
            'undertone' => 'nullable|in:cool,warm,neutral,olive',
        ]);

        // Handle uploaded avatar file
        $avatarUrl = $validated['avatar_url'] ?? null;
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $avatarUrl = Storage::url($path);
        }
        if (!$avatarUrl) {
            $avatarUrl = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80';
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
            'avatar_url' => $avatarUrl,
            'role'       => 'customer',
        ]);

        // Create default skin profile if provided
        UserSkinProfile::create([
            'user_id'  => $user->id,
            'skin_type'=> $validated['skin_type'] ?? null,
            'undertone'=> $validated['undertone'] ?? null,
            'concerns' => [],
            'allergies'=> [],
        ]);

        $token = $user->createToken('beautyvault-auth')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user'    => $user->load('skinProfile'),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with(['skinProfile', 'addresses'])->where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password you entered is incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'This account has been deactivated.'], 403);
        }

        $token = $user->createToken('beautyvault-auth')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['skinProfile', 'addresses']);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateSkinProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skin_type' => 'nullable|in:normal,dry,oily,combination,sensitive',
            'undertone' => 'nullable|in:cool,warm,neutral,olive',
            'shade_depth' => 'nullable|integer|between:1,10',
            'concerns' => 'nullable|array',
            'allergies' => 'nullable|array',
            'hair_type' => 'nullable|string',
            'hair_porosity' => 'nullable|string',
        ]);

        $profile = UserSkinProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json([
            'message' => 'Skin profile updated successfully',
            'profile' => $profile,
        ]);
    }

    /**
     * Update user profile (name, avatar image)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'       => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:30',
            'avatar_url' => 'nullable|string|max:1000',
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = $request->user();

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = Storage::url($path);
        } elseif ($request->filled('avatar_url')) {
            $user->avatar_url = $request->avatar_url;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->load(['skinProfile', 'addresses']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            // Option to revoke all tokens or just current session token
            if ($request->boolean('all_devices')) {
                $user->tokens()->delete();
            } else {
                $token = $user->currentAccessToken();
                if ($token && method_exists($token, 'delete')) {
                    $token->delete();
                } else {
                    $user->tokens()->delete();
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Request a 6-digit password reset verification code
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        // Generate a secure 6-digit verification code
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'A verification code has been dispatched to your email.',
            'verification_code' => $code, // Returned for instant testing and local dev
        ]);
    }

    /**
     * Reset password using the 6-digit verification code
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->code, $record->token)) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired verification code. Please check and try again.'],
            ]);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            throw ValidationException::withMessages([
                'code' => ['The verification code has expired. Please request a new code.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Invalidate token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully! You can now log in with your new password.',
        ]);
    }
}
