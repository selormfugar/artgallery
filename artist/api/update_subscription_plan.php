<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['artist_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
    $customDescription = filter_input(INPUT_POST, 'custom_description', FILTER_SANITIZE_STRING);
    
    // Verify the plan belongs to this artist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subscription_plans WHERE plan_id = ? AND artist_id = ?");
    $stmt->execute([$planId, $artistId]);
    
    if ($stmt->fetchColumn()) {
        try {
            $updateStmt = $pdo->prepare("UPDATE subscription_plans SET description = ? WHERE plan_id = ?");
            $updateStmt->execute([$customDescription ?: null, $planId]);
            
            $response['success'] = true;
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Invalid subscription plan';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);