<?php
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../config/Config.php';

class WalletController {
    private $walletModel;

    public function __construct() {
        // Ensure session is configured
        SessionHelper::configureSession();

        try {
            $this->walletModel = new Wallet();
        } catch (Exception $e) {
            error_log("Wallet model initialization failed: " . $e->getMessage());
            Response::error('Database connection failed');
        }
    }
    
    private function checkAuth() {
        error_log("Session status: " . session_status());
        error_log("Session ID: " . session_id());

        $userId = SessionHelper::getCurrentUserId();
        error_log("Session user_id: " . ($userId ?: 'not set'));

        // If session user_id is not found, check for Authorization header token
        if (!$userId) {
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
                error_log("Token found in Authorization header: " . $token);

                // For now, assume token is session_id
                if (session_id() === $token) {
                    $userId = SessionHelper::getCurrentUserId();
                    error_log("Token matches session, user_id: " . ($userId ?: 'not set'));
                }
            }
        }

        if (!$userId) {
            Response::unauthorized('Authentication required');
        }
        return $userId;
    }
    
    public function getBalance() {
        try {
            error_log("getBalance called");
            $user_id = $this->checkAuth();
            error_log("User ID: " . $user_id);

            $balance = $this->walletModel->getBalance($user_id);
            error_log("Balance: " . $balance);

            Response::success(['balance' => $balance]);
        } catch (Exception $e) {
            error_log("Error in getBalance: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Response::error('An error occurred while fetching balance');
        }
    }
    
    public function getWalletInfo() {
        $user_id = $this->checkAuth();
        
        $wallet = $this->walletModel->getWalletByUserId($user_id);
        
        if (!$wallet) {
            Response::error('Wallet not found');
        }
        
        Response::success(['wallet' => $wallet]);
    }
    
    public function transferMoney() {
        $user_id = $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['receiver_account']) || !isset($data['amount'])) {
            Response::error('Receiver account and amount are required');
        }
        
        $receiver_account = trim($data['receiver_account']);
        $amount = floatval($data['amount']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        
        // Validate amount
        if ($amount <= 0) {
            Response::error('Amount must be greater than 0');
        }
        
        if ($amount > Config::MAX_TRANSFER_AMOUNT) {
            Response::error('Amount exceeds maximum limit');
        }
        
        // Check if receiver is not the same as sender
        $sender_wallet = $this->walletModel->getWalletByUserId($user_id);
        if ($sender_wallet['account_number'] === $receiver_account) {
            Response::error('Cannot transfer to your own account');
        }
        
        $result = $this->walletModel->transferMoney($user_id, $receiver_account, $amount, $description);
        
        if ($result['success']) {
            Response::success([
                'reference_number' => $result['reference_number'],
                'new_balance' => $result['new_balance']
            ], $result['message']);
        } else {
            Response::error($result['message']);
        }
    }
    
    public function searchAccount() {
        $user_id = $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Response::error('Method not allowed', 405);
        }

        $account_number = isset($_GET['account']) ? trim($_GET['account']) : '';

        if (empty($account_number)) {
            Response::error('Account number is required');
        }

        $wallet = $this->walletModel->getWalletByAccountNumber($account_number);

        if (!$wallet) {
            Response::error('Account not found');
        }

        // Don't return own account
        if ($wallet['user_id'] == $user_id) {
            Response::error('Cannot search own account');
        }

        Response::success([
            'account_number' => $wallet['account_number'],
            'full_name' => $wallet['full_name']
        ]);
    }

    public function addMoney() {
        $user_id = $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error('Invalid JSON data provided');
        }

        if (!isset($data['amount'])) {
            Response::error('Amount is required');
        }

        $amount = floatval($data['amount']);

        // Validate amount
        if ($amount <= 0) {
            Response::error('Amount must be greater than 0');
        }

        if ($amount > Config::MAX_ADD_MONEY_AMOUNT) {
            Response::error('Amount exceeds maximum limit');
        }

        $result = $this->walletModel->addMoney($user_id, $amount);

        if ($result['success']) {
            Response::success([
                'reference_number' => $result['reference_number'],
                'new_balance' => $result['new_balance']
            ], $result['message']);
        } else {
            Response::error($result['message']);
        }
    }

    public function payBills() {
        $user_id = $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['bill_account']) || !isset($data['amount'])) {
            Response::error('Bill account and amount are required');
        }

        $bill_account = trim($data['bill_account']);
        $amount = floatval($data['amount']);

        // Validate amount
        if ($amount <= 0) {
            Response::error('Amount must be greater than 0');
        }

        if ($amount > Config::MAX_BILL_PAYMENT_AMOUNT) {
            Response::error('Amount exceeds maximum limit');
        }

        $result = $this->walletModel->payBills($user_id, $bill_account, $amount);

        if ($result['success']) {
            Response::success([
                'reference_number' => $result['reference_number'],
                'new_balance' => $result['new_balance']
            ], $result['message']);
        } else {
            Response::error($result['message']);
        }
    }
}
?>
