<?php
require_once 'db.php';

// Authentication functions
function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

function isBuyer() {
  return isset($_SESSION['role']) && $_SESSION['role'] == 'buyer';
}

function requireLogin() {
  if (!isLoggedIn()) {
      header('Location: ' . SITE_URL . '/login.php');
      exit;
  }
}

function requireBuyer() {
  requireLogin();
  if (!isBuyer()) {
      header('Location: ' . SITE_URL . '/index.php');
      exit;
  }
}

function getUserCollectionsCount($user_id, $filters = []) {
    global $pdo;
    
    $where = "uc.user_id = :user_id AND uc.archived = 0";
    $params = [':user_id' => $user_id];
    $join = "";
    
    // Apply filters
    if (!empty($filters['category'])) {
        $where .= " AND a.category = :category";
        $params[':category'] = $filters['category'];
    }
    
    if (!empty($filters['artist'])) {
        $where .= " AND (u.firstname LIKE :artist OR u.lastname LIKE :artist)";
        $params[':artist'] = "%" . $filters['artist'] . "%";
    }
    
    if (!empty($filters['price_min'])) {
        $where .= " AND a.price >= :price_min";
        $params[':price_min'] = $filters['price_min'];
    }
    
    if (!empty($filters['price_max'])) {
        $where .= " AND a.price <= :price_max";
        $params[':price_max'] = $filters['price_max'];
    }
    
    if (!empty($filters['collection_type'])) {
        if ($filters['collection_type'] == 'purchased') {
            $where .= " AND uc.is_purchased = 1";
        } elseif ($filters['collection_type'] == 'wishlist') {
            $where .= " AND uc.is_purchased = 0";
        }
    }
    
    if (!empty($filters['folder_id'])) {
        $join = "JOIN collection_folder_items cfi ON uc.collection_id = cfi.collection_id";
        $where .= " AND cfi.folder_id = :folder_id";
        $params[':folder_id'] = $filters['folder_id'];
    }
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM user_collections uc
        JOIN artworks a ON uc.artwork_id = a.artwork_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        LEFT JOIN orders o ON uc.purchase_order_id = o.order_id
        $join
        WHERE $where
    ");
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getUserCollections($user_id, $filters = [], $limit = null, $offset = null) {
    global $pdo;
    
    $where = "uc.user_id = :user_id AND uc.archived = 0";
    $params = [':user_id' => $user_id];
    $join = "";
    $pagination = "";
    
    // Apply filters
    if (!empty($filters['category'])) {
        $where .= " AND a.category = :category";
        $params[':category'] = $filters['category'];
    }
    
    if (!empty($filters['artist'])) {
        $where .= " AND (u.firstname LIKE :artist OR u.lastname LIKE :artist)";
        $params[':artist'] = "%" . $filters['artist'] . "%";
    }
    
    if (!empty($filters['price_min'])) {
        $where .= " AND a.price >= :price_min";
        $params[':price_min'] = $filters['price_min'];
    }
    
    if (!empty($filters['price_max'])) {
        $where .= " AND a.price <= :price_max";
        $params[':price_max'] = $filters['price_max'];
    }
    
    if (!empty($filters['collection_type'])) {
        if ($filters['collection_type'] == 'purchased') {
            $where .= " AND uc.is_purchased = 1";
        } elseif ($filters['collection_type'] == 'wishlist') {
            $where .= " AND uc.is_purchased = 0";
        }
    }
    
    if (!empty($filters['folder_id'])) {
        $join = "JOIN collection_folder_items cfi ON uc.collection_id = cfi.collection_id";
        $where .= " AND cfi.folder_id = :folder_id";
        $params[':folder_id'] = $filters['folder_id'];
    }
    
    // Add pagination if specified
    if ($limit !== null) {
        $pagination = " LIMIT :limit";
        $params[':limit'] = (int)$limit;
        
        if ($offset !== null) {
            $pagination .= " OFFSET :offset";
            $params[':offset'] = (int)$offset;
        }
    }
    
    $stmt = $pdo->prepare("
       SELECT uc.*, a.*, ar.*, u.firstname, u.lastname, u.email, o.created_at, o.payment_status
        FROM user_collections uc
        JOIN artworks a ON uc.artwork_id = a.artwork_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        LEFT JOIN orders o ON uc.purchase_order_id = o.order_id
        $join
        WHERE $where
        ORDER BY uc.created_at DESC
        $pagination
    ");
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get user collection folders
 * 
 * @param int $user_id The ID of the user
 * @return array Returns an array with 'folders' and 'items' keys
 */
function getUserCollectionFolders($user_id) {
    global $pdo;// Assuming you have a database connection
    
    // Initialize the return array with empty default values
    $result = [
        'folders' => [],
        'items' => [],
        'total' => 0
    ];
    
    if (!$user_id) {
        return $result; // Return empty default structure if no user_id provided
    }
    
    try {
        // Get collection folders
            // Get collection folders
            $folder_query = "SELECT * FROM collection_folders WHERE user_id = :user_id ORDER BY created_at DESC";
            $folder_stmt = $pdo->prepare($folder_query);
            $folder_stmt->execute([':user_id' => $user_id]);
            $result['folders'] = $folder_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get collection items not in folders
            $items_query = "SELECT c.*, a.title as artwork_title, a.image_url 
                    FROM collections c 
                    LEFT JOIN artworks a ON c.artwork_id = a.id 
                    WHERE c.user_id = :user_id AND (c.folder_id IS NULL OR c.folder_id = 0) 
                    ORDER BY c.created_at DESC";
            $items_stmt = $pdo->prepare($items_query);
            $items_stmt->execute([':user_id' => $user_id]);
            $result['items'] = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Count total items (including those in folders)
            $count_query = "SELECT COUNT(*) as total FROM collections WHERE user_id = :user_id";
            $count_stmt = $pdo->prepare($count_query);
            $count_stmt->execute([':user_id' => $user_id]);
            $result['total'] = $count_stmt->fetchColumn();

            return $result;
            
            } catch (Exception $e) {
            // Log error
            error_log("Error in getUserCollectionFolders: " . $e->getMessage());
            return $result; // Return the default structure on error
            }
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
        ORDER BY o.created_at DESC
        LIMIT ?", 
        [$artistId, $limit]
    );
}

function getArtistArtworks($artistId, $limit = 10, $offset = 0) {
    global $db;
    
    return $db->select("
        SELECT * FROM artworks 
        WHERE artist_id = ? AND archived = 0
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?", 
        [$artistId, $limit, $offset]
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

// Buyer dashboard functions
function getBuyerStats($userId) {
  global $db;
  
  $stats = [];
  
  // Total purchases
  $purchases = $db->selectOne("SELECT COUNT(*) as count FROM orders WHERE buyer_id = ? AND archived = 0", [$userId]);
  $stats['total_purchases'] = $purchases['count'];
  
  // Total spent
  $spent = $db->selectOne("SELECT SUM(total_price) as total FROM orders WHERE buyer_id = ? AND payment_status = 'completed' AND archived = 0", [$userId]);
  $stats['total_spent'] = $spent['total'] ? $spent['total'] : 0;
  
  // Wishlist count
  $wishlist = $db->selectOne("SELECT COUNT(*) as count FROM wishlists WHERE user_id = ? AND archived = 0", [$userId]);
  $stats['wishlist_count'] = $wishlist['count'];
  
  // Pending orders
  $pendingOrders = $db->selectOne("SELECT COUNT(*) as count FROM orders WHERE buyer_id = ? AND payment_status = 'pending' AND archived = 0", [$userId]);
  $stats['pending_orders'] = $pendingOrders['count'];
  
  // Unread messages
  $messages = $db->selectOne("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND seen = 0 AND archived = 0", [$userId]);
  $stats['unread_messages'] = $messages['count'];
  
  return $stats;
}

function getRecentPurchases($userId, $limit = 5) {
  global $db;
  
  return $db->select("
      SELECT o.*, a.title, a.image_url, u.email as artist_name
      FROM orders o 
      JOIN artworks a ON o.artwork_id = a.artwork_id 
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      WHERE o.buyer_id = ? AND o.archived = 0
    ORDER BY o.created_at DESC
    LIMIT  ". (int)$limit,
    [$userId]
  );
}
function recordArtworkView($artworkId, $userId) {
    global $db;

    // Check if the artwork was viewed recently (within the last hour)
    $recentView = $db->selectOne("
        SELECT * FROM artwork_views
        WHERE artwork_id = ? AND user_id = ? AND viewed_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ORDER BY viewed_at DESC LIMIT 1",
        [$artworkId, $userId]
    );

    if ($recentView) {
        // Update the existing view timestamp
        return $db->update('artwork_views',
            ['viewed_at' => date('Y-m-d H:i:s')],
            'view_id = ?',
            [$recentView['view_id']]
        );
    } else {
        // Insert a new view record
        $data = [
            'user_id' => $userId,
            'artwork_id' => $artworkId,
            'viewed_at' => date('Y-m-d H:i:s')
        ];

        return $db->insert('artwork_views', $data);
    }
}
function getRecentlyViewedArtworks($userId, $limit = 3) {
    global $db;

    return $db->select("
      SELECT a.*, av.viewed_at,u.email as artist_name
        FROM artwork_views av
        JOIN artworks a ON av.artwork_id = a.artwork_id
         JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE av.user_id = ? AND a.archived = 0
        ORDER BY av.viewed_at DESC
        LIMIT  " .  (int)$limit,
        [$userId]
    );
}

function getCollection($userId, $limit = 10, $offset = 0, $filters = []) {
  global $db;
  
  $sql = "
      SELECT a.*, o.order_id, o.created_at, o.total_price, u.email as artist_name
      FROM orders o 
      JOIN artworks a ON o.artwork_id = a.artwork_id 
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      WHERE o.buyer_id = ? AND o.payment_status = 'completed' AND o.archived = 0
  ";
  
  $params = [$userId];
  
  // Apply filters
  if (!empty($filters['category'])) {
      $sql .= " AND a.category = ?";
      $params[] = $filters['category'];
  }
  
  if (!empty($filters['artist'])) {
      $sql .= " AND u.email LIKE ?";
      $params[] = "%" . $filters['artist'] . "%";
  }
  
  if (!empty($filters['price_min'])) {
      $sql .= " AND o.total_price >= ?";
      $params[] = $filters['price_min'];
  }
  
  if (!empty($filters['price_max'])) {
      $sql .= " AND o.total_price <= ?";
      $params[] = $filters['price_max'];
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
          case 'date_desc':
              $sql .= " ORDER BY o.created_at DESC";
              break;
          case 'date_asc':
              $sql .= " ORDER BY o.created_at ASC";
              break;
          case 'price_desc':
              $sql .= " ORDER BY o.total_price DESC";
              break;
          case 'price_asc':
              $sql .= " ORDER BY o.total_price ASC";
              break;
          case 'title_asc':
              $sql .= " ORDER BY a.title ASC";
              break;
          case 'title_desc':
              $sql .= " ORDER BY a.title DESC";
              break;
          default:
              $sql .= " ORDER BY o.created_at DESC";
      }
  } else {
      $sql .= " ORDER BY o.created_at DESC";
  }
  
  // Handle LIMIT and OFFSET directly in the SQL (since we're manually casting them)
  $limit = (int)$limit;
  $offset = (int)$offset;
  $sql .= " LIMIT $limit OFFSET $offset";
  
  return $db->select($sql, $params);
}
function getWishlistItems($userId) {
    global $db;

    return $db->select("
        SELECT w.*, a.*, u.email as artist_name
        FROM wishlists w
        JOIN artworks a ON w.artwork_id = a.artwork_id
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        WHERE w.user_id = ? AND w.archived = 0
        ORDER BY w.created_at DESC limit 5",
        [$userId]
    );
}

function getWishlist($userId, $limit = 10, $offset = 0, $filters = []) {
  global $db;
  
  $sql = "
      SELECT w.*, a.*, u.email as artist_name
      FROM wishlists w
      JOIN artworks a ON w.artwork_id = a.artwork_id
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      WHERE w.user_id = ? AND w.archived = 0
  ";
  
  $params = [$userId];
  
  // Apply filters
  if (!empty($filters['category'])) {
      $sql .= " AND a.category = ?";
      $params[] = $filters['category'];
  }
  
  if (!empty($filters['artist'])) {
      $sql .= " AND u.email LIKE ?";
      $params[] = "%" . $filters['artist'] . "%";
  }
  
  if (!empty($filters['price_min'])) {
      $sql .= " AND a.price >= ?";
      $params[] = $filters['price_min'];
  }
  
  if (!empty($filters['price_max'])) {
      $sql .= " AND a.price <= ?";
      $params[] = $filters['price_max'];
  }
  
  // Apply sorting
  if (!empty($filters['sort'])) {
      switch ($filters['sort']) {
          case 'date_desc':
              $sql .= " ORDER BY w.created_at DESC";
              break;
          case 'date_asc':
              $sql .= " ORDER BY w.created_at ASC";
              break;
          case 'price_desc':
              $sql .= " ORDER BY a.price DESC";
              break;
          case 'price_asc':
              $sql .= " ORDER BY a.price ASC";
              break;
          case 'title_asc':
              $sql .= " ORDER BY a.title ASC";
              break;
          case 'title_desc':
              $sql .= " ORDER BY a.title DESC";
              break;
          default:
              $sql .= " ORDER BY w.created_at DESC";
      }
  } else {
      $sql .= " ORDER BY w.created_at DESC";
  }
  
  $sql .= " LIMIT ? OFFSET ?";
  $params[] = $limit;
  $params[] = $offset;
  
  return $db->select($sql, $params);
}

function getPurchaseHistory($userId, $limit = 10, $offset = 0, $filters = []) {
  global $db;
  
  $sql = "
      SELECT o.*, a.title, a.image_url, u.email as artist_name
      FROM orders o 
      JOIN artworks a ON o.artwork_id = a.artwork_id 
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      WHERE o.buyer_id = ? AND o.archived = 0
  ";
  
  $params = [$userId];
  
  // Apply filters
  if (!empty($filters['status'])) {
      $sql .= " AND o.payment_status = ?";
      $params[] = $filters['status'];
  }
  
  if (!empty($filters['artist'])) {
      $sql .= " AND u.email LIKE ?";
      $params[] = "%" . $filters['artist'] . "%";
  }
  
  if (!empty($filters['price_min'])) {
      $sql .= " AND o.total_price >= ?";
      $params[] = $filters['price_min'];
  }
  
  if (!empty($filters['price_max'])) {
      $sql .= " AND o.total_price <= ?";
      $params[] = $filters['price_max'];
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
          case 'date_desc':
              $sql .= " ORDER BY o.created_at DESC";
              break;
          case 'date_asc':
              $sql .= " ORDER BY o.created_at ASC";
              break;
          case 'price_desc':
              $sql .= " ORDER BY o.total_price DESC";
              break;
          case 'price_asc':
              $sql .= " ORDER BY o.total_price ASC";
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

function getOrderDetails($orderId, $userId) {
  global $db;
  
  return $db->selectOne("
      SELECT o.*, a.title, a.description, a.image_url, a.category,
             u.email as artist_name, u.email as artist_email,
             s.tracking_number, s.carrier, s.shipping_date, s.estimated_delivery
      FROM orders o 
      JOIN artworks a ON o.artwork_id = a.artwork_id 
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      LEFT JOIN shipments s ON o.order_id = s.order_id
      WHERE o.order_id = ? AND o.buyer_id = ? AND o.archived = 0", 
      [$orderId, $userId]
  );
}

function getArtworkDetails($artworkId) {
  global $db;
  
  return $db->selectOne("
      SELECT a.*, u.email as artist_name, u.email as artist_email
      FROM artworks a
      JOIN artists ar ON a.artist_id = ar.artist_id
      JOIN users u ON ar.user_id = u.user_id
      WHERE a.artwork_id = ? AND a.archived = 0", 
      [$artworkId]
  );
}

function isInWishlist($artworkId, $userId) {
  global $db;
  
  $result = $db->selectOne("
      SELECT COUNT(*) as count
      FROM wishlists
      WHERE artwork_id = ? AND user_id = ? AND archived = 0", 
      [$artworkId, $userId]
  );
  
  return $result['count'] > 0;
}

function addToWishlist($artworkId, $userId) {
  global $db;
  
  // Check if already in wishlist
  if (isInWishlist($artworkId, $userId)) {
      return false;
  }
  
  $data = [
      'user_id' => $userId,
      'artwork_id' => $artworkId,
      'added_at' => date('Y-m-d H:i:s')
  ];
  
  return $db->insert('wishlist', $data);
}

function removeFromWishlist($artworkId, $userId) {
  global $db;
  
  return $db->delete('wishlist', 'artwork_id = ? AND user_id = ?', [$artworkId, $userId]);
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

function getUserProfile($userId) {
  global $db;
  
  return $db->selectOne("
      SELECT u.*, 
             a.street, a.city, a.state, a.postal_code, a.country,
             p.card_type, p.last_four, p.expiry_date
      FROM users u
      LEFT JOIN addresses a ON u.user_id = a.user_id AND a.is_default = 1
      LEFT JOIN payment_methods p ON u.user_id = p.user_id AND p.is_default = 1
      WHERE u.user_id = ? AND u.archived = 0", 
      [$userId]
  );
}

function getUserDetails($userId) {
  global $db;

  return $db->selectOne("
      SELECT user_id, email, role
      FROM users
      WHERE user_id = ? AND archived = 0",
      [$userId]
  );
}

function getUserAddresses($userId) {
  global $db;
  
  return $db->select("
      SELECT * FROM addresses
      WHERE user_id = ? AND archived = 0
      ORDER BY is_default DESC", 
      [$userId]
  );
}

function getUserPaymentMethods($userId) {
  global $db;
  
  return $db->select("
      SELECT * FROM payment_methods
      WHERE user_id = ? AND archived = 0
      ORDER BY is_default DESC", 
      [$userId]
  );
}

function updateUserProfile($userId, $data) {
  global $db;
  
  return $db->update('users', $data, 'user_id = ?', [$userId]);
}

function addUserAddress($userId, $data) {
  global $db;
  
  $data['user_id'] = $userId;
  
  // If setting as default, unset all other defaults
  if (isset($data['is_default']) && $data['is_default'] == 1) {
      $db->update('addresses', ['is_default' => 0], 'user_id = ?', [$userId]);
  }
  
  return $db->insert('addresses', $data);
}

function updateUserAddress($addressId, $userId, $data) {
  global $db;
  
  // If setting as default, unset all other defaults
  if (isset($data['is_default']) && $data['is_default'] == 1) {
      $db->update('addresses', ['is_default' => 0], 'user_id = ?', [$userId]);
  }
  
  return $db->update('addresses', $data, 'address_id = ? AND user_id = ?', [$addressId, $userId]);
}

function deleteUserAddress($addressId, $userId) {
  global $db;
  
  return $db->delete('addresses', 'address_id = ? AND user_id = ?', [$addressId, $userId]);
}

function addUserPaymentMethod($userId, $data) {
  global $db;
  
  $data['user_id'] = $userId;
  
  // If setting as default, unset all other defaults
  if (isset($data['is_default']) && $data['is_default'] == 1) {
      $db->update('payment_methods', ['is_default' => 0], 'user_id = ?', [$userId]);
  }
  
  return $db->insert('payment_methods', $data);
}

function updateUserPaymentMethod($paymentId, $userId, $data) {
  global $db;
  
  // If setting as default, unset all other defaults
  if (isset($data['is_default']) && $data['is_default'] == 1) {
      $db->update('payment_methods', ['is_default' => 0], 'user_id = ?', [$userId]);
  }
  
  return $db->update('payment_methods', $data, 'payment_id = ? AND user_id = ?', [$paymentId, $userId]);
}

function deleteUserPaymentMethod($paymentId, $userId) {
  global $db;
  
  return $db->delete('payment_methods', 'payment_id = ? AND user_id = ?', [$paymentId, $userId]);
}

function updateNotificationPreferences($userId, $preferences) {
  global $db;
  
  return $db->update('users', ['notification_preferences' => json_encode($preferences)], 'user_id = ?', [$userId]);
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

function generateCertificateOfAuthenticity($orderId, $userId) {
  global $db;
  
  $order = getOrderDetails($orderId, $userId);
  
  if (!$order) {
      return false;
  }
  
  // Generate certificate content
  $certificate = [
      'order_id' => $order['order_id'],
      'artwork_title' => $order['title'],
      'artist_name' => $order['artist_name'],
      'purchase_date' => $order['created_at'],
      'buyer_name' => $_SESSION['email'],
      'certificate_number' => 'COA-' . $order['order_id'] . '-' . date('Ymd'),
      'issue_date' => date('Y-m-d H:i:s'),
      'artwork_details' => $order['description'],
      'category' => $order['category']
  ];
  
  // Store certificate in database
  $data = [
      'order_id' => $order['order_id'],
      'certificate_number' => $certificate['certificate_number'],
      'issue_date' => $certificate['issue_date'],
      'certificate_data' => json_encode($certificate)
  ];
  
  $db->insert('certificates', $data);
  
  return $certificate;
}

function getShippingCarriers() {
  return [
      'fedex' => 'FedEx',
      'ups' => 'UPS',
      'usps' => 'USPS',
      'dhl' => 'DHL'
  ];
}

function getTrackingUrl($carrier, $trackingNumber) {
  switch ($carrier) {
      case 'fedex':
          return "https://www.fedex.com/apps/fedextrack/?tracknumbers={$trackingNumber}";
      case 'ups':
          return "https://www.ups.com/track?tracknum={$trackingNumber}";
      case 'usps':
          return "https://tools.usps.com/go/TrackConfirmAction?tLabels={$trackingNumber}";
      case 'dhl':
          return "https://www.dhl.com/en/express/tracking.html?AWB={$trackingNumber}";
      default:
          return "#";
  }
}
?>

