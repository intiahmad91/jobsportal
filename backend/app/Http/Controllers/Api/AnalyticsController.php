<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get analytics data for the current employer.
     */
    public function getEmployerAnalytics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Load the profile relationship
            $user->load('profile');
            
            // Get the user's company
            $company = $user->company;
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'No company profile found for this user'
                ], 404);
            }

            // Get period filter
            $period = $request->get('period', '30d');
            $startDate = $this->getStartDate($period);

            // Overview stats
            $overview = $this->getOverviewStats($company->id, $startDate);
            
            // Job performance
            $jobPerformance = $this->getJobPerformance($company->id, $startDate);
            
            // Application trends
            $applicationTrends = $this->getApplicationTrends($company->id, $startDate);
            
            // Top skills
            $topSkills = $this->getTopSkills($company->id, $startDate);
            
            // Location stats
            $locationStats = $this->getLocationStats($company->id, $startDate);
            
            // Source stats
            $sourceStats = $this->getSourceStats($company->id, $startDate);
            
            // Time to hire
            $timeToHire = $this->getTimeToHire($company->id, $startDate);
            
            // Candidate quality
            $candidateQuality = $this->getCandidateQuality($company->id, $startDate);

            $analytics = [
                'overview' => $overview,
                'jobPerformance' => $jobPerformance,
                'applicationTrends' => $applicationTrends,
                'topSkills' => $topSkills,
                'locationStats' => $locationStats,
                'sourceStats' => $sourceStats,
                'timeToHire' => $timeToHire,
                'candidateQuality' => $candidateQuality
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getStartDate($period)
    {
        switch ($period) {
            case '7d':
                return Carbon::now()->subDays(7);
            case '30d':
                return Carbon::now()->subDays(30);
            case '90d':
                return Carbon::now()->subDays(90);
            case '1y':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }

    private function getOverviewStats($companyId, $startDate)
    {
        $totalJobs = Job::where('company_id', $companyId)->count();
        $activeJobs = Job::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();
        
        $totalApplications = JobApplication::whereHas('job', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->count();
        
        $totalViews = Job::where('company_id', $companyId)
            ->sum('views_count') ?? 0;
        
        $conversionRate = $totalViews > 0 ? round(($totalApplications / $totalViews) * 100, 1) : 0;
        
        // Calculate average time to hire (simplified)
        $avgTimeToHire = JobApplication::whereHas('job', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('status', 'hired')
        ->where('created_at', '>=', $startDate)
        ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
        ->value('avg_days') ?? 0;

        return [
            'totalJobs' => $totalJobs,
            'activeJobs' => $activeJobs,
            'totalApplications' => $totalApplications,
            'totalViews' => $totalViews,
            'conversionRate' => $conversionRate,
            'avgTimeToHire' => round($avgTimeToHire)
        ];
    }

    private function getJobPerformance($companyId, $startDate)
    {
        return Job::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->withCount('applications')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($job) {
                $conversionRate = $job->views_count > 0 ? 
                    round(($job->applications_count / $job->views_count) * 100, 1) : 0;
                
                return [
                    'jobId' => $job->id,
                    'title' => $job->title,
                    'views' => $job->views_count ?? 0,
                    'applications' => $job->applications_count,
                    'conversionRate' => $conversionRate,
                    'postedDate' => $job->created_at->format('Y-m-d')
                ];
            });
    }

    private function getApplicationTrends($companyId, $startDate)
    {
        $trends = [];
        $months = [];
        
        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $applications = JobApplication::whereHas('job', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
            
            $views = Job::where('company_id', $companyId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('views_count') ?? 0;
            
            $trends[] = [
                'month' => $date->format('M'),
                'applications' => $applications,
                'views' => $views
            ];
        }
        
        return $trends;
    }

    private function getTopSkills($companyId, $startDate)
    {
        // Aggregate skills from company's job tags (JSON array on jobs.tags)
        $tags = Job::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('tags')
            ->pluck('tags')
            ->filter()
            ->flatMap(function ($tagsJson) {
                try {
                    $decoded = is_array($tagsJson) ? $tagsJson : json_decode($tagsJson, true);
                    if (!is_array($decoded)) {
                        return [];
                    }
                    // normalize: trim, lowercase
                    return array_map(function ($t) {
                        return strtolower(trim((string)$t));
                    }, $decoded);
                } catch (\Throwable $e) {
                    return [];
                }
            })
            ->filter(function ($t) { return $t !== ''; })
            ->countBy()
            ->sortDesc();

        $top = $tags->take(10);
        $total = $top->sum();

        return $top->map(function ($count, $skill) use ($total) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return [
                'skill' => $skill,
                'count' => $count,
                'percentage' => $percentage
            ];
        })->values();
    }

    private function getLocationStats($companyId, $startDate)
    {
        $locations = JobApplication::whereHas('job', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('job_applications.created_at', '>=', $startDate)
        ->join('users', 'job_applications.user_id', '=', 'users.id')
        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->select('user_profiles.location', DB::raw('COUNT(*) as count'))
        ->groupBy('user_profiles.location')
        ->orderBy('count', 'desc')
        ->limit(10)
        ->get();

        $total = $locations->sum('count');
        
        return $locations->map(function ($location) use ($total) {
            $percentage = $total > 0 ? round(($location->count / $total) * 100, 1) : 0;
            return [
                'location' => $location->location ?? 'Not specified',
                'applications' => $location->count,
                'percentage' => $percentage
            ];
        });
    }

    private function getSourceStats($companyId, $startDate)
    {
        // Derive traffic/application sources from job views referer
        // Falls back to 'Direct/Unknown' when referer is null
        $sources = \App\Models\JobView::whereHas('job', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('viewed_at', '>=', $startDate)
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->groupBy('referer')
            ->orderBy('count', 'desc')
            ->get();

        $total = $sources->sum('count');

        return $sources->map(function ($row) use ($total) {
            $label = $row->referer ?: 'Direct/Unknown';
            $percentage = $total > 0 ? round(($row->count / $total) * 100, 1) : 0;
            return [
                'source' => $label,
                'applications' => $row->count,
                'percentage' => $percentage
            ];
        });
    }

    private function getTimeToHire($companyId, $startDate)
    {
        return JobApplication::whereHas('job', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('status', 'hired')
        ->where('created_at', '>=', $startDate)
        ->with('job')
        ->get()
        ->map(function ($application) {
            $daysToHire = $application->created_at->diffInDays($application->updated_at);
            return [
                'jobTitle' => $application->job->title,
                'daysToHire' => $daysToHire,
                'status' => 'Hired'
            ];
        });
    }

    private function getCandidateQuality($companyId, $startDate)
    {
        // Get candidate quality based on experience level and application status
        $quality = JobApplication::whereHas('job', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('job_applications.created_at', '>=', $startDate)
        ->join('users', 'job_applications.user_id', '=', 'users.id')
        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->select(
            'user_profiles.experience_level',
            'job_applications.status',
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('user_profiles.experience_level', 'job_applications.status')
        ->get();

        $total = $quality->sum('count');
        
        // Categorize by experience level and status
        $categories = [
            'Excellent' => 0,
            'Good' => 0,
            'Average' => 0,
            'Below Average' => 0,
            'Poor' => 0
        ];

        foreach ($quality as $item) {
            $experience = $item->experience_level ?? 'entry';
            $status = $item->status;
            $count = $item->count;

            // Categorize based on experience level and application status
            if (in_array($experience, ['senior', 'lead', 'principal']) && in_array($status, ['hired', 'approved'])) {
                $categories['Excellent'] += $count;
            } elseif (in_array($experience, ['mid', 'senior']) && in_array($status, ['hired', 'approved', 'shortlisted'])) {
                $categories['Good'] += $count;
            } elseif (in_array($experience, ['entry', 'mid']) && in_array($status, ['pending', 'shortlisted', 'approved'])) {
                $categories['Average'] += $count;
            } elseif (in_array($experience, ['entry']) && in_array($status, ['pending', 'rejected'])) {
                $categories['Below Average'] += $count;
            } else {
                $categories['Poor'] += $count;
            }
        }

        return collect($categories)->map(function ($count, $rating) use ($total) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return [
                'rating' => $rating,
                'count' => $count,
                'percentage' => $percentage
            ];
        })->filter(function ($item) {
            return $item['count'] > 0;
        })->values();
    }
}
