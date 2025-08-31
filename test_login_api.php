<?php
// Test script to check login functionality
session_start();
require_once __DIR__ . '/app/config/Config.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

// Simulate a POST request for login
$_SERVER['REQUEST_METHOD'] = 'POST';

// Create test data
$testData = [
    'phone_number' => '09123456789', // Replace with a valid phone number
    'password' => 'password123' // Replace with the correct password
];

// Convert data to JSON string for php://input
$jsonData = json_encode($testData);

// Set the content type and input data
$_SERVER['CONTENT_TYPE'] = 'application/json';
$inputStream = fopen('php://memory', 'r+');
fwrite($inputStream, $jsonData);
rewind($inputStream);

// Replace the input stream
stream_wrapper_unregister('php');
stream_wrapper_register('php', 'TestStream');
TestStream::$data = $jsonData;

$authController = new AuthController();
$result = $authController->login();

echo "Login Test Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT);

// Custom stream wrapper for testing
class TestStream {
    public static $data;
    public $position = 0;
    
    public function stream_open($path, $mode, $options, &$opened_path) {
        return true;
    }
    
    public function stream_read($count) {
        $ret = substr(self::$data, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    
    public function stream_eof() {
        return $this->position >= strlen(self::$data);
    }
    
    public function stream_stat() {
        return [];
    }
}
?>
