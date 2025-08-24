<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Set error reporting to prevent warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
