<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['user_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $itemId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$itemId) {
        $response['error'] = 'Invalid item ID';
        echo json_encode($response);
        exit;
    }
    
    // Verify the item belongs to this artist
    $stmt = $pdo->prepare("SELECT image_url FROM portfolio_items WHERE item_id = ? AND artist_id = ?");
    $stmt->execute([$itemId, $artistId]);
    $item = $stmt->fetch();
    
    if ($item) {
        try {
            // Soft delete the item
            $deleteStmt = $pdo->prepare("UPDATE portfolio_items SET archived = 1 WHERE item_id = ?");
            $deleteStmt->execute([$itemId]);
            
            // Optionally delete the image file
            if ($item['image_url'] && file_exists('../../' . $item['image_url'])) {
                unlink('../../' . $item['image_url']);
            }
            
            $response['success'] = true;
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Portfolio item not found or not owned by you';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);