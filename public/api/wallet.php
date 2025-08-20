<?php
require_once '../../app/controllers/WalletController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new WalletController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'balance':
        $controller->getBalance();
        break;
    case 'info':
        $controller->getWalletInfo();
        break;
    case 'transfer':
        $controller->transferMoney();
        break;
    case 'search':
        $controller->searchAccount();
        break;
    default:
        Response::error('Invalid action');
}
?>
