<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get and sanitize input data
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $input = $_POST; // Fallback to regular POST if JSON parse fails
}

$action = filter_var($input['action'] ?? '', FILTER_SANITIZE_STRING);
$userId = filter_var($input['user_id'] ?? '', FILTER_VALIDATE_INT);

// Validate inputs
if ($action !== 'archive' || $userId === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Prevent self-archiving
if ($userId == $_SESSION['user_id']) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'You cannot archive your own account']);
    exit;
}

try {
    // Check if user exists and is active
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND status = 'active'");
    $checkStmt->execute([$userId]);
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Active user not found']);
        exit;
    }

    // Prepare the update statement (soft delete/archive)
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET status = 'archived', 
            archived_at = NOW(),
            archived_by = ?
        WHERE id = ? 
        AND status = 'active'
    ");

    // Execute the statement
    $result = $updateStmt->execute([$_SESSION['user_id'], $userId]);

    if ($result && $updateStmt->rowCount() > 0) {
        // Log the archiving action
        logAction($_SESSION['user_id'], 'archive_user', "Archived user ID: $userId");
        
        echo json_encode([
            'success' => true, 
            'message' => 'User archived successfully',
            'user_id' => $userId
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to archive user']);
    }

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    error_log("Database error in api/users.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Error in api/users.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}