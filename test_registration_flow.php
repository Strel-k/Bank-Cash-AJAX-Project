<?php
// Test the complete registration flow

require_once 'app/controllers/AuthController.php';
require_once 'app/helpers/Response.php';

echo "<h1>Registration Flow Test</h1>";

// Simulate a registration request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Test data
$testData = [
    'full_name' => 'Test User',
    'phone_number' => '09123456789',
    'email' => 'test@example.com',
    'password' => 'testpass123',
    'birthdate' => '1990-01-01',
    'address' => 'Test Address',
    'gender' => 'male',
    'document_type' => 'national_id',
    'document_number' => 'TEST123456789'
];

echo "<h2>1. Test Data</h2>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

// Simulate the request
echo "<h2>2. Simulating Registration Request</h2>";

// Capture the output
ob_start();

try {
    // Mock the input
    $GLOBALS['mock_input'] = json_encode($testData);
    
    // Override file_get_contents for php://input
    function file_get_contents($filename) {
        if ($filename === 'php://input') {
            return $GLOBALS['mock_input'];
        }
        return call_user_func_array('file_get_contents', func_get_args());
    }
    
    $controller = new AuthController();
    $controller->register();
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

$output = ob_get_clean();

echo "<h2>3. Registration Response</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to parse as JSON
$response = json_decode($output, true);
if ($response) {
    echo "<h2>4. Parsed Response</h2>";
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    
    if (isset($response['success']) && $response['success']) {
        echo "<h3>✓ Registration Successful</h3>";
        if (isset($response['data']['verification_id'])) {
            echo "✓ Verification ID returned: " . $response['data']['verification_id'] . "<br>";
        } else {
            echo "✗ No verification ID in response<br>";
            echo "Response data keys: " . implode(', ', array_keys($response['data'] ?? [])) . "<br>";
        }
    } else {
        echo "<h3>✗ Registration Failed</h3>";
        echo "Error: " . ($response['message'] ?? 'Unknown error') . "<br>";
    }
} else {
    echo "<h2>4. Failed to Parse Response as JSON</h2>";
    echo "Raw output length: " . strlen($output) . " characters<br>";
    if (strlen($output) > 0) {
        echo "First 200 characters: " . htmlspecialchars(substr($output, 0, 200)) . "<br>";
    }
}

// Check session
echo "<h2>5. Session Check</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "User ID in session: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
echo "Full name in session: " . ($_SESSION['full_name'] ?? 'Not set') . "<br>";
?>