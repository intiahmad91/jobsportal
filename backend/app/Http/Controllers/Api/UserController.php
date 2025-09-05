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
     * Update user profile (Authenticated user)
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:1000'
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
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
}
