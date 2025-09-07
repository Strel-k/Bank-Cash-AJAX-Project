// Authentication JavaScript for B-Cash
console.log('Auth.js loaded successfully');

class AuthService {
    constructor() {
        this.apiUrl = 'api/auth.php'; // Changed to relative URL
    }
    
    async register(userData) {
        try {
            console.log('AuthService: Starting registration...');
            console.log('AuthService: API URL:', `${this.apiUrl}?action=register`);
            console.log('AuthService: User data:', JSON.stringify(userData));

            const response = await fetch(`${this.apiUrl}?action=register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData),
                credentials: 'include' // Added to include cookies for session
            });
            
            console.log('AuthService: Response status:', response.status);
            console.log('AuthService: Response headers:', response.headers);
            
            const result = await response.json();
            console.log('AuthService: Parsed result:', result);
            
            if (result.success) {
                console.log('AuthService: Registration successful');
                return { success: true, data: result.data };
            } else {
                console.log('AuthService: Registration failed:', result.message);
                return { success: false, message: result.message };
            }
        } catch (error) {
            console.error('AuthService: Registration error:', error);
            return { success: false, message: 'Registration failed: ' + error.message };
        }
    }
    
    async login(credentials) {
        try {
            console.log('AuthService: Starting login...');
            console.log('AuthService: API URL:', `${this.apiUrl}?action=login`);
            console.log('AuthService: Credentials:', credentials);

            const response = await fetch(`${this.apiUrl}?action=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(credentials),
                credentials: 'include' // Added to include cookies for session
            });

            console.log('AuthService: Response status:', response.status);
            console.log('AuthService: Response headers:', response.headers);

            const result = await response.json();
            console.log('AuthService: Login result:', result);

            if (result.success) {
                console.log('AuthService: Login successful');
                // Session cookie is automatically handled by browser
                return { success: true, data: result.data };
            } else {
                console.log('AuthService: Login failed:', result.message);
                return { success: false, message: result.message };
            }
        } catch (error) {
            console.error('AuthService: Login error:', error);
            return { success: false, message: 'Login failed: ' + error.message };
        }
    }
    
    async logout() {
        try {
            const response = await fetch(`${this.apiUrl}?action=logout`, {
                credentials: 'include' // Added to include cookies for session
            });
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
window.authService = authService; // Make it globally accessible

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

            console.log('Login form submitted');

            const formData = new FormData(this);
            const credentials = {
                phone_number: formData.get('phone_number'),
                password: formData.get('password')
            };

            console.log('Login credentials:', credentials);

            const result = await authService.login(credentials);

            console.log('Login result:', result);

            if (result.success) {
                console.log('Login successful, redirecting to index.php');
                window.location.replace('index.php');
            } else {
                console.log('Login failed:', result.message);
                alert(result.message);
            }
        });
    }
});
