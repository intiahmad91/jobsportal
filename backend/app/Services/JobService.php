<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class JobService
{
    /**
     * Create a new job.
     */
    public function createJob(array $data): Job
    {
        return DB::transaction(function () use ($data) {
            // Handle location - if location string is provided, find or create location
            $locationId = 1; // Default location ID
            if (isset($data['location']) && !empty($data['location'])) {
                $slug = \Illuminate\Support\Str::slug($data['location']);
                $location = \App\Models\JobLocation::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'city' => $data['location'],
                        'state' => '', 
                        'country' => 'USA', 
                        'status' => 'active'
                    ]
                );
                $locationId = $location->id;
            } elseif (isset($data['location_id'])) {
                $locationId = $data['location_id'];
            }
            
            $job = Job::create([
                'company_id' => $data['company_id'],
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'requirements' => $data['requirements'] ?? null,
                'responsibilities' => $data['responsibilities'] ?? null,
                'benefits' => $data['benefits'] ?? null,
                'category_id' => $data['category_id'],
                'location_id' => $locationId,
                'employment_type' => $data['employment_type'],
                'experience_level' => $data['experience_level'],
                'min_salary' => $data['min_salary'] ?? null,
                'max_salary' => $data['max_salary'] ?? null,
                'salary_currency' => $data['salary_currency'] ?? 'USD',
                'salary_period' => $data['salary_period'] ?? 'monthly',
                'salary_negotiable' => $data['salary_negotiable'] ?? false,
                'remote_work' => $data['remote_work'] ?? false,
                'relocation_assistance' => $data['relocation_assistance'] ?? false,
                'application_deadline' => $data['application_deadline'] ?? null,
                'positions_available' => $data['positions_available'] ?? 1,
                'status' => $data['status'] ?? 'active',
                'is_featured' => $data['is_featured'] ?? false,
                'is_premium' => $data['is_premium'] ?? false,
                'tags' => $data['tags'] ?? null,
            ]);

            // Attach skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                $skillIds = [];
                foreach ($data['skills'] as $skillData) {
                    if (is_array($skillData) && isset($skillData['id'])) {
                        $skillIds[$skillData['id']] = [
                            'proficiency_level' => $skillData['proficiency_level'] ?? null,
                            'years_experience' => $skillData['years_experience'] ?? null,
                        ];
                    } elseif (is_numeric($skillData)) {
                        $skillIds[$skillData] = [];
                    }
                }
                
                if (!empty($skillIds)) {
                    $job->skills()->attach($skillIds);
                }
            }

            return $job->load(['company', 'category', 'location', 'skills']);
        });
    }

    /**
     * Update an existing job.
     */
    public function updateJob(Job $job, array $data): Job
    {
        return DB::transaction(function () use ($job, $data) {
            // Handle location - if location string is provided, find or create location
            if (isset($data['location']) && !empty($data['location'])) {
                $slug = \Illuminate\Support\Str::slug($data['location']);
                $location = \App\Models\JobLocation::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'city' => $data['location'],
                        'state' => '', 
                        'country' => 'USA', 
                        'status' => 'active'
                    ]
                );
                $data['location_id'] = $location->id;
                unset($data['location']); // Remove the location string since we now have location_id
            }

            // Filter out null values and update the job
            $updateData = array_filter($data, function($value) {
                return $value !== null;
            });

            $job->update($updateData);

            // Update skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                $skillIds = [];
                foreach ($data['skills'] as $skillData) {
                    if (is_array($skillData) && isset($skillData['id'])) {
                        $skillIds[$skillData['id']] = [
                            'proficiency_level' => $skillData['proficiency_level'] ?? null,
                            'years_experience' => $skillData['years_experience'] ?? null,
                        ];
                    } elseif (is_numeric($skillData)) {
                        $skillIds[$skillData] = [];
                    }
                }
                
                $job->skills()->sync($skillIds);
            }

            return $job->fresh()->load(['company', 'category', 'location', 'skills']);
        });
    }

    /**
     * Search jobs by criteria.
     */
    public function searchJobs(array $criteria, int $perPage = 15): LengthAwarePaginator
    {
        $query = Job::with(['company', 'category', 'location', 'skills'])
            ->acceptingApplications();

        // Apply filters
        if (isset($criteria['title'])) {
            $query->where('title', 'like', '%' . $criteria['title'] . '%');
        }

        if (isset($criteria['category_id'])) {
            $query->where('category_id', $criteria['category_id']);
        }

        if (isset($criteria['location_id'])) {
            $query->where('location_id', $criteria['location_id']);
        }

        if (isset($criteria['employment_type'])) {
            $query->where('employment_type', $criteria['employment_type']);
        }

        if (isset($criteria['experience_level'])) {
            $query->where('experience_level', $criteria['experience_level']);
        }

        if (isset($criteria['remote_work'])) {
            $query->where('remote_work', $criteria['remote_work']);
        }

        if (isset($criteria['min_salary'])) {
            $query->where(function($q) use ($criteria) {
                $q->where('max_salary', '>=', $criteria['min_salary'])
                  ->orWhere('min_salary', '>=', $criteria['min_salary']);
            });
        }

        if (isset($criteria['max_salary'])) {
            $query->where(function($q) use ($criteria) {
                $q->where('min_salary', '<=', $criteria['max_salary'])
                  ->orWhere('max_salary', '<=', $criteria['max_salary']);
            });
        }

        if (isset($criteria['skills']) && is_array($criteria['skills'])) {
            $query->whereHas('skills', function($q) use ($criteria) {
                $q->whereIn('skills.id', $criteria['skills']);
            });
        }

        if (isset($criteria['company_id'])) {
            $query->where('company_id', $criteria['company_id']);
        }

        // Apply sorting
        $sortBy = $criteria['sort_by'] ?? 'created_at';
        $sortOrder = $criteria['sort_order'] ?? 'desc';
        
        if ($sortBy === 'salary') {
            $query->orderByRaw('COALESCE(min_salary, 0) ' . $sortOrder);
        } elseif ($sortBy === 'relevance') {
            // Add relevance scoring logic here
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('is_premium', 'desc')
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get featured jobs.
     */
    public function getFeaturedJobs(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Job::with(['company', 'category', 'location'])
            ->featured()
            ->acceptingApplications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get premium jobs.
     */
    public function getPremiumJobs(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Job::with(['company', 'category', 'location'])
            ->premium()
            ->acceptingApplications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommended jobs.
     */
    public function getRecommendedJobs(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Job::with(['company', 'category', 'location'])
            ->recommended()
            ->limit($limit)
            ->get();
    }

    /**
     * Get jobs by company.
     */
    public function getJobsByCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return Job::with(['category', 'company', 'location'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get job statistics.
     */
    public function getJobStats(): array
    {
        return [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::active()->count(),
            'featured_jobs' => Job::featured()->count(),
            'premium_jobs' => Job::premium()->count(),
            'jobs_this_month' => Job::whereMonth('created_at', now()->month)->count(),
            'top_categories' => JobCategory::withCount('jobs')
                ->orderBy('jobs_count', 'desc')
                ->limit(5)
                ->get(),
            'top_locations' => Job::join('job_locations', 'jobs.location_id', '=', 'job_locations.id')
                ->selectRaw('job_locations.city, job_locations.country, COUNT(*) as jobs_count')
                ->groupBy('job_locations.city', 'job_locations.country')
                ->orderBy('jobs_count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Increment job views.
     */
    public function incrementViews(Job $job): void
    {
        $job->increment('views_count');
    }

    /**
     * Toggle job featured status.
     */
    public function toggleFeatured(Job $job, bool $featured, ?string $until = null): Job
    {
        $job->update([
            'is_featured' => $featured,
            'featured_until' => $featured ? $until : null,
        ]);

        return $job->fresh();
    }

    /**
     * Toggle job premium status.
     */
    public function togglePremium(Job $job, bool $premium, ?string $until = null): Job
    {
        $job->update([
            'is_premium' => $premium,
            'premium_until' => $premium ? $until : null,
        ]);

        return $job->fresh();
    }

    /**
     * Close a job.
     */
    public function closeJob(Job $job): Job
    {
        $job->update(['status' => 'closed']);
        return $job->fresh();
    }

    /**
     * Delete a job.
     */
    public function deleteJob(Job $job): bool
    {
        return DB::transaction(function () use ($job) {
            // Delete related applications
            $job->applications()->delete();
            
            // Delete job
            return $job->delete();
        });
    }
}
