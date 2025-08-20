<?php
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../helpers/Response.php';

class WalletController {
    private $walletModel;
    
    public function __construct() {
        $this->walletModel = new Wallet();
    }
    
    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            Response::unauthorized('Authentication required');
        }
        return $_SESSION['user_id'];
    }
    
    public function getBalance() {
        $user_id = $this->checkAuth();
        
        $balance = $this->walletModel->getBalance($user_id);
        
        Response::success(['balance' => $balance]);
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
}
?>
