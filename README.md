# B-Cash AJAX - Digital Wallet System

A modern, secure digital wallet application built with PHP, JavaScript, and MySQL. B-Cash provides a complete solution for digital payments, money transfers, and user verification.

## Features

### 🏦 Core Wallet Features
- **Account Management**: Create and manage digital wallet accounts
- **Balance Tracking**: Real-time balance updates and transaction history
- **Money Transfers**: Send money to other users via account numbers
- **Transaction History**: Complete audit trail of all financial activities

### 🔐 Security & Verification
- **Multi-Step Registration**: Comprehensive user onboarding process
- **ID Verification**: Document upload and verification system
- **Face Recognition**: Biometric verification for enhanced security
- **Session Management**: Secure authentication and authorization

### 💳 Payment Features
- **Quick Actions**: Send, receive, add money, and pay bills
- **Account Search**: Find users by account number
- **Transaction Reference**: Unique reference numbers for all transactions
- **Real-time Updates**: Live balance and transaction updates

## System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache/Nginx
- **Browser**: Modern browsers with JavaScript enabled

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd B-Cash-AJAX
```

### 2. Database Setup
1. Create a MySQL database named `b_cash_ajax`
2. Import the database schema:
```bash
mysql -u root -p < database/setup.sql
```

### 3. Configuration
1. Update database credentials in `app/config/Config.php`:
```php
const DB_HOST = 'localhost';
const DB_NAME = 'b_cash_ajax';
const DB_USER = 'your_username';
const DB_PASS = 'your_password';
```

2. Update application URL:
```php
const APP_URL = 'http://your-domain.com';
```

### 4. Web Server Configuration
1. Point your web server to the `public/` directory
2. Ensure the `uploads/` directory is writable
3. Configure URL rewriting if needed

### 5. File Permissions
```bash
chmod 755 uploads/
chmod 755 uploads/verification/
```

## Usage

### User Registration
1. Navigate to `/register.php`
2. Complete the 4-step registration process:
   - Basic Information
   - ID Document Upload
   - Face Verification
   - Account Activation

### User Login
1. Navigate to `/login.php`
2. Use phone number and password to authenticate
3. Access your digital wallet dashboard

### Wallet Operations
- **View Balance**: Check your current balance
- **Send Money**: Transfer funds to other users
- **Receive Money**: Share your account number
- **Transaction History**: View all past transactions

## API Endpoints

### Authentication
- `POST /api/auth.php?action=register` - User registration
- `POST /api/auth.php?action=login` - User login
- `POST /api/auth.php?action=logout` - User logout

### Wallet
- `GET /api/wallet.php?action=balance` - Get wallet balance
- `GET /api/wallet.php?action=info` - Get wallet information
- `POST /api/wallet.php?action=transfer` - Transfer money
- `GET /api/wallet.php?action=search` - Search accounts

### Transactions
- `GET /api/transaction.php?action=history` - Get transaction history
- `GET /api/transaction.php?action=stats` - Get transaction statistics
- `GET /api/transaction.php?action=search` - Search transactions
- `GET /api/transaction.php?action=reference` - Get transaction by reference

### Verification
- `POST /api/verification.php?action=upload-document` - Upload ID documents
- `POST /api/verification.php?action=upload-face` - Upload face image
- `POST /api/verification.php?action=verify` - Perform verification
- `GET /api/verification.php?action=status` - Get verification status

## Database Schema

### Core Tables
- **users**: User account information
- **wallets**: Wallet balances and account numbers
- **transactions**: Financial transaction records
- **security_tokens**: Authentication tokens

### Verification Tables
- **user_verification**: ID and face verification data
- **verification_logs**: Audit trail for verification actions

## Security Features

- **Password Hashing**: Bcrypt encryption for passwords
- **Session Management**: Secure PHP sessions
- **Input Validation**: Comprehensive data validation
- **SQL Injection Protection**: Prepared statements
- **File Upload Security**: Type and size validation
- **CSRF Protection**: Cross-site request forgery prevention

## Testing

### Sample Accounts
The system includes test accounts for development:

**Admin User:**
- Phone: 09123456789
- Password: password
- Balance: ₱10,000.00

**Test User:**
- Phone: 09187654321
- Password: password
- Balance: ₱5,000.00

### API Testing
Use the included test files:
- `test_login_api.php` - Test authentication endpoints
- `test_transaction_api.php` - Test transaction endpoints
- `test_wallet_api.php` - Test wallet endpoints

## Development

### Project Structure
```
B-Cash AJAX/
├── app/
│   ├── config/          # Configuration files
│   ├── controllers/     # Application controllers
│   ├── helpers/         # Helper classes
│   ├── models/          # Data models
│   └── services/        # Business logic services
├── css/                 # Stylesheets
├── database/            # Database schemas
├── img/                 # Images and assets
├── js/                  # JavaScript files
├── public/              # Public web files
├── uploads/             # File uploads
└── README.md            # This file
```

### Adding New Features
1. Create model classes in `app/models/`
2. Add controller methods in `app/controllers/`
3. Create API endpoints in `public/api/`
4. Add JavaScript functionality in `public/js/`
5. Update database schema if needed

## Troubleshooting

### Common Issues

**Database Connection Error**
- Verify database credentials in `Config.php`
- Ensure MySQL service is running
- Check database exists and is accessible

**File Upload Issues**
- Verify `uploads/` directory permissions
- Check PHP upload settings in `php.ini`
- Ensure sufficient disk space

**Session Issues**
- Check PHP session configuration
- Verify cookie settings
- Clear browser cookies and cache

### Debug Mode
Enable debug mode by setting:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## Changelog

### Version 1.0.0
- Initial release
- Core wallet functionality
- User authentication system
- ID and face verification
- Transaction management
- Responsive web interface

---

**B-Cash AJAX** - Making digital payments simple, secure, and accessible. 