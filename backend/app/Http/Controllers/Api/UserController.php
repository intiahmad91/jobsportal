<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobView;
use App\Models\ActivityLog;
use App\Models\SavedJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->whereHas('profile', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            // Filter by user type
            if ($request->has('type') && $request->type !== 'all') {
                $query->whereHas('profile', function($q) use ($request) {
                    $q->where('user_type', $request->type);
                });
            }

            $users = $query->with(['profile', 'company'])->latest()->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'user_type' => ['required', Rule::in(['jobseeker', 'employer', 'admin'])],
                'phone' => 'nullable|string|max:20',
                'status' => ['required', Rule::in(['active', 'pending', 'inactive'])],
                'bio' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'experience_level' => 'nullable|string|in:entry,junior,mid,senior,expert',
                // Company fields (optional)
                'companyName' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'industry' => 'nullable|string|max:255',
                'companySize' => 'nullable|string|max:255',
                'foundedYear' => 'nullable|integer|min:1800|max:' . date('Y')
            ]);

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create the user profile
            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => explode(' ', $validated['name'])[0] ?? $validated['name'],
                'last_name' => explode(' ', $validated['name'])[1] ?? '',
                'user_type' => $validated['user_type'],
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
                'bio' => $validated['bio'] ?? null,
                'location' => $validated['location'] ?? null,
                'experience_level' => $validated['experience_level'] ?? 'mid',
                'open_to_work' => $validated['user_type'] === 'jobseeker'
            ]);

            // If creating an employer, also create a company record
            if ($validated['user_type'] === 'employer') {
                Company::create([
                    'user_id' => $user->id,
                    'name' => $validated['companyName'] ?? $validated['name'] . ' Company',
                    'description' => $validated['description'] ?? '',
                    'website' => $validated['website'] ?? '',
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'industry' => $validated['industry'] ?? '',
                    'size' => $validated['companySize'] ?? '',
                    'founded_year' => $validated['foundedYear'] ?? null,
                ]);
            }

            // Load the profile and company relationships
            $user->load(['profile', 'company']);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user (Admin only)
     */
    public function show(string $id)
    {
        try {
            $user = User::with('profile')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update the specified user (Admin only)
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'email', Rule::unique('users')->ignore($id)],
                'user_type' => ['sometimes', Rule::in(['jobseeker', 'employer', 'admin'])],
                'phone' => 'nullable|string|max:20',
                'status' => ['sometimes', Rule::in(['active', 'pending', 'inactive'])],
                'bio' => 'nullable|string|max:1000',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'experience_level' => ['nullable', Rule::in(['entry', 'junior', 'mid', 'senior', 'expert'])],
                'open_to_work' => 'nullable|boolean',
                'education' => 'nullable|string|max:500',
                'expected_salary' => 'nullable|string|max:100',
                'preferred_location' => 'nullable|string|max:255',
                'work_type' => 'nullable|string|max:100',
                'availability' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'certifications' => 'nullable|string',
                'languages' => 'nullable|string'
            ]);

            // Update user basic info
            $user->update([
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
            ]);

            // Update or create profile
            $profileData = [
                'user_type' => $validated['user_type'] ?? $user->profile?->user_type,
                'status' => $validated['status'] ?? $user->profile?->status,
                'phone' => $validated['phone'] ?? $user->profile?->phone,
                'bio' => $validated['bio'] ?? $user->profile?->bio,
                'first_name' => $validated['first_name'] ?? $user->profile?->first_name,
                'last_name' => $validated['last_name'] ?? $user->profile?->last_name,
                'location' => $validated['location'] ?? $user->profile?->location,
                'experience_level' => $validated['experience_level'] ?? $user->profile?->experience_level,
                'open_to_work' => $validated['open_to_work'] ?? $user->profile?->open_to_work ?? false,
                'education' => $validated['education'] ?? $user->profile?->education,
                'expected_salary' => $validated['expected_salary'] ?? $user->profile?->expected_salary,
                'preferred_location' => $validated['preferred_location'] ?? $user->profile?->preferred_location,
                'work_type' => $validated['work_type'] ?? $user->profile?->work_type,
                'availability' => $validated['availability'] ?? $user->profile?->availability,
                'skills' => $validated['skills'] ?? $user->profile?->skills,
                'certifications' => $validated['certifications'] ?? $user->profile?->certifications,
                'languages' => $validated['languages'] ?? $user->profile?->languages,
            ];

            // Remove null values
            $profileData = array_filter($profileData, function($value) {
                return $value !== null;
            });

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }

            // Reload user with profile
            $user->load('profile');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user (Admin only)
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile (Authenticated user)
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile'
            ], 500);
        }
    }

    /**
     * Get user profile (Authenticated user)
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $user->profile;
            
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'profile' => $profile->load('skills')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile (Authenticated user)
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            // Validate user basic fields
            $userValidated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            ]);

            // Validate profile fields
            $profileValidated = $request->validate([
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:1000',
                'location' => 'nullable|string|max:255',
                'linkedin' => 'nullable|url|max:255',
                'github' => 'nullable|url|max:255',
                'portfolio' => 'nullable|url|max:255',
                'twitter' => 'nullable|url|max:255',
                'experience_level' => 'nullable|string|in:entry,mid,senior,executive',
                'expected_salary' => 'nullable|numeric|min:0',
                'open_to_work' => 'nullable|boolean',
                'open_to_remote' => 'nullable|boolean',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'nationality' => 'nullable|string|max:100',
                'title' => 'nullable|string|max:255',
                'summary' => 'nullable|string|max:1000',
                'education' => 'nullable|string|max:255',
                'skills' => 'nullable|array',
                'languages' => 'nullable|array',
                'availability' => 'nullable|string|max:255',
                'work_type' => 'nullable|array',
                'job_types' => 'nullable|array',
                'locations' => 'nullable|array',
                'industries' => 'nullable|array',
                'company_size' => 'nullable|string|max:100',
                'notifications' => 'nullable|boolean'
            ]);

            // Update user basic fields
            if (!empty($userValidated)) {
                $user->update($userValidated);
            }

            // Update or create profile
            $profile = $user->profile;
            if (!$profile) {
                $profile = $user->profile()->create([
                    'user_type' => 'jobseeker',
                    'status' => 'active'
                ]);
            }

            // Update profile fields
            $profile->update($profileValidated);

            // Handle skills if provided
            if (isset($profileValidated['skills']) && is_array($profileValidated['skills'])) {
                $skillIds = [];
                foreach ($profileValidated['skills'] as $skillName) {
                    $skill = \App\Models\Skill::firstOrCreate(
                        ['name' => $skillName],
                        ['slug' => \Illuminate\Support\Str::slug($skillName)]
                    );
                    $skillIds[] = $skill->id;
                }
                $profile->skills()->sync($skillIds);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->fresh(),
                    'profile' => $profile->fresh()->load('skills')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload user CV/Resume (Authenticated user)
     */
    public function uploadCV(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $user->profile;
            
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found'
                ], 404);
            }

            $request->validate([
                'cv' => 'required|file|mimes:pdf,doc,docx|max:10240' // 10MB max
            ]);

            if ($request->hasFile('cv')) {
                $file = $request->file('cv');
                $filename = 'cv_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('resumes', $filename, 'public');
                
                // Update profile with CV path
                $profile->update([
                    'cv_path' => $path,
                    'cv_original_name' => $file->getClientOriginalName(),
                    'cv_size' => $file->getSize(),
                    'cv_mime_type' => $file->getMimeType()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'CV uploaded successfully',
                    'data' => [
                        'cv_path' => $path,
                        'cv_url' => asset('storage/' . $path),
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload CV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user CV/Resume (Authenticated user)
     */
    public function getCV(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $user->profile;
            
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found'
                ], 404);
            }

            if (!$profile->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'cv_path' => $profile->cv_path,
                    'cv_url' => asset('storage/' . $profile->cv_path),
                    'original_name' => $profile->cv_original_name,
                    'size' => $profile->cv_size,
                    'mime_type' => $profile->cv_mime_type,
                    'uploaded_at' => $profile->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get CV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user CV/Resume (Authenticated user)
     */
    public function deleteCV(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $user->profile;
            
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found'
                ], 404);
            }

            if (!$profile->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded'
                ], 404);
            }

            // Delete file from storage
            if (\Storage::disk('public')->exists($profile->cv_path)) {
                \Storage::disk('public')->delete($profile->cv_path);
            }

            // Update profile
            $profile->update([
                'cv_path' => null,
                'cv_original_name' => null,
                'cv_size' => null,
                'cv_mime_type' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'CV deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete CV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin statistics (Admin only)
     */
    public function statistics()
    {
        try {
            $stats = [
                'totalUsers' => User::count(),
                'totalJobs' => Job::where('status', 'active')->count(),
                'totalApplications' => JobApplication::count(),
                'totalCompanies' => Company::count(),
                'recentUsers' => User::with('profile')->latest()->take(5)->get()->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->profile ? ucfirst($user->profile->user_type) : 'User',
                        'status' => $user->profile ? ucfirst($user->profile->status ?? 'active') : 'Active',
                        'created_at' => $user->created_at->format('Y-m-d'),
                        'phone' => $user->profile ? $user->profile->phone : null
                    ];
                }),
                'recentJobs' => Job::with('company')->latest()->take(5)->get()->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company ? $job->company->name : 'N/A',
                        'status' => ucfirst($job->status ?? 'active'),
                        'applications_count' => $job->applications()->count(),
                        'created_at' => $job->created_at->format('Y-m-d')
                    ];
                }),
                'recentCompanies' => Company::latest()->take(5)->get()->map(function($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'industry' => $company->industry ?? 'N/A',
                        'status' => ucfirst($company->status ?? 'pending'),
                        'created_at' => $company->created_at->format('Y-m-d')
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify user (Admin only)
     */
    public function verify(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'User verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend user (Admin only)
     */
    public function suspend(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data (Admin only)
     */
    public function analytics()
    {
        try {
            $analytics = [
                'totalUsers' => User::count(),
                'activeJobs' => Job::where('status', 'active')->count(),
                'totalApplications' => JobApplication::count(),
                'totalCompanies' => Company::count(),
                'verifiedCompanies' => Company::where('is_verified', true)->count(),
                'featuredJobs' => Job::where('is_featured', true)->count(),
                'premiumJobs' => Job::where('is_premium', true)->count(),
                'recentUsers' => User::with('profile')->latest()->take(5)->get(),
                'recentJobs' => Job::with('company')->latest()->take(5)->get(),
                'recentApplications' => JobApplication::with(['user', 'job'])->latest()->take(5)->get(),
                'jobCategories' => Job::with('category')
                    ->selectRaw('category_id, COUNT(*) as count')
                    ->groupBy('category_id')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->map(function ($job) {
                        return [
                            'category' => $job->category ? $job->category->name : 'Unknown',
                            'count' => $job->count
                        ];
                    }),
                'monthlyStats' => [
                    'users' => User::whereMonth('created_at', now()->month)->count(),
                    'jobs' => Job::whereMonth('created_at', now()->month)->count(),
                    'applications' => JobApplication::whereMonth('created_at', now()->month)->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reports data (Admin only)
     */
    public function reports()
    {
        try {
            $reports = [
                // User Reports
                'userReports' => [
                    'totalUsers' => User::count(),
                    'activeUsers' => User::whereHas('profile', function($query) {
                        $query->where('status', 'active');
                    })->count(),
                    'jobseekers' => User::whereHas('profile', function($query) {
                        $query->where('user_type', 'jobseeker');
                    })->count(),
                    'employers' => User::whereHas('profile', function($query) {
                        $query->where('user_type', 'employer');
                    })->count(),
                    'newUsersThisMonth' => User::whereMonth('created_at', now()->month)->count(),
                    'newUsersLastMonth' => User::whereMonth('created_at', now()->subMonth()->month)->count(),
                ],

                // Job Reports
                'jobReports' => [
                    'totalJobs' => Job::count(),
                    'activeJobs' => Job::where('status', 'active')->count(),
                    'closedJobs' => Job::where('status', 'closed')->count(),
                    'featuredJobs' => Job::where('is_featured', true)->count(),
                    'premiumJobs' => Job::where('is_premium', true)->count(),
                    'newJobsThisMonth' => Job::whereMonth('created_at', now()->month)->count(),
                    'newJobsLastMonth' => Job::whereMonth('created_at', now()->subMonth()->month)->count(),
                    'jobsByType' => Job::selectRaw('employment_type, COUNT(*) as count')
                        ->groupBy('employment_type')
                        ->get()
                        ->map(function ($job) {
                            return [
                                'type' => ucfirst(str_replace('_', ' ', $job->employment_type)),
                                'count' => $job->count
                            ];
                        }),
                    'jobsByExperience' => Job::selectRaw('experience_level, COUNT(*) as count')
                        ->groupBy('experience_level')
                        ->get()
                        ->map(function ($job) {
                            return [
                                'level' => ucfirst($job->experience_level),
                                'count' => $job->count
                            ];
                        }),
                ],

                // Application Reports
                'applicationReports' => [
                    'totalApplications' => JobApplication::count(),
                    'pendingApplications' => JobApplication::where('status', 'pending')->count(),
                    'approvedApplications' => JobApplication::where('status', 'reviewed')->count(),
                    'rejectedApplications' => JobApplication::where('status', 'rejected')->count(),
                    'hiredApplications' => JobApplication::where('status', 'hired')->count(),
                    'interviewScheduled' => JobApplication::where('status', 'interview_scheduled')->count(),
                    'newApplicationsThisMonth' => JobApplication::whereMonth('created_at', now()->month)->count(),
                    'newApplicationsLastMonth' => JobApplication::whereMonth('created_at', now()->subMonth()->month)->count(),
                    'applicationsByStatus' => JobApplication::selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->get()
                        ->map(function ($app) {
                            return [
                                'status' => ucfirst(str_replace('_', ' ', $app->status)),
                                'count' => $app->count
                            ];
                        }),
                ],

                // Company Reports
                'companyReports' => [
                    'totalCompanies' => Company::count(),
                    'verifiedCompanies' => Company::where('is_verified', true)->count(),
                    'unverifiedCompanies' => Company::where('is_verified', false)->count(),
                    'featuredCompanies' => Company::where('is_featured', true)->count(),
                    'newCompaniesThisMonth' => Company::whereMonth('created_at', now()->month)->count(),
                    'newCompaniesLastMonth' => Company::whereMonth('created_at', now()->subMonth()->month)->count(),
                    'companiesBySize' => Company::selectRaw('company_size, COUNT(*) as count')
                        ->whereNotNull('company_size')
                        ->groupBy('company_size')
                        ->get()
                        ->map(function ($company) {
                            return [
                                'size' => $company->company_size,
                                'count' => $company->count
                            ];
                        }),
                ],

                // Monthly Trends (Last 6 months)
                'monthlyTrends' => $this->getMonthlyTrends(),

                // Top Performing Jobs
                'topPerformingJobs' => Job::with('company')
                    ->withCount('applications')
                    ->orderBy('applications_count', 'desc')
                    ->take(10)
                    ->get()
                    ->map(function ($job) {
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'company' => $job->company ? $job->company->name : 'Unknown',
                            'applications' => $job->applications_count,
                            'views' => $job->views_count ?? 0,
                            'status' => $job->status,
                        ];
                    }),

                // Top Companies by Job Count
                'topCompanies' => Company::withCount('jobs')
                    ->orderBy('jobs_count', 'desc')
                    ->take(10)
                    ->get()
                    ->map(function ($company) {
                        return [
                            'id' => $company->id,
                            'name' => $company->name,
                            'jobs' => $company->jobs_count,
                            'is_verified' => $company->is_verified,
                            'created_at' => $company->created_at,
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reports: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly trends for the last 6 months
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            
            $trends[] = [
                'month' => $monthName,
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'jobs' => Job::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'applications' => JobApplication::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'companies' => Company::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        return $trends;
    }

    /**
     * Get admin settings (Admin only)
     */
    public function getAdminSettings()
    {
        try {
            $settings = [
                'general' => [
                    'siteName' => \App\Models\Setting::get('siteName', 'Jobs Portal'),
                    'siteDescription' => \App\Models\Setting::get('siteDescription', 'Find your dream job with our comprehensive job portal'),
                    'siteUrl' => \App\Models\Setting::get('siteUrl', 'http://localhost:3000'),
                    'adminEmail' => \App\Models\Setting::get('adminEmail', 'admin@jobsportal.com'),
                    'timezone' => \App\Models\Setting::get('timezone', 'UTC'),
                    'language' => \App\Models\Setting::get('language', 'en'),
                    'maintenanceMode' => \App\Models\Setting::get('maintenanceMode', false),
                ],
                'email' => [
                    'smtpHost' => \App\Models\Setting::get('smtpHost', 'smtp.gmail.com'),
                    'smtpPort' => \App\Models\Setting::get('smtpPort', 587),
                    'smtpUsername' => \App\Models\Setting::get('smtpUsername', ''),
                    'smtpPassword' => \App\Models\Setting::get('smtpPassword', ''),
                    'fromEmail' => \App\Models\Setting::get('fromEmail', 'noreply@jobsportal.com'),
                    'fromName' => \App\Models\Setting::get('fromName', 'Jobs Portal'),
                    'emailNotifications' => \App\Models\Setting::get('emailNotifications', true),
                ],
                'security' => [
                    'passwordMinLength' => \App\Models\Setting::get('passwordMinLength', 8),
                    'requireEmailVerification' => \App\Models\Setting::get('requireEmailVerification', true),
                    'allowRegistration' => \App\Models\Setting::get('allowRegistration', true),
                    'sessionTimeout' => \App\Models\Setting::get('sessionTimeout', 30),
                    'maxLoginAttempts' => \App\Models\Setting::get('maxLoginAttempts', 5),
                    'twoFactorAuth' => \App\Models\Setting::get('twoFactorAuth', false),
                ],
                'system' => [
                    'maxFileSize' => \App\Models\Setting::get('maxFileSize', 5),
                    'allowedFileTypes' => \App\Models\Setting::get('allowedFileTypes', ['pdf', 'doc', 'docx']),
                    'backupFrequency' => \App\Models\Setting::get('backupFrequency', 'daily'),
                    'logLevel' => \App\Models\Setting::get('logLevel', 'info'),
                    'cacheEnabled' => \App\Models\Setting::get('cacheEnabled', true),
                    'debugMode' => \App\Models\Setting::get('debugMode', false),
                ],
                'notifications' => [
                    'newUserNotification' => \App\Models\Setting::get('newUserNotification', true),
                    'newJobNotification' => \App\Models\Setting::get('newJobNotification', true),
                    'newApplicationNotification' => \App\Models\Setting::get('newApplicationNotification', true),
                    'systemAlerts' => \App\Models\Setting::get('systemAlerts', true),
                    'emailDigest' => \App\Models\Setting::get('emailDigest', false),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin settings (Admin only)
     */
    public function updateAdminSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'general' => 'sometimes|array',
                'general.siteName' => 'sometimes|string|max:255',
                'general.siteDescription' => 'sometimes|string|max:500',
                'general.siteUrl' => 'sometimes|url|max:255',
                'general.adminEmail' => 'sometimes|email|max:255',
                'general.timezone' => 'sometimes|string|max:50',
                'general.language' => 'sometimes|string|max:10',
                'general.maintenanceMode' => 'sometimes|boolean',
                
                'email' => 'sometimes|array',
                'email.smtpHost' => 'sometimes|string|max:255',
                'email.smtpPort' => 'sometimes|integer|min:1|max:65535',
                'email.smtpUsername' => 'sometimes|string|max:255',
                'email.smtpPassword' => 'sometimes|string|max:255',
                'email.fromEmail' => 'sometimes|email|max:255',
                'email.fromName' => 'sometimes|string|max:255',
                'email.emailNotifications' => 'sometimes|boolean',
                
                'security' => 'sometimes|array',
                'security.passwordMinLength' => 'sometimes|integer|min:6|max:50',
                'security.requireEmailVerification' => 'sometimes|boolean',
                'security.allowRegistration' => 'sometimes|boolean',
                'security.sessionTimeout' => 'sometimes|integer|min:15|max:480',
                'security.maxLoginAttempts' => 'sometimes|integer|min:3|max:20',
                'security.twoFactorAuth' => 'sometimes|boolean',
                
                'system' => 'sometimes|array',
                'system.maxFileSize' => 'sometimes|integer|min:1|max:100',
                'system.allowedFileTypes' => 'sometimes|array',
                'system.backupFrequency' => 'sometimes|string|in:daily,weekly,monthly',
                'system.logLevel' => 'sometimes|string|in:debug,info,warn,error',
                'system.cacheEnabled' => 'sometimes|boolean',
                'system.debugMode' => 'sometimes|boolean',
                
                'notifications' => 'sometimes|array',
                'notifications.newUserNotification' => 'sometimes|boolean',
                'notifications.newJobNotification' => 'sometimes|boolean',
                'notifications.newApplicationNotification' => 'sometimes|boolean',
                'notifications.systemAlerts' => 'sometimes|boolean',
                'notifications.emailDigest' => 'sometimes|boolean',
            ]);

            // Update settings in database
            if (isset($validated['general'])) {
                foreach ($validated['general'] as $key => $value) {
                    $settingKey = str_replace('_', '_', $key);
                    if ($key === 'maintenanceMode') {
                        \App\Models\Setting::set($settingKey, $value, 'boolean', 'general');
                    } else {
                        \App\Models\Setting::set($settingKey, $value, 'string', 'general');
                    }
                }
            }

            if (isset($validated['email'])) {
                foreach ($validated['email'] as $key => $value) {
                    $settingKey = str_replace('_', '_', $key);
                    $type = in_array($key, ['smtpPort']) ? 'integer' : 'string';
                    if ($key === 'emailNotifications') $type = 'boolean';
                    \App\Models\Setting::set($settingKey, $value, $type, 'email');
                }
            }

            if (isset($validated['security'])) {
                foreach ($validated['security'] as $key => $value) {
                    $settingKey = str_replace('_', '_', $key);
                    $type = in_array($key, ['passwordMinLength', 'sessionTimeout', 'maxLoginAttempts']) ? 'integer' : 'boolean';
                    \App\Models\Setting::set($settingKey, $value, $type, 'security');
                }
            }

            if (isset($validated['system'])) {
                foreach ($validated['system'] as $key => $value) {
                    $settingKey = str_replace('_', '_', $key);
                    if ($key === 'allowedFileTypes') {
                        \App\Models\Setting::set($settingKey, $value, 'json', 'system');
                    } elseif (in_array($key, ['maxFileSize'])) {
                        \App\Models\Setting::set($settingKey, $value, 'integer', 'system');
                    } elseif (in_array($key, ['cacheEnabled', 'debugMode'])) {
                        \App\Models\Setting::set($settingKey, $value, 'boolean', 'system');
                    } else {
                        \App\Models\Setting::set($settingKey, $value, 'string', 'system');
                    }
                }
            }

            if (isset($validated['notifications'])) {
                foreach ($validated['notifications'] as $key => $value) {
                    $settingKey = str_replace('_', '_', $key);
                    \App\Models\Setting::set($settingKey, $value, 'boolean', 'notifications');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data (Admin only)
     */
    public function exportData()
    {
        try {
            // Create a simple CSV export
            $filename = 'job-portal-data-' . date('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, ['Data Type', 'ID', 'Name/Title', 'Email', 'Status', 'Created At']);
                
                // Export Users
                $users = User::with('profile')->get();
                foreach ($users as $user) {
                    fputcsv($file, [
                        'User',
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->profile->status ?? 'N/A',
                        $user->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                // Export Jobs
                $jobs = Job::with('company')->get();
                foreach ($jobs as $job) {
                    fputcsv($file, [
                        'Job',
                        $job->id,
                        $job->title,
                        $job->company->email ?? 'N/A',
                        $job->status,
                        $job->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                // Export Companies
                $companies = Company::with('user')->get();
                foreach ($companies as $company) {
                    fputcsv($file, [
                        'Company',
                        $company->id,
                        $company->name,
                        $company->email,
                        $company->status,
                        $company->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                // Export Applications
                $applications = JobApplication::with(['user', 'job'])->get();
                foreach ($applications as $application) {
                    fputcsv($file, [
                        'Application',
                        $application->id,
                        $application->user->name ?? 'N/A',
                        $application->user->email ?? 'N/A',
                        $application->status,
                        $application->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for jobseeker
     */
    public function jobseekerDashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get user profile with relationships
            $profile = UserProfile::where('user_id', $user->id)->first();
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found'
                ], 404);
            }

            // Get application statistics
            $totalApplications = JobApplication::where('user_id', $user->id)->count();
            $pendingApplications = JobApplication::where('user_id', $user->id)->where('status', 'pending')->count();
            $approvedApplications = JobApplication::where('user_id', $user->id)->where('status', 'approved')->count();
            $rejectedApplications = JobApplication::where('user_id', $user->id)->where('status', 'rejected')->count();

            // Get recent applications (last 10)
            $recentApplications = JobApplication::with(['job.company', 'job.location'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(10)
                ->get()
                ->map(function($app) {
                    return [
                        'id' => $app->id,
                        'jobTitle' => $app->job->title ?? 'N/A',
                        'company' => $app->job->company->name ?? 'N/A',
                        'appliedDate' => $app->created_at->format('Y-m-d'),
                        'status' => ucfirst($app->status),
                        'location' => $app->job->location->city ?? 'N/A'
                    ];
                });

            // Get saved jobs (if you have a saved_jobs table)
            $savedJobs = collect([]); // Placeholder for now

            // Get recommended jobs based on user skills and experience
            $userSkills = $user->skills()->pluck('name')->toArray();
            $recommendedJobs = Job::with(['company', 'location'])
                ->where('status', 'active')
                ->where('positions_available', '>', 0)
                ->where(function($query) use ($userSkills, $profile) {
                    if (!empty($userSkills)) {
                        $query->whereHas('skills', function($q) use ($userSkills) {
                            $q->whereIn('name', $userSkills);
                        });
                    }
                    if ($profile->experience_level) {
                        $query->where('experience_level', $profile->experience_level);
                    }
                })
                ->latest()
                ->limit(6)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company->name ?? 'N/A',
                        'location' => $job->location->city ?? 'N/A',
                        'salary' => $this->formatSalary($job->min_salary, $job->max_salary, $job->salary_currency, $job->salary_period),
                        'postedDate' => $job->created_at->format('Y-m-d'),
                        'type' => ucfirst(str_replace('_', ' ', $job->employment_type)),
                        'matchScore' => rand(80, 100) // Placeholder match score
                    ];
                });

            // Calculate profile completion percentage and identify incomplete sections
            // Only check fields that actually exist in the profile form
            $profileFields = [
                'bio' => ['label' => 'Professional Summary', 'section' => 'professional', 'tab' => 'professional'],
                'location' => ['label' => 'Location', 'section' => 'personal', 'tab' => 'personal'],
                'phone' => ['label' => 'Phone Number', 'section' => 'personal', 'tab' => 'personal'],
                'linkedin' => ['label' => 'LinkedIn Profile', 'section' => 'social', 'tab' => 'social'],
                'experience_level' => ['label' => 'Experience Level', 'section' => 'professional', 'tab' => 'professional'],
                'expected_salary' => ['label' => 'Expected Salary', 'section' => 'professional', 'tab' => 'professional'],
                'open_to_work' => ['label' => 'Open to Work Status', 'section' => 'preferences', 'tab' => 'preferences'],
                'open_to_remote' => ['label' => 'Remote Work Preference', 'section' => 'preferences', 'tab' => 'preferences']
            ];
            
            $completedFields = 0;
            $incompleteSections = [];
            
            foreach ($profileFields as $field => $fieldData) {
                $fieldValue = $profile->$field;
                $isCompleted = false;
                
                // Check if field is completed based on field type
                if (in_array($field, ['open_to_work', 'open_to_remote'])) {
                    // For boolean fields, check if they have a value (0, 1, true, false)
                    $isCompleted = !is_null($fieldValue) && $fieldValue !== '';
                } else {
                    // For other fields, check if they are not empty
                    $isCompleted = !empty($fieldValue);
                }
                
                if ($isCompleted) {
                    $completedFields++;
                } else {
                    $incompleteSections[] = [
                        'field' => $field,
                        'label' => $fieldData['label'],
                        'section' => $fieldData['section'],
                        'tab' => $fieldData['tab'],
                        'required' => in_array($field, ['bio', 'location', 'experience_level']),
                        'url' => "/jobseeker/profile?tab=" . $fieldData['tab']
                    ];
                }
            }
            $profileCompletion = round(($completedFields / count($profileFields)) * 100);

            // Get user skills
            $skills = $user->skills()->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'title' => $profile->title ?? 'Job Seeker',
                        'location' => $profile->location ?? 'Not specified',
                        'profileCompletion' => $profileCompletion,
                        'profilePicture' => $profile->avatar ?? '/api/placeholder/100/100',
                        'resumeUrl' => $profile->resume_url ?? null,
                        'skills' => $skills,
                        'experience' => $profile->experience_level ?? 'Not specified',
                        'education' => $profile->education_level ?? 'Not specified',
                        'incompleteSections' => $incompleteSections
                    ],
                    'stats' => [
                        'totalApplications' => $totalApplications,
                        'pendingApplications' => $pendingApplications,
                        'approvedApplications' => $approvedApplications,
                        'rejectedApplications' => $rejectedApplications,
                        'profileViews' => $profile->profile_views ?? 0,
                        'savedJobs' => $savedJobs->count()
                    ],
                    'recentApplications' => $recentApplications,
                    'savedJobs' => $savedJobs,
                    'recommendedJobs' => $recommendedJobs
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for employer
     */
    public function employerDashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            // For testing without auth, use first company data
            if (!$user) {
                $company = Company::first();
                if (!$company) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No company found'
                    ], 404);
                }
            } else {
                $company = $user->company;
                if (!$company) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Company not found'
                    ], 404);
                }
            }

            // Get job statistics
            $totalJobs = Job::where('company_id', $company->id)->count();
            $activeJobs = Job::where('company_id', $company->id)->where('status', 'active')->count();
            $totalApplications = JobApplication::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count();
            $pendingApplications = JobApplication::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'pending')->count();
            $approvedApplications = JobApplication::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'approved')->count();
            $rejectedApplications = JobApplication::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'rejected')->count();

            // Get recent jobs (last 5)
            $recentJobs = Job::with(['location', 'category'])
                ->where('company_id', $company->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($job) {
                    $applicationsCount = JobApplication::where('job_id', $job->id)->count();
                    $viewsCount = JobView::where('job_id', $job->id)->count();
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'location' => $job->location ? $job->location->city . ', ' . $job->location->state : 'Location not specified',
                        'salary' => $this->formatSalary($job->salary_min, $job->salary_max, $job->salary_currency, $job->salary_period),
                        'type' => ucfirst($job->employment_type),
                        'status' => ucfirst($job->status),
                        'postedAt' => $job->created_at->diffForHumans(),
                        'applications' => $applicationsCount,
                        'views' => $viewsCount // Real view count from database
                    ];
                });

            // Get recent applications (last 10)
            $recentApplications = JobApplication::with(['user.profile', 'job'])
                ->whereHas('job', function($query) use ($company) {
                    $query->where('company_id', $company->id);
                })
                ->latest()
                ->limit(10)
                ->get()
                ->map(function($app) {
                    return [
                        'id' => $app->id,
                        'jobTitle' => $app->job->title ?? 'N/A',
                        'candidateName' => $app->user->profile->first_name . ' ' . $app->user->profile->last_name ?? 'N/A',
                        'candidateEmail' => $app->user->email ?? 'N/A',
                        'status' => ucfirst($app->status),
                        'appliedAt' => $app->created_at->diffForHumans(),
                        'experience' => $app->user->profile->experience_level ?? 'Not specified',
                        'skills' => $app->user->skills()->pluck('name')->take(3)->toArray()
                    ];
                });

            // Get top skills from job applications
            $topSkills = JobApplication::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with('user.skills')
            ->get()
            ->flatMap(function($app) {
                return $app->user->skills;
            })
            ->groupBy('name')
            ->map(function($skills, $name) {
                return [
                    'skill' => $name,
                    'count' => $skills->count(),
                    'growth' => rand(5, 25) // Mock growth for now
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();

            // Get real view counts from JobView table
            $monthlyViews = JobView::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('viewed_at', '>=', now()->subMonth())->count();
            
            $profileViews = JobView::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count();

            // Get real activity logs from ActivityLog table
            $recentActivity = ActivityLog::where('company_id', $company->id)
                ->latest('activity_at')
                ->limit(5)
                ->get()
                ->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'type' => $activity->type,
                        'description' => $activity->description,
                        'timestamp' => $activity->activity_at->diffForHumans(),
                        'user' => $activity->user_name ?? 'System',
                        'icon' => $activity->icon ?? 'fas fa-info'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'totalJobs' => $totalJobs,
                    'activeJobs' => $activeJobs,
                    'totalApplications' => $totalApplications,
                    'pendingApplications' => $pendingApplications,
                    'approvedApplications' => $approvedApplications,
                    'rejectedApplications' => $rejectedApplications,
                    'monthlyViews' => $monthlyViews,
                    'profileViews' => $profileViews,
                    'recentJobs' => $recentJobs,
                    'recentApplications' => $recentApplications,
                    'topSkills' => $topSkills,
                    'recentActivity' => $recentActivity,
                    'companyInfo' => [
                        'name' => $company->name,
                        'industry' => $company->industry ?? 'Technology',
                        'size' => $company->company_size ?? '50-200 employees',
                        'location' => $company->address ?? 'Location not specified',
                        'website' => $company->website ?? 'Website not specified',
                        'description' => $company->description ?? 'Company description not available',
                        'rating' => 4.5, // Mock rating
                        'totalReviews' => rand(20, 100) // Mock reviews
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employer dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format salary for display
     */
    private function formatSalary($min, $max, $currency, $period)
    {
        if (!$min && !$max) return 'Salary not specified';
        
        $currencySymbol = $currency === 'USD' ? '$' : ($currency === 'AED' ? 'AED ' : $currency);
        $periodText = $period === 'yearly' ? '/year' : ($period === 'monthly' ? '/month' : '/' . $period);
        
        if ($min && $max) {
            return $currencySymbol . number_format($min) . ' - ' . $currencySymbol . number_format($max) . $periodText;
        } elseif ($min) {
            return $currencySymbol . number_format($min) . '+' . $periodText;
        } else {
            return 'Up to ' . $currencySymbol . number_format($max) . $periodText;
        }
    }

    /**
     * Get user applications
     */
    public function applications(Request $request)
    {
        try {
            $user = $request->user();
            
            $applications = JobApplication::with([
                'job.company',
                'job.location',
                'job.category'
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function($app) {
                return [
                    'id' => $app->id,
                    'jobTitle' => $app->job->title ?? 'N/A',
                    'company' => $app->job->company->name ?? 'N/A',
                    'companyLogo' => $app->job->company->logo ?? '/api/placeholder/50/50',
                    'location' => ($app->job->location->city ?? 'N/A') . ', ' . ($app->job->location->state ?? 'N/A'),
                    'appliedDate' => $app->created_at->format('Y-m-d'),
                    'status' => ucfirst($app->status),
                    'salary' => $this->formatSalary($app->job->min_salary, $app->job->max_salary, $app->job->salary_currency, $app->job->salary_period),
                    'jobType' => ucfirst(str_replace('_', ' ', $app->job->employment_type ?? 'N/A')),
                    'experience' => ucfirst($app->job->experience_level ?? 'N/A'),
                    'skills' => $app->job->tags ?? [],
                    'coverLetter' => $app->cover_letter ?? '',
                    'resumeUrl' => $app->cv_path ?? '',
                    'interviewDate' => $app->interviewed_at ? $app->interviewed_at->format('Y-m-d') : null,
                    'notes' => $app->candidate_notes ?? '',
                    'rejectionReason' => $app->rejection_reason ?? null,
                    'rating' => $app->rating ?? null,
                    'isFavorite' => $app->is_favorite ?? false
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's saved jobs
     */
    public function savedJobs(Request $request)
    {
        try {
            $user = $request->user();
            
            $savedJobs = SavedJob::with(['job.company', 'job.location', 'job.skills'])
                ->where('user_id', $user->id)
                ->orderBy('saved_at', 'desc')
                ->get()
                ->map(function ($savedJob) {
                    $job = $savedJob->job;
                    return [
                        'id' => $savedJob->id,
                        'jobId' => $job->id,
                        'jobTitle' => $job->title,
                        'company' => $job->company->name ?? 'Unknown Company',
                        'companyLogo' => $job->company->logo ?? '/api/placeholder/50/50',
                        'location' => $job->location ? 
                            $job->location->city . ', ' . $job->location->state : 
                            'Location not specified',
                        'salary' => $job->min_salary && $job->max_salary ? 
                            '$' . number_format($job->min_salary) . ' - $' . number_format($job->max_salary) . '/' . $job->salary_period : 
                            'Salary not specified',
                        'jobType' => ucfirst(str_replace('_', ' ', $job->employment_type)),
                        'experience' => ucfirst($job->experience_level) . ' level',
                        'skills' => $job->skills->pluck('name')->toArray(),
                        'description' => $job->description,
                        'postedDate' => $job->created_at->format('Y-m-d'),
                        'deadline' => $job->application_deadline ? 
                            $job->application_deadline->format('Y-m-d') : 
                            'No deadline',
                        'savedDate' => $savedJob->saved_at->format('Y-m-d'),
                        'matchScore' => rand(70, 95), // This would be calculated based on user profile
                        'isRemote' => $job->remote_work ?? false,
                        'benefits' => $job->benefits ? json_decode($job->benefits, true) : [],
                        'requirements' => $job->requirements ? json_decode($job->requirements, true) : []
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $savedJobs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saved jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save a job
     */
    public function saveJob(Request $request, $jobId)
    {
        try {
            $user = $request->user();
            
            // Check if job exists
            $job = Job::findOrFail($jobId);
            
            // Check if already saved
            $existingSavedJob = SavedJob::where('user_id', $user->id)
                ->where('job_id', $jobId)
                ->first();
                
            if ($existingSavedJob) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job already saved'
                ], 400);
            }

            // Save the job
            SavedJob::create([
                'user_id' => $user->id,
                'job_id' => $jobId,
                'saved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a saved job
     */
    public function unsaveJob(Request $request, $jobId)
    {
        try {
            $user = $request->user();
            
            $savedJob = SavedJob::where('user_id', $user->id)
                ->where('job_id', $jobId)
                ->first();
                
            if (!$savedJob) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saved job not found'
                ], 404);
            }

            $savedJob->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job removed from saved jobs'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove saved job',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
