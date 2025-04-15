<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['user_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tierId = filter_input(INPUT_POST, 'tier_id', FILTER_VALIDATE_INT);
    $customDescription = filter_input(INPUT_POST, 'custom_description', FILTER_SANITIZE_STRING);
    
    if (!$tierId) {
        $response['error'] = 'Invalid tier ID';
        echo json_encode($response);
        exit;
    }
    
    // Verify tier exists and is active
    $stmt = $pdo->prepare("SELECT * FROM subscription_tiers WHERE tier_id = ? AND is_active = 1");
    $stmt->execute([$tierId]);
    $tier = $stmt->fetch();
    
    if ($tier) {
        try {
            $insertStmt = $pdo->prepare("INSERT INTO subscription_plans 
                                      (artist_id, tier_id, name, description, duration_type, price, discount_percentage) 
                                      SELECT ?, tier_id, name, ?, duration_type, price, discount_percentage 
                                      FROM subscription_tiers 
                                      WHERE tier_id = ?");
            $insertStmt->execute([
                $artistId,
                $customDescription ?: null,
                $tierId
            ]);
            
            $planId = $pdo->lastInsertId();
            
            // Get the full plan details for response
            $planStmt = $pdo->prepare("SELECT sp.*, st.name as tier_name 
                                     FROM subscription_plans sp
                                     JOIN subscription_tiers st ON sp.tier_id = st.tier_id
                                     WHERE sp.plan_id = ?");
            $planStmt->execute([$planId]);
            $plan = $planStmt->fetch();
            
            // Add subscriber count
            $plan['subscriber_count'] = 0;
            
            $response['success'] = true;
            $response['plan'] = $plan;
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Invalid subscription tier';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);