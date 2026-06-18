<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Employee / Admin Login
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)
            ->with(['company', 'department', 'section'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid username or password.', 401);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Your account is deactivated. Contact admin.', 403);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Revoke old tokens & create fresh token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Login successful.');
    }

    /**
     * Logout
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'Logged out successfully.');
    }

    /**
     * Get authenticated user profile
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['company', 'department', 'section']);

        return $this->successResponse(new UserResource($user));
    }
}