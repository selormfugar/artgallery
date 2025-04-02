<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a buyer
requireBuyer();

// Get user ID from session
$user_id = $_SESSION['user_id'];

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
    'sort' => isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'date_desc',
    'collection_type' => isset($_GET['collection_type']) ? sanitizeInput($_GET['collection_type']) : 'all'
];

// Get collections data
$collectionsData = getUserCollections($user_id, $limit, $offset, $filters);
$collection = isset($collectionsData['items']) ? $collectionsData['items'] : [];
$totalItems = isset($collectionsData['total']) ? $collectionsData['total'] : 0;

$totalPages = ceil($totalItems / $limit);

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
        <h1 class="h2">My Collections</h1>
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
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'date_desc') ? 'active' : ''; ?>" href="#" data-sort="date_desc">Newest First</a></li>
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'date_asc') ? 'active' : ''; ?>" href="#" data-sort="date_asc">Oldest First</a></li>
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'price_desc') ? 'active' : ''; ?>" href="#" data-sort="price_desc">Price: High to Low</a></li>
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'price_asc') ? 'active' : ''; ?>" href="#" data-sort="price_asc">Price: Low to High</a></li>
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'title_asc') ? 'active' : ''; ?>" href="#" data-sort="title_asc">Title: A-Z</a></li>
                <li><a class="dropdown-item <?php echo ($filters['sort'] == 'title_desc') ? 'active' : ''; ?>" href="#" data-sort="title_desc">Title: Z-A</a></li>
            </ul>
            
            <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#newCollectionModal">
                <i class="fas fa-plus me-1"></i> New Collection
            </button>
        </div>
    </div>

    <!-- Collection Type Tabs -->
    <ul class="nav nav-tabs mb-4" id="collectionTypeTabs">
        <li class="nav-item">
            <a class="nav-link <?php echo ($filters['collection_type'] == 'all') ? 'active' : ''; ?>" href="#" data-type="all">All Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($filters['collection_type'] == 'purchased') ? 'active' : ''; ?>" href="#" data-type="purchased">Purchased</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($filters['collection_type'] == 'wishlist') ? 'active' : ''; ?>" href="#" data-type="wishlist">Wishlist</a>
        </li>
    </ul>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="get" id="filterForm">
                        <input type="hidden" name="sort" value="<?php echo $filters['sort']; ?>">
                        <input type="hidden" name="collection_type" value="<?php echo $filters['collection_type']; ?>">
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

    <!-- Collection Folders -->
    <?php
// Add this near the top of your file, before you use $collectionsData
if (!isset($collectionsData['folders'])) {
    $collectionsData['folders'] = [];
}
?>

<div class="row mb-4" id="collectionFolders">
    <?php if (!empty($collectionsData['folders'])): ?>
        <?php foreach ($collectionsData['folders'] as $folder): ?>
            <div class="col-md-3 col-lg-2 mb-3">
                <div class="card folder-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <a href="#" class="stretched-link folder-link" data-folder-id="<?php echo $folder['folder_id']; ?>"></a>
                        <i class="fas fa-folder fa-3x text-warning mb-2"></i>
                        <h6 class="card-title mb-1"><?php echo $folder['folder_name']; ?></h6>
                        <small class="text-muted"><?php echo $folder['item_count']; ?> items</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center">
            <p>No collection folders found.</p>
        </div>
    <?php endif; ?>
</div>

    <!-- Collection Content -->
    <div id="collectionContent">
        <!-- Content will be loaded via AJAX -->
        <?php include 'partials/collection_content.php'; ?>
    </div>

    <!-- Pagination -->
    <div id="collectionPagination">
        <?php include 'partials/collection_pagination.php'; ?>
    </div>
</div>

<!-- New Collection Modal -->
<div class="modal fade" id="newCollectionModal" tabindex="-1" aria-labelledby="newCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCollectionModalLabel">Create New Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCollectionForm">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">Collection Name</label>
                        <input type="text" class="form-control" id="folderName" name="folder_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="folderDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="folderDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isPublic" name="is_public">
                        <label class="form-check-label" for="isPublic">
                            Make this collection public
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCollectionBtn">Create Collection</button>
            </div>
        </div>
    </div>
</div>

<!-- Add to Collection Modal -->
<div class="modal fade" id="addToCollectionModal" tabindex="-1" aria-labelledby="addToCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCollectionModalLabel">Add to Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addToCollectionForm">
                    <input type="hidden" id="modalArtworkId" name="artwork_id">
                    <div class="mb-3">
                        <label for="selectCollection" class="form-label">Select Collection</label>
                        <select class="form-select" id="selectCollection" name="collection_id">
                            <option value="">-- Select a collection --</option>
                            <?php foreach ($collectionsData['folders'] as $folder): ?>
                                <option value="<?php echo $folder['folder_id']; ?>"><?php echo $folder['folder_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="collectionNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="collectionNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="createNewCollectionFromModal">
                        <i class="fas fa-plus me-1"></i> Create New Collection
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveToCollectionBtn">Add to Collection</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Current filters state
    let currentFilters = {
        page: <?php echo $page; ?>,
        sort: '<?php echo $filters['sort']; ?>',
        category: '<?php echo $filters['category']; ?>',
        artist: '<?php echo $filters['artist']; ?>',
        price_min: '<?php echo $filters['price_min']; ?>',
        price_max: '<?php echo $filters['price_max']; ?>',
        collection_type: '<?php echo $filters['collection_type']; ?>'
    };

    // Grid/List View Toggle
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    
    gridViewBtn.addEventListener('click', function() {
        document.cookie = "collection_view=grid; path=/; max-age=31536000";
        loadCollectionContent();
    });
    
    listViewBtn.addEventListener('click', function() {
        document.cookie = "collection_view=list; path=/; max-age=31536000";
        loadCollectionContent();
    });

    // Collection Type Tabs
    document.querySelectorAll('#collectionTypeTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilters.collection_type = this.dataset.type;
            currentFilters.page = 1; // Reset to first page when changing type
            document.querySelectorAll('#collectionTypeTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            loadCollectionContent();
        });
    });

    // Sort Dropdown
    document.querySelectorAll('.dropdown-item[data-sort]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilters.sort = this.dataset.sort;
            document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            loadCollectionContent();
        });
    });

    // Folder Click
    document.querySelectorAll('.folder-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilters.folder_id = this.dataset.folderId;
            loadCollectionContent();
        });
    });

    // Filter Form Submission
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        currentFilters.category = document.getElementById('category').value;
        currentFilters.artist = document.getElementById('artist').value;
        currentFilters.price_min = document.getElementById('price_min').value;
        currentFilters.price_max = document.getElementById('price_max').value;
        currentFilters.page = 1; // Reset to first page when applying new filters
        loadCollectionContent();
    });

    // Create New Collection
    document.getElementById('saveCollectionBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('createCollectionForm'));
        
        fetch('../api/collections/create.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#newCollectionModal').modal('hide');
                document.getElementById('createCollectionForm').reset();
                loadCollectionFolders();
            } else {
                alert(data.message || 'Error creating collection');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the collection');
        });
    });

    // Add to Collection Modal
    let currentArtworkId = null;
    document.querySelectorAll('.add-to-collection-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentArtworkId = this.dataset.artworkId;
            document.getElementById('modalArtworkId').value = currentArtworkId;
            $('#addToCollectionModal').modal('show');
        });
    });

    // Save to Collection
    document.getElementById('saveToCollectionBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('addToCollectionForm'));
        
        fetch('../api/collections/add_item.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#addToCollectionModal').modal('hide');
                document.getElementById('addToCollectionForm').reset();
                loadCollectionContent();
            } else {
                alert(data.message || 'Error adding to collection');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding to collection');
        });
    });

    // Create New Collection from Modal
    document.getElementById('createNewCollectionFromModal').addEventListener('click', function() {
        $('#addToCollectionModal').modal('hide');
        $('#newCollectionModal').modal('show');
    });

    // Pagination
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link')) {
            e.preventDefault();
            if (e.target.parentElement.classList.contains('disabled')) return;
            
            if (e.target.getAttribute('aria-label') === 'Previous') {
                currentFilters.page--;
            } else if (e.target.getAttribute('aria-label') === 'Next') {
                currentFilters.page++;
            } else {
                currentFilters.page = parseInt(e.target.textContent);
            }
            
            loadCollectionContent();
        }
    });

    // Load collection content via AJAX
    function loadCollectionContent() {
        const params = new URLSearchParams(currentFilters);
        
        fetch(`../api/collections/get_content.php?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('collectionContent').innerHTML = html;
            updateUrl();
        })
        .catch(error => {
            console.error('Error loading collection content:', error);
        });
    }

    // Load pagination via AJAX
    function loadCollectionPagination() {
        const params = new URLSearchParams(currentFilters);
        
        fetch(`../api/collections/get_pagination.php?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('collectionPagination').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading pagination:', error);
        });
    }

    // Load collection folders via AJAX
    function loadCollectionFolders() {
        fetch('../api/collections/get_folders.php', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('collectionFolders').innerHTML = html;
            // Reattach event listeners to new folder links
            document.querySelectorAll('.folder-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentFilters.folder_id = this.dataset.folderId;
                    loadCollectionContent();
                });
            });
        })
        .catch(error => {
            console.error('Error loading folders:', error);
        });
    }

    // Update URL without reloading
    function updateUrl() {
        const params = new URLSearchParams(currentFilters);
        window.history.replaceState(null, null, `?${params.toString()}`);
        loadCollectionPagination();
    }

    // Initialize
    loadCollectionContent();
});
</script>

<?php include_once '../includes/footer.php'; ?>