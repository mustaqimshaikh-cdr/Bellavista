<?php
// Simple test API endpoint
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo json_encode([
    'success' => true,
    'message' => 'API is working correctly',
    'php_version' => phpversion(),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
