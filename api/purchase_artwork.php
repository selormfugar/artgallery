<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/db.php';


// Authenticate user

if (isset($_SESSION['user_id'])) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND archived = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

// Get artwork ID from request
$artworkId = $_POST['artwork_id'] ?? null;

if (!$artworkId) {
    echo json_encode(['status' => 'error', 'message' => 'Artwork ID required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Begin transaction
    $conn->beginTransaction();
    
    // 1. Get artwork details and verify availability
    $stmt = $conn->prepare("SELECT * FROM artworks WHERE artwork_id = ? AND is_for_sale = 1 AND archived = 0 AND moderation_status = 'completed'");
    $stmt->execute([$artworkId]);
    $artwork = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$artwork) {
        throw new Exception("Artwork not available for purchase");
    }
    
    // 2. Check for active subscription discount
    $discount = 0;
    $subscriptionId = null;
    $planId = null;
    
    $stmt = $conn->prepare("
        SELECT s.subscription_id, p.plan_id, p.discount_percentage 
        FROM user_subscriptions s
        JOIN subscription_plans p ON s.plan_id = p.plan_id
        WHERE s.user_id = ? 
        AND s.status = 'active'
        AND p.artist_id = ?
        AND s.end_date > NOW()
        ORDER BY p.discount_percentage DESC
        LIMIT 1
    ");
    $stmt->execute([$user['user_id'], $artwork['artist_id']]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($subscription) {
        $discount = $subscription['discount_percentage'];
        $subscriptionId = $subscription['subscription_id'];
        $planId = $subscription['plan_id'];
    }
    
    // 3. Calculate final price
    $originalPrice = $artwork['price'];
    $discountAmount = $originalPrice * ($discount / 100);
    $finalPrice = $originalPrice - $discountAmount;
    
    // 4. Create order record
    $stmt = $conn->prepare("
        INSERT INTO orders (
            buyer_id, 
            artwork_id, 
            total_price, 
            payment_status,
            subscription_discount,
            applied_subscription_id,
            subscription_plan_id
        ) VALUES (?, ?, ?, 'completed', ?, ?, ?)
    ");
    $stmt->execute([
        $user['user_id'],
        $artworkId,
        $finalPrice,
        $discount,
        $subscriptionId,
        $planId
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // 5. Mark artwork as sold
    $stmt = $conn->prepare("UPDATE artworks SET is_for_sale = 0 WHERE artwork_id = ?");
    $stmt->execute([$artworkId]);
    
    // 6. Add to user's collection
    $stmt = $conn->prepare("
        INSERT INTO user_collections (
            user_id, 
            artwork_id, 
            is_purchased, 
            purchase_date, 
            purchase_order_id
        ) VALUES (?, ?, 1, NOW(), ?)
    ");
    $stmt->execute([$user['user_id'], $artworkId, $orderId]);
    
    // 7. Create notification
    $message = "You purchased '{$artwork['title']}' for $" . number_format($finalPrice, 2);
    if ($discount > 0) {
        $message .= " (with your {$discount}% subscription discount)";
    }
    
    $stmt = $conn->prepare("
        INSERT INTO notifications (
            user_id, 
            message, 
            seen
        ) VALUES (?, ?, 0)
    ");
    $stmt->execute([$user['user_id'], $message]);
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'order_id' => $orderId,
        'subscription_discount_applied' => $discount > 0,
        'discount_amount' => '$' . number_format($discountAmount, 2),
        'final_price' => $finalPrice
    ]);
    
} catch (Exception $e) {
    // Roll back transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}