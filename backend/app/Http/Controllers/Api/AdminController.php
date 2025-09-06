<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function dashboard(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'totalUsers' => 1250,
                    'totalJobseekers' => 980,
                    'totalCompanies' => 270,
                    'totalJobs' => 450,
                    'activeJobs' => 320,
                    'totalApplications' => 2100,
                    'pendingApplications' => 150,
                    'approvedApplications' => 1800,
                    'monthlyRevenue' => 45000,
                    'conversionRate' => 85.6,
                    'recentJobs' => [
                        [
                            'id' => 1,
                            'title' => 'Senior Software Developer',
                            'company' => 'TechCorp Inc.',
                            'location' => 'San Francisco, CA',
                            'salary' => '$120k - $150k',
                            'type' => 'Full-time',
                            'status' => 'Active',
                            'postedAt' => '2 hours ago',
                            'applications' => 25,
                            'views' => 150
                        ]
                    ],
                    'recentApplications' => [
                        [
                            'id' => 1,
                            'jobTitle' => 'Senior Software Developer',
                            'candidateName' => 'John Smith',
                            'candidateEmail' => 'john@email.com',
                            'status' => 'Pending',
                            'appliedAt' => '1 hour ago',
                            'experience' => '5 years'
                        ]
                    ],
                    'topSkills' => [
                        ['skill' => 'JavaScript', 'count' => 245, 'growth' => 12],
                        ['skill' => 'Python', 'count' => 198, 'growth' => 8],
                        ['skill' => 'React', 'count' => 156, 'growth' => 15]
                    ],
                    'recentActivity' => [
                        [
                            'id' => 1,
                            'type' => 'job_posted',
                            'description' => 'New job posted: Senior Software Developer',
                            'timestamp' => '2 hours ago',
                            'user' => 'TechCorp Inc.',
                            'icon' => 'fa-briefcase'
                        ]
                    ],
                    'systemMetrics' => [
                        'serverLoad' => 65,
                        'responseTime' => '245ms',
                        'uptime' => '99.9%',
                        'activeUsers' => 1250
                    ],
                    'monthlyStats' => [
                        ['month' => 'Jan', 'users' => 120, 'jobs' => 45, 'applications' => 180, 'revenue' => 8500],
                        ['month' => 'Feb', 'users' => 135, 'jobs' => 52, 'applications' => 210, 'revenue' => 9200]
                    ],
                    'jobCategories' => [
                        ['category' => 'Technology', 'count' => 180, 'percentage' => 40],
                        ['category' => 'Healthcare', 'count' => 90, 'percentage' => 20]
                    ],
                    'topCompanies' => [
                        ['name' => 'TechCorp Inc.', 'jobs' => 25, 'applications' => 150, 'rating' => 4.8],
                        ['name' => 'Design Studio', 'jobs' => 18, 'applications' => 120, 'rating' => 4.6]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}