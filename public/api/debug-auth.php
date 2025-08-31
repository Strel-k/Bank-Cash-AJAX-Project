<?php
// Debug version of auth API to identify the issue

// Start output buffering to prevent any unwanted output
ob_start();

// Set error reporting to prevent warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once '../../app/controllers/AuthController.php';
require_once '../../app/helpers/Response.php';

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
    error_log("Debug Auth API called");
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Request URI: " . $_SERVER['REQUEST_URI']);
    error_log("Action: " . ($_GET['action'] ?? 'none'));
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'register':
            // Get the raw input
            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, true);
            
            error_log("Raw registration data: " . $rawData);
            error_log("Parsed registration data: " . json_encode($data));
            
            // Call the actual controller
            $controller = new AuthController();
            $controller->register();
            break;
            
        case 'check-session':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            echo json_encode([
                'success' => true,
                'debug' => [
                    'session_id' => session_id(),
                    'session_data' => $_SESSION,
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'full_name' => $_SESSION['full_name'] ?? null
                ]
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'debug' => [
                    'action' => $action,
                    'available_actions' => ['register', 'check-session']
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
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>