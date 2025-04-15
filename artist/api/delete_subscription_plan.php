<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['user_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $planId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$planId) {
        $response['error'] = 'Invalid plan ID';
        echo json_encode($response);
        exit;
    }
    
    // Verify the plan belongs to this artist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subscription_plans WHERE plan_id = ? AND artist_id = ?");
    $stmt->execute([$planId, $artistId]);
    
    if ($stmt->fetchColumn()) {
        try {
            // Soft delete the plan
            $deleteStmt = $pdo->prepare("UPDATE subscription_plans SET archived = 1 WHERE plan_id = ?");
            $deleteStmt->execute([$planId]);
            
            $response['success'] = true;
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Subscription plan not found or not owned by you';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);