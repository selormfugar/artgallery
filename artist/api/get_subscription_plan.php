<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['artist_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $planId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$planId) {
        $response['error'] = 'Invalid plan ID';
        echo json_encode($response);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT sp.*, st.name as tier_name 
                             FROM subscription_plans sp
                             JOIN subscription_tiers st ON sp.tier_id = st.tier_id
                             WHERE sp.plan_id = ? AND sp.artist_id = ?");
        $stmt->execute([$planId, $artistId]);
        $plan = $stmt->fetch();
        
        if ($plan) {
            // Get subscriber count
            $subscriberStmt = $pdo->prepare("SELECT COUNT(*) FROM user_subscriptions 
                                            WHERE plan_id = ? AND status = 'active'");
            $subscriberStmt->execute([$planId]);
            $plan['subscriber_count'] = $subscriberStmt->fetchColumn();
            
            $response['success'] = true;
            $response['plan'] = $plan;
        } else {
            $response['error'] = 'Subscription plan not found or not owned by you';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);