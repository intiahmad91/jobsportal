<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'employer@fulltimez.com')->first();
if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

$token = $user->createToken('dev')->plainTextToken;
echo $token . "\n";

