<?php

// Test the new API endpoint
$baseUrl = 'https://coremediauae.com/jobs/public/api';

echo "=== TESTING NEW API ENDPOINT ===\n";
echo "Base URL: " . $baseUrl . "\n\n";

// Test public endpoints first
$testEndpoints = [
    '/job-seekers' => 'GET',
    '/jobs' => 'GET',
    '/categories' => 'GET',
    '/companies' => 'GET'
];

foreach ($testEndpoints as $endpoint => $method) {
    $url = $baseUrl . $endpoint;
    echo "Testing: $method $endpoint\n";
    echo "URL: $url\n";
    
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                       "Accept: application/json\r\n",
            'method' => $method,
            'timeout' => 10
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        echo "❌ Failed to connect\n";
    } else {
        $response = json_decode($result, true);
        if ($response && isset($response['success'])) {
            echo "✅ Success - " . ($response['message'] ?? 'No message') . "\n";
            if (isset($response['data']) && is_array($response['data'])) {
                echo "   Data count: " . count($response['data']) . "\n";
            }
        } else {
            echo "❌ Invalid response format\n";
            echo "Response: " . substr($result, 0, 200) . "...\n";
        }
    }
    echo "\n";
}

// Test authentication endpoint
echo "Testing authentication endpoint...\n";
$authUrl = $baseUrl . '/auth/login';
$authData = [
    'email' => 'employer@example.com',
    'password' => 'password123'
];

$authOptions = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($authData),
        'timeout' => 10
    ]
];

$authContext = stream_context_create($authOptions);
$authResult = @file_get_contents($authUrl, false, $authContext);

if ($authResult === false) {
    echo "❌ Authentication endpoint failed to connect\n";
} else {
    $authResponse = json_decode($authResult, true);
    if ($authResponse && isset($authResponse['success'])) {
        echo "✅ Authentication endpoint working\n";
    } else {
        echo "❌ Authentication endpoint error: " . ($authResponse['message'] ?? 'Unknown error') . "\n";
    }
}

