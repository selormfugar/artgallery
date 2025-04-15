<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$artistId = $_SESSION['artist_id'];
$response = ['success' => false, 'data' => []];

try {
    // Get artwork statistics for the last 30 days
    $stmt = $pdo->prepare("SELECT a.artwork_id, a.title, 
                          COUNT(av.view_id) as view_count,
                          DATE(av.viewed_at) as view_date
                          FROM artworks a
                          LEFT JOIN artwork_views av ON a.artwork_id = av.artwork_id
                          WHERE a.artist_id = ? AND a.archived = 0
                          AND av.viewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                          GROUP BY a.artwork_id, DATE(av.viewed_at)
                          ORDER BY a.artwork_id, view_date");
    $stmt->execute([$artistId]);
    
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for chart
    $artworks = [];
    $dates = [];
    $currentDate = new DateTime();
    $dateInterval = new DateInterval('P1D');
    
    // Generate date range for last 30 days
    for ($i = 0; $i < 30; $i++) {
        $dateKey = $currentDate->format('Y-m-d');
        $dates[$dateKey] = 0;
        $currentDate->sub($dateInterval);
    }
    
    foreach ($stats as $stat) {
        $artworkId = $stat['artwork_id'];
        
        if (!isset($artworks[$artworkId])) {
            $artworks[$artworkId] = [
                'id' => $artworkId,
                'title' => $stat['title'],
                'views' => array_merge([], $dates) // Clone dates array
            ];
        }
        
        $viewDate = $stat['view_date'];
        if (isset($artworks[$artworkId]['views'][$viewDate])) {
            $artworks[$artworkId]['views'][$viewDate] = (int)$stat['view_count'];
        }
    }
    
    $response['success'] = true;
    $response['data'] = array_values($artworks);
} catch (PDOException $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);