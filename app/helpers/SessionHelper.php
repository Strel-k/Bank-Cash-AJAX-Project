<?php

class SessionHelper {
    /**
     * Configure session settings for consistent behavior across all API endpoints
     */
    public static function configureSession() {
        // Check if we're running in CLI mode
        $isCli = (php_sapi_name() === 'cli');

        if (!$isCli) {
            // Force set session save path to a writable directory
            $sessionPath = __DIR__ . '/../../sessions';
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0755, true);
            }
            session_save_path($sessionPath);

            // Set session cookie parameters for localhost
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }

        // Start the session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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
