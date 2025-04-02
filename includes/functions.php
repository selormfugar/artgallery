<?php
require_once 'db.php';
// require_once 'config.php';


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

function requireArtist() {
    requireLogin();
    if (!isArtist()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}
function login($email, $password) {
    global $db;
   
    // Input Validation
    if (empty($email) || empty($password)) {
        error_log("Login failed: Empty email or password.");
        return false;
    }

    // Sanitize email (optional, depending on your database layer)
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   
    $user = $db->selectOne("SELECT * FROM users WHERE email = ? AND archived = 0", [$email]);
   
    if (!$user) {
        // Generic error message to prevent email enumeration
        error_log("Login failed: Invalid credentials for email $email.");
        return false;
    }
   
    if (!password_verify($password, $user['password_hash'])) {
        error_log("Login failed: Incorrect password for email $email.");
        return false;
    }
   
    // Consider adding additional checks
    // if ($user['status'] === 'suspended') {
    //     error_log("Login failed: Account suspended for email $email.");
    //     return false;
    // }

    // Session management
    session_regenerate_id(true); // Prevent session fixation
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
   
    // Role-based session setup
    switch ($user['role']) {
        case 'artist':
            $_SESSION['artist_id'] = $user['user_id'];
            break;
        case 'admin':
            $_SESSION['is_admin'] = true;
            break;
        case 'buyer':
            $_SESSION['is_buyer'] = true;
            break;
        default:
            error_log("Unexpected user role: " . $user['role']);
            return false;
    }
   
    // Optional: Log successful login
    error_log("Successful login for email $email");

    return true;
}
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
        ORDER BY o.created_at DESC", 
        [$artistId]
    );
}

function getArtistArtworks($artistId, $limit = 10, $offset = 0) {
    global $db;
    
    return $db->select("
        SELECT * FROM artworks 
        WHERE artist_id = ? AND archived = 0
        ORDER BY created_at DESC
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
    
    return $db->select("
        SELECT m.*, 
               u_sender.email as sender_name,
               u_receiver.email as receiver_name
        FROM messages m
        JOIN users u_sender ON m.sender_id = u_sender.user_id
        JOIN users u_receiver ON m.receiver_id = u_receiver.user_id
        WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.archived = 0
        ORDER BY m.created_at DESC
        LIMIT ? OFFSET ?", 
        [$userId, $userId, $limit, $offset]
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

function uploadImage($file, $directory = UPLOAD_DIR) {
    // Check if directory exists, if not create it
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $targetFile = $directory . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $directory . $newFileName;
    
    // Check if image file is a actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false;
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $newFileName;
    } else {
        return false;
    }
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}
/**
 * Fetch user details by user ID.
 *
 * @param int $userId The ID of the user.
 * @return array|null The user details or null if not found.
 */
function getUserDetails($userId) {
    global $db;
    return $db->selectOne("SELECT * FROM users WHERE user_id = ? AND archived = 0", [$userId]);
}
?>

