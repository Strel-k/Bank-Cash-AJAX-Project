<?php
// Start output buffering FIRST to prevent any unwanted output
ob_start();

// Configure session with standardized settings BEFORE any output
require_once '../../app/helpers/SessionHelper.php';
SessionHelper::configureSession();

// Set error reporting to prevent warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session information
error_log("Auth API - Session ID: " . session_id());
error_log("Auth API - Session status: " . session_status());
error_log("Auth API - Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Auth API - All session data: " . print_r($_SESSION, true));
error_log("Auth API - Cookies received: " . print_r($_COOKIE, true));

require_once '../../app/controllers/AuthController.php';
require_once '../../app/helpers/Response.php';

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $controller = new AuthController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'register':
            $controller->register();
            break;
        case 'login':
            $controller->login();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'check':
            $controller->checkAuth();
            break;
        default:
            Response::error('Invalid action');
    }
} catch (Exception $e) {
    Response::error('An unexpected error occurred. Please try again.');
}
?>
