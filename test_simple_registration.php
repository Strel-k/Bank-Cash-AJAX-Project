<?php
// Simple test to check registration and verification creation

require_once 'app/models/User.php';
require_once 'app/models/Verification.php';
require_once 'app/config/Database.php';

echo "<h1>Simple Registration Test</h1>";

try {
    // Create models
    $userModel = new User();
    $verificationModel = new Verification();
    
    echo "<h2>1. Testing User Registration</h2>";
    
    // Generate unique phone number for testing
    $phoneNumber = '0912' . rand(1000000, 9999999);
    $email = 'test' . rand(1000, 9999) . '@example.com';
    
    echo "Testing with phone: $phoneNumber<br>";
    echo "Testing with email: $email<br>";
    
    // Register user
    $result = $userModel->register(
        $phoneNumber,
        $email,
        'Test User',
        'testpass123',
        '1990-01-01',
        'Test Address',
        'male'
    );
    
    if ($result['success']) {
        $userId = $result['user_id'];
        echo "✓ User registered successfully with ID: $userId<br>";
        
        echo "<h2>2. Testing Verification Creation</h2>";
        
        // Create verification request
        $verificationResult = $verificationModel->createVerificationRequest(
            $userId,
            'national_id',
            'TEST123456789'
        );
        
        if ($verificationResult['success']) {
            $verificationId = $verificationResult['verification_id'];
            echo "✓ Verification created successfully with ID: $verificationId<br>";
            
            echo "<h2>3. Testing Verification Retrieval</h2>";
            
            // Get verification status
            $status = $verificationModel->getVerificationStatus($userId);
            if ($status) {
                echo "✓ Verification status retrieved<br>";
                echo "Verification ID: " . $status['id'] . "<br>";
                echo "Status: " . $status['verification_status'] . "<br>";
                echo "Document Type: " . $status['id_document_type'] . "<br>";
                echo "Document Number: " . $status['id_document_number'] . "<br>";
                
                echo "<h2>4. Complete Registration Response Simulation</h2>";
                
                // Simulate what the AuthController should return
                $responseData = [
                    'success' => true,
                    'data' => [
                        'user_id' => $userId,
                        'verification_id' => $verificationId
                    ],
                    'message' => 'Registration successful. Please complete verification.'
                ];
                
                echo "Expected response:<br>";
                echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
                
            } else {
                echo "✗ Failed to retrieve verification status<br>";
            }
            
        } else {
            echo "✗ Failed to create verification<br>";
            echo "Error: " . $verificationResult['message'] . "<br>";
        }
        
    } else {
        echo "✗ User registration failed<br>";
        echo "Error: " . $result['message'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>