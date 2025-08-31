<?php
/**
 * Run Verification Schema Script
 * 
 * This script executes the verification database schema
 * using the existing database connection
 */

echo "📊 Running Verification Database Schema...\n";
echo "==========================================\n\n";

// Load database configuration
require_once 'app/config/Database.php';

try {
    // Create database connection
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        echo "❌ Database connection failed\n";
        exit(1);
    }
    
    echo "✅ Connected to database: " . Config::DB_NAME . "\n";
    
    // Read the verification schema
    $schemaFile = 'database/verification_schema.sql';
    if (!file_exists($schemaFile)) {
        echo "❌ Schema file not found: $schemaFile\n";
        exit(1);
    }
    
    $schemaSQL = file_get_contents($schemaFile);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schemaSQL)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            echo "✅ Executed: " . substr($statement, 0, 50) . "...\n";
            $successCount++;
        } catch (PDOException $e) {
            // Check if it's a "table already exists" error
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠️  Table already exists: " . substr($statement, 0, 50) . "...\n";
                $successCount++;
            } else {
                echo "❌ Error executing: " . substr($statement, 0, 50) . "...\n";
                echo "   Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }
    
    echo "\n📊 Schema Execution Summary:\n";
    echo "===========================\n";
    echo "✅ Successful statements: $successCount\n";
    echo "❌ Errors: $errorCount\n";
    
    if ($errorCount === 0) {
        echo "\n🎉 Verification schema executed successfully!\n";
        echo "The verification tables are now ready for use.\n";
    } else {
        echo "\n⚠️  Some errors occurred during schema execution.\n";
        echo "Check the database manually if needed.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
