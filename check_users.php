<?php
/**
 * Check Existing Users
 * This script checks what users already exist in the database
 */

require_once 'app/config/Config.php';
require_once 'app/config/Database.php';

echo "B-Cash Existing Users Check\n";
echo "===========================\n\n";

try {
    $database = new Database();
    $connection = $database->connect();
    
    if (!$connection) {
        echo "✗ Database connection failed\n";
        exit;
    }
    
    echo "✓ Database connected successfully\n\n";
    
    // Check existing users
    echo "Existing users:\n";
    $stmt = $connection->query("SELECT id, phone_number, email, full_name, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Phone: {$user['phone_number']}, Email: {$user['email']}, Name: {$user['full_name']}, Created: {$user['created_at']}\n";
    }
    
    echo "\nTotal users: " . count($users) . "\n";
    
    // Check if the test phone number from registration exists
    $test_phone = '09123456789';
    $stmt = $connection->prepare("SELECT id, phone_number, email, full_name FROM users WHERE phone_number = ?");
    $stmt->execute([$test_phone]);
    
    if ($stmt->rowCount() > 0) {
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\n⚠ Phone number $test_phone already exists:\n";
        echo "  - ID: {$existing_user['id']}\n";
        echo "  - Email: {$existing_user['email']}\n";
        echo "  - Name: {$existing_user['full_name']}\n";
    } else {
        echo "\n✓ Phone number $test_phone is available\n";
    }
    
    // Check wallets
    echo "\nExisting wallets:\n";
    $stmt = $connection->query("SELECT id, user_id, account_number, balance FROM wallets ORDER BY id");
    $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($wallets as $wallet) {
        echo "- ID: {$wallet['id']}, User ID: {$wallet['user_id']}, Account: {$wallet['account_number']}, Balance: {$wallet['balance']}\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "User check completed!\n";
?> 