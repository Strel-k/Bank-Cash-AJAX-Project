<?php
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
SessionHelper::configureSession();

require_once __DIR__ . '/../app/config/Config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Debug session information
error_log("Index.php - Session ID: " . session_id());
error_log("Index.php - Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Index.php - Session full_name: " . (isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'not set'));
error_log("Index.php - Session status: " . session_status());
error_log("Index.php - All session data: " . print_r($_SESSION, true));

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    error_log("Index.php - User not authenticated, redirecting to login.php");
    header('Location: login.php');
    exit();
}

error_log("Index.php - User authenticated, proceeding to load page");
$authController = new AuthController();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Cash - Your Digital Wallet</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/auth.js"></script>
    <script src="js/wallet.js"></script>
    <script src="js/transaction.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-wallet"></i> B-Cash
            </div>
            <nav class="nav-links">
                <span style="color: var(--gcash-blue); margin-right: var(--spacing-md);">
                    <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                </span>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- Welcome Section -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Welcome to B-Cash</h1>
                <p class="text-center">Your secure digital wallet solution</p>
            </div>
        </div>

        <!-- Balance Display -->
        <div class="balance-card">
            <div class="balance-label">Available Balance</div>
            <div class="balance-amount" id="balanceDisplay">₱0.00</div>
            <button class="form-button mt-2" onclick="refreshBalance()" style="max-width: 200px; margin: 0 auto;">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div class="quick-actions">
                <button class="action-button" onclick="sendMoney()">
                    <div class="action-icon" style="color: var(--gcash-blue);">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="action-label">Send Money</div>
                </button>
                <button class="action-button" onclick="receiveMoney()">
                    <div class="action-icon" style="color: var(--gcash-green);">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="action-label">Receive</div>
                </button>
                <button class="action-button" onclick="addMoney()">
                    <div class="action-icon" style="color: var(--gcash-orange);">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-label">Add Money</div>
                </button>
                <button class="action-button" onclick="payBills()">
                    <div class="action-icon" style="color: var(--gcash-purple);">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="action-label">Pay Bills</div>
                </button>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Transactions</h2>
            </div>
            <div id="transactionHistory">
                <div class="transaction-list">
                    <div class="text-center" style="padding: var(--spacing-lg); color: #666;">
                        <i class="fas fa-history" style="font-size: 48px; margin-bottom: var(--spacing-md);"></i>
                        <p>No transactions yet</p>
                        <small>Start using B-Cash to see your transaction history</small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <!-- Send Money Modal -->
    <div id="sendMoneyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('sendMoneyModal')">&times;</span>
            <h2>Send Money</h2>
            <form id="sendMoneyForm">
                <label for="receiver_phone">Receiver Phone Number:</label>
                <input type="text" id="receiver_phone" name="receiver_phone" required>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>

                <label for="description">Description (optional):</label>
                <textarea id="description" name="description"></textarea>

                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <!-- Receive Money Modal -->
    <div id="receiveMoneyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('receiveMoneyModal')">&times;</span>
            <h2>Receive Money</h2>
            <p>Show your QR code or account number here for others to send money.</p>
            <div id="qrCodeContainer"></div>
            <p>Account Number: <span id="userAccountNumber"></span></p>
        </div>
    </div>

    <!-- Add Money Modal -->
    <div id="addMoneyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addMoneyModal')">&times;</span>
            <h2>Add Money</h2>
            <form id="addMoneyForm">
                <label for="add_amount">Amount:</label>
                <input type="number" id="add_amount" name="amount" min="0.01" step="0.01" required>
                
                <button type="submit">Add</button>
            </form>
        </div>
    </div>

    <!-- Pay Bills Modal -->
    <div id="payBillsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('payBillsModal')">&times;</span>
            <h2>Pay Bills</h2>
            <form id="payBillsForm">
                <label for="bill_account">Bill Account Number:</label>
                <input type="text" id="bill_account" name="bill_account" required>
                
                <label for="bill_amount">Amount:</label>
                <input type="number" id="bill_amount" name="amount" min="0.01" step="0.01" required>
                
                <button type="submit">Pay</button>
            </form>
        </div>
    </div>

    <script>
        // Initialize balance and transactions
        async function refreshBalance() {
            const balanceDisplay = document.getElementById('balanceDisplay');
            balanceDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            try {
                const result = await walletService.getBalance();
                if (result.success) {
                    balanceDisplay.textContent = `₱${parseFloat(result.data.balance).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                } else {
                    balanceDisplay.textContent = '₱0.00';
                    alert(result.message);
                }
            } catch (error) {
                balanceDisplay.textContent = '₱0.00';
                alert('Failed to load balance');
            }
        }

        async function loadTransactionHistory() {
            const transactionHistory = document.getElementById('transactionHistory');
            transactionHistory.innerHTML = '<div class="text-center" style="padding: var(--spacing-lg); color: #666;"><i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: var(--spacing-md);"></i><p>Loading transactions...</p></div>';
            
            try {
                const result = await transactionService.getTransactionHistory();
                if (result.success && result.data.transactions.length > 0) {
                    displayTransactions(result.data.transactions);
                } else {
                    transactionHistory.innerHTML = '<div class="text-center" style="padding: var(--spacing-lg); color: #666;"><i class="fas fa-history" style="font-size: 48px; margin-bottom: var(--spacing-md);"></i><p>No transactions yet</p><small>Start using B-Cash to see your transaction history</small></div>';
                }
            } catch (error) {
                transactionHistory.innerHTML = '<div class="text-center" style="padding: var(--spacing-lg); color: #666;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: var(--spacing-md);"></i><p>Failed to load transactions</p></div>';
            }
        }

        function displayTransactions(transactions) {
            const container = document.getElementById('transactionHistory');
            container.innerHTML = '';
            
            const transactionList = document.createElement('div');
            transactionList.className = 'transaction-list';
            
            transactions.forEach(transaction => {
                const transactionItem = document.createElement('div');
                transactionItem.className = 'transaction-item';
                
                const isSend = transaction.transaction_type === 'send';
                const amountClass = isSend ? 'transaction-amount negative' : 'transaction-amount positive';
                const icon = isSend ? 'fa-arrow-up' : 'fa-arrow-down';
                const typeText = isSend ? 'Sent' : 'Received';
                
                transactionItem.innerHTML = `
                    <div class="transaction-details">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="margin: 0; color: var(--text-primary);">${typeText}</h4>
                                <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">${transaction.description || 'Transaction'}</p>
                                <p style="margin: 0; color: var(--text-secondary); font-size: 0.75rem;">Ref: ${transaction.reference_number}</p>
                            </div>
                            <div class="${amountClass}" style="font-weight: 600;">
                                <i class="fas ${icon}"></i> ₱${parseFloat(transaction.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </div>
                        </div>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">
                            ${new Date(transaction.created_at).toLocaleDateString()} ${new Date(transaction.created_at).toLocaleTimeString()}
                        </p>
                    </div>
                `;
                
                transactionList.appendChild(transactionItem);
            });
            
            container.appendChild(transactionList);
        }

        function sendMoney() {
            openModal('sendMoneyModal');
        }

        function receiveMoney() {
            openModal('receiveMoneyModal');
        }

        function addMoney() {
            openModal('addMoneyModal');
        }

        function payBills() {
            openModal('payBillsModal');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            refreshBalance();
            loadTransactionHistory();
        });
    </script>
</body>
</html>
