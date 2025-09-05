<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'sadmin@fulltimez.com',
            'password' => Hash::make('admin123'),
        ]);

        // Create admin profile
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'user_type' => 'admin',
            'phone' => '+1234567890',
            'bio' => 'System Administrator',
            'location' => 'United States',
            'experience_level' => 'expert',
            'open_to_work' => false,
        ]);

        // Create a sample employer user
        $employer = User::create([
            'name' => 'Employer User',
            'email' => 'employer@fulltimez.com',
            'password' => Hash::make('employer123'),
        ]);

        UserProfile::create([
            'user_id' => $employer->id,
            'first_name' => 'Employer',
            'last_name' => 'User',
            'user_type' => 'employer',
            'phone' => '+1234567891',
            'bio' => 'Sample Employer Account',
            'location' => 'United States',
            'experience_level' => 'senior',
            'open_to_work' => false,
        ]);

        // Create a sample jobseeker user
        $jobseeker = User::create([
            'name' => 'Jobseeker User',
            'email' => 'jobseeker@fulltimez.com',
            'password' => Hash::make('jobseeker123'),
        ]);

        UserProfile::create([
            'user_id' => $jobseeker->id,
            'first_name' => 'Jobseeker',
            'last_name' => 'User',
            'user_type' => 'jobseeker',
            'phone' => '+1234567892',
            'bio' => 'Sample Jobseeker Account',
            'location' => 'United States',
            'experience_level' => 'mid',
            'open_to_work' => true,
        ]);

        $this->command->info('Sample users created successfully!');
        $this->command->info('Admin: sadmin@fulltimez.com / admin123');
        $this->command->info('Employer: employer@fulltimez.com / employer123');
        $this->command->info('Jobseeker: jobseeker@fulltimez.com / jobseeker123');
    }
}

