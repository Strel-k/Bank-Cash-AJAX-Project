<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['phone_number']) || !isset($data['password'])) {
            Response::error('Phone number and password are required');
        }
        
        $phone_number = trim($data['phone_number']);
        $email = isset($data['email']) ? trim($data['email']) : null;
        $full_name = isset($data['full_name']) ? trim($data['full_name']) : '';
        $password = $data['password'];
        
        // Validate phone number
        if (!preg_match('/^09\d{9}$/', $phone_number)) {
            Response::error('Invalid phone number format');
        }
        
        // Validate password
        if (strlen($password) < 6) {
            Response::error('Password must be at least 6 characters');
        }
        
        $result = $this->userModel->register($phone_number, $email, $full_name, $password);
        
        if ($result['success']) {
            Response::success(['user_id' => $result['user_id']], $result['message']);
        } else {
            Response::error($result['message']);
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['phone_number']) || !isset($data['password'])) {
            Response::error('Phone number and password are required');
        }
        
        $phone_number = trim($data['phone_number']);
        $password = $data['password'];
        
        $result = $this->userModel->login($phone_number, $password);
        
        if ($result['success']) {
            // Start session
            session_start();
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['full_name'] = $result['user']['full_name'];
            
            Response::success([
                'user' => $result['user'],
                'token' => session_id()
            ], $result['message']);
        } else {
            Response::error($result['message'], 401);
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        Response::success([], 'Logged out successfully');
    }
    
    public function checkAuth() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            Response::unauthorized('Not authenticated');
        }
        
        Response::success([
            'user_id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['full_name']
        ]);
    }
}
?>
