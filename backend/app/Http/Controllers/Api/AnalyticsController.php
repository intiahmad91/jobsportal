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
        // For now, return mock data since we don't have skills tracking
        return [
            ['skill' => 'JavaScript', 'count' => 15, 'percentage' => 18.4],
            ['skill' => 'React', 'count' => 12, 'percentage' => 15.5],
            ['skill' => 'Python', 'count' => 10, 'percentage' => 13.1],
            ['skill' => 'Node.js', 'count' => 8, 'percentage' => 11.4],
            ['skill' => 'AWS', 'count' => 7, 'percentage' => 10.2],
            ['skill' => 'TypeScript', 'count' => 6, 'percentage' => 9.0],
            ['skill' => 'Docker', 'count' => 5, 'percentage' => 7.3],
            ['skill' => 'SQL', 'count' => 4, 'percentage' => 6.1]
        ];
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
        // For now, return mock data since we don't have source tracking
        return [
            ['source' => 'Direct Application', 'applications' => 45, 'percentage' => 40.0],
            ['source' => 'LinkedIn', 'applications' => 30, 'percentage' => 26.7],
            ['source' => 'Indeed', 'applications' => 20, 'percentage' => 17.8],
            ['source' => 'Glassdoor', 'applications' => 12, 'percentage' => 10.7],
            ['source' => 'Referral', 'applications' => 5, 'percentage' => 4.5]
        ];
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
        // For now, return mock data since we don't have rating system
        return [
            ['rating' => 'Excellent (4.5-5.0)', 'count' => 15, 'percentage' => 18.4],
            ['rating' => 'Good (4.0-4.4)', 'count' => 25, 'percentage' => 31.8],
            ['rating' => 'Average (3.5-3.9)', 'count' => 30, 'percentage' => 36.3],
            ['rating' => 'Below Average (3.0-3.4)', 'count' => 8, 'percentage' => 11.4],
            ['rating' => 'Poor (Below 3.0)', 'count' => 2, 'percentage' => 2.0]
        ];
    }
}
