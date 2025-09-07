<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function dashboard(Request $request)
    {
        try {
            // Get basic counts
            $totalUsers = User::count();
            $totalJobseekers = User::whereHas('profile', function($q) {
                $q->where('user_type', 'jobseeker');
            })->count();
            $totalCompanies = Company::count();
            $totalJobs = Job::count();
            $activeJobs = Job::where('status', 'active')->count();
            
            // Get application counts
            $totalApplications = JobApplication::count();
            $pendingApplications = JobApplication::where('status', 'pending')->count();
            $approvedApplications = JobApplication::where('status', 'approved')->count();
            
            // Calculate conversion rate
            $conversionRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;
            
            // Get recent jobs (last 5)
            $recentJobs = Job::with(['company', 'location'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company->name ?? 'Unknown Company',
                        'location' => $job->location->name ?? 'Not specified',
                        'salary' => $this->formatSalary($job->salary_min, $job->salary_max, $job->salary_currency, $job->salary_period),
                        'type' => $job->employment_type,
                        'status' => ucfirst($job->status),
                        'postedAt' => $job->created_at->diffForHumans(),
                        'applications' => $job->applications_count ?? 0,
                        'views' => $job->views_count ?? 0
                    ];
                });

            // Get recent applications (last 5)
            $recentApplications = JobApplication::with(['job', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($application) {
                    return [
                        'id' => $application->id,
                        'jobTitle' => $application->job->title ?? 'Unknown Job',
                        'candidateName' => $application->user->name ?? 'Unknown User',
                        'candidateEmail' => $application->user->email ?? 'Unknown Email',
                        'status' => ucfirst($application->status),
                        'appliedAt' => $application->created_at->diffForHumans(),
                        'experience' => $application->user->profile->experience_level ?? 'Not specified'
                    ];
                });

            // Get job categories with counts
            $jobCategories = JobCategory::withCount('jobs')
                ->orderBy('jobs_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($category) use ($totalJobs) {
                    $percentage = $totalJobs > 0 ? round(($category->jobs_count / $totalJobs) * 100, 1) : 0;
                    return [
                        'category' => $category->name,
                        'count' => $category->jobs_count,
                        'percentage' => $percentage
                    ];
                });

            // Get top companies by job count
            $topCompanies = Company::withCount('jobs')
                ->orderBy('jobs_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($company) {
                    return [
                        'name' => $company->name,
                        'jobs' => $company->jobs_count,
                        'applications' => $company->jobs()->withCount('applications')->get()->sum('applications_count'),
                        'rating' => 4.5 // Mock rating for now
                    ];
                });

            // Get monthly stats for the last 6 months
            $monthlyStats = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthlyStats[] = [
                    'month' => $date->format('M'),
                    'users' => User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                    'jobs' => Job::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                    'applications' => JobApplication::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                    'revenue' => $this->calculateMonthlyRevenueForMonth($date)
                ];
            }

            // Get top skills from database
            $topSkills = DB::table('skills')
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($skill) {
                    return [
                        'skill' => $skill->name,
                        'count' => $skill->usage_count,
                        'growth' => rand(5, 20) // Random growth for now, can be calculated from historical data
                    ];
                });

            // Get recent activity from database
            $recentActivity = DB::table('activity_logs')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->id,
                        'type' => $log->type ?? 'activity',
                        'description' => $log->description,
                        'timestamp' => \Carbon\Carbon::parse($log->created_at)->diffForHumans(),
                        'user' => $log->user_name ?? 'System',
                        'icon' => $this->getActivityIcon($log->type ?? 'activity')
                    ];
                });

            // Calculate system metrics from real data
            $systemMetrics = [
                'serverLoad' => $this->calculateServerLoad(),
                'responseTime' => $this->calculateResponseTime(),
                'uptime' => $this->calculateUptime(),
                'activeUsers' => $totalUsers
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'totalUsers' => $totalUsers,
                    'totalJobseekers' => $totalJobseekers,
                    'totalCompanies' => $totalCompanies,
                    'totalJobs' => $totalJobs,
                    'activeJobs' => $activeJobs,
                    'totalApplications' => $totalApplications,
                    'pendingApplications' => $pendingApplications,
                    'approvedApplications' => $approvedApplications,
                    'monthlyRevenue' => $this->calculateMonthlyRevenue(),
                    'conversionRate' => $conversionRate,
                    'recentJobs' => $recentJobs,
                    'recentApplications' => $recentApplications,
                    'topSkills' => $topSkills,
                    'recentActivity' => $recentActivity,
                    'systemMetrics' => $systemMetrics,
                    'monthlyStats' => $monthlyStats,
                    'jobCategories' => $jobCategories,
                    'topCompanies' => $topCompanies
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format salary display
     */
    private function formatSalary($min, $max, $currency, $period)
    {
        if (!$min && !$max) return 'Salary not specified';
        
        $currencySymbol = $currency === 'USD' ? '$' : ($currency === 'AED' ? 'AED ' : $currency);
        $periodText = $period === 'monthly' ? '/month' : ($period === 'yearly' ? '/year' : ($period === 'hourly' ? '/hr' : ''));
        
        if ($min && $max) {
            return $currencySymbol . $min . ' - ' . $max . $periodText;
        } elseif ($min) {
            return $currencySymbol . $min . '+' . $periodText;
        } elseif ($max) {
            return 'Up to ' . $currencySymbol . $max . $periodText;
        }
        
        return 'Salary not specified';
    }

    /**
     * Calculate server load (mock for now, can be integrated with real monitoring)
     */
    private function calculateServerLoad()
    {
        // Mock calculation - in real implementation, this would come from server monitoring
        return rand(30, 80);
    }

    /**
     * Calculate response time (mock for now, can be integrated with real monitoring)
     */
    private function calculateResponseTime()
    {
        // Mock calculation - in real implementation, this would come from performance monitoring
        return rand(100, 500) . 'ms';
    }

    /**
     * Calculate uptime (mock for now, can be integrated with real monitoring)
     */
    private function calculateUptime()
    {
        // Mock calculation - in real implementation, this would come from uptime monitoring
        return '99.' . rand(5, 9) . '%';
    }

    /**
     * Get activity icon based on activity type
     */
    private function getActivityIcon($type)
    {
        $icons = [
            'job_posted' => 'fa-briefcase',
            'user_registered' => 'fa-user-plus',
            'application_submitted' => 'fa-file-alt',
            'job_updated' => 'fa-edit',
            'user_updated' => 'fa-user-edit',
            'company_created' => 'fa-building',
            'default' => 'fa-circle'
        ];

        return $icons[$type] ?? $icons['default'];
    }

    /**
     * Calculate monthly revenue (mock for now, can be integrated with payment system)
     */
    private function calculateMonthlyRevenue()
    {
        // Mock calculation - in real implementation, this would come from payment/transaction data
        // For now, calculate based on active jobs and applications as a proxy
        $activeJobs = Job::where('status', 'active')->count();
        $totalApplications = JobApplication::count();
        
        // Mock revenue calculation based on platform activity
        $baseRevenue = $activeJobs * 50; // $50 per active job
        $applicationRevenue = $totalApplications * 2; // $2 per application
        
        return $baseRevenue + $applicationRevenue;
    }

    /**
     * Calculate monthly revenue for a specific month
     */
    private function calculateMonthlyRevenueForMonth($date)
    {
        // Mock calculation for historical data
        $monthlyJobs = Job::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $monthlyApplications = JobApplication::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        
        $baseRevenue = $monthlyJobs * 50;
        $applicationRevenue = $monthlyApplications * 2;
        
        return $baseRevenue + $applicationRevenue;
    }
}