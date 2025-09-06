<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobApplication;
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

            $users = $query->with('profile')->latest()->paginate(15);

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
                'experience_level' => 'nullable|string|in:entry,junior,mid,senior,expert'
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

            // Load the profile relationship
            $user->load('profile');

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
            $user = User::findOrFail($id);
            
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
                'user_type' => ['sometimes', Rule::in(['jobseeker', 'employer'])],
                'phone' => 'nullable|string|max:20',
                'status' => ['sometimes', Rule::in(['active', 'pending', 'inactive'])]
            ]);

            $user->update($validated);

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
                'jobCategories' => Job::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderBy('count', 'desc')
                    ->get(),
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
}
