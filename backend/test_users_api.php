<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\UserProfile;

echo "Testing Users API Data...\n\n";

// Check if users exist
$users = User::with('profile')->get();

echo "Total users found: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Created: " . $user->created_at . "\n";
    
    if ($user->profile) {
        echo "Profile Type: " . $user->profile->user_type . "\n";
        echo "Profile Status: " . $user->profile->status . "\n";
        echo "Phone: " . ($user->profile->phone ?? 'N/A') . "\n";
    } else {
        echo "No profile found\n";
    }
    
    echo "---\n";
}

echo "\nTest completed!\n";
