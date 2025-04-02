<?php
require_once 'db.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], [ROLE_SUPER_ADMIN, ROLE_ADMIN]);
}

function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == ROLE_SUPER_ADMIN;
}

// function requireLogin() {
//     if (!isLoggedIn()) {
//         header('Location: ' . SITE_URL . '/login.php');
//         exit;
//     }
// }

function requirePermission($permission) {
    requireLogin();
    
    if (!hasPermission($permission)) {
        header('Location: ' . SITE_URL . '/access-denied.php');
        exit;
    }
}

function hasPermission($permission) {
    if (!isLoggedIn()) {
        return false;
    }
    
    global $role_permissions;
    $role = $_SESSION['admin_role'];
    
    return in_array($permission, $role_permissions[$role]);
}

// function login($email, $password) {
//     global $db;
    
//     $admin = $db->selectOne("SELECT * FROM users WHERE email = ? AND archived = 0", [$email]);
    
//     if ($admin && password_verify($password, $admin['password_hash'])) {
//         $_SESSION['admin_id'] = $admin['user_id'];
//         $_SESSION['admin_name'] = $admin['email'];
//         $_SESSION['admin_role'] = $admin['role'];
        
//         // Log login
//         logAdminActivity('login', 'Admin logged in');
        
//         return true;
//     }
    
//     return false;
// }

function logout() {
    // Log logout
    if (isLoggedIn()) {
        logAdminActivity('logout', 'Admin logged out');
    }
    
    session_unset();
    session_destroy();
}

// Admin dashboard functions
function getDashboardStats() {
    global $db;
    
    $stats = [];
    
    // Total users
    $users = $db->selectOne("SELECT COUNT(*) as count FROM users WHERE archived = 0");
    $stats['total_users'] = $users['count'];
    
    // New users in last 30 days
    $newUsers = $db->selectOne("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND archived = 0");
    $stats['new_users'] = $newUsers['count'];
    
    // User growth percentage
    $previousPeriodUsers = $db->selectOne("SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY) AND archived = 0");
    $stats['user_growth'] = $previousPeriodUsers['count'] > 0 ? round(($newUsers['count'] - $previousPeriodUsers['count']) / $previousPeriodUsers['count'] * 100, 2) : 100;
    
    // Total artworks
    $artworks = $db->selectOne("SELECT COUNT(*) as count FROM artworks WHERE archived = 0");
    $stats['total_artworks'] = $artworks['count'];
    
    // Pending moderation
    $pendingModeration = $db->selectOne("SELECT COUNT(*) as count FROM artworks WHERE moderation_status = 'pending' AND archived = 0");
    $stats['pending_moderation'] = $pendingModeration['count'];
    
    // Total sales
    $sales = $db->selectOne("SELECT COUNT(*) as count, SUM(total_price) as revenue FROM orders WHERE payment_status = 'completed' AND archived = 0");
    $stats['total_sales'] = $sales['count'];
    $stats['total_revenue'] = $sales['revenue'] ? $sales['revenue'] : 0;
    
    // Sales in last 30 days
    $recentSales = $db->selectOne("SELECT COUNT(*) as count, SUM(total_price) as revenue FROM orders WHERE payment_status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND archived = 0");
    $stats['recent_sales'] = $recentSales['count'];
    $stats['recent_revenue'] = $recentSales['revenue'] ? $recentSales['revenue'] : 0;
    
    // Sales growth percentage
    $previousPeriodSales = $db->selectOne("SELECT SUM(total_price) as revenue FROM orders WHERE payment_status = 'completed' AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY) AND archived = 0");
    $previousRevenue = $previousPeriodSales['revenue'] ? $previousPeriodSales['revenue'] : 0;
    $stats['revenue_growth'] = $previousRevenue > 0 ? round(($recentSales['revenue'] - $previousRevenue) / $previousRevenue * 100, 2) : 100;
    
    // Flagged content
    $flagged = $db->selectOne("SELECT COUNT(*) as count FROM content_flags WHERE status = 'pending' AND archived = 0");
    $stats['flagged_content'] = $flagged['count'];
    
    return $stats;
}

function getRecentUsers($limit = 5) {
    global $db;
    
    return $db->select("
        SELECT * FROM users 
        WHERE archived = 0 
        ORDER BY created_at DESC 
        LIMIT $limit"
    );
}

function getRecentSales($limit = 5) {
    global $db;
    
    return $db->select("
        SELECT o.*, a.title, a.image_url, 
               b.email as buyer_name, 
               s.email as seller_name
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        JOIN users b ON o.buyer_id = b.user_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users s ON ar.user_id = s.user_id
        WHERE o.archived = 0
        ORDER BY o.created_at DESC 
        LIMIT $limit"
    );
}

function getPendingModeration($limit = 5) {
    global $db;
    
    return $db->select("
        SELECT a.*, u.email as artist_name
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE a.moderation_status = 'pending' 
        AND a.archived = 0
        ORDER BY a.created_at ASC
        LIMIT $limit"
    );
}


function getAllUsers() {
    global $db; // Assuming $db is your database connection
    $query = "SELECT user_id, email, role, created_at FROM users";
    $result = $db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

function checkAdminPrivileges() {
    if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], [ROLE_SUPER_ADMIN, ROLE_ADMIN])) {
        header('Location: ' . SITE_URL . '/access-denied.php');
        exit;
    }
}

function fetchReportedContent($limit = 20, $offset = 0) {
    global $db;
    return $db->select("
        SELECT f.*, u.email as reporter_name
        FROM content_flags f
        LEFT JOIN users u ON f.reporter_id = u.user_id
        WHERE f.archived = 0
        ORDER BY f.created_at DESC
        LIMIT ? OFFSET ?", 
        [$limit, $offset]
    );
}

function getUsers($limit = 20, $offset = 0, $filters = []) {
    global $db;
    
    $sql = "
        SELECT u.*, 
               (SELECT COUNT(*) FROM artworks a JOIN artists ar ON a.artist_id = ar.artist_id WHERE ar.user_id = u.user_id AND a.archived = 0) as artwork_count,
               (SELECT COUNT(*) FROM orders WHERE buyer_id = u.user_id AND archived = 0) as order_count
        FROM users u
        WHERE u.archived = 0
    ";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['search'])) {
        $sql .= " AND (u.email LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($filters['role'])) {
        $sql .= " AND u.role = ?";
        $params[] = $filters['role'];
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND u.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND u.created_at >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND u.created_at <= ?";
        $params[] = $filters['date_to'];
    }
    
    // Apply sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'username_asc':
                $sql .= " ORDER BY u.email ASC";
                break;
            case 'username_desc':
                $sql .= " ORDER BY u.email DESC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY u.created_at ASC";
                break;
            case 'date_desc':
                $sql .= " ORDER BY u.created_at DESC";
                break;
            default:
                $sql .= " ORDER BY u.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY u.created_at DESC";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->select($sql, $params);
}

function getUserDetails($userId) {
    global $db;
    
    $user = $db->selectOne("
        SELECT u.*, 
               (SELECT COUNT(*) FROM artworks a JOIN artists ar ON a.artist_id = ar.artist_id WHERE ar.user_id = u.user_id AND a.archived = 0) as artwork_count,
               (SELECT COUNT(*) FROM orders WHERE buyer_id = u.user_id AND archived = 0) as order_count,
               (SELECT SUM(total_price) FROM orders WHERE buyer_id = u.user_id AND payment_status = 'completed' AND archived = 0) as total_spent
        FROM users u
        WHERE u.user_id = ? AND u.archived = 0", 
        [$userId]
    );
    
    if (!$user) {
        return null;
    }
    
    // Get user's addresses
    $user['addresses'] = $db->select("
        SELECT * FROM addresses 
        WHERE user_id = ? AND archived = 0
        ORDER BY is_default DESC", 
        [$userId]
    );
    
    // Get user's payment methods (masked)
    $user['payment_methods'] = $db->select("
        SELECT * FROM payment_methods 
        WHERE user_id = ? AND archived = 0
        ORDER BY is_default DESC", 
        [$userId]
    );
    
    // Get user's recent orders
    $user['recent_orders'] = $db->select("
        SELECT o.*, a.title, a.image_url
        FROM orders o
        JOIN artworks a ON o.artwork_id = a.artwork_id
        WHERE o.buyer_id = ? AND o.archived = 0
        ORDER BY o.created_at DESC
        LIMIT 5", 
        [$userId]
    );
    
    // If user is an artist, get artist details
    $artist = $db->selectOne("
        SELECT * FROM artists 
        WHERE user_id = ? AND archived = 0", 
        [$userId]
    );
    
    if ($artist) {
        $user['is_artist'] = true;
        $user['artist_id'] = $artist['artist_id'];
        $user['artist_bio'] = $artist['bio'];
        $user['artist_website'] = $artist['website'];
        
        // Get artist's artworks
        $user['artworks'] = $db->select("
            SELECT a.*, 
                   (SELECT COUNT(*) FROM orders WHERE artwork_id = a.artwork_id AND archived = 0) as order_count
            FROM artworks a
            WHERE a.artist_id = ? AND a.archived = 0
            ORDER BY a.created_at DESC
            LIMIT 10", 
            [$artist['artist_id']]
        );
    } else {
        $user['is_artist'] = false;
    }
    
    // Get login history
    $user['login_history'] = $db->select("
        SELECT * FROM user_logins
        WHERE user_id = ?
        ORDER BY login_time DESC
        LIMIT 10", 
        [$userId]
    );
    
    return $user;
}

function updateUserStatus($userId, $status) {
    global $db;
    
    $result = $db->update('users', 
        ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 
        'user_id = ?', 
        [$userId]
    );
    
    if ($result) {
        logAdminActivity('update_user_status', "Updated user ID $userId status to $status");
    }
    
    return $result;
}

function getArtworks($limit = 20, $offset = 0, $filters = []) {
    global $db;
    
    $sql = "
        SELECT a.*, u.email as artist_name
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE a.archived = 0
    ";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['search'])) {
        $sql .= " AND (a.title LIKE ? OR a.description LIKE ? OR u.email LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($filters['category'])) {
        $sql .= " AND a.category = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['moderation_status'])) {
        $sql .= " AND a.moderation_status = ?";
        $params[] = $filters['moderation_status'];
    }
    
    if (!empty($filters['price_min'])) {
        $sql .= " AND a.price >= ?";
        $params[] = $filters['price_min'];
    }
    
    if (!empty($filters['price_max'])) {
        $sql .= " AND a.price <= ?";
        $params[] = $filters['price_max'];
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND a.created_at >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND a.created_at <= ?";
        $params[] = $filters['date_to'];
    }
    
    // Apply sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'title_asc':
                $sql .= " ORDER BY a.title ASC";
                break;
            case 'title_desc':
                $sql .= " ORDER BY a.title DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY a.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY a.price DESC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY a.created_at ASC";
                break;
            case 'date_desc':
                $sql .= " ORDER BY a.created_at DESC";
                break;
            default:
                $sql .= " ORDER BY a.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY a.created_at DESC";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->select($sql, $params);
}

function getArtworkDetails($artworkId) {
    global $db;
    
    $artwork = $db->selectOne("
        SELECT a.*, u.email as artist_name, u.email as artist_email,
               ar.bio as artist_bio, ar.website as artist_website
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE a.artwork_id = ? AND a.archived = 0", 
        [$artworkId]
    );
    
    if (!$artwork) {
        return null;
    }
    
    // Get moderation history
    $artwork['moderation_history'] = $db->select("
        SELECT * FROM moderation_logs
        WHERE artwork_id = ?
        ORDER BY created_at DESC", 
        [$artworkId]
    );
    
    // Get sales history
    $artwork['sales'] = $db->select("
        SELECT o.*, u.email as buyer_name
        FROM orders o
        JOIN users u ON o.buyer_id = u.user_id
        WHERE o.artwork_id = ? AND o.archived = 0
        ORDER BY o.created_at DESC", 
        [$artworkId]
    );
    
    // Get content flags
    $artwork['flags'] = $db->select("
        SELECT f.*, u.email as reporter_name
        FROM content_flags f
        LEFT JOIN users u ON f.reporter_id = u.user_id
        WHERE f.content_type = 'artwork' AND f.content_id = ? AND f.archived = 0
        ORDER BY f.created_at DESC", 
        [$artworkId]
    );
    
    return $artwork;
}

function moderateArtwork($artworkId, $status, $notes = '') {
    global $db;
    
    try {
        $db->beginTransaction();
        
        // Update artwork status
        $result = $db->update('artworks', 
            [
                'moderation_status' => $status, 
                'moderated_at' => date('Y-m-d H:i:s'),
                'moderated_by' => $_SESSION['admin_id']
            ], 
            'artwork_id = ?', 
            [$artworkId]
        );
        
        if (!$result) {
            $db->rollback();
            return false;
        }
        
        // Log moderation action
        $logData = [
            'artwork_id' => $artworkId,
            'admin_id' => $_SESSION['admin_id'],
            'status' => $status,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('moderation_logs', $logData);
        
        // Log admin activity
        logAdminActivity('moderate_artwork', "Moderated artwork ID $artworkId with status $status");
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function batchModerateArtworks($artworkIds, $status, $notes = '') {
    global $db;
    
    try {
        $db->beginTransaction();
        
        foreach ($artworkIds as $artworkId) {
            // Update artwork status
            $result = $db->update('artworks', 
                [
                    'moderation_status' => $status, 
                    'moderated_at' => date('Y-m-d H:i:s'),
                    'moderated_by' => $_SESSION['admin_id']
                ], 
                'artwork_id = ?', 
                [$artworkId]
            );
            
            if (!$result) {
                $db->rollback();
                return false;
            }
            
            // Log moderation action
            $logData = [
                'artwork_id' => $artworkId,
                'admin_id' => $_SESSION['admin_id'],
                'status' => $status,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('moderation_logs', $logData);
        }
        
        // Log admin activity
        $artworkCount = count($artworkIds);
        logAdminActivity('batch_moderate_artwork', "Batch moderated $artworkCount artworks with status $status");
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function getTransactions($limit = 20, $offset = 0, $filters = []) {
    global $db;
    
    $sql = "
        SELECT o.*, a.title as artwork_title, a.image_url,
               b.email as buyer_name, 
               s.email as seller_name
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        JOIN users b ON o.buyer_id = b.user_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users s ON ar.user_id = s.user_id
        WHERE o.archived = 0
    ";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['search'])) {
        $sql .= " AND (a.title LIKE ? OR b.email LIKE ? OR s.email LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND o.payment_status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['amount_min'])) {
        $sql .= " AND o.total_price >= ?";
        $params[] = $filters['amount_min'];
    }
    
    if (!empty($filters['amount_max'])) {
        $sql .= " AND o.total_price <= ?";
        $params[] = $filters['amount_max'];
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND o.created_at >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND o.created_at <= ?";
        $params[] = $filters['date_to'];
    }
    
    // Apply sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'date_asc':
                $sql .= " ORDER BY o.created_at ASC";
                break;
            case 'date_desc':
                $sql .= " ORDER BY o.created_at DESC";
                break;
            case 'amount_asc':
                $sql .= " ORDER BY o.total_price ASC";
                break;
            case 'amount_desc':
                $sql .= " ORDER BY o.total_price DESC";
                break;
            default:
                $sql .= " ORDER BY o.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY o.created_at DESC";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->select($sql, $params);
}

function getTransactionDetails($orderId) {
    global $db;
    
    $order = $db->selectOne("
        SELECT o.*, a.title as artwork_title, a.description as artwork_description, 
               a.image_url, a.category,
               b.email as buyer_name, b.email as buyer_email,
               s.email as seller_name, s.email as seller_email,
               sh.tracking_number, sh.carrier, sh.shipping_date, sh.estimated_delivery
        FROM orders o 
        JOIN artworks a ON o.artwork_id = a.artwork_id 
        JOIN users b ON o.buyer_id = b.user_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users s ON ar.user_id = s.user_id
        LEFT JOIN shipments sh ON o.order_id = sh.order_id
        WHERE o.order_id = ? AND o.archived = 0", 
        [$orderId]
    );
    
    if (!$order) {
        return null;
    }
    
    // Get payment details
    $order['payment'] = $db->selectOne("
        SELECT * FROM payments
        WHERE order_id = ?", 
        [$orderId]
    );
    
    // Get transaction history
    $order['transaction_history'] = $db->select("
        SELECT * FROM transaction_logs
        WHERE order_id = ?
        ORDER BY created_at DESC", 
        [$orderId]
    );
    
    // Get refund history if any
    $order['refunds'] = $db->select("
        SELECT * FROM refunds
        WHERE order_id = ?
        ORDER BY created_at DESC", 
        [$orderId]
    );
    
    return $order;
}

function updateTransactionStatus($orderId, $status, $notes = '') {
    global $db;
    
    try {
        $db->beginTransaction();
        
        // Update order status
        $result = $db->update('orders', 
            [
                'payment_status' => $status, 
                'updated_at' => date('Y-m-d H:i:s')
            ], 
            'order_id = ?', 
            [$orderId]
        );
        
        if (!$result) {
            $db->rollback();
            return false;
        }
        
        // Log transaction action
        $logData = [
            'order_id' => $orderId,
            'admin_id' => $_SESSION['admin_id'],
            'status' => $status,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('transaction_logs', $logData);
        
        // Log admin activity
        logAdminActivity('update_transaction', "Updated transaction ID $orderId status to $status");
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function processRefund($orderId, $amount, $reason, $notes = '') {
    global $db;
    
    try {
        $db->beginTransaction();
        
        // Get order details
        $order = $db->selectOne("
            SELECT * FROM orders
            WHERE order_id = ? AND archived = 0", 
            [$orderId]
        );
        
        if (!$order || $order['payment_status'] != 'completed') {
            $db->rollback();
            return false;
        }
        
        // Create refund record
        $refundData = [
            'order_id' => $orderId,
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'processed_by' => $_SESSION['admin_id'],
            'status' => 'processed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $refundId = $db->insert('refunds', $refundData);
        
        if (!$refundId) {
            $db->rollback();
            return false;
        }
        
        // Update order status if full refund
        if ($amount >= $order['total_price']) {
            $db->update('orders', 
                [
                    'payment_status' => 'refunded', 
                    'updated_at' => date('Y-m-d H:i:s')
                ], 
                'order_id = ?', 
                [$orderId]
            );
        } else {
            $db->update('orders', 
                [
                    'payment_status' => 'partially_refunded', 
                    'updated_at' => date('Y-m-d H:i:s')
                ], 
                'order_id = ?', 
                [$orderId]
            );
        }
        
        // Log transaction action
        $logData = [
            'order_id' => $orderId,
            'admin_id' => $_SESSION['admin_id'],
            'status' => 'refund_processed',
            'notes' => "Refund processed: $amount. Reason: $reason",
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('transaction_logs', $logData);
        
        // Log admin activity
        logAdminActivity('process_refund', "Processed refund for order ID $orderId. Amount: $amount");
        
        $db->commit();
        return $refundId;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function getFlaggedContent($limit = 20, $offset = 0, $filters = []) {
    global $db;
    
    $sql = "
        SELECT f.*, 
               u.email as reporter_name,
               CASE 
                   WHEN f.content_type = 'artwork' THEN a.title
                   WHEN f.content_type = 'comment' THEN c.content
                   WHEN f.content_type = 'message' THEN m.content
                   ELSE 'Unknown'
               END as content_title
        FROM content_flags f
        LEFT JOIN users u ON f.reporter_id = u.user_id
        LEFT JOIN artworks a ON f.content_type = 'artwork' AND f.content_id = a.artwork_id
        LEFT JOIN comments c ON f.content_type = 'comment' AND f.content_id = c.comment_id
        LEFT JOIN messages m ON f.content_type = 'message' AND f.content_id = m.message_id
        WHERE f.archived = 0
    ";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['content_type'])) {
        $sql .= " AND f.content_type = ?";
        $params[] = $filters['content_type'];
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND f.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['severity'])) {
        $sql .= " AND f.severity = ?";
        $params[] = $filters['severity'];
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND f.created_at >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND f.created_at <= ?";
        $params[] = $filters['date_to'];
    }
    
    // Apply sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'date_asc':
                $sql .= " ORDER BY f.created_at ASC";
                break;
            case 'date_desc':
                $sql .= " ORDER BY f.created_at DESC";
                break;
            case 'severity_asc':
                $sql .= " ORDER BY f.severity ASC";
                break;
            case 'severity_desc':
                $sql .= " ORDER BY f.severity DESC";
                break;
            default:
                $sql .= " ORDER BY f.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY f.severity DESC, f.created_at DESC";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->select($sql, $params);
}

function getFlagDetails($flagId) {
    global $db;
    
    $flag = $db->selectOne("
        SELECT f.*, u.email as reporter_name, u.email as reporter_email
        FROM content_flags f
        LEFT JOIN users u ON f.reporter_id = u.user_id
        WHERE f.flag_id = ? AND f.archived = 0", 
        [$flagId]
    );
    
    if (!$flag) {
        return null;
    }
    
    // Get content details based on type
    switch ($flag['content_type']) {
        case 'artwork':
            $content = $db->selectOne("
                SELECT a.*, u.email as artist_name
                FROM artworks a
                JOIN artists ar ON a.artist_id = ar.artist_id
                JOIN users u ON ar.user_id = u.user_id
                WHERE a.artwork_id = ?", 
                [$flag['content_id']]
            );
            break;
        case 'comment':
            $content = $db->selectOne("
                SELECT c.*, u.email as commenter_name
                FROM comments c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.comment_id = ?", 
                [$flag['content_id']]
            );
            break;
        case 'message':
            $content = $db->selectOne("
                SELECT m.*, 
                       s.email as sender_name,
                       r.email as receiver_name
                FROM messages m
                JOIN users s ON m.sender_id = s.user_id
                JOIN users r ON m.receiver_id = r.user_id
                WHERE m.message_id = ?", 
                [$flag['content_id']]
            );
            break;
        default:
            $content = null;
    }
    
    $flag['content_details'] = $content;
    
    // Get resolution history
    $flag['resolution_history'] = $db->select("
        SELECT * FROM flag_resolutions
        WHERE flag_id = ?
        ORDER BY created_at DESC", 
        [$flagId]
    );
    
    return $flag;
}

function resolveFlaggedContent($flagId, $resolution, $action, $notes = '') {
    global $db;
    
    try {
        $db->beginTransaction();
        
        // Get flag details
        $flag = $db->selectOne("
            SELECT * FROM content_flags
            WHERE flag_id = ? AND archived = 0", 
            [$flagId]
        );
        
        if (!$flag) {
            $db->rollback();
            return false;
        }
        
        // Update flag status
        $result = $db->update('content_flags', 
            [
                'status' => 'resolved', 
                'resolution' => $resolution,
                'resolved_at' => date('Y-m-d H:i:s'),
                'resolved_by' => $_SESSION['admin_id']
            ], 
            'flag_id = ?', 
            [$flagId]
        );
        
        if (!$result) {
            $db->rollback();
            return false;
        }
        
        // Take action on content if needed
        if ($action != 'none') {
            switch ($flag['content_type']) {
                case 'artwork':
                    if ($action == 'remove') {
                        $db->softDelete('artworks', 'artwork_id = ?', [$flag['content_id']]);
                    } else if ($action == 'moderate') {
                        $db->update('artworks', 
                            [
                                'moderation_status' => 'flagged',
                                'moderated_at' => date('Y-m-d H:i:s'),
                                'moderated_by' => $_SESSION['admin_id']
                            ], 
                            'artwork_id = ?', 
                            [$flag['content_id']]
                        );
                    }
                    break;
                case 'comment':
                    if ($action == 'remove') {
                        $db->softDelete('comments', 'comment_id = ?', [$flag['content_id']]);
                    }
                    break;
                case 'message':
                    if ($action == 'remove') {
                        $db->softDelete('messages', 'message_id = ?', [$flag['content_id']]);
                    }
                    break;
            }
        }
        
        // Log resolution
        $resolutionData = [
            'flag_id' => $flagId,
            'admin_id' => $_SESSION['admin_id'],
            'resolution' => $resolution,
            'action' => $action,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('flag_resolutions', $resolutionData);
        
        // Log admin activity
        logAdminActivity('resolve_flag', "Resolved flag ID $flagId with resolution: $resolution, action: $action");
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function getSystemSettings() {
    global $db;
    
    $settings = [];
    
    $results = $db->select("SELECT * FROM system_settings");
    
    foreach ($results as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

function updateSystemSettings($settings) {
    global $db;
    
    try {
        $db->beginTransaction();
        
        foreach ($settings as $key => $value) {
            $db->update('system_settings', 
                ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                'setting_key = ?', 
                [$key]
            );
        }
        
        // Log admin activity
        logAdminActivity('update_settings', "Updated system settings");
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function getEmailTemplates() {
    global $db;
    
    return $db->select("SELECT * FROM email_templates ORDER BY name ASC");
}

function getEmailTemplate($templateId) {
    global $db;
    
    return $db->selectOne("SELECT * FROM email_templates WHERE template_id = ?", [$templateId]);
}

function updateEmailTemplate($templateId, $data) {
    global $db;
    
    $result = $db->update('email_templates', 
        [
            'subject' => $data['subject'],
            'body' => $data['body'],
            'updated_at' => date('Y-m-d H:i:s')
        ], 
        'template_id = ?', 
        [$templateId]
    );
    
    if ($result) {
        logAdminActivity('update_email_template', "Updated email template ID $templateId");
    }
    
    return $result;
}

function getAdminUsers() {
    global $db;
    
    return $db->select("
        SELECT a.*, r.name as role_name
        FROM admin_users a
        JOIN admin_roles r ON a.role_id = r.role_id
        WHERE a.archived = 0
        ORDER BY a.name ASC
    ");
}

function getAdminUser($adminId) {
    global $db;
    
    return $db->selectOne("
        SELECT a.*, r.name as role_name
        FROM admin_users a
        JOIN admin_roles r ON a.role_id = r.role_id
        WHERE a.admin_id = ? AND a.archived = 0", 
        [$adminId]
    );
}

function createAdminUser($data) {
    global $db;
    
    // Check if email already exists
    $existing = $db->selectOne("SELECT * FROM admin_users WHERE email = ?", [$data['email']]);
    
    if ($existing) {
        return false;
    }
    
    $adminData = [
        'name' => $data['name'],
        'email' => $data['email'],
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role_id' => $data['role_id'],
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => $_SESSION['admin_id']
    ];
    
    $adminId = $db->insert('admin_users', $adminData);
    
    if ($adminId) {
        logAdminActivity('create_admin', "Created new admin user ID $adminId");
    }
    
    return $adminId;
}

function updateAdminUser($adminId, $data) {
    global $db;
    
    $adminData = [
        'name' => $data['name'],
        'role_id' => $data['role_id'],
        'status' => $data['status'],
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Update password if provided
    if (!empty($data['password'])) {
        $adminData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    
    $result = $db->update('admin_users', $adminData, 'admin_id = ?', [$adminId]);
    
    if ($result) {
        logAdminActivity('update_admin', "Updated admin user ID $adminId");
    }
    
    return $result;
}

function deleteAdminUser($adminId) {
    global $db;
    
    // Don't allow deleting yourself
    if ($adminId == $_SESSION['admin_id']) {
        return false;
    }
    
    $result = $db->softDelete('admin_users', 'admin_id = ?', [$adminId]);
    
    if ($result) {
        logAdminActivity('delete_admin', "Deleted admin user ID $adminId");
    }
    
    return $result;
}

function getAdminRoles() {
    global $db;
    
    return $db->select("SELECT * FROM admin_roles ORDER BY role_id ASC");
}

function logAdminActivity($action, $description) {
    global $db;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $logData = [
        'admin_id' => $_SESSION['admin_id'],
        'action' => $action,
        'description' => $description,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return $db->insert('admin_activity_logs', $logData);
}

function getAdminActivityLogs($limit = 100, $adminId = null) {
    global $db;
    
    $sql = "
        SELECT l.*, a.name as admin_name
        FROM admin_activity_logs l
        JOIN admin_users a ON l.admin_id = a.admin_id
    ";
    
    $params = [];
    
    if ($adminId) {
        $sql .= " WHERE l.admin_id = ?";
        $params[] = $adminId;
    }
    
    $sql .= " ORDER BY l.created_at DESC LIMIT ?";
    $params[] = $limit;
    
    return $db->select($sql, $params);
}

function getSalesReportData($period = 'month', $startDate = null, $endDate = null) {
    global $db;
    
    // Set default dates if not provided
    if (!$startDate) {
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'month':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'year':
                $startDate = date('Y-m-d', strtotime('-1 year'));
                break;
            default:
                $startDate = date('Y-m-d', strtotime('-30 days'));
        }
    }
    
    if (!$endDate) {
        $endDate = date('Y-m-d');
    }
    
    // Format for grouping
    $groupFormat = '';
    switch ($period) {
        case 'week':
            $groupFormat = '%Y-%m-%d';
            break;
        case 'month':
            $groupFormat = '%Y-%m-%d';
            break;
        case 'year':
            $groupFormat = '%Y-%m';
            break;
        default:
            $groupFormat = '%Y-%m-%d';
    }
    
    // Get sales data
    $salesData = $db->select("
        SELECT 
            DATE_FORMAT(created_at, ?) as date,
            COUNT(*) as order_count,
            SUM(total_price) as revenue,
            AVG(total_price) as average_order_value
        FROM orders
        WHERE payment_status = 'completed'
        AND created_at BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(created_at, ?)
        ORDER BY date ASC
    ", [$groupFormat, $startDate, $endDate, $groupFormat]);
    
    // Get category breakdown
    $categoryData = $db->select("
        SELECT 
            a.category,
            COUNT(*) as order_count,
            SUM(o.total_price) as revenue
        FROM orders o
        JOIN artworks a ON o.artwork_id = a.artwork_id
        WHERE o.payment_status = 'completed'
        AND o.created_at BETWEEN ? AND ?
        GROUP BY a.category
        ORDER BY revenue DESC
    ", [$startDate, $endDate]);
    
    // Get top artists
    $topArtists = $db->select("
        SELECT 
            u.email as artist_name,
            COUNT(*) as order_count,
            SUM(o.total_price) as revenue
        FROM orders o
        JOIN artworks a ON o.artwork_id = a.artwork_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE o.payment_status = 'completed'
        AND o.created_at BETWEEN ? AND ?
        GROUP BY u.email
        ORDER BY revenue DESC
        LIMIT 10
    ", [$startDate, $endDate]);
    
    // Get top buyers
    $topBuyers = $db->select("
        SELECT 
            u.email as buyer_name,
            COUNT(*) as order_count,
            SUM(o.total_price) as total_spent
        FROM orders o
        JOIN users u ON o.buyer_id = u.user_id
        WHERE o.payment_status = 'completed'
        AND o.created_at BETWEEN ? AND ?
        GROUP BY u.email
        ORDER BY total_spent DESC
        LIMIT 10
    ", [$startDate, $endDate]);
    
    // Get totals
    $totals = $db->selectOne("
        SELECT 
            COUNT(*) as total_orders,
            SUM(total_price) as total_revenue,
            AVG(total_price) as average_order_value
        FROM orders
        WHERE payment_status = 'completed'
        AND created_at BETWEEN ? AND ?
    ", [$startDate, $endDate]);
    
    return [
        'sales_data' => $salesData,
        'category_data' => $categoryData,
        'top_artists' => $topArtists,
        'top_buyers' => $topBuyers,
        'totals' => $totals,
        'period' => $period,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];
}

function getUserReportData($period = 'month', $startDate = null, $endDate = null) {
    global $db;
    
    // Set default dates if not provided
    if (!$startDate) {
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'month':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'year':
                $startDate = date('Y-m-d', strtotime('-1 year'));
                break;
            default:
                $startDate = date('Y-m-d', strtotime('-30 days'));
        }
    }
    
    if (!$endDate) {
        $endDate = date('Y-m-d');
    }
    
    // Format for grouping
    $groupFormat = '';
    switch ($period) {
        case 'week':
            $groupFormat = '%Y-%m-%d';
            break;
        case 'month':
            $groupFormat = '%Y-%m-%d';
            break;
        case 'year':
            $groupFormat = '%Y-%m';
            break;
        default:
            $groupFormat = '%Y-%m-%d';
    }
    
    // Get user registration data
    $registrationData = $db->select("
        SELECT 
            DATE_FORMAT(created_at, ?) as date,
            COUNT(*) as user_count
        FROM users
        WHERE created_at BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(created_at, ?)
        ORDER BY date ASC
    ", [$groupFormat, $startDate, $endDate, $groupFormat]);
    
    // Get user role breakdown
    $roleData = $db->select("
        SELECT 
            role,
            COUNT(*) as user_count
        FROM users
        WHERE created_at BETWEEN ? AND ?
        GROUP BY role
    ", [$startDate, $endDate]);
    
    // Get active users (users who made purchases)
    $activeUsers = $db->selectOne("
        SELECT 
            COUNT(DISTINCT buyer_id) as active_buyer_count
        FROM orders
        WHERE created_at BETWEEN ? AND ?
    ", [$startDate, $endDate]);
    
    // Get active artists (artists who sold artwork)
    $activeArtists = $db->selectOne("
        SELECT 
            COUNT(DISTINCT a.artist_id) as active_artist_count
        FROM orders o
        JOIN artworks a ON o.artwork_id = a.artwork_id
        WHERE o.created_at BETWEEN ? AND ?
    ", [$startDate, $endDate]);
    
    // Get totals
    $totals = $db->selectOne("
        SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) as total_buyers,
            SUM(CASE WHEN role = 'artist' THEN 1 ELSE 0 END) as total_artists
        FROM users
        WHERE created_at BETWEEN ? AND ?
    ", [$startDate, $endDate]);
    
    return [
        'registration_data' => $registrationData,
        'role_data' => $roleData,
        'active_users' => $activeUsers,
        'active_artists' => $activeArtists,
        'totals' => $totals,
        'period' => $period,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];
}

// Helper functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($date) {
    return date('M d, Y h:i A', strtotime($date));
}

function getPercentageChange($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    
    return round(($current - $previous) / $previous * 100, 2);
}

function generateCSV($data, $headers) {
    $output = fopen('php://temp', 'w');
    
    // Add headers
    fputcsv($output, $headers);
    
    // Add data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

function generatePDF($html, $filename) {
    // This is a placeholder. In a real application, you would use a library like TCPDF, FPDF, or Dompdf
    // to generate a PDF file from HTML content.
    
    // For now, we'll just return the HTML
    return $html;
}
?>

