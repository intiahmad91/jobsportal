<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt to authenticate a user.
     */
    public function attemptLogin(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load(['profile', 'company']),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        $userService = app(UserService::class);
        $user = $userService->createUser($data);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout the current user.
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
        Auth::logout();
    }

    /**
     * Refresh the current user's token.
     */
    public function refreshToken(User $user): array
    {
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load(['profile', 'company']),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Get the authenticated user.
     */
    public function getUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Check if the user is authenticated.
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Validate user credentials.
     */
    public function validateCredentials(array $credentials): bool
    {
        return Auth::validate($credentials);
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }

    /**
     * Send password reset link.
     */
    public function sendPasswordResetLink(string $email): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Here you would typically send an email with the reset link
        // For now, we'll just return true
        return true;
    }

    /**
     * Reset user password.
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Here you would typically validate the reset token
        // For now, we'll just update the password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }

    /**
     * Verify user email.
     */
    public function verifyEmail(string $email, string $token): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Here you would typically validate the verification token
        // For now, we'll just mark the email as verified
        $user->update([
            'email_verified_at' => now(),
        ]);

        return true;
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(User $user, string $permission): bool
    {
        // Basic permission checking based on user type
        switch ($permission) {
            case 'post_jobs':
                return $user->isEmployer();
            case 'apply_jobs':
                return $user->isJobseeker();
            case 'manage_users':
                return $user->isAdmin();
            case 'manage_jobs':
                return $user->isEmployer() || $user->isAdmin();
            default:
                return false;
        }
    }

    /**
     * Get user permissions.
     */
    public function getUserPermissions(User $user): array
    {
        $permissions = [];

        if ($user->isEmployer()) {
            $permissions[] = 'post_jobs';
            $permissions[] = 'manage_jobs';
            $permissions[] = 'view_applications';
            $permissions[] = 'manage_company';
        }

        if ($user->isJobseeker()) {
            $permissions[] = 'apply_jobs';
            $permissions[] = 'manage_profile';
            $permissions[] = 'view_jobs';
        }

        if ($user->isAdmin()) {
            $permissions[] = 'manage_users';
            $permissions[] = 'manage_jobs';
            $permissions[] = 'manage_companies';
            $permissions[] = 'view_statistics';
        }

        return $permissions;
    }
}
