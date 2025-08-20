<?php
require_once '../../app/controllers/TransactionController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new TransactionController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'history':
        $controller->getHistory();
        break;
    case 'stats':
        $controller->getStats();
        break;
    case 'search':
        $controller->searchTransactions();
        break;
    case 'reference':
        $controller->getTransactionByReference();
        break;
    default:
        Response::error('Invalid action');
}
?>
