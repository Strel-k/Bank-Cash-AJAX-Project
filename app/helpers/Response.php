<?php
class Response {
    public static function json($data, $status = 200) {
        // Clear any previous output
        if (ob_get_length()) {
            ob_clean();
        }
        
        http_response_code($status);
        header('Content-Type: application/json');
        
        // Ensure no errors are displayed
        ini_set('display_errors', 0);
        
        echo json_encode($data);
        exit;
    }
    
    public static function success($data = [], $message = 'Success') {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    public static function error($message = 'Error', $status = 400) {
        self::json([
            'success' => false,
            'message' => $message
        ], $status);
    }
    
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    public static function notFound($message = 'Not Found') {
        self::error($message, 404);
    }
}
?>
