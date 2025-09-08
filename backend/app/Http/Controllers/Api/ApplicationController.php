<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use App\Models\JobCategory;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = JobApplication::with(['user', 'job.company']);
            
            $limit = $request->get('limit', 10);
            
            $applications = $query->latest()->take($limit)->get()->map(function($application) {
                return [
                    'id' => $application->id,
                    'user_name' => $application->user ? $application->user->name : 'N/A',
                    'job_title' => $application->job ? $application->job->title : 'N/A',
                    'company_name' => $application->job && $application->job->company ? $application->job->company->name : 'N/A',
                    'status' => ucfirst($application->status ?? 'pending'),
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'cover_letter' => $application->cover_letter ?? 'No cover letter provided'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $applications
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $jobId)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'cover_letter' => 'nullable|string|max:2000',
                'resume_url' => 'nullable|string|max:500',
                'expected_salary' => 'nullable|numeric|min:0',
                'availability_date' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Get the authenticated user
            $user = $request->user();
            
            // Check if job exists
            $job = \App\Models\Job::findOrFail($jobId);
            
            // Check if user has already applied for this job
            $existingApplication = JobApplication::where('user_id', $user->id)
                ->where('job_id', $jobId)
                ->first();
                
            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job'
                ], 400);
            }

            // Create the application
            $application = JobApplication::create([
                'user_id' => $user->id,
                'job_id' => $jobId,
                'cover_letter' => $validated['cover_letter'] ?? null,
                'resume_url' => $validated['resume_url'] ?? null,
                'expected_salary' => $validated['expected_salary'] ?? null,
                'availability_date' => $validated['availability_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'applied_at' => now()
            ]);

            // Increment application count for the job
            $job->increment('applications_count');

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_id' => $application->id,
                    'job_title' => $job->title,
                    'company_name' => $job->company ? $job->company->name : 'N/A',
                    'status' => $application->status,
                    'applied_at' => $application->applied_at ? $application->applied_at->format('Y-m-d H:i:s') : $application->created_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Admin: Get all applications with detailed information
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = JobApplication::with(['user.profile', 'job.company', 'job.location']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('job', function($jobQuery) use ($search) {
                        $jobQuery->where('title', 'like', "%{$search}%");
                    });
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by job
            if ($request->has('job_id') && $request->job_id !== 'all') {
                $query->where('job_id', $request->job_id);
            }

            $applications = $query->latest()->paginate(15);

            $formattedApplications = $applications->map(function($application) {
                return [
                    'id' => $application->id,
                    'jobTitle' => $application->job ? $application->job->title : 'Unknown Job',
                    'jobId' => $application->job_id,
                    'candidateName' => $application->user ? $application->user->name : 'Unknown User',
                    'candidateEmail' => $application->user ? $application->user->email : 'Unknown Email',
                    'candidatePhone' => $application->user && $application->user->profile ? $application->user->profile->phone : 'Not provided',
                    'experience' => $application->user && $application->user->profile ? $application->user->profile->experience_level : 'Not specified',
                    'skills' => $application->user && $application->user->profile ? 
                        explode(',', $application->user->profile->skills ?? '') : [],
                    'status' => ucfirst($application->status ?? 'pending'),
                    'appliedAt' => $application->created_at->format('Y-m-d'),
                    'resumeUrl' => $application->user && $application->user->profile ? $application->user->profile->cv_path : null,
                    'coverLetter' => $application->cover_letter ?? 'No cover letter provided',
                    'expectedSalary' => $application->expected_salary ?? 'Not specified',
                    'availability' => $application->availability ?? 'Not specified',
                    'company' => $application->job && $application->job->company ? $application->job->company->name : 'Unknown Company',
                    'location' => $application->job && $application->job->location ? 
                        $application->job->location->city . ', ' . $application->job->location->country : 'Not specified',
                    'jobType' => $application->job ? $application->job->employment_type : 'Not specified',
                    'notes' => $application->notes ?? 'No notes',
                    'avatar' => $application->user && $application->user->profile ? $application->user->profile->avatar : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $formattedApplications,
                    'pagination' => [
                        'current_page' => $applications->currentPage(),
                        'last_page' => $applications->lastPage(),
                        'per_page' => $applications->perPage(),
                        'total' => $applications->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get application statistics
     */
    public function adminStats()
    {
        try {
            $totalApplications = JobApplication::count();
            $pendingApplications = JobApplication::where('status', 'pending')->count();
            $approvedApplications = JobApplication::where('status', 'approved')->count();
            $rejectedApplications = JobApplication::where('status', 'rejected')->count();
            $hiredApplications = JobApplication::where('status', 'hired')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $totalApplications,
                    'pending' => $pendingApplications,
                    'approved' => $approvedApplications,
                    'rejected' => $rejectedApplications,
                    'hired' => $hiredApplications
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch application statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Update application
     */
    public function adminUpdate(Request $request, $id)
    {
        try {
            $application = JobApplication::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,approved,rejected,hired,interview_scheduled',
                'notes' => 'sometimes|string|max:1000'
            ]);

            $application->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Delete application
     */
    public function adminDestroy($id)
    {
        try {
            $application = JobApplication::findOrFail($id);
            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Employer: Get applications for employer's jobs
     */
    public function employerIndex(Request $request)
    {
        try {
            $employer = $request->user();
            
            // Get employer's job IDs
            $employerJobIds = Job::where('user_id', $employer->id)->pluck('id')->toArray();
            
            if (empty($employerJobIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'applications' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'per_page' => 15,
                            'total' => 0
                        ]
                    ]
                ]);
            }

            $query = JobApplication::with(['user.profile', 'job.company', 'job.location'])
                ->whereIn('job_id', $employerJobIds);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('job', function($jobQuery) use ($search) {
                        $jobQuery->where('title', 'like', "%{$search}%");
                    });
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by job
            if ($request->has('job_id') && $request->job_id !== 'all') {
                $query->where('job_id', $request->job_id);
            }

            $applications = $query->latest()->paginate(15);

            $formattedApplications = $applications->map(function($application) {
                return [
                    'id' => $application->id,
                    'jobTitle' => $application->job ? $application->job->title : 'Unknown Job',
                    'jobId' => $application->job_id,
                    'candidateName' => $application->user ? $application->user->name : 'Unknown User',
                    'candidateEmail' => $application->user ? $application->user->email : 'Unknown Email',
                    'candidatePhone' => $application->user && $application->user->profile ? $application->user->profile->phone : 'Not provided',
                    'experience' => $application->user && $application->user->profile ? $application->user->profile->experience_level : 'Not specified',
                    'skills' => $application->user && $application->user->profile ? 
                        explode(',', $application->user->profile->skills ?? '') : [],
                    'status' => ucfirst($application->status ?? 'pending'),
                    'appliedAt' => $application->created_at->format('Y-m-d'),
                    'resumeUrl' => $application->user && $application->user->profile ? $application->user->profile->cv_path : null,
                    'coverLetter' => $application->cover_letter ?? 'No cover letter provided',
                    'expectedSalary' => $application->expected_salary ?? 'Not specified',
                    'availability' => $application->availability ?? 'Not specified',
                    'education' => $application->user && $application->user->profile ? $application->user->profile->education : 'Not specified',
                    'location' => $application->user && $application->user->profile ? $application->user->profile->location : 'Not specified',
                    'rating' => $application->rating ?? 0,
                    'notes' => $application->notes ?? 'No notes',
                    'avatar' => $application->user && $application->user->profile ? $application->user->profile->avatar : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $formattedApplications,
                    'pagination' => [
                        'current_page' => $applications->currentPage(),
                        'last_page' => $applications->lastPage(),
                        'per_page' => $applications->perPage(),
                        'total' => $applications->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Employer: Get application statistics
     */
    public function employerStats(Request $request)
    {
        try {
            $employer = $request->user();
            
            // Get employer's job IDs
            $employerJobIds = Job::where('user_id', $employer->id)->pluck('id')->toArray();
            
            if (empty($employerJobIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total' => 0,
                        'pending' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'hired' => 0
                    ]
                ]);
            }

            $totalApplications = JobApplication::whereIn('job_id', $employerJobIds)->count();
            $pendingApplications = JobApplication::whereIn('job_id', $employerJobIds)->where('status', 'pending')->count();
            $approvedApplications = JobApplication::whereIn('job_id', $employerJobIds)->where('status', 'approved')->count();
            $rejectedApplications = JobApplication::whereIn('job_id', $employerJobIds)->where('status', 'rejected')->count();
            $hiredApplications = JobApplication::whereIn('job_id', $employerJobIds)->where('status', 'hired')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $totalApplications,
                    'pending' => $pendingApplications,
                    'approved' => $approvedApplications,
                    'rejected' => $rejectedApplications,
                    'hired' => $hiredApplications
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch application statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Employer: Update application
     */
    public function employerUpdate(Request $request, $id)
    {
        try {
            $employer = $request->user();
            
            // Get employer's job IDs
            $employerJobIds = Job::where('user_id', $employer->id)->pluck('id')->toArray();
            
            $application = JobApplication::whereIn('job_id', $employerJobIds)->findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,reviewed,shortlisted,interviewed,offered,rejected,withdrawn',
                'employer_notes' => 'sometimes|string|max:1000',
                'rating' => 'sometimes|numeric|min:1|max:5'
            ]);

            $application->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an application (employer only)
     */
    public function employerDestroy($id)
    {
        try {
            $application = JobApplication::findOrFail($id);
            
            // Check if the authenticated user is the employer who posted the job
            $user = auth()->user();
            if (!$user || $application->job->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this application'
                ], 403);
            }
            
            $application->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage()
            ], 500);
        }
    }
}
