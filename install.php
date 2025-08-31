<?php
/**
 * B-Cash AJAX Installation Script
 * This script helps set up the B-Cash system
 */

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('B-Cash requires PHP 7.4 or higher. Current version: ' . PHP_VERSION);
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    die('Missing required PHP extensions: ' . implode(', ', $missing_extensions));
}

// Check if already installed
if (file_exists('app/config/Config.php') && file_exists('database/setup.sql')) {
    echo "B-Cash appears to be already installed.\n";
    echo "If you need to reinstall, please remove the existing files first.\n";
    exit;
}

echo "B-Cash AJAX Installation Script\n";
echo "===============================\n\n";

// Get database configuration
echo "Database Configuration:\n";
echo "Database Host (default: localhost): ";
$db_host = trim(fgets(STDIN)) ?: 'localhost';

echo "Database Name (default: b_cash_ajax): ";
$db_name = trim(fgets(STDIN)) ?: 'b_cash_ajax';

echo "Database Username: ";
$db_user = trim(fgets(STDIN));

echo "Database Password: ";
$db_pass = trim(fgets(STDIN));

echo "Application URL (default: http://localhost): ";
$app_url = trim(fgets(STDIN)) ?: 'http://localhost';

echo "\nTesting database connection...\n";

try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Create database
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "✓ Database '$db_name' created/verified\n";
    
    $pdo->exec("USE `$db_name`");
} catch (PDOException $e) {
    die("✗ Failed to create database: " . $e->getMessage() . "\n");
}

// Import schema
echo "Importing database schema...\n";
try {
    $sql = file_get_contents('database/setup.sql');
    if (!$sql) {
        throw new Exception("Could not read database/setup.sql");
    }
    
    // Remove CREATE DATABASE and USE statements from setup.sql
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE.*?;/i', '', $sql);
    
    $pdo->exec($sql);
    echo "✓ Database schema imported successfully\n";
} catch (Exception $e) {
    die("✗ Failed to import schema: " . $e->getMessage() . "\n");
}

// Update configuration file
echo "Updating configuration...\n";
try {
    $config_content = file_get_contents('app/config/Config.php');
    if (!$config_content) {
        throw new Exception("Could not read app/config/Config.php");
    }
    
    $config_content = str_replace(
        "const DB_HOST = 'localhost';",
        "const DB_HOST = '$db_host';",
        $config_content
    );
    
    $config_content = str_replace(
        "const DB_NAME = 'b_cash_ajax';",
        "const DB_NAME = '$db_name';",
        $config_content
    );
    
    $config_content = str_replace(
        "const DB_USER = 'root';",
        "const DB_USER = '$db_user';",
        $config_content
    );
    
    $config_content = str_replace(
        "const DB_PASS = '';",
        "const DB_PASS = '$db_pass';",
        $config_content
    );
    
    $config_content = str_replace(
        "const APP_URL = 'http://localhost/b-cash';",
        "const APP_URL = '$app_url';",
        $config_content
    );
    
    file_put_contents('app/config/Config.php', $config_content);
    echo "✓ Configuration updated successfully\n";
} catch (Exception $e) {
    die("✗ Failed to update configuration: " . $e->getMessage() . "\n");
}

// Create upload directories
echo "Creating upload directories...\n";
try {
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    if (!is_dir('uploads/verification')) {
        mkdir('uploads/verification', 0755, true);
    }
    echo "✓ Upload directories created\n";
} catch (Exception $e) {
    echo "⚠ Warning: Could not create upload directories: " . $e->getMessage() . "\n";
}

// Test the system
echo "\nTesting system...\n";
try {
    require_once 'app/config/Config.php';
    require_once 'app/config/Database.php';
    
    $database = new Database();
    $connection = $database->connect();
    
    if ($connection) {
        echo "✓ System test successful\n";
    } else {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    die("✗ System test failed: " . $e->getMessage() . "\n");
}

echo "\n🎉 B-Cash AJAX Installation Complete!\n\n";
echo "Next Steps:\n";
echo "1. Point your web server to the 'public' directory\n";
echo "2. Access the application at: $app_url\n";
echo "3. Test with sample accounts:\n";
echo "   - Admin: 09123456789 / password\n";
echo "   - User: 09187654321 / password\n\n";
echo "For more information, see README.md\n";
echo "Happy coding! 🚀\n";
?> 