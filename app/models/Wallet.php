<?php
require_once __DIR__ . '/../config/Database.php';

class Wallet {
    private $db;    
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        if ($this->db === null) {
            throw new Exception('Database connection failed');
        }
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
            if ($this->db === null) {
                throw new Exception('Database connection is null');
            }
            $stmt = $this->db->prepare("
                SELECT balance
                FROM wallets
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);

            $result = $stmt->fetch();
            return $result ? $result['balance'] : 0;

        } catch(PDOException $e) {
            error_log("PDO Error in getBalance: " . $e->getMessage());
            return 0;
        } catch(Exception $e) {
            error_log("Exception in getBalance: " . $e->getMessage());
            return 0;
        }
    }
    
    public function updateBalance($user_id, $amount, $operation = 'add', $use_transaction = true) {
        try {
            if ($use_transaction) {
                $this->db->beginTransaction();
            }
            
            // Get current balance
            $current_balance = $this->getBalance($user_id);
            
            // Calculate new balance
            if ($operation === 'add') {
                $new_balance = $current_balance + $amount;
            } else {
                $new_balance = $current_balance - $amount;
                
                // Check if sufficient balance
                if ($new_balance < 0) {
                    if ($use_transaction) {
                        $this->db->rollBack();
                    }
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
            
            if ($use_transaction) {
                $this->db->commit();
            }
            return ['success' => true, 'new_balance' => $new_balance];
            
        } catch(PDOException $e) {
            if ($use_transaction) {
                $this->db->rollBack();
            }
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
    
    public function getWalletByPhoneNumber($phone_number) {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, u.full_name 
                FROM wallets w
                JOIN users u ON w.user_id = u.id
                WHERE u.phone_number = ?
            ");
            $stmt->execute([$phone_number]);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return null;
        }
    }
    
    public function transferMoney($sender_id, $receiver_account, $amount, $description = '') {
        error_log("Wallet::transferMoney called - sender_id: $sender_id, receiver_account: $receiver_account, amount: $amount, description: $description");
        try {
            $this->db->beginTransaction();
            error_log("Database transaction started");

            // Get sender wallet
            $sender_wallet = $this->getWalletByUserId($sender_id);
            if (!$sender_wallet) {
                error_log("Sender wallet not found for user_id: $sender_id");
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Sender wallet not found'];
            }
            error_log("Sender wallet found: " . json_encode($sender_wallet));

            // Get receiver wallet
            // First try to find by account number
            $receiver_wallet = $this->getWalletByAccountNumber($receiver_account);
            // If not found, try to find by phone number
            if (!$receiver_wallet) {
                $receiver_wallet = $this->getWalletByPhoneNumber($receiver_account);
            }
            
            if (!$receiver_wallet) {
                error_log("Receiver wallet not found for account: $receiver_account");
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Receiver account not found'];
            }
            error_log("Receiver wallet found: " . json_encode($receiver_wallet));

            // Check if sender has sufficient balance
            if ($sender_wallet['balance'] < $amount) {
                error_log("Insufficient balance - sender balance: {$sender_wallet['balance']}, required: $amount");
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Insufficient balance'];
            }
            error_log("Balance check passed - sender balance: {$sender_wallet['balance']}, amount: $amount");

            // Generate unique reference number for sender transaction
            do {
                $sender_ref_number = 'TXN' . date('YmdHis') . uniqid(mt_rand(), true);
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM transactions WHERE reference_number = ?");
                $stmt->execute([$sender_ref_number]);
                $exists = $stmt->fetch()['count'] > 0;
            } while ($exists);

            // Generate unique reference number for receiver transaction
            do {
                $receiver_ref_number = 'TXN' . date('YmdHis') . uniqid(mt_rand(), true);
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM transactions WHERE reference_number = ?");
                $stmt->execute([$receiver_ref_number]);
                $exists = $stmt->fetch()['count'] > 0;
            } while ($exists);

            // Deduct from sender (without starting a new transaction)
            error_log("Deducting $amount from sender wallet");
            $result = $this->updateBalance($sender_id, $amount, 'subtract', false);
            if (!$result['success']) {
                error_log("Failed to deduct from sender: " . json_encode($result));
                $this->db->rollBack();
                return $result;
            }
            error_log("Sender balance updated successfully");

            // Add to receiver (without starting a new transaction)
            error_log("Adding $amount to receiver wallet");
            $result = $this->updateBalance($receiver_wallet['user_id'], $amount, 'add', false);
            if (!$result['success']) {
                error_log("Failed to add to receiver: " . json_encode($result));
                $this->db->rollBack();
                return $result;
            }
            error_log("Receiver balance updated successfully");

            // Record transaction for sender
            error_log("Recording sender transaction - ref: $sender_ref_number");
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'send', ?, ?)
            ");
            $stmt->execute([
                $sender_wallet['id'],
                $receiver_wallet['id'],
                $amount,
                $sender_ref_number,
                $description
            ]);
            error_log("Sender transaction recorded successfully");

            // Record transaction for receiver
            error_log("Recording receiver transaction - ref: $receiver_ref_number");
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'receive', ?, ?)
            ");
            $stmt->execute([
                $sender_wallet['id'],
                $receiver_wallet['id'],
                $amount,
                $receiver_ref_number,
                $description
            ]);
            error_log("Receiver transaction recorded successfully");

            error_log("Committing database transaction");
            $this->db->commit();
            error_log("Database transaction committed successfully");

            // Refresh sender wallet balance after commit
            $updated_sender_wallet = $this->getWalletByUserId($sender_id);
            error_log("Updated sender balance: {$updated_sender_wallet['balance']}");

            return [
                'success' => true,
                'message' => 'Transfer successful',
                'reference_number' => $sender_ref_number,
                'new_balance' => $updated_sender_wallet['balance']
            ];

        } catch(PDOException $e) {
            error_log("PDO Exception in transferMoney: " . $e->getMessage());
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()];
        }
    }

    public function addMoney($user_id, $amount) {
        try {
            $this->db->beginTransaction();

            // Get user wallet
            $wallet = $this->getWalletByUserId($user_id);
            if (!$wallet) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Wallet not found'];
            }

            // Generate unique reference number using uniqid for guaranteed uniqueness
            $reference_number = 'ADD' . date('YmdHis') . strtoupper(substr(uniqid(mt_rand(), true), -8));

            // Add to balance (without starting a new transaction)
            $result = $this->updateBalance($user_id, $amount, 'add', false);
            if (!$result['success']) {
                $this->db->rollBack();
                return $result;
            }

            // Record transaction
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'add_money', ?, 'Money added to wallet')
            ");
            $stmt->execute([
                $wallet['id'],
                $wallet['id'],
                $amount,
                $reference_number
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Money added successfully',
                'reference_number' => $reference_number,
                'new_balance' => $result['new_balance']
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Add money failed: ' . $e->getMessage()];
        }
    }

    public function payBills($user_id, $bill_account, $amount) {
        try {
            $this->db->beginTransaction();

            // Get user wallet
            $wallet = $this->getWalletByUserId($user_id);
            if (!$wallet) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Wallet not found'];
            }

            // Check if user has sufficient balance
            if ($wallet['balance'] < $amount) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Insufficient balance'];
            }

            // Generate unique reference number
            $reference_number = 'BILL' . date('YmdHis') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Deduct from balance (without starting a new transaction)
            $result = $this->updateBalance($user_id, $amount, 'subtract', false);
            if (!$result['success']) {
                $this->db->rollBack();
                return $result;
            }

            // Record transaction
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (sender_wallet_id, receiver_wallet_id, amount, transaction_type, reference_number, description)
                VALUES (?, ?, ?, 'pay_bills', ?, ?)
            ");
            $stmt->execute([
                $wallet['id'],
                null,
                $amount,
                $reference_number,
                "Bill payment to account: $bill_account"
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Bill payment successful',
                'reference_number' => $reference_number,
                'new_balance' => $result['new_balance']
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Bill payment failed: ' . $e->getMessage()];
        }
    }

    // Public getter for database connection (for debugging/testing purposes)
    public function getDbConnection() {
        return $this->db;
    }

    // Helper function to generate UUID v4 using PHP's com_create_guid if available, else fallback
    private function generateUUIDv4() {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        } else {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
        }
    }
}
?>
