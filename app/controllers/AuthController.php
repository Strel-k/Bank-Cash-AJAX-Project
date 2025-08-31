<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Verification.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../services/IDVerificationService.php';
require_once __DIR__ . '/../services/FaceRecognitionService.php';

class AuthController {
    private $userModel;
    private $verificationModel;
    private $idVerificationService;
    private $faceRecognitionService;
    
    public function __construct() {
        $this->userModel = new User();
        $this->verificationModel = new Verification();
        $this->idVerificationService = new IDVerificationService();
        $this->faceRecognitionService = new FaceRecognitionService();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function register() {
        // Start output buffering to prevent any unwanted output
        ob_start();
        
        // Set error reporting to prevent warnings from breaking JSON
        error_reporting(0);
        ini_set('display_errors', 0);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }
        
        try {
            // Handle both JSON and form data
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $rawData = file_get_contents('php://input');
                $data = json_decode($rawData, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Response::error('Invalid JSON data provided');
                }
            } else {
                $data = $_POST;
            }
            
            // Debugging: Log incoming request data
            error_log("Incoming request data: " . json_encode($data)); // Debugging line
            
            // Validate required fields
            if (!isset($data['phone_number']) || !isset($data['password'])) {
                Response::error('Phone number and password are required');
            }
            
            $phone_number = trim($data['phone_number']);
            $email = isset($data['email']) ? trim($data['email']) : null;
            $full_name = isset($data['full_name']) ? trim($data['full_name']) : '';
            $password = $data['password'];
            $confirm_password = isset($data['confirm_password']) ? $data['confirm_password'] : null;
            $birthdate = isset($data['birthdate']) ? trim($data['birthdate']) : null;
            $address = isset($data['address']) ? trim($data['address']) : null;
            $gender = isset($data['gender']) ? trim($data['gender']) : null;
            
            // Validate password confirmation
            if ($confirm_password !== null && $password !== $confirm_password) {
                Response::error('Passwords do not match');
            }
            
            // Validate phone number
            if (!preg_match('/^09\d{9}$/', $phone_number)) {
                Response::error('Invalid phone number format. Must be 09XXXXXXXXX');
            }
            
            // Validate email if provided
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Response::error('Invalid email format');
            }
            
            // Validate password
            if (strlen($password) < 6) {
                Response::error('Password must be at least 6 characters');
            }
            
            // Validate birthdate if provided
            if ($birthdate) {
                $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
                if (!$birthdateObj || $birthdateObj->format('Y-m-d') !== $birthdate) {
                    Response::error('Invalid birthdate format');
                }
                
                // Check if user is at least 18 years old
                $today = new DateTime();
                $age = $today->diff($birthdateObj)->y;
                if ($age < 18) {
                    Response::error('You must be at least 18 years old to register');
                }
            }
            
            // Validate gender if provided
            if ($gender && !in_array($gender, ['male', 'female', 'other'])) {
                Response::error('Invalid gender selection');
            }
            
            // Extract verification data if provided
            $document_type = isset($data['document_type']) ? trim($data['document_type']) : null;
            $document_number = isset($data['document_number']) ? trim($data['document_number']) : null;
            
            $result = $this->userModel->register($phone_number, $email, $full_name, $password, $birthdate, $address, $gender);
            
            if ($result['success']) {
                $userId = $result['user_id'];
                
                // Start verification process if document data is provided
                // Set session variables for authentication
                $_SESSION['user_id'] = $userId;
                $_SESSION['full_name'] = $full_name;
                
                // Always create a verification request for document uploads
                $verificationResult = $this->startVerificationProcess(
                    $userId, 
                    $document_type ?: 'national_id', // Default to national_id if not provided
                    $document_number ?: 'PENDING'    // Default placeholder if not provided
                );
                
                error_log("AuthController: User registered with ID: $userId");
                error_log("AuthController: Verification result: " . json_encode($verificationResult));
                
                if ($verificationResult['success']) {
                    error_log("AuthController: Verification created successfully with ID: " . $verificationResult['verification_id']);
                    Response::success([
                        'user_id' => $userId,
                        'verification_id' => $verificationResult['verification_id']
                    ], 'Registration successful. Please complete verification.');
                } else {
                    error_log("AuthController: Verification creation failed: " . $verificationResult['message']);
                    
                    // Try to create verification directly as fallback
                    try {
                        $directResult = $this->verificationModel->createVerificationRequest(
                            $userId,
                            $document_type ?: 'national_id',
                            $document_number ?: 'PENDING'
                        );
                        
                        if ($directResult['success']) {
                            error_log("AuthController: Direct verification creation successful with ID: " . $directResult['verification_id']);
                            Response::success([
                                'user_id' => $userId,
                                'verification_id' => $directResult['verification_id']
                            ], 'Registration successful. Please complete verification.');
                        } else {
                            error_log("AuthController: Direct verification creation also failed: " . $directResult['message']);
                            Response::success([
                                'user_id' => $userId,
                                'verification_error' => $directResult['message']
                            ], 'Registration successful but verification setup failed. Please complete verification later.');
                        }
                    } catch (Exception $e) {
                        error_log("AuthController: Exception in direct verification creation: " . $e->getMessage());
                        Response::success([
                            'user_id' => $userId,
                            'verification_error' => 'Could not create verification record'
                        ], 'Registration successful but verification setup failed. Please complete verification later.');
                    }
                }
            } else {
                Response::error($result['message']);
            }
            
        } catch (Exception $e) {
            Response::error('An unexpected error occurred. Please try again.');
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
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['full_name'] = $result['user']['full_name'];
            
            Response::success([
                'user' => $result['user'],
                'token' => session_id()
            ], $result['message'] ?? 'Login successful');
        } else {
            Response::error($result['message'], 401);
        }
    }
    
    public function logout() {
        session_destroy();
        Response::success([], 'Logged out successfully');
    }
    
    /**
     * Start verification process for a user
     * @param int $userId
     * @param string $documentType
     * @param string $documentNumber
     * @return array
     */
    private function startVerificationProcess($userId, $documentType, $documentNumber) {
        try {
            // Validate document type
            $validTypes = ['passport', 'drivers_license', 'national_id', 'other'];
            if (!in_array($documentType, $validTypes)) {
                return ['success' => false, 'message' => 'Invalid document type'];
            }
            
            // Create verification request
            $result = $this->verificationModel->createVerificationRequest(
                $userId,
                $documentType,
                $documentNumber
            );
            
            if ($result['success']) {
                // Log the verification start
                $this->verificationModel->logVerificationAction(
                    $userId,
                    'verification_started',
                    [
                        'document_type' => $documentType,
                        'document_number' => $documentNumber
                    ]
                );
                
                return [
                    'success' => true,
                    'verification_id' => $result['verification_id'],
                    'message' => 'Verification process started successfully'
                ];
            } else {
                return ['success' => false, 'message' => $result['message']];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to start verification process'];
        }
    }
    
    public function checkAuth() {
        if (!isset($_SESSION['user_id']) || 
            $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_destroy();
            Response::unauthorized('Session validation failed');
        }
        
        Response::success([
            'user_id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['full_name']
        ]);
    }
}
?>
