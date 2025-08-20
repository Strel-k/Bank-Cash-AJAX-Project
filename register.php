<?php
session_start();
require_once 'app/config/Config.php';
require_once 'app/controllers/AuthController.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - B-Cash</title>
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
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="fas fa-user-plus" style="font-size: 48px; color: var(--gcash-green); margin-bottom: var(--spacing-md);"></i>
                    <h1 class="card-title">Create Account</h1>
                    <p style="color: #666;">Join millions of users on B-Cash</p>
                </div>
                
                <form id="registerForm" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="full_name">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" 
                               class="form-input" 
                               name="full_name" 
                               placeholder="Enter your full name" 
                               required>
                    </div>
                    
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
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" 
                               class="form-input" 
                               name="email" 
                               placeholder="Enter your email" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" 
                               class="form-input" 
                               name="password" 
                               placeholder="Create a strong password" 
                               required 
                               minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <input type="password" 
                               class="form-input" 
                               name="confirm_password" 
                               placeholder="Confirm your password" 
                               required 
                               minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: var(--spacing-sm);">
                            <input type="checkbox" required>
                            <span style="font-size: var(--font-size-sm);">
                                I agree to the <a href="#" style="color: var(--gcash-blue);">Terms of Service</a> and <a href="#" style="color: var(--gcash-blue);">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="form-button">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="text-center" style="margin-top: var(--spacing-lg);">
                    <p style="color: #666;">Already have an account?</p>
                    <a href="login.php" style="color: var(--gcash-blue); text-decoration: none; font-weight: 600;">
                        <i class="fas fa-sign-in-alt"></i> Sign In
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
            submitBtn.disabled = true;
            
            // Simulate registration process
            setTimeout(() => {
                alert('Registration functionality will be implemented with your backend API');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>
