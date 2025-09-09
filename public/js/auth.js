// Authentication JavaScript for B-Cash
console.log('Auth.js loaded successfully');

class AuthService {
    constructor() {
        this.apiUrl = '/public/api/auth.php'; // Root path to public API
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
                    'Accept': 'application/json'
                },
                credentials: 'include',
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
            console.log('AuthService: Starting login process');
            const requestUrl = `${this.apiUrl}?action=login`;
            console.log('AuthService: Full request URL:', new URL(requestUrl, window.location.href).href);
            console.log('AuthService: Credentials:', JSON.stringify(credentials));
            
            // First, check if we're already logged in
            if (localStorage.getItem('user_id')) {
                console.log('AuthService: Found existing user_id, clearing it');
                localStorage.removeItem('user_id');
            }
            
            console.log('AuthService: Sending fetch request...');
            const response = await fetch(requestUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(credentials),
                credentials: 'include',
                mode: 'cors',
                cache: 'no-cache'
            });
            
            console.log('AuthService: Response received:', {
                status: response.status,
                statusText: response.statusText,
                headers: Object.fromEntries(response.headers.entries())
            });

            console.log('AuthService: Response status:', response.status);
            
            // Try to get response text regardless of status
            const responseText = await response.text();
            console.log('AuthService: Raw response:', responseText);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, response: ${responseText}`);
            }
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('AuthService: Parsed response:', result);
            } catch (e) {
                console.error('AuthService: Failed to parse JSON response:', e);
                throw new Error('Invalid JSON response from server');
            }

            if (result.success) {
                console.log('AuthService: Login successful');
                // Store user data
                if (result.data && result.data.user_id) {
                    localStorage.setItem('user_id', result.data.user_id);
                }
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
            e.preventDefault(); // Prevent form from submitting normally
            
            try {
                console.log('Login form submitted');
                
                // Disable form while processing
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                
                const formData = new FormData(this);
                const credentials = {
                    phone_number: formData.get('phone_number'),
                    password: formData.get('password')
                };

                console.log('Login credentials:', credentials);
                
                const result = await authService.login(credentials);
                console.log('Login result:', result);

                if (result.success && result.data) {
                    console.log('Login successful, preparing redirect...');
                    
                    // Store any necessary data
                    if (result.data.user && result.data.user.id) {
                        localStorage.setItem('user_id', result.data.user.id);
                    }
                    
                    // Determine redirect based on user type
                    const isAdmin = result.data.user && result.data.user.is_admin === true;
                    const redirectUrl = isAdmin ? 'admin.php' : 'index.php';
                    
                    console.log(`Redirecting to ${redirectUrl}...`);
                    window.location.href = redirectUrl;
                } else {
                    console.log('Login failed:', result.message);
                    alert(result.message || 'Login failed. Please try again.');
                    submitButton.disabled = false;
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('Login failed. Please try again.');
                submitButton.disabled = false;
            }
        });
    }
});
