<?php

class SessionHelper {
    /**
     * Configure session settings for consistent behavior across all API endpoints
     */
    public static function configureSession() {
        // Check if we're running in CLI mode
        $isCli = (php_sapi_name() === 'cli');

        if (!$isCli) {
            // Force set session save path
            $sessionPath = sys_get_temp_dir() . '/php_sessions';
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0755, true);
            }
            session_save_path($sessionPath);

            // Determine if we're on localhost
            $isLocalhost = (isset($_SERVER['HTTP_HOST']) &&
                           ($_SERVER['HTTP_HOST'] === 'localhost' ||
                            $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
                            strpos($_SERVER['HTTP_HOST'], 'localhost') !== false));

            // Set consistent session cookie parameters
            session_set_cookie_params([
                'lifetime' => 0,  // Session cookie (expires when browser closes)
                'path' => '/',    // Root path to work across subdirectories
                'domain' => $isLocalhost ? '' : $_SERVER['HTTP_HOST'], // Empty for localhost
                'secure' => false,  // Set to false to allow HTTP on localhost and testing
                'httponly' => false,  // Set to false to allow JavaScript access for debugging
                'samesite' => $isLocalhost ? 'Lax' : 'None'  // Lax for localhost, None for cross-origin
            ]);
        }

        // Start the session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            if (headers_sent()) {
                // Headers already sent, cannot start session with custom params
                session_start();
            } else {
                session_start();
            }
        }
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
        return self::getCurrentUserId() !== null;
    }

    /**
     * Set user session data
     */
    public static function setUserSession($userId, $userData = []) {
        self::configureSession();
        $_SESSION['user_id'] = $userId;

        if (isset($userData['full_name'])) {
            $_SESSION['full_name'] = $userData['full_name'];
        }

        // Ensure session is written (only in web mode, not CLI)
        $isCli = (php_sapi_name() === 'cli');
        if (!$isCli) {
            session_write_close();
        }
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
