<?php
// Include error handler first
require_once 'error_handler.php';

// Check if vendor directory exists
if (!file_exists('../vendor/autoload.php')) {
    handleApiError('Dependencies not installed', 500);
}

require_once '../vendor/autoload.php';

use App\SmsService;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get configuration from environment variables
$apiKey = getenv('INFOBIP_API_KEY') ?: '';
$baseUrl = getenv('INFOBIP_BASE_URL') ?: 'https://api.infobip.com';

// Validate API key
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit();
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['to']) || !isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: to, message']);
    exit();
}

$to = $input['to'];
$message = $input['message'];
$from = $input['from'] ?? 'InfoSMS'; // Default sender ID

// Validate phone number format (basic validation)
if (!preg_match('/^\+?[1-9]\d{1,14}$/', $to)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid phone number format']);
    exit();
}

try {
    // Initialize SMS service with official Infobip client
    $smsService = new SmsService($apiKey, $baseUrl);
    
    // Send SMS using the official client
    $result = $smsService->sendSms($to, $message, $from);
    
    if ($result['success']) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode($result);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unexpected error: ' . $e->getMessage()
    ]);
}
?>
