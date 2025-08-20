<?php
require_once '../../app/controllers/VerificationController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new VerificationController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'start':
        $controller->startVerification();
        break;
    case 'upload-document':
        $controller->uploadDocument();
        break;
    case 'upload-face':
        $controller->uploadFaceImage();
        break;
    case 'verify':
        $controller->performVerification();
        break;
    case 'status':
        $controller->getVerificationStatus();
        break;
    default:
        Response::error('Invalid action');
}
?>
