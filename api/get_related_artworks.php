<?php
require_once '../includes/config.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

try {
    $artist_id = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 0;
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : 0;

    if ($artist_id <= 0) {
        throw new Exception("Invalid artist ID");
    }

    $stmt = $pdo->prepare("
        SELECT a.*, CONCAT(u.firstname, ' ', u.lastname) as artist_name
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE a.artist_id = ? 
        AND a.artwork_id != ?
        AND a.archived = 0
        AND a.moderation_status = 'completed'
        ORDER BY RAND()
        LIMIT 4
    ");
    $stmt->execute([$artist_id, $exclude_id]);
    $artworks = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'artworks' => $artworks
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}