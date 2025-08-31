<?php
// Test script to check transaction API functionality
session_start();
require_once __DIR__ . '/app/config/Config.php';
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/controllers/TransactionController.php';

// Simulate a logged-in user session
$_SESSION['user_id'] = 1; // Assuming user ID 1 exists
$_SESSION['full_name'] = 'Test User';

echo "Testing Transaction API...\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . $_SESSION['user_id'] . "\n\n";

// Test the transaction controller directly
$controller = new TransactionController();

echo "Testing getHistory():\n";
$result = $controller->getHistory();
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "Testing getStats():\n";
$result = $controller->getStats();
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "Test completed.\n";
?>
