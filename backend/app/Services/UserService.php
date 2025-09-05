<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Create a new user with profile.
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->profile()->create([
                'first_name' => $data['first_name'] ?? $data['name'],
                'last_name' => $data['last_name'] ?? '',
                'user_type' => $data['user_type'] ?? 'jobseeker',
                'phone' => $data['phone'] ?? null,
                'location' => $data['location'] ?? null,
            ]);

            // If user is employer, create company profile
            if (($data['user_type'] ?? 'jobseeker') === 'employer' && isset($data['company'])) {
                $user->company()->create([
                    'name' => $data['company'],
                    'description' => null,
                    'website' => $data['website'] ?? null,
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'industry' => null,
                    'company_size' => null,
                    'city' => null,
                    'country' => null,
                ]);
            }

            return $user->load(['profile', 'company']);
        });
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, array $data): UserProfile
    {
        $profile = $user->profile;
        
        $profile->update(array_filter($data, function($value) {
            return $value !== null;
        }));

        return $profile->fresh();
    }

    /**
     * Update user company profile.
     */
    public function updateCompanyProfile(User $user, array $data): Company
    {
        $company = $user->company;
        
        if (!$company) {
            $company = $user->company()->create($data);
        } else {
            $company->update(array_filter($data, function($value) {
                return $value !== null;
            }));
        }

        return $company->fresh();
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(User $user, $file): string
    {
        $path = $file->store('avatars', 'public');
        
        // Delete old avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }
        
        $user->profile()->update(['avatar' => $path]);
        
        return $path;
    }

    /**
     * Upload user CV.
     */
    public function uploadCV(User $user, $file): string
    {
        $path = $file->store('cvs', 'public');
        
        // Delete old CV if exists
        if ($user->profile && $user->profile->cv_path) {
            Storage::disk('public')->delete($user->profile->cv_path);
        }
        
        $user->profile()->update(['cv_path' => $path]);
        
        return $path;
    }

    /**
     * Get user dashboard data.
     */
    public function getDashboardData(User $user): array
    {
        $data = [
            'user' => $user->load(['profile', 'company']),
            'stats' => [
                'jobs_posted' => 0,
                'applications_submitted' => 0,
                'applications_received' => 0,
            ]
        ];

        if ($user->isEmployer()) {
            $data['stats']['jobs_posted'] = $user->jobs()->count();
            $data['stats']['applications_received'] = $user->jobs()
                ->withCount('applications')
                ->get()
                ->sum('applications_count');
        } elseif ($user->isJobseeker()) {
            $data['stats']['applications_submitted'] = $user->jobApplications()->count();
        }

        return $data;
    }

    /**
     * Search users by criteria.
     */
    public function searchUsers(array $criteria, int $perPage = 15)
    {
        $query = User::with(['profile', 'company'])
            ->whereHas('profile', function($q) use ($criteria) {
                if (isset($criteria['user_type'])) {
                    $q->where('user_type', $criteria['user_type']);
                }
                
                if (isset($criteria['location'])) {
                    $q->where('location', 'like', '%' . $criteria['location'] . '%');
                }
                
                if (isset($criteria['experience_level'])) {
                    $q->where('experience_level', $criteria['experience_level']);
                }
                
                if (isset($criteria['open_to_work'])) {
                    $q->where('open_to_work', $criteria['open_to_work']);
                }
            });

        if (isset($criteria['skills']) && is_array($criteria['skills'])) {
            $query->whereHas('skills', function($q) use ($criteria) {
                $q->whereIn('skills.id', $criteria['skills']);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Delete user account.
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Delete files
            if ($user->profile) {
                if ($user->profile->avatar) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
                if ($user->profile->cv_path) {
                    Storage::disk('public')->delete($user->profile->cv_path);
                }
            }

            // Delete user (cascade will handle related records)
            return $user->delete();
        });
    }
}
