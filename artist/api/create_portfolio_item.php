<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';;

header('Content-Type: application/json');

$artistId = $_SESSION['artist_id'];
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
    
    if (empty($title) || empty($category)) {
        $response['error'] = 'Title and category are required';
        echo json_encode($response);
        exit;
    }
    
    // Handle image upload
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadPortfolioImage($_FILES['image'], $artistId);
        
        if ($uploadResult['success']) {
            try {
                $stmt = $pdo->prepare("INSERT INTO portfolio_items 
                                      (artist_id, title, description, image_url, category, tags) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $artistId, 
                    $title, 
                    $description, 
                    $uploadResult['path'], 
                    $category,
                    $tags
                ]);
                
                $response['success'] = true;
                $response['item'] = [
                    'id' => $pdo->lastInsertId(),
                    'title' => $title,
                    'image_url' => $uploadResult['path']
                ];
            } catch (PDOException $e) {
                $response['error'] = 'Database error: ' . $e->getMessage();
            }
        } else {
            $response['error'] = $uploadResult['error'];
        }
    } else {
        $response['error'] = 'Image upload failed';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);