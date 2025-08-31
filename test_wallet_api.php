<?php
// Test script to check wallet API functionality
session_start();
require_once __DIR__ . '/app/config/Config.php';
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/controllers/WalletController.php';

// Simulate a logged-in user session
$_SESSION['user_id'] = 1; // Assuming user ID 1 exists
$_SESSION['full_name'] = 'Test User';

echo "Testing Wallet API...\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . $_SESSION['user_id'] . "\n\n";

// Test the wallet controller directly
$controller = new WalletController();

echo "Testing getBalance():\n";
$result = $controller->getBalance();
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "Testing getWalletInfo():\n";
$result = $controller->getWalletInfo();
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "Test completed.\n";
?>
