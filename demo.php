<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Cash AJAX - Demo & Features</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" relstylesheet>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--gcash-green);
            margin-bottom: 1rem;
        }
        
        .demo-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .demo-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--gcash-green);
            color: white;
        }
        
        .btn-secondary {
            background: var(--gcash-blue);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--gcash-green);
            border: 2px solid var(--gcash-green);
        }
        
        .demo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--gcash-green), var(--gcash-blue));
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .tech-stack {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            margin: 2rem 0;
        }
        
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .tech-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-wallet"></i> B-Cash
            </div>
            <nav class="nav-links">
                <a href="public/index.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="public/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="public/register.php"><i class="fas fa-user-plus"></i> Register</a>
            </nav>
        </div>
    </header>

    <main class="demo-container">
        <!-- Hero Section -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Welcome to B-Cash AJAX</h1>
                <p class="text-center">A complete digital wallet solution with advanced security features</p>
            </div>
        </div>

        <!-- Demo Buttons -->
        <div class="demo-buttons">
            <a href="public/register.php" class="demo-btn btn-primary">
                <i class="fas fa-user-plus"></i> Try Registration
            </a>
            <a href="public/login.php" class="demo-btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Demo Login
            </a>
            <a href="test_system.php" class="demo-btn btn-outline">
                <i class="fas fa-cog"></i> System Test
            </a>
            <a href="install.php" class="demo-btn btn-outline">
                <i class="fas fa-download"></i> Installation
            </a>
        </div>

        <!-- Feature Grid -->
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Authentication</h3>
                <p>Multi-factor authentication with session management and secure password hashing</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>ID Verification</h3>
                <p>Document upload and verification system with OCR data extraction</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3>Face Recognition</h3>
                <p>Biometric verification using advanced facial recognition technology</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3>Money Transfers</h3>
                <p>Instant money transfers between users with real-time balance updates</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Transaction History</h3>
                <p>Complete audit trail of all financial activities with search and filtering</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Responsive Design</h3>
                <p>Mobile-first design that works perfectly on all devices and screen sizes</p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">4</div>
                <div>Registration Steps</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15+</div>
                <div>API Endpoints</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div>Mobile Responsive</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div>System Availability</div>
            </div>
        </div>

        <!-- Technology Stack -->
        <div class="tech-stack">
            <h2 style="text-align: center; margin-bottom: 1rem;">Technology Stack</h2>
            <div class="tech-grid">
                <div class="tech-item">
                    <i class="fab fa-php" style="font-size: 2rem; color: var(--gcash-blue);"></i>
                    <div>PHP 7.4+</div>
                </div>
                <div class="tech-item">
                    <i class="fas fa-database" style="font-size: 2rem; color: var(--gcash-green);"></i>
                    <div>MySQL 5.7+</div>
                </div>
                <div class="tech-item">
                    <i class="fab fa-js-square" style="font-size: 2rem; color: var(--gcash-orange);"></i>
                    <div>JavaScript ES6</div>
                </div>
                <div class="tech-item">
                    <i class="fab fa-css3-alt" style="font-size: 2rem; color: var(--gcash-purple);"></i>
                    <div>CSS3 + Flexbox</div>
                </div>
                <div class="tech-item">
                    <i class="fas fa-server" style="font-size: 2rem; color: var(--gcash-red);"></i>
                    <div>RESTful API</div>
                </div>
                <div class="tech-item">
                    <i class="fas fa-lock" style="font-size: 2rem; color: var(--gcash-green);"></i>
                    <div>Security First</div>
                </div>
            </div>
        </div>

        <!-- Quick Start -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Start Guide</h2>
            </div>
            <div class="card-body">
                <ol style="line-height: 2;">
                    <li><strong>Setup Database:</strong> Run <code>mysql -u root -p < database/setup.sql</code></li>
                    <li><strong>Configure:</strong> Update database credentials in <code>app/config/Config.php</code></li>
                    <li><strong>Test System:</strong> Run <code>php test_system.php</code> to verify installation</li>
                    <li><strong>Point Web Server:</strong> Configure your web server to point to the <code>public/</code> directory</li>
                    <li><strong>Access Application:</strong> Navigate to your configured URL</li>
                    <li><strong>Test Accounts:</strong> Use sample accounts (09123456789/password or 09187654321/password)</li>
                </ol>
            </div>
        </div>

        <!-- API Documentation -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">API Documentation</h2>
            </div>
            <div class="card-body">
                <p>The system provides a comprehensive RESTful API for all operations:</p>
                <ul style="line-height: 2;">
                    <li><strong>Authentication:</strong> <code>/api/auth.php</code> - Register, login, logout</li>
                    <li><strong>Wallet:</strong> <code>/api/wallet.php</code> - Balance, transfers, account search</li>
                    <li><strong>Transactions:</strong> <code>/api/transaction.php</code> - History, stats, search</li>
                    <li><strong>Verification:</strong> <code>/api/verification.php</code> - Document and face uploads</li>
                </ul>
                <p>All API endpoints return JSON responses and support CORS for cross-origin requests.</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 B-Cash AJAX. All rights reserved.</p>
            <div class="footer-links">
                <a href="README.md">Documentation</a>
                <a href="test_system.php">System Test</a>
                <a href="install.php">Installation</a>
            </div>
        </div>
    </footer>
</body>
</html> 