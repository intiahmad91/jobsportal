<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class EmployerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create employer user
        $employer = User::create([
            'name' => 'Test Employer',
            'email' => 'employer@test.com',
            'password' => Hash::make('123456'),
        ]);

        // Create employer profile
        UserProfile::create([
            'user_id' => $employer->id,
            'user_type' => 'employer',
            'phone' => '+1234567890',
            'location' => 'New York, USA',
            'bio' => 'Test employer account for demonstration purposes',
            'status' => 'active'
        ]);

        $this->command->info('Employer user created: employer@test.com / 123456');
    }
}
