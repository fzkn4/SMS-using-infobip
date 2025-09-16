<?php
// Error handler for API endpoints

function handleApiError($error, $code = 500) {
    http_response_code($code);
    header('Content-Type: application/json');
    
    $response = [
        'success' => false,
        'error' => $error
    ];
    
    echo json_encode($response);
    exit();
}

// Set error reporting based on environment
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>
