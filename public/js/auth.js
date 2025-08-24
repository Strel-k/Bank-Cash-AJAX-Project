// Authentication JavaScript for B-Cash
class AuthService {
    constructor() {
        this.apiUrl = '/api/auth.php'; // Use relative path
    }
    
    async register(userData) {
        try {
            const response = await fetch(`${this.apiUrl}?action=register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                return { success: true, data: result };
            } else {
                return { success: false, message: result.message };
            }
        } catch (error) {
            return { success: false, message: 'Registration failed' };
        }
    }
    
    async login(credentials) {
        try {
            const response = await fetch(`${this.apiUrl}?action=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(credentials)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Store session token
                localStorage.setItem('b_cash_token', result.data.token);
                return { success: true, data: result.data };
            } else {
                return { success: false, message: result.message };
            }
        } catch (error) {
            return { success: false, message: 'Login failed' };
        }
    }
    
    async logout() {
        try {
            const response = await fetch(`${this.apiUrl}?action=logout`);
            const result = await response.json();
            
            if (result.success) {
                localStorage.removeItem('b_cash_token');
                return { success: true };
            }
        } catch (error) {
            return { success: false, message: 'Logout failed' };
        }
    }
    
    isLoggedIn() {
        return localStorage.getItem('b_cash_token') !== null;
    }
}

// Initialize auth service
const authService = new AuthService();

// Form handlers
document.addEventListener('DOMContentLoaded', function() {
    // Registration form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userData = {
                phone_number: formData.get('phone_number'),
                email: formData.get('email'),
                full_name: formData.get('full_name'),
                password: formData.get('password')
            };
            
            const result = await authService.register(userData);
            
            if (result.success) {
                alert('Registration successful!');
                window.location.href = '/login.php';
            } else {
                alert(result.message);
            }
        });
    }
    
    // Login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const credentials = {
                phone_number: formData.get('phone_number'),
                password: formData.get('password')
            };
            
            const result = await authService.login(credentials);
            
            if (result.success) {
                window.location.href = 'index.php';
            } else {
                alert(result.message);
            }
        });
    }
});
