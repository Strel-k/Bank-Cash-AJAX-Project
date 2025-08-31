<?php
/**
 * Simple Database Test Script
 * This script tests the database connection and basic operations
 */

echo "B-Cash Database Test\n";
echo "===================\n\n";

// Test 1: Check if config file exists
echo "1. Checking config file... ";
if (file_exists('app/config/Config.php')) {
    echo "✓ Found\n";
} else {
    echo "✗ Missing\n";
    exit;
}

// Test 2: Check if database class exists
echo "2. Checking database class... ";
if (file_exists('app/config/Database.php')) {
    echo "✓ Found\n";
} else {
    echo "✗ Missing\n";
    exit;
}

// Test 3: Test database connection
echo "3. Testing database connection... ";
try {
    require_once 'app/config/Config.php';
    require_once 'app/config/Database.php';
    
    $database = new Database();
    $connection = $database->connect();
    
    if ($connection) {
        echo "✓ Connected successfully\n";
    } else {
        echo "✗ Connection failed\n";
        exit;
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit;
}

// Test 4: Test basic database operations
echo "4. Testing basic database operations... ";
try {
    // Test if we can query the database
    $stmt = $connection->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        echo "✓ Query successful\n";
    } else {
        echo "✗ Query failed\n";
        exit;
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit;
}

// Test 5: Check if tables exist
echo "5. Checking database tables... ";
try {
    $tables = ['users', 'wallets', 'transactions'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $connection->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "✓ All tables exist\n";
    } else {
        echo "⚠ Missing tables: " . implode(', ', $missing_tables) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 6: Test user model
echo "6. Testing user model... ";
try {
    require_once 'app/models/User.php';
    $user = new User();
    echo "✓ User model loaded\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 30) . "\n";
echo "Database test completed!\n";

if (isset($connection)) {
    echo "Database connection: ✓ Working\n";
    echo "Database name: " . Config::DB_NAME . "\n";
    echo "Database host: " . Config::DB_HOST . "\n";
} else {
    echo "Database connection: ✗ Failed\n";
}
?> 