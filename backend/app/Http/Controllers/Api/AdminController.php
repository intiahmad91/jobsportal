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
                    'revenue' => 0 // Mock revenue for now
                ];
            }

            // Mock data for features not yet implemented
            $topSkills = [
                ['skill' => 'JavaScript', 'count' => 245, 'growth' => 12],
                ['skill' => 'Python', 'count' => 198, 'growth' => 8],
                ['skill' => 'React', 'count' => 156, 'growth' => 15]
            ];

            $recentActivity = [
                [
                    'id' => 1,
                    'type' => 'job_posted',
                    'description' => 'New job posted: ' . ($recentJobs->first()['title'] ?? 'Recent Job'),
                    'timestamp' => '2 hours ago',
                    'user' => $recentJobs->first()['company'] ?? 'Company',
                    'icon' => 'fa-briefcase'
                ]
            ];

            $systemMetrics = [
                'serverLoad' => 65,
                'responseTime' => '245ms',
                'uptime' => '99.9%',
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
                    'monthlyRevenue' => 0, // Mock revenue
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
}