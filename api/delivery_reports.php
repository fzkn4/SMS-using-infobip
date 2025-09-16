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
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
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

try {
    // Initialize SMS service with official Infobip client
    $smsService = new SmsService($apiKey, $baseUrl);
    
    // Get parameters
    $bulkId = $_GET['bulkId'] ?? null;
    $messageId = $_GET['messageId'] ?? null;
    $limit = (int)($_GET['limit'] ?? 10);
    
    // Get delivery reports using the official client
    $result = $smsService->getDeliveryReports($bulkId, $messageId, $limit);
    
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
