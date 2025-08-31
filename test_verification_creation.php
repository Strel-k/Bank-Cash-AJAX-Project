<?php
// Test verification creation directly

require_once 'app/models/Verification.php';
require_once 'app/config/Database.php';

echo "<h1>Verification Creation Test</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    $database = new Database();
    $db = $database->connect();
    echo "✓ Database connection successful<br>";
    
    // Test verification model
    echo "<h2>2. Testing Verification Model</h2>";
    $verification = new Verification();
    echo "✓ Verification model created<br>";
    
    // Test creating a verification request
    echo "<h2>3. Testing Verification Creation</h2>";
    $userId = 999; // Test user ID
    $documentType = 'national_id';
    $documentNumber = 'TEST123456789';
    
    $result = $verification->createVerificationRequest($userId, $documentType, $documentNumber);
    
    if ($result['success']) {
        echo "✓ Verification created successfully<br>";
        echo "Verification ID: " . $result['verification_id'] . "<br>";
        
        // Test getting verification status
        echo "<h2>4. Testing Verification Status Retrieval</h2>";
        $status = $verification->getVerificationStatus($userId);
        if ($status) {
            echo "✓ Verification status retrieved<br>";
            echo "Status: " . $status['verification_status'] . "<br>";
            echo "Document Type: " . $status['id_document_type'] . "<br>";
            echo "Document Number: " . $status['id_document_number'] . "<br>";
        } else {
            echo "✗ Failed to retrieve verification status<br>";
        }
        
    } else {
        echo "✗ Failed to create verification<br>";
        echo "Error: " . $result['message'] . "<br>";
    }
    
    // Test user_verification table structure
    echo "<h2>5. Testing Table Structure</h2>";
    $stmt = $db->query("DESCRIBE user_verification");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "user_verification table columns:<br>";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
    
    // Check if table exists and has data
    echo "<h2>6. Testing Table Data</h2>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM user_verification");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Records in user_verification table: " . $count['count'] . "<br>";
    
    // Show recent records
    $stmt = $db->query("SELECT * FROM user_verification ORDER BY created_at DESC LIMIT 5");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($records) {
        echo "<h3>Recent verification records:</h3>";
        foreach ($records as $record) {
            echo "ID: {$record['id']}, User ID: {$record['user_id']}, Status: {$record['verification_status']}, Created: {$record['created_at']}<br>";
        }
    } else {
        echo "No verification records found<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>