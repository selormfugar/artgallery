<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a buyer
requireBuyer();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
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
    $whereClause .= " AND u.username LIKE ?";
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

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Collection</h1>
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
                                <input type="number" class="form-control" id="price_min" name="price_min" value="<?php echo $filters['price_min']; ?>" placeholder="Min">
                            </div>
                            <div class="col-md-2">
                                <label for="price_max" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="price_max" name="price_max" value="<?php echo $filters['price_max']; ?>" placeholder="Max">
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

    <!-- Collection Grid View -->
    <div id="gridView" class="<?php echo (!isset($_COOKIE['collection_view']) || $_COOKIE['collection_view'] == 'grid') ? '' : 'd-none'; ?>">
        <?php if (count($collection) > 0): ?>
            <div class="row">
                <?php foreach ($collection as $item): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <a href="<?php echo SITE_URL; ?>/dashboard/artwork-details.php?id=<?php echo $item['artwork_id']; ?>">
                                <img src="<?php echo UPLOAD_URL . $item['image_url']; ?>" class="card-img-top artwork-thumbnail" alt="<?php echo $item['title']; ?>">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $item['title']; ?></h5>
                                <p class="card-text text-muted">by <?php echo $item['artist_name']; ?></p>
                                <p class="card-text fw-bold"><?php echo formatCurrency($item['total_price']); ?></p>
                                <p class="card-text small">Purchased: <?php echo formatDate($item['purchase_date'] ?? $item['created_at']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo SITE_URL; ?>/dashboard/artwork-details.php?id=<?php echo $item['artwork_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/dashboard/certificate.php?order_id=<?php echo $item['order_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-certificate me-1"></i> Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Your collection is empty. Start building your art collection today!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Collection List View -->
    <div id="listView" class="<?php echo (isset($_COOKIE['collection_view']) && $_COOKIE['collection_view'] == 'list') ? '' : 'd-none'; ?>">
        <?php if (count($collection) > 0): ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Artwork</th>
                                <th>Title</th>
                                <th>Artist</th>
                                <th>Purchase Date</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($collection as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo UPLOAD_URL . $item['image_url']; ?>" alt="<?php echo $item['title']; ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td><?php echo $item['title']; ?></td>
                                    <td><?php echo $item['artist_name']; ?></td>
                                    <td><?php echo formatDate($item['purchase_date'] ?? $item['created_at']); ?></td>
                                    <td><?php echo formatCurrency($item['total_price']); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/dashboard/artwork-details.php?id=<?php echo $item['artwork_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/dashboard/certificate.php?order_id=<?php echo $item['order_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-certificate"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Your collection is empty. Start building your art collection today!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Collection pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $filters['sort']; ?>&category=<?php echo $filters['category']; ?>&artist=<?php echo $filters['artist']; ?>&price_min=<?php echo $filters['price_min']; ?>&price_max=<?php echo $filters['price_max']; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $filters['sort']; ?>&category=<?php echo $filters['category']; ?>&artist=<?php echo $filters['artist']; ?>&price_min=<?php echo $filters['price_min']; ?>&price_max=<?php echo $filters['price_max']; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $filters['sort']; ?>&category=<?php echo $filters['category']; ?>&artist=<?php echo $filters['artist']; ?>&price_min=<?php echo $filters['price_min']; ?>&price_max=<?php echo $filters['price_max']; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grid/List View Toggle
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    
    gridViewBtn.addEventListener('click', function() {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        document.cookie = "collection_view=grid; path=/; max-age=31536000"; // 1 year
    });
    
    listViewBtn.addEventListener('click', function() {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        document.cookie = "collection_view=list; path=/; max-age=31536000"; // 1 year
    });
    
    // Form submission
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e) {
        // Remove empty fields to keep URL clean
        const formElements = Array.from(filterForm.elements);
        formElements.forEach(element => {
            if (element.value === '' && element.name !== 'sort') {
                element.disabled = true;
            }
        });
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>

