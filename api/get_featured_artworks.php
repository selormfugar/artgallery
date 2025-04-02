<?php
// Enable CORS and set JSON content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database connection
require_once '../includes/db.php';
require_once '../includes/config.php';
require_once '../includes/auth_check.php'; // Uncomment if authentication is needed

try {
    // Prepare SQL query to get featured artworks
    $sql = "SELECT 
                a.artwork_id,
                a.title,
                a.image_url,
                a.price,
                CONCAT(u.firstname, ' ', u.lastname) as artist_name
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            WHERE a.is_for_sale = 1 OR a.is_for_auction = 1
            ORDER BY a.created_at DESC
            LIMIT 3";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Fetch all results as associative array
    $featuredArtworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return JSON response
    echo json_encode($featuredArtworks);

} catch (PDOException $e) {
    // Return error message
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Close the connection
$pdo = null;
?>