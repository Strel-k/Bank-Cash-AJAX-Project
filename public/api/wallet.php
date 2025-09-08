<?php
// Start output buffering FIRST to prevent any unwanted output
ob_start();

// Configure session with standardized settings BEFORE any output
require_once '../../app/helpers/SessionHelper.php';

// Start session explicitly to ensure session data is available
if (session_status() === PHP_SESSION_NONE) {
    SessionHelper::configureSession();
    session_start();
}

// Set error reporting to prevent warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session information
error_log("Wallet API - Session ID: " . session_id());
error_log("Wallet API - Session status: " . session_status());
error_log("Wallet API - Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Wallet API - All session data: " . print_r($_SESSION, true));
error_log("Wallet API - Cookies received: " . print_r($_COOKIE, true));

require_once __DIR__ . '/../../app/controllers/WalletController.php';
require_once __DIR__ . '/../../app/helpers/Response.php';

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
    $controller = new WalletController();
    $action = $_GET['action'] ?? '';

    error_log("Wallet API called with action: " . $action);

    switch ($action) {
        case 'balance':
            $controller->getBalance();
            break;
        case 'info':
            $controller->getWalletInfo();
            break;
        case 'search':
            $controller->searchAccount();
            break;
        case 'transfer':
            $controller->transferMoney();
            break;
        case 'addMoney':
            $controller->addMoney();
            break;
        case 'payBills':
            $controller->payBills();
            break;
        default:
            Response::error('Invalid action');
    }
} catch (Exception $e) {
    error_log("Exception in wallet.php: " . $e->getMessage());
    Response::error('An unexpected error occurred. Please try again.');
}
?>
