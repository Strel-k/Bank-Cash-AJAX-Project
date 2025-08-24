<?php
require_once '../../app/controllers/VerificationController.php';

header('Content-Type: application/json');
// Allow credentials by setting specific origin - cannot use wildcard with credentials
$allowedOrigins = [
    'http://localhost:8080',
    'http://localhost:3000', // Added React development server
    'http://localhost',
    'http://127.0.0.1:8080',
    'http://127.0.0.1:3000', // Added React development server
    'http://127.0.0.1'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Always set a specific origin or none, never wildcard when credentials are allowed
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} else {
    // For non-allowed origins, don't set credentials header
    header('Access-Control-Allow-Origin: null');
}

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
