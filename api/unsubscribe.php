<?php
// unsubscribe.php - Handle cancellation of artist subscription
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
$subscriptionId = isset($data['subscription_id']) ? intval($data['subscription_id']) : 0;

if (!$subscriptionId) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid subscription ID']);
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start transaction
    $db->beginTransaction();
    
    // Check if subscription exists and belongs to the user
    $stmt = $db->prepare("
        SELECT us.*, ass.artist_id 
        FROM user_subscriptions us
        JOIN artist_subscription_settings ass ON us.plan_id = ass.setting_id
        WHERE us.subscription_id = :subscription_id 
        AND us.user_id = :user_id
        AND us.status = 'active'
    ");
    $stmt->bindParam(':subscription_id', $subscriptionId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$subscription) {
        throw new Exception('Active subscription not found or does not belong to you.');
    }
    
    // Update subscription status to cancelled
    $stmt = $db->prepare("
        UPDATE user_subscriptions 
        SET status = 'cancelled', 
            auto_renew = 0,
            end_date = CASE 
                WHEN end_date > NOW() THEN end_date 
                ELSE NOW() 
            END
        WHERE subscription_id = :subscription_id
    ");
    $stmt->bindParam(':subscription_id', $subscriptionId);
    $stmt->execute();
    
    // Create a cancellation record in orders (optional)
    $stmt = $db->prepare("
        INSERT INTO orders 
        (buyer_id, payment_status, subscription_plan_id, is_subscription_cancellation)
        VALUES 
        (:user_id, 'refunded', :plan_id, 1)
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':plan_id', $subscription['plan_id']);
    $stmt->execute();
    
    // Commit transaction
    $db->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Subscription successfully cancelled.',
        'artist_id' => $subscription['artist_id'],
        'end_date' => $subscription['end_date']
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