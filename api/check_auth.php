<?php
// Ensure no output before headers
require_once '../includes/config.php';
require_once '../includes/auth_check.php';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header
header('Content-Type: application/json');

// Prepare response
$response = [
    'logged_in' => is_logged_in(),
    'user_id' => is_logged_in() ? $_SESSION['user_id'] : null
];

// Output JSON
echo json_encode($response);
// Ensure no code/output after this line