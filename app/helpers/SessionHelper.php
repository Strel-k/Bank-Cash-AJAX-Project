<?php

class SessionHelper {
    /**
     * Configure session settings for consistent behavior across all API endpoints
     */
    public static function configureSession() {
        error_log("SessionHelper::configureSession - Starting configuration");
        
        // Check if headers were already sent
        if (headers_sent($file, $line)) {
            error_log("SessionHelper::configureSession - Headers already sent in $file on line $line");
        }

        // Check if we're running in CLI mode
        $isCli = (php_sapi_name() === 'cli');
        
        if (!$isCli) {
            // Only configure if session is not active
            if (session_status() === PHP_SESSION_NONE) {
                error_log("SessionHelper::configureSession - Configuring new session");
                
                // Load configuration first
                $config = require __DIR__ . '/../config/session.php';
                
                // Configure session parameters
                ini_set('session.use_strict_mode', 1);
                ini_set('session.use_cookies', 1);
                ini_set('session.use_only_cookies', 1);
                ini_set('session.cookie_httponly', 1);
                ini_set('session.gc_maxlifetime', $config['gc_maxlifetime']);
                
                // Set session name
                session_name($config['name']);
                
                // Set session cookie parameters
                session_set_cookie_params(
                    $config['cookie_lifetime'],
                    $config['cookie_path'],
                    $config['cookie_domain'],
                    $config['cookie_secure'],
                    $config['cookie_httponly']
                );
                
                // Set up session directory
                $sessionPath = sys_get_temp_dir() . '/php_sessions';
                if (!is_dir($sessionPath)) {
                    mkdir($sessionPath, 0755, true);
                }
                session_save_path($sessionPath);
                
                // Start the session
                session_start();
                error_log("SessionHelper::configureSession - New session started with ID: " . session_id());
            } else if (session_status() === PHP_SESSION_ACTIVE) {
                error_log("SessionHelper::configureSession - Session already active with ID: " . session_id());
            }
            
            error_log("SessionHelper::configureSession - Final session data: " . print_r($_SESSION, true));
        }
        
        error_log("SessionHelper::configureSession - Session data: " . print_r($_SESSION, true));
    }

    /**
     * Get current user ID from session
     */
    public static function getCurrentUserId() {
        self::configureSession();
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        self::configureSession();
        error_log("SessionHelper::isAuthenticated - Checking session: " . session_id());
        error_log("SessionHelper::isAuthenticated - Session data: " . print_r($_SESSION, true));
        return self::getCurrentUserId() !== null;
    }

    /**
     * Set user session data
     */
    public static function setUserSession($userId, $userData = []) {
        self::configureSession();
        
        // Store data temporarily
        $tempData = [
            'user_id' => $userId,
            'full_name' => $userData['full_name'] ?? '',
            'is_admin' => $userData['is_admin'] ?? false,
            'last_activity' => time()
        ];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set the session data after regeneration
        $_SESSION = array_merge($_SESSION, $tempData);
        
        error_log("SessionHelper::setUserSession - New session ID: " . session_id());
        error_log("SessionHelper::setUserSession - Session data set: " . print_r($_SESSION, true));
    }

    /**
     * Clear user session
     */
    public static function clearUserSession() {
        self::configureSession();
        session_unset();
        session_destroy();
    }
}
?>
