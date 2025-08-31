<?php
/**
 * B-Cash AJAX System Test Script
 * This script tests all major components of the system
 */

echo "B-Cash AJAX System Test\n";
echo "=======================\n\n";

$tests_passed = 0;
$tests_total = 0;

function run_test($test_name, $test_function) {
    global $tests_passed, $tests_total;
    $tests_total++;
    
    echo "Testing: $test_name... ";
    
    try {
        $result = $test_function();
        if ($result) {
            echo "✓ PASSED\n";
            $tests_passed++;
        } else {
            echo "✗ FAILED\n";
        }
    } catch (Exception $e) {
        echo "✗ FAILED: " . $e->getMessage() . "\n";
    }
}

// Test 1: Check PHP version
run_test("PHP Version", function() {
    return version_compare(PHP_VERSION, '7.4.0', '>=');
});

// Test 2: Check required extensions
run_test("Required Extensions", function() {
    $required = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            throw new Exception("Missing extension: $ext");
        }
    }
    return true;
});

// Test 3: Check file structure
run_test("File Structure", function() {
    $required_files = [
        'app/config/Config.php',
        'app/config/Database.php',
        'app/controllers/AuthController.php',
        'app/models/User.php',
        'app/helpers/Response.php',
        'public/index.php',
        'public/login.php',
        'public/register.php'
    ];
    
    foreach ($required_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Missing file: $file");
        }
    }
    return true;
});

// Test 4: Check database configuration
run_test("Database Configuration", function() {
    if (!file_exists('app/config/Config.php')) {
        throw new Exception("Config file not found");
    }
    
    require_once 'app/config/Config.php';
    
    if (!defined('Config::DB_HOST') || !defined('Config::DB_NAME')) {
        throw new Exception("Database constants not defined");
    }
    
    return true;
});

// Test 5: Test database connection
run_test("Database Connection", function() {
    require_once 'app/config/Database.php';
    
    $database = new Database();
    $connection = $database->connect();
    
    if (!$connection) {
        throw new Exception("Database connection failed");
    }
    
    return true;
});

// Test 6: Test user model
run_test("User Model", function() {
    require_once 'app/models/User.php';
    
    $user = new User();
    
    // Test if we can create a user instance
    if (!$user) {
        throw new Exception("Could not create User instance");
    }
    
    return true;
});

// Test 7: Test wallet model
run_test("Wallet Model", function() {
    require_once 'app/models/Wallet.php';
    
    $wallet = new Wallet();
    
    if (!$wallet) {
        throw new Exception("Could not create Wallet instance");
    }
    
    return true;
});

// Test 8: Test transaction model
run_test("Transaction Model", function() {
    require_once 'app/models/Transaction.php';
    
    $transaction = new Transaction();
    
    if (!$transaction) {
        throw new Exception("Could not create Transaction instance");
    }
    
    return true;
});

// Test 9: Check API endpoints
run_test("API Endpoints", function() {
    $api_files = [
        'public/api/auth.php',
        'public/api/wallet.php',
        'public/api/transaction.php',
        'public/api/verification.php'
    ];
    
    foreach ($api_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Missing API file: $file");
        }
    }
    
    return true;
});

// Test 10: Check JavaScript files
run_test("JavaScript Files", function() {
    $js_files = [
        'public/js/auth.js',
        'public/js/wallet.js',
        'public/js/transaction.js',
        'public/js/register-verification.js'
    ];
    
    foreach ($js_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Missing JS file: $file");
        }
    }
    
    return true;
});

// Test 11: Check CSS files
run_test("CSS Files", function() {
    $css_files = [
        'css/styles.css',
        'css/verification.css'
    ];
    
    foreach ($css_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Missing CSS file: $file");
        }
    }
    
    return true;
});

// Test 12: Check upload directories
run_test("Upload Directories", function() {
    $dirs = ['uploads', 'uploads/verification'];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            // Try to create directory
            if (!mkdir($dir, 0755, true)) {
                throw new Exception("Could not create directory: $dir");
            }
        }
        
        if (!is_writable($dir)) {
            throw new Exception("Directory not writable: $dir");
        }
    }
    
    return true;
});

// Test 13: Test response helper
run_test("Response Helper", function() {
    require_once 'app/helpers/Response.php';
    
    // Test if Response class exists
    if (!class_exists('Response')) {
        throw new Exception("Response class not found");
    }
    
    return true;
});

// Test 14: Test session functionality
run_test("Session Functionality", function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['test'] = 'test_value';
    $result = $_SESSION['test'] === 'test_value';
    unset($_SESSION['test']);
    
    return $result;
});

// Test 15: Test JSON functionality
run_test("JSON Functionality", function() {
    $test_data = ['test' => 'value', 'number' => 123];
    $json = json_encode($test_data);
    $decoded = json_decode($json, true);
    
    return $decoded === $test_data;
});

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test Results: $tests_passed/$tests_total tests passed\n";

if ($tests_passed === $tests_total) {
    echo "🎉 All tests passed! The system is ready to use.\n";
    echo "\nNext steps:\n";
    echo "1. Point your web server to the 'public' directory\n";
    echo "2. Access the application at your configured URL\n";
    echo "3. Test with sample accounts:\n";
    echo "   - Admin: 09123456789 / password\n";
    echo "   - User: 09187654321 / password\n";
} else {
    echo "⚠ Some tests failed. Please check the errors above.\n";
    echo "\nCommon issues:\n";
    echo "- Database connection problems\n";
    echo "- Missing PHP extensions\n";
    echo "- File permissions issues\n";
    echo "- Configuration errors\n";
}

echo "\nFor more information, see README.md\n";
?> 