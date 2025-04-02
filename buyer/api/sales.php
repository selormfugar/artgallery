<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an artist
requireArtist();

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'details' && isset($_GET['order_id'])) {
        // Get order details
        $orderId = (int)$_GET['order_id'];
        
        global $db;
        $order = $db->selectOne("
            SELECT o.*, a.title, a.description, a.category, a.image_url, 
                   u.username as buyer_name, u.email as buyer_email
            FROM orders o 
            JOIN artworks a ON o.artwork_id = a.artwork_id 
            JOIN users u ON o.buyer_id = u.user_id
            WHERE o.order_id = ? AND a.artist_id = ? AND o.archived = 0", 
            [$orderId, $_SESSION['artist_id']]
        );
        
        if ($order) {
            echo json_encode($order);
        } else {
            $response['message'] = 'Order not found.';
            echo json_encode($response);
        }
    } elseif ($action === 'filter' && isset($_GET['range'])) {
        // Filter sales by time range
        $range = $_GET['range'];
        $artistId = $_SESSION['artist_id'];
        
        global $db;
        $whereClause = "a.artist_id = ? AND o.archived = 0";
        $params = [$artistId];
        
        if ($range === 'week') {
            $whereClause .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($range === 'month') {
            $whereClause .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($range === 'year') {
            $whereClause .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }
        
        // Get filtered sales
        $sales = $db->select("
            SELECT o.*, a.title, a.image_url, u.username as buyer_name, u.user_id as buyer_id
            FROM orders o 
            JOIN artworks a ON o.artwork_id = a.artwork_id 
            JOIN users u ON o.buyer_id = u.user_id
            WHERE $whereClause
            ORDER BY o.created_at DESC", 
            $params
        );
        
        // Get stats for filtered sales
        $stats = [
            'total_sales' => count($sales),
            'total_revenue' => 0,
            'completed_sales' => 0,
            'pending_sales' => 0,
            'failed_sales' => 0
        ];
        
        foreach ($sales as $sale) {
            if ($sale['payment_status'] === 'completed') {
                $stats['total_revenue'] += $sale['total_price'];
                $stats['completed_sales']++;
            } elseif ($sale['payment_status'] === 'pending') {
                $stats['pending_sales']++;
            } elseif ($sale['payment_status'] === 'failed') {
                $stats['failed_sales']++;
            }
        }
        
        echo json_encode([
            'sales' => $sales,
            'stats' => $stats
        ]);
    } elseif (isset($_GET['period'])) {
        // Get sales data for chart
        $period = $_GET['period'];
        $artistId = $_SESSION['artist_id'];
        
        $salesData = getSalesData($artistId, $period);
        echo json_encode($salesData);
    } else {
        $response['message'] = 'Invalid action.';
        echo json_encode($response);
    }
}
?>

