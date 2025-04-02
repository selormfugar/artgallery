<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query based on filters
$where = "WHERE a.archived = 0 AND a.moderation_status = 'completed'";
$params = [];

// Category filter
if (!empty($_GET['category'])) {
    $where .= " AND a.category = ?";
    $params[] = $_GET['category'];
}

// Price range filter
if (!empty($_GET['price_range'])) {
    list($min, $max) = explode('-', $_GET['price_range']);
    if ($max === '') {
        $where .= " AND a.price >= ?";
        $params[] = $min;
    } else {
        $where .= " AND a.price BETWEEN ? AND ?";
        $params[] = $min;
        $params[] = $max;
    }
}

// Search query
if (!empty($_GET['search'])) {
    $where .= " AND (a.title LIKE ? OR a.description LIKE ? OR CONCAT(u.firstname, ' ', u.lastname) LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Count total artworks for pagination
$countStmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM artworks a
    JOIN artists ar ON a.artist_id = ar.artist_id
    JOIN users u ON ar.user_id = u.user_id
    $where
");
$countStmt->execute($params);
$totalArtworks = $countStmt->fetch()['total'];

// Fetch artworks
$stmt = $pdo->prepare("
    SELECT 
        a.*, 
        CONCAT(u.firstname, ' ', u.lastname) as artist_name,
        c.name as category_name
    FROM artworks a
    JOIN artists ar ON a.artist_id = ar.artist_id
    JOIN users u ON ar.user_id = u.user_id
    JOIN categories c ON a.category = c.name
    $where
    ORDER BY a.created_at DESC
    LIMIT ? OFFSET ?
");
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$artworks = $stmt->fetchAll();

// Prepare response
$response = [
    'artworks' => $artworks,
    'has_more' => ($totalArtworks > ($offset + $limit))
];

echo json_encode($response);