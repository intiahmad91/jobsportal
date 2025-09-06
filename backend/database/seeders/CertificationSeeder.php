<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Certification;

class CertificationSeeder extends Seeder
{
    public function run()
    {
        // Get job seeker users
        $jobSeekers = User::whereHas('profile', function($query) {
            $query->where('user_type', 'jobseeker');
        })->get();

        if ($jobSeekers->isEmpty()) {
            $this->command->info('No job seekers found. Please run JobSeekerSeeder first.');
            return;
        }

        $certifications = [
            [
                'name' => 'AWS Certified Solutions Architect',
                'issuing_organization' => 'Amazon Web Services',
                'issue_date' => '2023-03-15',
                'expiry_date' => '2026-03-15',
                'credential_id' => 'AWS-SAA-123456',
                'credential_url' => 'https://aws.amazon.com/verification/'
            ],
            [
                'name' => 'Google Analytics Certified',
                'issuing_organization' => 'Google',
                'issue_date' => '2023-01-20',
                'expiry_date' => '2025-01-20',
                'credential_id' => 'GAIQ-789012',
                'credential_url' => 'https://skillshop.withgoogle.com/'
            ],
            [
                'name' => 'Certified Scrum Master (CSM)',
                'issuing_organization' => 'Scrum Alliance',
                'issue_date' => '2022-11-10',
                'expiry_date' => '2024-11-10',
                'credential_id' => 'CSM-345678',
                'credential_url' => 'https://www.scrumalliance.org/'
            ],
            [
                'name' => 'Adobe Certified Expert (ACE)',
                'issuing_organization' => 'Adobe',
                'issue_date' => '2023-06-05',
                'expiry_date' => '2025-06-05',
                'credential_id' => 'ACE-901234',
                'credential_url' => 'https://www.adobe.com/certification/'
            ],
            [
                'name' => 'Microsoft Azure Fundamentals',
                'issuing_organization' => 'Microsoft',
                'issue_date' => '2023-02-28',
                'expiry_date' => null,
                'credential_id' => 'AZ-900-567890',
                'credential_url' => 'https://docs.microsoft.com/certifications/'
            ],
            [
                'name' => 'PMP (Project Management Professional)',
                'issuing_organization' => 'Project Management Institute',
                'issue_date' => '2022-09-15',
                'expiry_date' => '2025-09-15',
                'credential_id' => 'PMP-234567',
                'credential_url' => 'https://www.pmi.org/certifications/'
            ],
            [
                'name' => 'Google Ads Certified',
                'issuing_organization' => 'Google',
                'issue_date' => '2023-04-12',
                'expiry_date' => '2025-04-12',
                'credential_id' => 'GADS-890123',
                'credential_url' => 'https://skillshop.withgoogle.com/'
            ],
            [
                'name' => 'HubSpot Content Marketing Certified',
                'issuing_organization' => 'HubSpot',
                'issue_date' => '2023-01-08',
                'expiry_date' => null,
                'credential_id' => 'HCM-456789',
                'credential_url' => 'https://academy.hubspot.com/'
            ],
            [
                'name' => 'Docker Certified Associate',
                'issuing_organization' => 'Docker',
                'issue_date' => '2023-05-20',
                'expiry_date' => '2026-05-20',
                'credential_id' => 'DCA-012345',
                'credential_url' => 'https://training.mirantis.com/'
            ],
            [
                'name' => 'Salesforce Certified Administrator',
                'issuing_organization' => 'Salesforce',
                'issue_date' => '2022-12-03',
                'expiry_date' => '2024-12-03',
                'credential_id' => 'SF-ADM-678901',
                'credential_url' => 'https://trailhead.salesforce.com/'
            ],
            [
                'name' => 'Certified Kubernetes Administrator',
                'issuing_organization' => 'Cloud Native Computing Foundation',
                'issue_date' => '2023-07-18',
                'expiry_date' => '2026-07-18',
                'credential_id' => 'CKA-234567',
                'credential_url' => 'https://www.cncf.io/certification/cka/'
            ],
            [
                'name' => 'Meta Certified Digital Marketing Associate',
                'issuing_organization' => 'Meta',
                'issue_date' => '2023-03-30',
                'expiry_date' => '2025-03-30',
                'credential_id' => 'META-DMA-890123',
                'credential_url' => 'https://www.facebook.com/business/learn/'
            ],
            [
                'name' => 'Tableau Desktop Specialist',
                'issuing_organization' => 'Tableau',
                'issue_date' => '2023-08-14',
                'expiry_date' => '2025-08-14',
                'credential_id' => 'TDS-456789',
                'credential_url' => 'https://www.tableau.com/learn/certification'
            ]
        ];

        foreach ($jobSeekers as $index => $jobSeeker) {
            // Assign 1-3 certifications per job seeker
            $numCertifications = rand(1, 3);
            $selectedCertifications = array_slice($certifications, $index * 2, $numCertifications);
            
            foreach ($selectedCertifications as $certData) {
                Certification::create([
                    'user_id' => $jobSeeker->id,
                    'name' => $certData['name'],
                    'issuing_organization' => $certData['issuing_organization'],
                    'issue_date' => $certData['issue_date'],
                    'expiry_date' => $certData['expiry_date'],
                    'credential_id' => $certData['credential_id'],
                    'credential_url' => $certData['credential_url']
                ]);
            }
        }

        $this->command->info('Certifications seeded successfully!');
    }
}
