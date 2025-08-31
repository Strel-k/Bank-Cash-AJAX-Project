<?php
/**
 * Check Database Structure
 * This script checks the database structure to identify any issues
 */

require_once 'app/config/Config.php';
require_once 'app/config/Database.php';

echo "B-Cash Database Structure Check\n";
echo "==============================\n\n";

try {
    $database = new Database();
    $connection = $database->connect();
    
    if (!$connection) {
        echo "✗ Database connection failed\n";
        exit;
    }
    
    echo "✓ Database connected successfully\n\n";
    
    // Check users table structure
    echo "1. Checking users table structure...\n";
    $stmt = $connection->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Users table columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
    }
    
    // Check if required columns exist
    $required_columns = ['id', 'phone_number', 'email', 'full_name', 'password_hash'];
    $missing_columns = [];
    
    foreach ($required_columns as $col) {
        $found = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $col) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $missing_columns[] = $col;
        }
    }
    
    if (empty($missing_columns)) {
        echo "   ✓ All required columns exist\n";
    } else {
        echo "   ✗ Missing columns: " . implode(', ', $missing_columns) . "\n";
    }
    
    // Check wallets table
    echo "\n2. Checking wallets table structure...\n";
    $stmt = $connection->query("DESCRIBE wallets");
    $wallet_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Wallets table columns:\n";
    foreach ($wallet_columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
    }
    
    // Check sample data
    echo "\n3. Checking sample data...\n";
    $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   Users count: $user_count\n";
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM wallets");
    $wallet_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   Wallets count: $wallet_count\n";
    
    // Test a simple insert (without committing)
    echo "\n4. Testing insert operation...\n";
    try {
        $connection->beginTransaction();
        
        // Test if we can prepare and execute a simple insert
        $stmt = $connection->prepare("INSERT INTO users (phone_number, email, full_name, password_hash) VALUES (?, ?, ?, ?)");
        $test_phone = '09999999999';
        $test_email = 'test@test.com';
        $test_name = 'Test User';
        $test_password = password_hash('test123', PASSWORD_BCRYPT);
        
        $result = $stmt->execute([$test_phone, $test_email, $test_name, $test_password]);
        
        if ($result) {
            echo "   ✓ Insert test successful\n";
        } else {
            echo "   ✗ Insert test failed\n";
        }
        
        // Rollback the test insert
        $connection->rollBack();
        
    } catch (Exception $e) {
        echo "   ✗ Insert test failed: " . $e->getMessage() . "\n";
        $connection->rollBack();
    }
    
    // Check for any existing users with the test phone number
    echo "\n5. Checking for conflicts...\n";
    $stmt = $connection->prepare("SELECT id FROM users WHERE phone_number = ? OR email = ?");
    $stmt->execute(['09123456789', 'admin@bcash.com']);
    
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Sample users already exist (this is normal)\n";
    } else {
        echo "   ✓ No conflicts found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "Database structure check completed!\n";
?> 