<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['user_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $itemId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$itemId) {
        $response['error'] = 'Invalid item ID';
        echo json_encode($response);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE item_id = ? AND artist_id = ?");
        $stmt->execute([$itemId, $artistId]);
        $item = $stmt->fetch();
        
        if ($item) {
            $response['success'] = true;
            $response['item'] = $item;
        } else {
            $response['error'] = 'Portfolio item not found or not owned by you';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);