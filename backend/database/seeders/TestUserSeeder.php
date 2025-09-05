<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create simple test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('123456'),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'user_type' => 'admin',
        ]);

        $this->command->info('Test user created: test@test.com / 123456');
    }
}

