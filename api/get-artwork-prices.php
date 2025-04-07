
<?php
// get-artwork-prices.php - Get artwork prices with subscription discounts applied
require_once '../includes/config.php';
// require_once '../includes/auth_check.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$artistId = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 0;

if (!$artistId) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid artist ID']);
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the user has an active subscription to this artist
    $stmt = $db->prepare("
        SELECT us.*, sp.discount_percentage 
        FROM user_subscriptions us
        JOIN subscription_plans sp ON us.plan_id = sp.plan_id
        WHERE us.user_id = :user_id 
        AND sp.artist_id = :artist_id
        AND us.status = 'active'
        AND us.end_date > NOW()
        ORDER BY sp.discount_percentage DESC
        LIMIT 1
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':artist_id', $artistId);
    $stmt->execute();
    
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    $discountPercentage = $subscription ? $subscription['discount_percentage'] : 0;
    
    // Get all artworks for the artist
    $stmt = $db->prepare("
        SELECT artwork_id, title, price
        FROM artworks
        WHERE artist_id = :artist_id
        AND active = 1
    ");
    $stmt->bindParam(':artist_id', $artistId);
    $stmt->execute();
    
    $artworks = [];
    while ($artwork = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalPrice = $artwork['price'];
        $discountedPrice = $originalPrice;
        
        if ($discountPercentage > 0) {
            $discountedPrice = round($originalPrice * (1 - ($discountPercentage / 100)), 2);
        }
        
        $artworks[] = [
            'artwork_id' => $artwork['artwork_id'],
            'title' => $artwork['title'],
            'original_price' => number_format($originalPrice, 2),
            'discounted_price' => number_format($discountedPrice, 2),
            'has_discount' => ($discountPercentage > 0)
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'artworks' => $artworks,
        'has_subscription' => !empty($subscription),
        'discount_percentage' => $discountPercentage
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>