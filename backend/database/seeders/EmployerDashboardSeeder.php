<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobView;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class EmployerDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company for employer dashboard
        $company = Company::first();
        if (!$company) {
            $this->command->info('No company found. Please run the main seeder first.');
            return;
        }

        // Get jobs for this company
        $jobs = Job::where('company_id', $company->id)->get();
        if ($jobs->isEmpty()) {
            $this->command->info('No jobs found for company. Please run the main seeder first.');
            return;
        }

        // Create job views for the last 30 days
        $this->createJobViews($jobs);

        // Create activity logs
        $this->createActivityLogs($company, $jobs);

        $this->command->info('Employer dashboard data seeded successfully!');
    }

    private function createJobViews($jobs)
    {
        $viewCounts = [50, 120, 200, 150, 80, 300, 90, 180, 250, 110];
        
        foreach ($jobs as $job) {
            $viewsCount = $viewCounts[array_rand($viewCounts)];
            
            for ($i = 0; $i < $viewsCount; $i++) {
                JobView::create([
                    'job_id' => $job->id,
                    'ip_address' => '192.168.1.' . rand(1, 254),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'referer' => 'https://google.com',
                    'viewed_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                ]);
            }
        }
    }

    private function createActivityLogs($company, $jobs)
    {
        $activities = [
            [
                'type' => 'job_posted',
                'description' => 'New job posted: ' . $jobs->first()->title,
                'user_name' => $company->name,
                'icon' => 'fa-briefcase',
                'company_id' => $company->id,
                'job_id' => $jobs->first()->id,
                'activity_at' => Carbon::now()->subHours(2),
            ],
            [
                'type' => 'application_received',
                'description' => 'New application received for ' . $jobs->first()->title,
                'user_name' => 'John Smith',
                'icon' => 'fa-user-plus',
                'company_id' => $company->id,
                'job_id' => $jobs->first()->id,
                'activity_at' => Carbon::now()->subHours(4),
            ],
            [
                'type' => 'profile_viewed',
                'description' => 'Company profile viewed by job seeker',
                'user_name' => 'Anonymous',
                'icon' => 'fa-eye',
                'company_id' => $company->id,
                'activity_at' => Carbon::now()->subHours(6),
            ],
            [
                'type' => 'job_viewed',
                'description' => 'Job viewed: ' . $jobs->skip(1)->first()->title,
                'user_name' => 'Anonymous',
                'icon' => 'fa-eye',
                'company_id' => $company->id,
                'job_id' => $jobs->skip(1)->first()->id,
                'activity_at' => Carbon::now()->subHours(8),
            ],
            [
                'type' => 'application_approved',
                'description' => 'Application approved for Sarah Johnson',
                'user_name' => 'Sarah Johnson',
                'icon' => 'fa-check-circle',
                'company_id' => $company->id,
                'activity_at' => Carbon::now()->subHours(12),
            ],
        ];

        foreach ($activities as $activity) {
            ActivityLog::create($activity);
        }
    }
}
