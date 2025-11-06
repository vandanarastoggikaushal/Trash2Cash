<?php
/**
 * Custom Error Handler for Trash2Cash
 * Catches PHP errors and displays branded error pages
 */

// Set custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Don't handle errors if error reporting is disabled
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    // Prevent infinite loops - check if we're already handling an error
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '500.php') !== false || 
        strpos($requestUri, '404.php') !== false ||
        strpos($requestUri, '403.php') !== false ||
        strpos($requestUri, '503.php') !== false) {
        return false; // Already on an error page
    }
    
    // Log the error
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    
    // For fatal errors, show 500 page
    if ($errno === E_ERROR || $errno === E_PARSE || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR) {
        http_response_code(500);
        $errorPage = __DIR__ . '/../500.php';
        if (file_exists($errorPage)) {
            // Clear any output
            if (ob_get_level()) {
                ob_clean();
            }
            include $errorPage;
            exit;
        }
    }
    
    return false; // Let PHP handle other errors
});

// Set exception handler
set_exception_handler(function($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    // Prevent infinite loops - check if we're already on an error page
    if (strpos($requestUri, '500.php') !== false ||
        strpos($requestUri, '404.php') !== false ||
        strpos($requestUri, '403.php') !== false) {
        return; // Already on an error page
    }
    
    // Show 500 page
    http_response_code(500);
    $errorPage = __DIR__ . '/../500.php';
    if (file_exists($errorPage)) {
        // Clear any output
        if (ob_get_level()) {
            ob_clean();
        }
        include $errorPage;
        exit;
    }
});

// Handle fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        // Prevent infinite loops - check if we're already on an error page
        if (strpos($requestUri, '500.php') !== false ||
            strpos($requestUri, '404.php') !== false ||
            strpos($requestUri, '403.php') !== false) {
            return; // Already on an error page
        }
        
        // Show 500 page
        http_response_code(500);
        $errorPage = __DIR__ . '/../500.php';
        if (file_exists($errorPage)) {
            // Clear any output
            if (ob_get_level()) {
                ob_clean();
            }
            include $errorPage;
            exit;
        }
    }
});

