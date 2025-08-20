<?php
session_start();
require_once 'app/config/Config.php';
require_once 'app/controllers/AuthController.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - B-Cash</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-wallet"></i> B-Cash
            </div>
            <nav class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="fas fa-user-circle" style="font-size: 48px; color: var(--gcash-blue); margin-bottom: var(--spacing-md);"></i>
                    <h1 class="card-title">Welcome Back</h1>
                    <p style="color: #666;">Sign in to your B-Cash account</p>
                </div>
                
                <form id="loginForm" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="phone_number">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" 
                               class="form-input" 
                               name="phone_number" 
                               placeholder="09XX XXX XXXX" 
                               required 
                               pattern="[0-9]{11}">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" 
                               class="form-input" 
                               name="password" 
                               placeholder="Enter your password" 
                               required>
                    </div>
                    
                    <button type="submit" class="form-button">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <div class="text-center" style="margin-top: var(--spacing-lg);">
                    <p style="color: #666;">Don't have an account?</p>
                    <a href="register.php" style="color: var(--gcash-blue); text-decoration: none; font-weight: 600;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                </div>
                
                <div class="text-center" style="margin-top: var(--spacing-md);">
                    <a href="#" style="color: #666; font-size: var(--font-size-sm); text-decoration: none;">
                        <i class="fas fa-question-circle"></i> Need help?
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            submitBtn.disabled = true;
            
            // Simulate login process
            setTimeout(() => {
                alert('Login functionality will be implemented with your backend API');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>
