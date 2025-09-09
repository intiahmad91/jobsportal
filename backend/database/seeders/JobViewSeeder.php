<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\JobView;
use Carbon\Carbon;

class JobViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all jobs
        $jobs = Job::all();
        
        if ($jobs->isEmpty()) {
            $this->command->info('No jobs found. Please run JobSeeder first.');
            return;
        }

        // Create job views for each job
        foreach ($jobs as $job) {
            // Create random number of views (10-100 per job)
            $viewCount = rand(10, 100);
            
            for ($i = 0; $i < $viewCount; $i++) {
                JobView::create([
                    'job_id' => $job->id,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'referer' => $this->generateRandomReferer(),
                    'viewed_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                ]);
            }
        }

        $this->command->info('Job views created successfully!');
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

    private function generateRandomReferer(): ?string
    {
        $referers = [
            'https://www.google.com/',
            'https://www.bing.com/',
            'https://www.linkedin.com/',
            'https://www.indeed.com/',
            'https://www.glassdoor.com/',
            'https://www.facebook.com/',
            'https://www.twitter.com/',
            null, // Direct visit
        ];
        
        return $referers[array_rand($referers)];
    }
}