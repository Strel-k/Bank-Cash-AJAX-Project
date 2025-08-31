<?php
/**
 * Test Registration API
 * This script tests the registration API directly
 */

echo "Testing Registration API\n";
echo "=======================\n\n";

// Test data
$test_data = [
    'phone_number' => '09999999999',
    'email' => 'test999@example.com',
    'full_name' => 'Test User 999',
    'password' => 'password123'
];

echo "Test data:\n";
foreach ($test_data as $key => $value) {
    echo "- $key: $value\n";
}

echo "\nMaking API request...\n";

// Make the API request
$url = 'http://localhost:8000/api/auth.php?action=register';
$data = json_encode($test_data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response: $response\n";
    
    $result = json_decode($response, true);
    if ($result) {
        echo "\nDecoded response:\n";
        echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
        if (isset($result['message'])) {
            echo "Message: " . $result['message'] . "\n";
        }
        if (isset($result['data'])) {
            echo "Data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "Failed to decode JSON response\n";
    }
}

echo "\nTest completed!\n";
?> 