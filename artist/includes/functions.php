<?php
require_once 'db.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isArtist() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'artist';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function uploadPortfolioImage($file, $artistId) {
    $result = ['success' => false, 'error' => '', 'path' => ''];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        $result['error'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        return $result;
    }
    
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds maximum limit of 5MB.';
        return $result;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../../uploads/portfolio/' . $artistId . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('portfolio_') . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $result['success'] = true;
        $result['path'] = str_replace('../../', '/', $destination); // Convert to web path
    } else {
        $result['error'] = 'Failed to move uploaded file.';
    }
    
    return $result;
}

function getSubscriberCount($pdo, $planId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_subscriptions 
                          WHERE plan_id = ? AND status = 'active'");
    $stmt->execute([$planId]);
    return $stmt->fetchColumn();
}

function requireArtist() {
    requireLogin();
    if (!isArtist()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

// function login($email, $password) {
//     global $db;
   
//     $user = $db->selectOne("SELECT * FROM users WHERE email = ? AND archived = 0", [$email]);
   
//     if ($user && password_verify($password, $user['password_hash'])) {  // Added password_verify for bcrypt
//         $_SESSION['user_id'] = $user['user_id'];
//         $_SESSION['email'] = $user['email'];
//         $_SESSION['role'] = $user['role'];
       
//         if ($user['role'] == 'artist') {
//             $artist = $db->selectOne("SELECT * FROM artists WHERE user_id = ?", [$user['user_id']]);
//             $_SESSION['artist_id'] = $artist['artist_id'];
//         }
       
//         return true;
//     }
   
//     return false;
// }
function logout() {
    session_unset();
    session_destroy();
}

// Artist dashboard functions
function getArtistStats($artistId) {
    global $db;
    
    $stats = [];
    
    // Total artworks
    $artworks = $db->selectOne("SELECT COUNT(*) as count FROM artworks WHERE artist_id = ? AND archived = 0", [$artistId]);
    $stats['total_artworks'] = $artworks['count'];
    
    // Total sales
    $sales = $db->selectOne("
        SELECT COUNT(*) as count, SUM(o.total_price) as revenue 
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        WHERE a.artist_id = ? AND o.payment_status = 'completed' AND o.archived = 0", 
        [$artistId]
    );
    $stats['total_sales'] = $sales['count'];
    $stats['total_revenue'] = $sales['revenue'] ? $sales['revenue'] : 0;
    
    // Pending sales
    $pendingSales = $db->selectOne("
        SELECT COUNT(*) as count 
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        WHERE a.artist_id = ? AND o.payment_status = 'pending' AND o.archived = 0", 
        [$artistId]
    );
    $stats['pending_sales'] = $pendingSales['count'];
    
    // Unread messages
    $messages = $db->selectOne("
        SELECT COUNT(*) as count 
        FROM messages 
        WHERE receiver_id = ? AND seen = 0 AND archived = 0", 
        [$_SESSION['user_id']]
    );
    $stats['unread_messages'] = $messages['count'];
    
    return $stats;
}

function getRecentSales($artistId, $limit = 5) {
    global $db;
    
    return $db->select("
        SELECT o.*, a.title, a.image_url, u.email as buyer_name
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        JOIN users u ON o.buyer_id = u.user_id
        WHERE a.artist_id = ? AND o.archived = 0
        ORDER BY o.created_at DESC limit 3", 
        [$artistId]
    );
}

function getArtistArtworks($artistId, $limit = 10, $offset = 0) {
    global $db;
    
    return $db->select("
     SELECT 
    a.artwork_id, 
    a.artist_id, 
    a.title, 
    a.description, 
    a.price,
    a.category,
    c.name AS category_name,
    a.image_url, 
    a.created_at, 
    a.updated_at, 
    a.moderation_status, 
    a.is_for_auction, 
    a.is_for_sale, 
    a.archived,
    au.auction_id,
    au.start_time,
    au.end_time AS auction_end_date,
    au.starting_price AS starting_bid,
    au.current_bid,
    au.status AS auction_status,
    COALESCE(b.bid_count, 0) AS bid_count
FROM 
    artworks a
LEFT JOIN 
    categories c ON a.category = c.category_id
LEFT JOIN 
    auctions au ON a.artwork_id = au.artwork_id AND au.archived = 0
LEFT JOIN (
    SELECT 
        auction_id, 
        COUNT(*) AS bid_count
    FROM 
        bids
    GROUP BY 
        auction_id
) b ON b.auction_id = au.auction_id
WHERE 
    a.artist_id = ? 
    AND a.archived = 0
ORDER BY 
    a.created_at DESC
       ", 
        [$artistId]
    );
}

function getArtworkById($artworkId, $artistId) {
    global $db;
    
    return $db->selectOne("
        SELECT * FROM artworks 
        WHERE artwork_id = ? AND artist_id = ? AND archived = 0", 
        [$artworkId, $artistId]
    );
}

function getCategories() {
    global $db;
    
    return $db->select("SELECT * FROM categories WHERE archived = 0");
}

function getMessages($userId, $limit = 10, $offset = 0) {
    global $db;
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    return $db->select("
        SELECT m.*, 
               u_sender.email as sender_name,
               u_receiver.email as receiver_name
        FROM messages m
        JOIN users u_sender ON m.sender_id = u_sender.user_id
        JOIN users u_receiver ON m.receiver_id = u_receiver.user_id
        WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.archived = 0
        ORDER BY m.created_at DESC
        LIMIT $limit OFFSET $offset", 
        [$userId, $userId]
    );
}

function getSalesData($artistId, $period = 'month') {
    global $db;
    
    $sql = "
        SELECT 
            DATE_FORMAT(o.created_at, '%Y-%m-%d') as date,
            COUNT(*) as sales_count,
            SUM(o.total_price) as revenue
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        WHERE a.artist_id = ? AND o.payment_status = 'completed' AND o.archived = 0
    ";
    
    if ($period == 'week') {
        $sql .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } else if ($period == 'month') {
        $sql .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } else if ($period == 'year') {
        $sql .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    }
    
    $sql .= " GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d') ORDER BY date";
    
    return $db->select($sql, [$artistId]);
}

// Helper functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (!function_exists('uploadImage')) {
    function uploadImage($file, $directory = UPLOAD_DIR) {
        // Validate input
        if (!isset($file) || !is_array($file) || !isset($file['tmp_name'])) {
            throw new InvalidArgumentException('Invalid file upload data');
        }

    // Check and create directory
    if (!file_exists($directory)) {
        if (!mkdir($directory, 0755, true)) { // More secure permissions (0755)
            throw new RuntimeException("Failed to create upload directory");
        }
    }

    // Generate secure filename
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid('img_', true) . '.' . $imageFileType;
    $targetFile = rtrim($directory, '/') . '/' . $newFileName;

    // Security validations
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Check if actual image
    if (!getimagesize($file['tmp_name'])) {
        throw new RuntimeException('File is not a valid image');
    }

    // Check file size (5MB limit)
    if ($file['size'] > 5000000) {
        throw new RuntimeException('File exceeds maximum size of 5MB');
    }

    // Check file extension
    if (!in_array($imageFileType, $validExtensions)) {
        throw new RuntimeException('Only JPG, JPEG, PNG & GIF files are allowed');
    }

    // Verify MIME type matches extension
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $validMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    if (!isset($validMimes[$imageFileType]) || $mime !== $validMimes[$imageFileType]) {
        throw new RuntimeException('File type does not match its content');
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        throw new RuntimeException('Failed to move uploaded file');
    }

    // Set proper permissions
    chmod($targetFile, 0644);
        return $newFileName;
    }
}


function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}
?>

