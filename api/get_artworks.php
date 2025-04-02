<?php
require_once '../includes/config.php';
// require_once '../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Get filter parameters
    $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
    $price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $items_per_page = 12;

    // Process price range
    $price_min = null;
    $price_max = null;
    if (!empty($price_range)) {
        $range_parts = explode('-', $price_range);
        if (count($range_parts) === 2) {
            $price_min = $range_parts[0] !== '' ? floatval($range_parts[0]) : null;
            $price_max = $range_parts[1] !== '' ? floatval($range_parts[1]) : null;
        }
    }

    // Build base SQL query
    $sql = "SELECT a.*, 
                   CONCAT(u.firstname, ' ', u.lastname) as artist_name,
                   c.name as category_name,
                   au.auction_id
            FROM artworks a
            JOIN artists ar ON a.artist_id = ar.artist_id
            JOIN users u ON ar.user_id = u.user_id
            LEFT JOIN categories c ON a.category = c.category_id
            LEFT JOIN auctions au ON a.artwork_id = au.artwork_id AND au.status = 'active'
            WHERE a.archived = 0 AND a.moderation_status = 'completed'";

    // Add filters to query
    $params = [];

    if ($category_id) {
        $sql .= " AND a.category = :category_id";
        $params[':category_id'] = $category_id;
    }

    if ($price_min !== null) {
        $sql .= " AND a.price >= :price_min";
        $params[':price_min'] = $price_min;
    }

    if ($price_max !== null) {
        $sql .= " AND a.price <= :price_max";
        $params[':price_max'] = $price_max;
    }

    if (!empty($search_query)) {
        $sql .= " AND (a.title LIKE :search OR a.description LIKE :search OR u.firstname LIKE :search OR u.lastname LIKE :search)";
        $params[':search'] = "%$search_query%";
    }

    // Add sorting
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY a.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY a.price DESC";
            break;
        case 'oldest':
            $sql .= " ORDER BY a.created_at ASC";
            break;
        default: // 'newest'
            $sql .= " ORDER BY a.created_at DESC";
            break;
    }

    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM ($sql) as total_query";
    $count_stmt = $pdo->prepare($count_sql);
    
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    
    $count_stmt->execute();
    $total_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
    $total_items = $total_result['total'];

    // Add pagination to main query
    $offset = ($page - 1) * $items_per_page;
    $sql .= " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $items_per_page;
    $params[':offset'] = $offset;

    // Get paginated results
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare response
    $response = [
        'success' => true,
        'artworks' => $artworks,
        'has_more' => ($total_items > $page * $items_per_page),
        'total' => $total_items
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching artworks: ' . $e->getMessage()
    ]);
}