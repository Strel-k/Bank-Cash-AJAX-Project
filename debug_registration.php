<?php
/**
 * Debug Registration Process
 * This script tests the registration process step by step
 */

echo "B-Cash Registration Debug\n";
echo "========================\n\n";

// Test 1: Check if all required files exist
echo "1. Checking required files...\n";
$required_files = [
    'app/config/Config.php',
    'app/config/Database.php',
    'app/models/User.php',
    'app/helpers/Response.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file\n";
        exit;
    }
}

// Test 2: Test database connection
echo "\n2. Testing database connection...\n";
try {
    require_once 'app/config/Config.php';
    require_once 'app/config/Database.php';
    
    $database = new Database();
    $connection = $database->connect();
    
    if ($connection) {
        echo "   ✓ Database connected successfully\n";
    } else {
        echo "   ✗ Database connection failed\n";
        exit;
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    exit;
}

// Test 3: Test User model
echo "\n3. Testing User model...\n";
try {
    require_once 'app/models/User.php';
    $userModel = new User();
    echo "   ✓ User model created successfully\n";
} catch (Exception $e) {
    echo "   ✗ User model error: " . $e->getMessage() . "\n";
    exit;
}

// Test 4: Test registration with unique data
echo "\n4. Testing registration with unique data...\n";
try {
    // Generate unique test data
    $timestamp = time();
    $test_phone = '09' . $timestamp;
    $test_email = 'test' . $timestamp . '@example.com';
    $test_name = 'Test User ' . $timestamp;
    $test_password = 'password123';
    
    echo "   Test data:\n";
    echo "   - Phone: $test_phone\n";
    echo "   - Email: $test_email\n";
    echo "   - Name: $test_name\n";
    
    // Check if user already exists
    $stmt = $connection->prepare("SELECT id FROM users WHERE phone_number = ? OR email = ?");
    $stmt->execute([$test_phone, $test_email]);
    
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ User already exists with this data\n";
    } else {
        echo "   ✓ Test data is unique\n";
    }
    
    // Test registration
    $result = $userModel->register($test_phone, $test_email, $test_name, $test_password);
    
    if ($result['success']) {
        echo "   ✓ Registration successful! User ID: " . $result['user_id'] . "\n";
        
        // Clean up test user
        $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$result['user_id']]);
        echo "   ✓ Test user cleaned up\n";
    } else {
        echo "   ✗ Registration failed: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Registration test error: " . $e->getMessage() . "\n";
}

// Test 5: Check database tables
echo "\n5. Checking database tables...\n";
try {
    $tables = ['users', 'wallets', 'transactions'];
    foreach ($tables as $table) {
        $stmt = $connection->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   - $table: $count records\n";
    }
} catch (Exception $e) {
    echo "   ✗ Table check error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "Debug completed!\n";
?> 