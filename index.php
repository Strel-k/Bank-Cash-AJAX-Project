<?php
session_start();
require_once 'app/config/Config.php';
require_once 'app/controllers/AuthController.php';

$authController = new AuthController();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Cash - Your Digital Wallet</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="public/js/auth.js"></script>
    <script src="public/js/wallet.js"></script>
    <script src="public/js/transaction.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-wallet"></i> B-Cash
            </div>
            <nav class="nav-links">
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
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

    <script>
        // Initialize balance and transactions
        function refreshBalance() {
            document.getElementById('balanceDisplay').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            setTimeout(() => {
                document.getElementById('balanceDisplay').textContent = '₱1,234.56';
            }, 1000);
        }

        function sendMoney() {
            alert('Send Money feature coming soon!');
        }

        function receiveMoney() {
            alert('Receive Money feature coming soon!');
        }

        function addMoney() {
            alert('Add Money feature coming soon!');
        }

        function payBills() {
            alert('Pay Bills feature coming soon!');
        }

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            refreshBalance();
        });
    </script>
</body>
</html>
