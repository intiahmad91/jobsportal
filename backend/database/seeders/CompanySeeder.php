<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'FullTimeZ',
                'description' => 'Leading job portal platform',
                'website' => 'https://fulltimez.com',
                'email' => 'info@fulltimez.com',
                'phone' => '+1-555-0123',
                'address' => '123 Business Street, City, State 12345',
                'industry' => 'Technology',
                'founded_year' => 2020,
                'status' => 'active',
                'user_id' => 1, // Admin user
            ],
            [
                'name' => 'TechCorp Solutions',
                'description' => 'Innovative technology solutions provider',
                'website' => 'https://techcorp.com',
                'email' => 'hr@techcorp.com',
                'phone' => '+1-555-0456',
                'address' => '456 Tech Avenue, Silicon Valley, CA 94000',
                'industry' => 'Technology',
                'founded_year' => 2018,
                'status' => 'active',
                'user_id' => 1,
            ],
            [
                'name' => 'Global Finance Inc',
                'description' => 'International financial services',
                'website' => 'https://globalfinance.com',
                'email' => 'careers@globalfinance.com',
                'phone' => '+1-555-0789',
                'address' => '789 Wall Street, New York, NY 10001',
                'industry' => 'Finance',
                'founded_year' => 2015,
                'status' => 'active',
                'user_id' => 1,
            ],
            [
                'name' => 'Creative Design Studio',
                'description' => 'Award-winning design and marketing agency',
                'website' => 'https://creativedesign.com',
                'email' => 'jobs@creativedesign.com',
                'phone' => '+1-555-0321',
                'address' => '321 Creative Lane, Los Angeles, CA 90210',
                'industry' => 'Marketing',
                'founded_year' => 2019,
                'status' => 'active',
                'user_id' => 1,
            ],
            [
                'name' => 'Healthcare Plus',
                'description' => 'Comprehensive healthcare services',
                'website' => 'https://healthcareplus.com',
                'email' => 'hr@healthcareplus.com',
                'phone' => '+1-555-0654',
                'address' => '654 Medical Center, Boston, MA 02101',
                'industry' => 'Healthcare',
                'founded_year' => 2017,
                'status' => 'active',
                'user_id' => 1,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}