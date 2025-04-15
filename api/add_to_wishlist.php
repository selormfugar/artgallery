<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
// require_once '../includes/auth_check.php';
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
// Validate required fields
if (!isset($_SESSION['user_id']) || !isset($data['artwork_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
$data['user_id'] = $_SESSION['user_id'];
try {
    // Check if the item is already in the wishlist
    $check_sql = "SELECT wishlist_id FROM wishlists
                  WHERE user_id = ? AND artwork_id = ? AND archived = 0";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data['user_id'], $data['artwork_id']]);
    $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(400);
        echo json_encode(['error' => 'Artwork already in wishlist']);
        exit;
    }
    // Insert new wishlist item
    $insert_sql = "INSERT INTO wishlists (user_id, artwork_id, created_at)
                   VALUES (?, ?, NOW())";
    $insert_stmt = $pdo->prepare($insert_sql);
   
    if ($insert_stmt->execute([$data['user_id'], $data['artwork_id']])) {
        echo json_encode([
            'success' => true,
            'message' => 'Added to wishlist successfully',
            'wishlist_id' => $pdo->lastInsertId()
        ]);
    } else {
        throw new Exception("Failed to add to wishlist");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
$pdo = null;
?>