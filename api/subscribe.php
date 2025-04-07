<?php
// subscribe.php - Handle artist subscription
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$planId = isset($data['plan_id']) ? intval($data['plan_id']) : 0;

if (!$planId) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid plan ID']);
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start transaction
    $db->beginTransaction();
    
    // Get plan details with tier information
    $stmt = $db->prepare("
        SELECT ass.setting_id, ass.artist_id, st.duration_type
        FROM artist_subscription_settings ass
        JOIN subscription_tiers st ON ass.tier_id = st.tier_id
        WHERE ass.setting_id = :plan_id 
        AND ass.is_enabled = 1
        AND st.is_active = 1
    ");
    $stmt->bindParam(':plan_id', $planId);
    $stmt->execute();
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan) {
        throw new Exception('Subscription plan not found or is no longer available.');
    }
    
    // Check if user already has an active subscription to this artist
    $stmt = $db->prepare("
        SELECT us.subscription_id
        FROM user_subscriptions us
        JOIN artist_subscription_settings ass ON us.plan_id = ass.setting_id
        WHERE us.user_id = :user_id 
        AND ass.artist_id = :artist_id
        AND us.status = 'active' 
        AND us.end_date > NOW()
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':artist_id', $plan['artist_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('You already have an active subscription to this artist.');
    }
    
    // Calculate subscription end date
    $startDate = new DateTime();
    $endDate = clone $startDate;
    
    switch ($plan['duration_type']) {
        case 'monthly':
            $endDate->modify('+1 month');
            break;
        case 'yearly':
            $endDate->modify('+1 year');
            break;
        case 'lifetime':
            $endDate->modify('+100 years');
            break;
        default:
            throw new Exception('Invalid subscription duration type.');
    }
    
    // Create the subscription
    $stmt = $db->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, auto_renew) 
        VALUES 
        (:user_id, :plan_id, :start_date, :end_date, 'active', :auto_renew)
    ");
    
    $startDateStr = $startDate->format('Y-m-d H:i:s');
    $endDateStr = $endDate->format('Y-m-d H:i:s');
    $autoRenew = ($plan['duration_type'] !== 'lifetime') ? 1 : 0;
    
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':plan_id', $planId);
    $stmt->bindParam(':start_date', $startDateStr);
    $stmt->bindParam(':end_date', $endDateStr);
    $stmt->bindParam(':auto_renew', $autoRenew);
    $stmt->execute();
    
    $subscriptionId = $db->lastInsertId();
    
    // Create an order record for the subscription purchase
    // Note: You'll need to implement actual payment processing here
    $stmt = $db->prepare("
        INSERT INTO orders 
        (buyer_id, payment_status, subscription_plan_id) 
        VALUES 
        (:user_id, 'pending', :plan_id)
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':plan_id', $planId);
    $stmt->execute();
    
    $orderId = $db->lastInsertId();
    
    // Commit transaction
    $db->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'subscription_id' => $subscriptionId,
        'order_id' => $orderId,
        'message' => 'Successfully subscribed!',
        'end_date' => $endDateStr
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>