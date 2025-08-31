<?php
// Debug version of verification API to identify the issue

// Start output buffering to prevent any unwanted output
ob_start();

// Set error reporting to prevent warnings from breaking JSON
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
    // Log the request for debugging
    error_log("Debug Verification API called");
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Request URI: " . $_SERVER['REQUEST_URI']);
    error_log("Action: " . ($_GET['action'] ?? 'none'));
    
    $action = $_GET['action'] ?? '';
    
    // Start session safely
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required',
            'debug' => [
                'session_id' => session_id(),
                'session_data' => $_SESSION,
                'action' => $action
            ]
        ]);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'upload-document':
            echo json_encode([
                'success' => true,
                'message' => 'Debug: Document upload endpoint reached',
                'debug' => [
                    'user_id' => $userId,
                    'files' => array_keys($_FILES),
                    'post_data' => array_keys($_POST),
                    'action' => $action
                ]
            ]);
            break;
            
        case 'upload-face':
            echo json_encode([
                'success' => true,
                'message' => 'Debug: Face upload endpoint reached',
                'debug' => [
                    'user_id' => $userId,
                    'files' => array_keys($_FILES),
                    'post_data' => array_keys($_POST),
                    'action' => $action
                ]
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'debug' => [
                    'action' => $action,
                    'available_actions' => ['upload-document', 'upload-face']
                ]
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Debug: Exception occurred',
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>