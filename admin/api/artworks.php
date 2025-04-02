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
    
    if ($action === 'get' && isset($_GET['id'])) {
        // Get artwork details
        $artworkId = (int)$_GET['id'];
        $artwork = getArtworkById($artworkId, $_SESSION['artist_id']);
        
        if ($artwork) {
            echo json_encode($artwork);
        } else {
            $response['message'] = 'Artwork not found.';
            echo json_encode($response);
        }
    } elseif ($action === 'search') {
        // Search artworks
        global $db;
        $term = isset($_GET['term']) ? sanitizeInput($_GET['term']) : '';
        $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
        $artistId = $_SESSION['artist_id'];
        
        $sql = "SELECT * FROM artworks WHERE artist_id = ? AND archived = 0";
        $params = [$artistId];
        
        if (!empty($term)) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = "%$term%";
            $params[] = "%$term%";
        }
        
        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $artworks = $db->select($sql, $params);
        echo json_encode($artworks);
    } else {
        $response['message'] = 'Invalid action.';
        echo json_encode($response);
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'update') {
        // Update artwork
        $artworkId = (int)$_POST['artwork_id'];
        $artwork = getArtworkById($artworkId, $_SESSION['artist_id']);
        
        if ($artwork) {
            global $db;
            
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);
            $price = floatval($_POST['price']);
            $category = sanitizeInput($_POST['category']);
            
            $data = [
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Handle image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = uploadImage($_FILES['image']);
                
                if ($imageName) {
                    $data['image_url'] = $imageName;
                } else {
                    $response['message'] = 'Error uploading image.';
                    echo json_encode($response);
                    exit;
                }
            }
            
            $result = $db->update('artworks', $data, 'artwork_id = ?', [$artworkId]);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Artwork updated successfully.';
            } else {
                $response['message'] = 'Error updating artwork.';
            }
        } else {
            $response['message'] = 'Artwork not found.';
        }
        
        echo json_encode($response);
    } elseif ($action === 'delete') {
        // Delete artwork (soft delete)
        $artworkId = (int)$_POST['artwork_id'];
        $artwork = getArtworkById($artworkId, $_SESSION['artist_id']);
        
        if ($artwork) {
            global $db;
            
            $result = $db->delete('artworks', 'artwork_id = ?', [$artworkId]);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Artwork deleted successfully.';
            } else {
                $response['message'] = 'Error deleting artwork.';
            }
        } else {
            $response['message'] = 'Artwork not found.';
        }
        
        echo json_encode($response);
    } else {
        $response['message'] = 'Invalid action.';
        echo json_encode($response);
    }
}
?>

