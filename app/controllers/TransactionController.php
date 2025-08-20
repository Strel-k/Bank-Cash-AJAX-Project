<?php
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../helpers/Response.php';

class TransactionController {
    private $transactionModel;
    
    public function __construct() {
        $this->transactionModel = new Transaction();
    }
    
    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            Response::unauthorized('Authentication required');
        }
        return $_SESSION['user_id'];
    }
    
    public function getHistory() {
        $user_id = $this->checkAuth();
        
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $transactions = $this->transactionModel->getTransactionHistory($user_id, $limit, $offset);
        
        Response::success(['transactions' => $transactions]);
    }
    
    public function getStats() {
        $user_id = $this->checkAuth();
        
        $stats = $this->transactionModel->getTransactionStats($user_id);
        
        Response::success(['stats' => $stats]);
    }
    
    public function searchTransactions() {
        $user_id = $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Response::error('Method not allowed', 405);
        }
        
        $search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
        
        if (empty($search_term)) {
            Response::error('Search term is required');
        }
        
        $transactions = $this->transactionModel->searchTransactions($user_id, $search_term, $limit);
        
        Response::success(['transactions' => $transactions]);
    }
    
    public function getTransactionByReference() {
        $user_id = $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Response::error('Method not allowed', 405);
        }
        
        $reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';
        
        if (empty($reference)) {
            Response::error('Reference number is required');
        }
        
        $transaction = $this->transactionModel->getTransactionByReference($reference);
        
        if (!$transaction) {
            Response::error('Transaction not found');
        }
        
        // Verify user owns this transaction
        $wallet = new Wallet();
        $user_wallet = $wallet->getWalletByUserId($user_id);
        
        if ($transaction['sender_wallet_id'] != $user_wallet['id'] && 
            $transaction['receiver_wallet_id'] != $user_wallet['id']) {
            Response::error('Access denied');
        }
        
        Response::success(['transaction' => $transaction]);
    }
}
?>
