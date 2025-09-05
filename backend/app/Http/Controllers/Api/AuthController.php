<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * User login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->attemptLogin($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * User registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * User logout.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());
            
            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh user token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->user());
            
            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getUser();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            return response()->json([
                'success' => true,
                'data' => new UserResource($user->load(['profile', 'company'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $sent = $this->authService->sendPasswordResetLink($request->email);
            
            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent successfully',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to send password reset link',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $reset = $this->authService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );
            
            if ($reset) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to reset password',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $changed = $this->authService->changePassword(
                $request->user(),
                $request->current_password,
                $request->password
            );
            
            if ($changed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password changed successfully',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to change password',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get user permissions.
     */
    public function permissions(Request $request): JsonResponse
    {
        try {
            $permissions = $this->authService->getUserPermissions($request->user());
            
            return response()->json([
                'success' => true,
                'data' => [
                    'permissions' => $permissions,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
