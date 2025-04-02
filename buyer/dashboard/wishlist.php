<?php
// Include necessary files (only once)
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['firstname'] ?? 'User') : 'Guest';

// Only attempt to fetch wishlist items if user is logged in
$wishlistItems = [];
if ($is_logged_in) {
    try {
        $query = "SELECT w.wishlist_id, a.artwork_id, a.title, a.price, a.image_url, 
                  u.firstname AS artist_name
                  FROM wishlists w
                  JOIN artworks a ON w.artwork_id = a.artwork_id
                  JOIN users u ON u.user_id = a.artist_id
                  WHERE w.user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
        $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log error
        error_log("Wishlist query error: " . $e->getMessage());
    }
}
// Set pagination parameters
$limit = 12; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get filters from query string
$filters = [
    'category' => isset($_GET['category']) ? sanitizeInput($_GET['category']) : '',
    'artist' => isset($_GET['artist']) ? sanitizeInput($_GET['artist']) : '',
    'price_min' => isset($_GET['price_min']) ? (float)$_GET['price_min'] : '',
    'price_max' => isset($_GET['price_max']) ? (float)$_GET['price_max'] : '',
    'date_from' => isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '',
    'date_to' => isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '',
    'sort' => isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'date_desc'
];

// Get collection
$collection = getCollection($_SESSION['user_id'], $limit, $offset, $filters);

// Get total count for pagination
global $db;
$whereClause = "o.buyer_id = ? AND o.payment_status = 'completed' AND o.archived = 0";
$params = [$_SESSION['user_id']];

if (!empty($filters['category'])) {
    $whereClause .= " AND a.category = ?";
    $params[] = $filters['category'];
}

if (!empty($filters['artist'])) {
    $whereClause .= " AND u.email LIKE ?";
    $params[] = "%" . $filters['artist'] . "%";
}

if (!empty($filters['price_min'])) {
    $whereClause .= " AND o.total_price >= ?";
    $params[] = $filters['price_min'];
}

if (!empty($filters['price_max'])) {
    $whereClause .= " AND o.total_price <= ?";
    $params[] = $filters['price_max'];
}

if (!empty($filters['date_from'])) {
    $whereClause .= " AND o.purchase_date >= ?";
    $params[] = $filters['date_from'];
}

if (!empty($filters['date_to'])) {
    $whereClause .= " AND o.purchase_date <= ?";
    $params[] = $filters['date_to'];
}

$totalItems = $db->selectOne("
    SELECT COUNT(*) as count 
    FROM orders o 
    JOIN artworks a ON o.artwork_id = a.artwork_id 
    JOIN artists ar ON a.artist_id = ar.artist_id
    JOIN users u ON ar.user_id = u.user_id
    WHERE $whereClause", 
    $params
);
$totalPages = ceil($totalItems['count'] / $limit);

// Get categories for filter
$categories = getCategories();

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include_once '../includes/sidebar.php'; ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Your Wishlist</h1>
                <?php if (!empty($wishlistItems)): ?>
                    <div class="btn-toolbar mb-4 mb-md-2">
                        <a href="<?php echo SITE_URL; ?>/gallery.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Browse More Artwork
                        </a>
                    </div>
                <?php endif; ?>
                    <?php if ($is_logged_in): ?>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="gridViewBtn" title="Grid View">
                <i class="fas fa-th"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="listViewBtn" title="List View">
                <i class="fas fa-list"></i>
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-sort me-1"></i> Sort
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'date_desc') ? 'active' : ''; ?>" href="?sort=date_desc">Newest First</a></li>
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'date_asc') ? 'active' : ''; ?>" href="?sort=date_asc">Oldest First</a></li>
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'price_desc') ? 'active' : ''; ?>" href="?sort=price_desc">Price: High to Low</a></li>
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'price_asc') ? 'active' : ''; ?>" href="?sort=price_asc">Price: Low to High</a></li>
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'title_asc') ? 'active' : ''; ?>" href="?sort=title_asc">Title: A-Z</a></li>
            <li><a class="dropdown-item <?php echo ($filters['sort'] == 'title_desc') ? 'active' : ''; ?>" href="?sort=title_desc">Title: Z-A</a></li>
        </ul>
    </div>  
    </div>
           <!-- Filter and Search -->
 <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="get" id="filterForm">
                        <input type="hidden" name="sort" value="<?php echo $filters['sort']; ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['name']; ?>" <?php echo ($filters['category'] == $category['name']) ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="artist" class="form-label">Artist</label>
                                <input type="text" class="form-control" id="artist" name="artist" value="<?php echo $filters['artist']; ?>" placeholder="Artist name">
                            </div>
                            <div class="col-md-2">
                                <label for="price_min" class="form-label">Min Price</label>
                                <input type="number" class="form-control" id="price_min" name="price_min" value="<?php echo $filters['price_min']; ?>" placeholder="Min" min="0">
                            </div>
                            <div class="col-md-2">
                                <label for="price_max" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="price_max" name="price_max" value="<?php echo $filters['price_max']; ?>" placeholder="Max" min="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>      
    
    <?php if (!empty($wishlistItems)): ?>
        <!-- Grid View -->
        <div class="card shadow mb-4" id="gridView">
            <div class="card-body">
                <div class="row">
                    <?php foreach ($wishlistItems as $artwork): ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-3 mb-4">
                            <div class="card h-100 artwork-card">
                                <a href="<?php echo SITE_URL; ?>/artwork/details.php?id=<?php echo $artwork['artwork_id']; ?>" class="artwork-link">
                                <div class="artwork-image-container">
                                    <img src="<?php echo SITE_URL . '/images/' . htmlspecialchars($artwork['image_url']); ?>" 

 class="card-img-top artwork-thumbnail" 
                                        alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                        loading="lazy">
                                </div>
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate"><?php echo htmlspecialchars($artwork['title']); ?></h6>
                                    <p class="card-text text-muted small text-truncate"><?php echo htmlspecialchars($artwork['artist_name']); ?></p>
                                    <p class="card-text fw-bold"><?php echo formatCurrency($artwork['price']); ?></p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 p-2">
                                  

<div class="d-flex justify-content-between">
                                        <a href="<?php echo SITE_URL; ?>/artwork/details.php?id=<?php echo $artwork['artwork_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="remove_wishlist.php" method="POST" class="d-inline">
                                            <input type="hidden" name="wishlist_id" value="<?php echo $artwork['wishlist_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Remove from wishlist">
                                                <i class="fas fa-heart-broken"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div class="card shadow mb-4 d-none" id="listView">
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($wishlistItems as $artwork): ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <a href="<?php echo SITE_URL; ?>/artwork/details.php?id=<?php echo $artwork['artwork_id']; ?>">
                                        <img src="<?php echo SITE_URL . '/images/' . htmlspecialchars($artwork['image_url']); ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                             loading="lazy"
                                             style="max-height: 100px;">
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($artwork['title']); ?></h6>
                                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars($artwork['artist_name']); ?></p>
                                    <p class="mb-0 fw-bold"><?php echo formatCurrency($artwork['price']); ?></p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo SITE_URL; ?>/artwork/details.php?id=<?php echo $artwork['artwork_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="View details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="remove_wishlist.php" method="POST" class="d-inline">
                                            <input type="hidden" name="wishlist_id" value="<?php echo $artwork['wishlist_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Remove from wishlist">
                                                <i class="fas fa-heart-broken"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="d-flex justify-content-center mt-4">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="card shadow">
                <div class="card-body py-5">
                    <i class="fas fa-heart fa-4x text-gray-300 mb-4"></i>
                    <h3 class="h5">Your wishlist is empty</h3>
                    <p class="text-muted">Start adding artwork you love to your wishlist</p>
                    <a href="<?php echo SITE_URL; ?>/artworks" class="btn btn-primary mt-3">
                        <i class="fas fa-palette"></i> Browse Artwork
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-lock fa-4x text-gray-300 mb-4"></i>
            <h3 class="h5">Please log in to view your wishlist</h3>
            <p class="text-muted">Sign in to access your saved artwork</p>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </a>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-outline-secondary">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        
        // Check for saved view preference in cookie
        const getCookie = (name) => {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        };
        
        const savedView = getCookie('wishlist_view');
        
        // Initialize view based on cookie or default to grid
        if (savedView === 'list') {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            listViewBtn.classList.add('active');
        } else {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridViewBtn.classList.add('active');
        }
        
        gridViewBtn.addEventListener('click', function() {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            document.cookie = "wishlist_view=grid; path=/; max-age=31536000"; // 1 year
        });
        
        listViewBtn.addEventListener('click', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            document.cookie = "wishlist_view=list; path=/; max-age=31536000"; // 1 year
        });
    });
</script>

<style>
    .btn-group .btn.active {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }
    
    .artwork-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .artwork-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .artwork-image-container {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .artwork-thumbnail {
        object-fit: cover;
        width: 100%;
        height: 100%;
        transition: transform 0.3s ease;
    }
    
    .artwork-link:hover .artwork-thumbnail {
        transform: scale(1.05);
    }
</style>



