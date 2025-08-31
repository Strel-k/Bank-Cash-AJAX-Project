<?php
// Test API endpoints to debug the registration issue

echo "<h1>API Endpoint Test</h1>";

// Test 1: Check if auth.php is accessible
echo "<h2>Test 1: Auth API Accessibility</h2>";
$authUrl = 'http://localhost/public/api/auth.php?action=register';
echo "Testing URL: $authUrl<br>";

// Test 2: Check if verification.php is accessible
echo "<h2>Test 2: Verification API Accessibility</h2>";
$verificationUrl = 'http://localhost/public/api/verification.php?action=upload-document';
echo "Testing URL: $verificationUrl<br>";

// Test 3: Check file paths
echo "<h2>Test 3: File Path Check</h2>";
$authFile = __DIR__ . '/public/api/auth.php';
$verificationFile = __DIR__ . '/public/api/verification.php';

echo "Auth file exists: " . (file_exists($authFile) ? 'YES' : 'NO') . " ($authFile)<br>";
echo "Verification file exists: " . (file_exists($verificationFile) ? 'YES' : 'NO') . " ($verificationFile)<br>";

// Test 4: Check .htaccess rules
echo "<h2>Test 4: .htaccess Rules</h2>";
$htaccessFile = __DIR__ . '/.htaccess';
$publicHtaccessFile = __DIR__ . '/public/.htaccess';

echo "Root .htaccess exists: " . (file_exists($htaccessFile) ? 'YES' : 'NO') . "<br>";
echo "Public .htaccess exists: " . (file_exists($publicHtaccessFile) ? 'YES' : 'NO') . "<br>";

// Test 5: Simulate a registration request
echo "<h2>Test 5: Simulate Registration Request</h2>";

// Create test data
$testData = [
    'full_name' => 'Test User',
    'phone_number' => '09123456789',
    'email' => 'test@example.com',
    'password' => 'testpass123',
    'birthdate' => '1990-01-01',
    'address' => 'Test Address',
    'gender' => 'male'
];

// Simulate the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/public/api/auth.php?action=register');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($testData))
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "cURL Error: " . ($error ? $error : 'None') . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Test 6: Check if it's returning HTML instead of JSON
echo "<h2>Test 6: Response Analysis</h2>";
if (strpos($response, '<!doctype') !== false || strpos($response, '<html') !== false) {
    echo "<strong style='color: red;'>ERROR: API is returning HTML instead of JSON!</strong><br>";
    echo "This suggests the request is not reaching the API endpoint correctly.<br>";
} else {
    echo "Response appears to be JSON (good)<br>";
}

// Test 7: Check server configuration
echo "<h2>Test 7: Server Configuration</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

?>