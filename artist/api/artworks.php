<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in and is an artist
requireArtist();

$response = ['success' => false, 'message' => ''];

/**
 * Handles auction data in the auctions table
 */
function handleAuctionData($db, $artworkId, $postData, $isCreating = false) {
    $isForAuction = isset($postData['is_for_auction']) && $postData['is_for_auction'];
    
    if ($isForAuction) {
        // Validate required fields
        $required = ['start_time', 'end_time', 'starting_price'];
        foreach ($required as $field) {
            if (empty($postData[$field])) {
                throw new Exception("Missing required auction field: $field");
            }
        }

        $startTime = $postData['start_time'];
        $endTime = $postData['end_time'];
        $startingPrice = (float)$postData['starting_price'];
        $reservePrice = !empty($postData['reserve_price']) ? (float)$postData['reserve_price'] : null;

        // Validate auction times
        if (strtotime($endTime) <= strtotime($startTime)) {
            throw new Exception('Auction end time must be after start time');
        }

        // Validate prices
        if ($startingPrice <= 0) {
            throw new Exception('Starting price must be positive');
        }

        if ($reservePrice !== null && $reservePrice < $startingPrice) {
            throw new Exception('Reserve price cannot be less than starting price');
        }

        $auctionData = [
            'artwork_id' => $artworkId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'starting_price' => $startingPrice,
            'reserve_price' => $reservePrice,
            'current_bid' => $startingPrice, // Initialize with starting price
            'status' => (strtotime($startTime) <= time()) ? 'active' : 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check for existing auction
        $existingAuction = $db->selectOne(
            "SELECT auction_id FROM auctions WHERE artwork_id = ? AND archived = 0",
            [$artworkId]
        );

        if ($existingAuction) {
            // Update existing auction
            $db->update('auctions', $auctionData, 'auction_id = ?', [$existingAuction['auction_id']]);
        } else {
            // Create new auction
            $auctionData['created_at'] = date('Y-m-d H:i:s');
            $db->insert('auctions', $auctionData);
        }
    } else {
        // Cancel any existing auction
        $db->update(
            'auctions',
            [
                'status' => 'cancelled',
                'updated_at' => date('Y-m-d H:i:s'),
                'archived' => 1
            ],
            'artwork_id = ?',
            [$artworkId]
        );
    }
}

try {
    // Verify CSRF token for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token'])) {
            throw new Exception('CSRF token is required');
        }
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception('Invalid CSRF token');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'update';
        
        // Validate and sanitize input
        $artworkId = isset($_POST['artwork_id']) ? (int)$_POST['artwork_id'] : null;
        $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
        $description = trim(htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'));
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $categoryId = trim(htmlspecialchars($_POST['category'] ?? '', ENT_QUOTES, 'UTF-8'));
        
        // Basic validation
        if (empty($title)) {
            throw new Exception('Title is required');
        }
        
        if (strlen($title) > 100) {
            throw new Exception('Title must be less than 100 characters');
        }
        
        if ($price === false || $price < 0) {
            throw new Exception('Price must be a positive number');
        }

        // Prepare artwork data (removed auction-specific fields)
        $isForAuction = isset($_POST['is_for_auction']) ? 1 : 0;
        $isForSale = isset($_POST['is_for_sale']) ? 1 : 0;
        
        // Ensure mutual exclusivity
        if ($isForAuction && $isForSale) {
            $isForSale = 0; // Force sale to 0 if auction is 1
        }

        $artworkData = [
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'category' => $categoryId,
            'is_for_sale' => $isForSale,
            'is_for_auction' => $isForAuction,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $upload = uploadImage($_FILES['image']);
                if (!$upload['success']) {
                    throw new Exception($upload['message']);
                }
                $artworkData['image_url'] = $upload['path'];
                
                // Delete old image if exists
                if ($artworkId && !empty($_POST['current_image'])) {
                    $oldImagePath = UPLOAD_DIR . basename($_POST['current_image']);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

        // Start transaction
        $db->getConnection()->beginTransaction();
        
        try {
            if ($action === 'update' && $artworkId) {
                // Verify artist owns this artwork
                $existingArtwork = $db->selectOne(
                    "SELECT artist_id, image_url FROM artworks WHERE artwork_id = ? AND archived = 0", 
                    [$artworkId]
                );
                
                if (!$existingArtwork) {
                    throw new Exception('Artwork not found');
                }
                
                if ($existingArtwork['artist_id'] !== $_SESSION['user_id']) {
                    throw new Exception('You can only edit your own artworks');
                }

                // Update artwork
                $db->update('artworks', $artworkData, 'artwork_id = ?', [$artworkId]);
                
                // Handle auction data
                handleAuctionData($db, $artworkId, $_POST);
                
                $response['message'] = 'Artwork updated successfully';
            } 
            elseif ($action === 'create') {
                // Create new artwork
                $artworkData['artist_id'] = $_SESSION['user_id'];
                $artworkData['created_at'] = date('Y-m-d H:i:s');
                $artworkId = $db->insert('artworks', $artworkData);
                
                // Handle auction data if enabled
                if ($isForAuction) {
                    handleAuctionData($db, $artworkId, $_POST, true);
                }
                
                $response['message'] = 'Artwork created successfully';
                $response['artwork_id'] = $artworkId;
            }
            elseif ($action === 'delete' && $artworkId) {
                // Verify ownership
                $existingArtwork = $db->selectOne(
                    "SELECT artist_id FROM artworks WHERE artwork_id = ? AND archived = 0", 
                    [$artworkId]
                );
                
                if (!$existingArtwork || $existingArtwork['artist_id'] !== $_SESSION['user_id']) {
                    throw new Exception('Artwork not found or you do not have permission');
                }

                // Soft delete artwork
                $db->update(
                    'artworks', 
                    ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')], 
                    'artwork_id = ?', 
                    [$artworkId]
                );
                
                // Also cancel any associated auctions
                $db->update(
                    'auctions',
                    [
                        'status' => 'cancelled',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'archived' => 1
                    ],
                    'artwork_id = ?',
                    [$artworkId]
                );
                
                $response['message'] = 'Artwork deleted successfully';
            }
            else {
                throw new Exception('Invalid action');
            }
            
            $db->getConnection()->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            throw $e;
        }
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get':
                // Get single artwork with auction details
                if (!isset($_GET['id'])) {
                    throw new Exception('Artwork ID is required');
                }
                
                $artworkId = (int)$_GET['id'];
                $artwork = $db->selectOne("
                    SELECT 
                        a.artwork_id, a.artist_id, a.title, a.description, a.price, 
                        a.category, a.image_url, a.created_at, a.updated_at, 
                        a.moderation_status, a.is_for_auction, a.is_for_sale, a.archived
                    FROM artworks a
                    WHERE a.artwork_id = ? AND a.artist_id = ? AND a.archived = 0
                ", [$artworkId, $_SESSION['user_id']]);
                
                if (!$artwork) {
                    throw new Exception('Artwork not found');
                }
                
                // Get auction details if this is an auction
                if ($artwork['is_for_auction']) {
                    $auction = $db->selectOne("
                        SELECT 
                            auction_id, start_time, end_time, starting_price, 
                            reserve_price, current_bid, status
                        FROM auctions
                        WHERE artwork_id = ? AND archived = 0
                        ORDER BY created_at DESC
                        LIMIT 1
                    ", [$artworkId]);
                    
                    if ($auction) {
                        $artwork['auction'] = $auction;
                    }
                }
                
                $response['success'] = true;
                $response['artwork'] = $artwork;
                break;
                
            case 'search':
                // Search artworks
                $term = isset($_GET['term']) ? trim($_GET['term']) : '';
                $category = isset($_GET['category']) ? $_GET['category'] : '';
                $artistId = isset($_GET['artist_id']) ? (int)$_GET['artist_id'] : $_SESSION['user_id'];
                
                $query = "
                    SELECT 
                        a.artwork_id, a.artist_id, a.title, a.description, a.price, 
                        a.category, a.image_url, a.created_at, a.is_for_auction, a.is_for_sale
                    FROM artworks a
                    WHERE a.artist_id = ? AND a.archived = 0
                ";
                
                $params = [$artistId];
                
                if (!empty($term)) {
                    $query .= " AND (a.title LIKE ? OR a.description LIKE ?)";
                    $params[] = "%$term%";
                    $params[] = "%$term%";
                }
                
                if (!empty($category)) {
                    $query .= " AND a.category = ?";
                    $params[] = $category;
                }
                
                $query .= " ORDER BY a.created_at DESC";
                
                $artworks = $db->select($query, $params);
                
                // Get auction status for auction items
                foreach ($artworks as &$artwork) {
                    if ($artwork['is_for_auction']) {
                        $auction = $db->selectOne("
                            SELECT status, end_time 
                            FROM auctions 
                            WHERE artwork_id = ? AND archived = 0
                            LIMIT 1
                        ", [$artwork['artwork_id']]);
                        
                        if ($auction) {
                            $artwork['auction_status'] = $auction['status'];
                            $artwork['auction_ended'] = strtotime($auction['end_time']) < time();
                        }
                    }
                }
                
                $response['success'] = true;
                $response['artworks'] = $artworks;
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);