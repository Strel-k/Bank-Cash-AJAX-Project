<?php
require_once __DIR__ . '/../config/Database.php';

class Wallet {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getWalletByUserId($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, u.full_name 
                FROM wallets w
                JOIN users u ON w.user_id = u.id
                WHERE w.user_id = ?
            ");
            $stmt->execute([$user_id]);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return null;
        }
    }
    
    public function getBalance($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT balance 
                FROM wallets 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            
            $result = $stmt->fetch();
            return $result ? $result['balance'] : 0;
            
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    public function updateBalance($user_id, $amount, $operation = 'add') {
        try {
            $this->db->beginTransaction();
            
            // Get current balance
            $current_balance = $this->getBalance($user_id);
            
            // Calculate new balance
            if ($operation === 'add') {
                $new_balance = $current_balance + $amount;
            } else {
                $new_balance = $current_balance - $amount;
                
                // Check if sufficient balance
                if ($new_balance < 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Insufficient balance'];
                }
            }
            
            // Update balance
            $stmt = $this->db->prepare("
                UPDATE wallets 
                SET balance = ? 
                WHERE user_id = ?
            ");
            $stmt->execute([$new_balance, $user_id]);
            
            $this->db->commit();
            return ['success' => true, 'new_balance' => $new_balance];
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Balance update failed: ' . $e->getMessage()];
        }
    }
    
    public function getWalletByAccountNumber($account_number) {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, u.full_name 
                FROM wallets w
                JOIN users u ON w.user_id = u.id
                WHERE w.account_number = ?
            ");
            $stmt->execute([$account_number]);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return null;
        }
    }
    
    public function transferMoney($sender_id, $receiver_account, $amount, $description = '') {
        try {
            $this->db->beginTransaction();
            
            // Get sender wallet
            $sender_wallet = $this->getWalletByUserId($sender_id);
            if (!$sender_wallet) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Sender wallet not found'];
            }
            
            // Get receiver wallet
            $receiver_wallet = $this->getWalletByAccountNumber($receiver_account);
            if (!$receiver_wallet) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Receiver account not found'];
            }
            
            // Check if sender has sufficient balance
            if ($sender_wallet['balance'] < $amount) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Insufficient balance'];
            }
            
            // Generate unique reference number
            $reference_number = 'TXN' . date('YmdHis') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Deduct from sender
            $this->updateBalance($sender_id, $amount, 'subtract');
            
            // Add to receiver
            $this->updateBalance($receiver_wallet['user_id'], $amount, 'add');
            
            // Record transaction for sender
            $stmt = $this->db->prepare("
                INSERT INTO transactions 
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'send', ?, ?)
            ");
            $stmt->execute([
                $sender_wallet['id'],
                $receiver_wallet['id'],
                $amount,
                $reference_number,
                $description
            ]);
            
            // Record transaction for receiver
            $stmt = $this->db->prepare("
                INSERT INTO transactions 
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'receive', ?, ?)
            ");
            $stmt->execute([
                $sender_wallet['id'],
                $receiver_wallet['id'],
                $amount,
                $reference_number,
                $description
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Transfer successful',
                'reference_number' => $reference_number,
                'new_balance' => $sender_wallet['balance'] - $amount
            ];
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()];
        }
    }
}
?>
