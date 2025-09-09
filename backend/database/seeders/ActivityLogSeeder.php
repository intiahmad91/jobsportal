<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get companies
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->info('No companies found. Please run CompanySeeder first.');
            return;
        }

        // Get jobs
        $jobs = Job::all();
        
        // Get users
        $users = User::all();

        foreach ($companies as $company) {
            // Create various activity logs for each company
            $activities = [
                [
                    'type' => 'job_posted',
                    'description' => 'New job posted: Software Developer',
                    'icon' => 'fas fa-plus',
                    'user_name' => 'Admin'
                ],
                [
                    'type' => 'application_received',
                    'description' => 'New application received for Marketing Manager',
                    'icon' => 'fas fa-user-plus',
                    'user_name' => 'System'
                ],
                [
                    'type' => 'job_updated',
                    'description' => 'Job requirements updated for Senior Developer',
                    'icon' => 'fas fa-edit',
                    'user_name' => 'HR Manager'
                ],
                [
                    'type' => 'application_reviewed',
                    'description' => 'Application reviewed for Data Analyst position',
                    'icon' => 'fas fa-eye',
                    'user_name' => 'Recruiter'
                ],
                [
                    'type' => 'interview_scheduled',
                    'description' => 'Interview scheduled for Frontend Developer',
                    'icon' => 'fas fa-calendar',
                    'user_name' => 'HR Team'
                ],
                [
                    'type' => 'candidate_hired',
                    'description' => 'Candidate hired for Backend Developer role',
                    'icon' => 'fas fa-check-circle',
                    'user_name' => 'Hiring Manager'
                ],
                [
                    'type' => 'job_closed',
                    'description' => 'Job posting closed for Project Manager',
                    'icon' => 'fas fa-times-circle',
                    'user_name' => 'Admin'
                ],
                [
                    'type' => 'profile_updated',
                    'description' => 'Company profile updated',
                    'icon' => 'fas fa-user-edit',
                    'user_name' => 'Admin'
                ]
            ];

            // Create 10-20 activity logs per company
            $activityCount = rand(10, 20);
            
            for ($i = 0; $i < $activityCount; $i++) {
                $activity = $activities[array_rand($activities)];
                $job = $jobs->isNotEmpty() ? $jobs->random() : null;
                $user = $users->isNotEmpty() ? $users->random() : null;

                ActivityLog::create([
                    'type' => $activity['type'],
                    'description' => $activity['description'],
                    'user_name' => $activity['user_name'],
                    'icon' => $activity['icon'],
                    'company_id' => $company->id,
                    'job_id' => $job ? $job->id : null,
                    'user_id' => $user ? $user->id : null,
                    'activity_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                    'metadata' => [
                        'ip_address' => $this->generateRandomIP(),
                        'user_agent' => $this->generateRandomUserAgent()
                    ]
                ]);
            }
        }

        $this->command->info('Activity logs created successfully!');
    }

    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    private function generateRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
        ];
        
        return $userAgents[array_rand($userAgents)];
    }
}