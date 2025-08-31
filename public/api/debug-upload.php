<?php
// Debug endpoint to see what's being received in document uploads

// Start output buffering to prevent any unwanted output
ob_start();

// Set error reporting to show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Log everything we receive
    error_log("=== DEBUG UPLOAD ENDPOINT ===");
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Request URI: " . $_SERVER['REQUEST_URI']);
    error_log("Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
    error_log("POST data: " . json_encode($_POST));
    error_log("FILES data: " . json_encode($_FILES));
    error_log("Session data: " . json_encode($_SESSION));
    error_log("Action: " . ($_GET['action'] ?? 'none'));
    
    $action = $_GET['action'] ?? '';
    
    $response = [
        'success' => true,
        'message' => 'Debug endpoint reached successfully',
        'debug' => [
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'action' => $action,
            'post_data' => $_POST,
            'files_received' => [],
            'session_data' => $_SESSION,
            'verification_id_received' => $_POST['verification_id'] ?? null,
            'user_id_from_session' => $_SESSION['user_id'] ?? null
        ]
    ];
    
    // Process files
    foreach ($_FILES as $key => $file) {
        $response['debug']['files_received'][$key] = [
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'],
            'error' => $file['error'],
            'tmp_name' => $file['tmp_name']
        ];
    }
    
    // Check specific requirements for document upload
    if ($action === 'upload-document') {
        $response['debug']['upload_document_check'] = [
            'has_document_file' => isset($_FILES['document']),
            'has_verification_id' => isset($_POST['verification_id']),
            'has_side' => isset($_POST['side']),
            'has_document_type' => isset($_POST['document_type']),
            'has_document_number' => isset($_POST['document_number']),
            'user_authenticated' => isset($_SESSION['user_id'])
        ];
        
        // Simulate the actual validation
        if (!isset($_FILES['document'])) {
            $response['debug']['validation_errors'][] = 'Document file is missing';
        }
        
        if (!isset($_POST['verification_id']) || empty($_POST['verification_id'])) {
            $response['debug']['validation_errors'][] = 'Verification ID is missing or empty';
        }
        
        if (!isset($_SESSION['user_id'])) {
            $response['debug']['validation_errors'][] = 'User not authenticated (no session)';
        }
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Debug: Exception occurred',
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ], JSON_PRETTY_PRINT);
}
?>