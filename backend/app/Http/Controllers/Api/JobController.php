<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\CreateJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(
        private JobService $jobService
    ) {}

    /**
     * Display a listing of jobs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = $request->all();
            $jobs = $this->jobService->searchJobs($criteria);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
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
     * Display a listing of jobs for admin.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Job::with(['company', 'user', 'category', 'location']);
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('company', function($companyQuery) use ($search) {
                          $companyQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by job type
            if ($request->has('job_type') && $request->job_type !== 'all') {
                $query->where('job_type', $request->job_type);
            }

            // Filter by experience level
            if ($request->has('experience_level') && $request->experience_level !== 'all') {
                $query->where('experience_level', $request->experience_level);
            }

            // Filter by location
            if ($request->has('location') && $request->location !== 'all') {
                $query->where('location', $request->location);
            }

            $limit = $request->get('limit', 15);
            $jobs = $query->latest()->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'jobs' => $jobs->items(),
                    'pagination' => [
                        'current_page' => $jobs->currentPage(),
                        'last_page' => $jobs->lastPage(),
                        'per_page' => $jobs->perPage(),
                        'total' => $jobs->total(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created job.
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;
            $data['company_id'] = $request->user()->company->id;
            
            $job = $this->jobService->createJob($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Job created successfully',
                'data' => new JobResource($job),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Store a newly created job by admin.
     */
    public function adminStore(Request $request): JsonResponse
    {
        try {
            \Log::info('Job creation request received', $request->all());
            \Log::info('Starting validation');
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'company_id' => 'required|exists:companies,id',
                'category_id' => 'required|exists:job_categories,id',
                'employment_type' => 'required|in:full_time,part_time,contract,internship,remote,freelance,temporary',
                'experience_level' => 'required|in:entry,junior,mid,senior,expert',
                'location' => 'required|string|max:255',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
                'salary_currency' => 'nullable|string|max:3',
                'salary_period' => 'nullable|in:hourly,daily,monthly,yearly',
                'status' => 'required|in:active,pending,closed,draft',
                'skills' => 'nullable|array',
                'benefits' => 'nullable|string',
                'requirements' => 'nullable|string'
            ]);
            \Log::info('Validation passed', $validated);

            // Find or create location based on location string
            $location = \App\Models\JobLocation::firstOrCreate(
                ['city' => $validated['location']],
                [
                    'city' => $validated['location'],
                    'state' => null,
                    'country' => 'Unknown',
                    'slug' => \Str::slug($validated['location']),
                    'is_active' => true
                ]
            );

            // Create job with admin user
            $job = Job::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'company_id' => $validated['company_id'],
                'category_id' => $validated['category_id'],
                'user_id' => $request->user()->id,
                'employment_type' => $validated['employment_type'],
                'experience_level' => $validated['experience_level'],
                'location_id' => $location->id,
                'min_salary' => $validated['min_salary'],
                'max_salary' => $validated['max_salary'],
                'salary_currency' => $validated['salary_currency'] ?? 'USD',
                'salary_period' => $validated['salary_period'] ?? 'yearly',
                'status' => $validated['status'],
                'benefits' => $validated['benefits'],
                'requirements' => $validated['requirements']
            ]);

            // Attach skills if provided (skip for now to avoid database issues)
            // if (!empty($validated['skills'])) {
            //     $job->skills()->attach($validated['skills']);
            // }
            \Log::info('Job created successfully', ['job_id' => $job->id]);

            return response()->json([
                'success' => true,
                'message' => 'Job created successfully by admin',
                'data' => $job->load(['company'])
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Job creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update the specified job by admin.
     */
    public function adminUpdate(Request $request, Job $job): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'company_id' => 'sometimes|exists:companies,id',
                'job_type' => 'sometimes|in:full-time,part-time,contract,internship,remote',
                'experience_level' => 'sometimes|in:entry,junior,mid,senior,expert',
                'education_level' => 'sometimes|in:high-school,bachelors,masters,phd',
                'location' => 'sometimes|string|max:255',
                'salary_min' => 'sometimes|numeric',
                'salary_max' => 'sometimes|numeric',
                'salary_type' => 'sometimes|in:per-hour,per-day,per-month,per-year',
                'status' => 'sometimes|in:active,pending,closed,draft',
                'skills' => 'sometimes|array',
                'benefits' => 'sometimes|string',
                'requirements' => 'sometimes|string'
            ]);

            $job->update($validated);

            // Update skills if provided
            if (isset($validated['skills'])) {
                $job->skills()->sync($validated['skills']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job updated successfully by admin',
                'data' => $job->load(['company', 'skills'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified job by admin.
     */
    public function adminDestroy(Job $job): JsonResponse
    {
        try {
            $job->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Job deleted successfully by admin'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job): JsonResponse
    {
        try {
            // Increment view count
            $this->jobService->incrementViews($job);
            
            return response()->json([
                'success' => true,
                'data' => new JobResource($job->load(['company', 'category', 'skills', 'location'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified job.
     */
    public function update(UpdateJobRequest $request, Job $job): JsonResponse
    {
        try {
            // Check if user owns this job
            if ($job->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this job',
                ], 403);
            }
            
            $updatedJob = $this->jobService->updateJob($job, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Job updated successfully',
                'data' => new JobResource($updatedJob),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified job.
     */
    public function destroy(Request $request, Job $job): JsonResponse
    {
        try {
            // Check if user owns this job
            if ($job->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this job',
                ], 403);
            }
            
            $deleted = $this->jobService->deleteJob($job);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Job deleted successfully',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete job',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured jobs.
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $jobs = $this->jobService->getFeaturedJobs($limit);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get premium jobs.
     */
    public function premium(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $jobs = $this->jobService->getPremiumJobs($limit);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recommended jobs.
     */
    public function recommended(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $jobs = $this->jobService->getRecommendedJobs($limit);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get jobs by company.
     */
    public function byCompany(Request $request, int $companyId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $jobs = $this->jobService->getJobsByCompany($companyId, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
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
     * Get job statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->jobService->getJobStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle job featured status.
     */
    public function toggleFeatured(Request $request, Job $job): JsonResponse
    {
        try {
            // Check if user owns this job
            if ($job->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to modify this job',
                ], 403);
            }
            
            $featured = $request->boolean('featured');
            $until = $request->get('until');
            
            $updatedJob = $this->jobService->toggleFeatured($job, $featured, $until);
            
            return response()->json([
                'success' => true,
                'message' => 'Job featured status updated successfully',
                'data' => new JobResource($updatedJob),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Toggle job premium status.
     */
    public function togglePremium(Request $request, Job $job): JsonResponse
    {
        try {
            // Check if user owns this job
            if ($job->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to modify this job',
                ], 403);
            }
            
            $premium = $request->boolean('premium');
            $until = $request->get('until');
            
            $updatedJob = $this->jobService->togglePremium($job, $premium, $until);
            
            return response()->json([
                'success' => true,
                'message' => 'Job premium status updated successfully',
                'data' => new JobResource($updatedJob),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Close a job.
     */
    public function close(Request $request, Job $job): JsonResponse
    {
        try {
            // Check if user owns this job
            if ($job->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to modify this job',
                ], 403);
            }
            
            $updatedJob = $this->jobService->closeJob($job);
            
            return response()->json([
                'success' => true,
                'message' => 'Job closed successfully',
                'data' => new JobResource($updatedJob),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get job statistics for admin.
     */
    public function adminStats(): JsonResponse
    {
        try {
            $stats = [
                'total_jobs' => Job::count(),
                'active_jobs' => Job::where('status', 'active')->count(),
                'pending_jobs' => Job::where('status', 'pending')->count(),
                'closed_jobs' => Job::where('status', 'closed')->count(),
                'draft_jobs' => Job::where('status', 'draft')->count(),
                'jobs_by_type' => [
                    'full-time' => Job::where('job_type', 'full-time')->count(),
                    'part-time' => Job::where('job_type', 'part-time')->count(),
                    'contract' => Job::where('job_type', 'contract')->count(),
                    'internship' => Job::where('job_type', 'internship')->count(),
                    'remote' => Job::where('job_type', 'remote')->count(),
                ],
                'jobs_by_experience' => [
                    'entry' => Job::where('experience_level', 'entry')->count(),
                    'junior' => Job::where('experience_level', 'junior')->count(),
                    'mid' => Job::where('experience_level', 'mid')->count(),
                    'senior' => Job::where('experience_level', 'senior')->count(),
                    'expert' => Job::where('experience_level', 'expert')->count(),
                ],
                'recent_jobs' => Job::with(['company', 'user'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($job) {
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'company_name' => $job->company ? $job->company->name : 'N/A',
                            'status' => $job->status,
                            'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                            'applications_count' => $job->applications()->count()
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
                'message' => 'Failed to fetch job statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get jobs owned by the authenticated user
     */
    public function myJobs(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $jobs = Job::with(['company', 'category', 'location', 'skills'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => JobResource::collection($jobs),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user jobs: ' . $e->getMessage()
            ], 500);
        }
    }
}
