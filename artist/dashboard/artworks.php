<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an artist
requireArtist();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get artworks
$artworks = getArtistArtworks($_SESSION['user_id'], $limit, $offset);
// Handle search functionality
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;

if (!empty($searchTerm) || $categoryFilter) {
    $query = "SELECT * FROM artworks WHERE artist_id = ? AND archived = 0";
    $params = [$_SESSION['user_id']];

    if (!empty($searchTerm)) {
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%{$searchTerm}%";
        $params[] = '%' . $searchTerm . '%';
        $params[] = '%' . $searchTerm . '%';
    }

    if ($categoryFilter) {
        $query .= " AND category_id = ?";
        $params[] = $categoryFilter;
    }

    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $artworks = $db->select($query, $params);
}
// Get total count for pagination
global $db;
$totalArtworks = $db->selectOne("SELECT COUNT(*) as count FROM artworks WHERE artist_id = ? AND archived = 0", [$_SESSION['user_id']]);
$totalPages = ceil($totalArtworks['count'] / $limit);

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Artworks</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo SITE_URL; ?>/artist/dashboard/upload.php" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Artwork
            </a>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search artworks..." id="searchArtworks">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-end">
                <select class="form-select" id="filterCategory">
                    <option value="">All Categories</option>
                    <?php 
                    $categories = getCategories();
                    foreach ($categories as $category) {
                        echo '<option value="' . $category['category_id'] . '">' . $category['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Artworks Grid -->
    <div class="row g-4" id="artworksGrid">
    <?php if (count($artworks) > 0): ?>
        <?php foreach ($artworks as $artwork): 
            $isActiveAuction = $artwork['is_for_auction'] && !empty($artwork['auction_end_date']) && strtotime($artwork['auction_end_date']) > time();
            $auctionEnded = $artwork['is_for_auction'] && !empty($artwork['auction_end_date']) && strtotime($artwork['auction_end_date']) <= time();
        ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <!-- Artwork Image with Status Badge -->
                    <div class="position-relative">
                        <img src="<?php echo UPLOAD_URL . $artwork['image_url']; ?>" 
                             class="card-img-top artwork-thumbnail" 
                             style="height: 200px; object-fit: cover;"
                             alt="<?php echo htmlspecialchars($artwork['title']); ?>">
                        
                        <!-- Status Badge -->
                        <?php if ($isActiveAuction): ?>
                            <span class="position-absolute top-0 start-0 m-2 badge bg-danger">
                                <i class="fas fa-gavel me-1"></i> Auction Live
                            </span>
                        <?php elseif ($auctionEnded): ?>
                            <span class="position-absolute top-0 start-0 m-2 badge bg-secondary">
                                <i class="fas fa-clock me-1"></i> Auction Ended
                            </span>
                        <?php elseif ($artwork['is_for_sale']): ?>
                            <span class="position-absolute top-0 start-0 m-2 badge bg-success">
                                <i class="fas fa-shopping-cart me-1"></i> For Sale
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Title and Category -->
                        <h5 class="card-title mb-1 text-truncate"><?php echo htmlspecialchars($artwork['title']); ?></h5>
                        <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars($artwork['category']); ?></p>
                        
                        <!-- Price/Bid Information -->
                        <div class="mt-auto">
                            <?php if ($isActiveAuction): ?>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">
                                            <?php if ($artwork['current_bid']): ?>
                                                Current Bid: <?php echo formatCurrency($artwork['current_bid']); ?>
                                            <?php else: ?>
                                                Starting Bid: <?php echo formatCurrency($artwork['starting_bid']); ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <?php echo $artwork['bid_count'] ?? 0; ?> bids
                                        </span>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-warning" 
                                             style="width: <?php echo min(100, ($artwork['bid_count'] / 10) * 100); ?>%">
                                        </div>
                                    </div>
                                    <small class="text-danger d-block mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        Ends: <?php echo formatDate($artwork['auction_end_date']); ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <p class="fw-bold mb-2">Price: <?php echo formatCurrency($artwork['price']); ?></p>
                            <?php endif; ?>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between border-top pt-2">
                                <a href="#" class="btn btn-sm btn-outline-primary edit-artwork" 
                                   data-id="<?php echo $artwork['artwork_id']; ?>"
                                   title="Edit artwork">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($isActiveAuction): ?>
                                    <a href="#" class="btn btn-sm btn-outline-info"
                                       title="View auction">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <button class="btn btn-sm btn-outline-danger delete-artwork" 
                                        data-id="<?php echo $artwork['artwork_id']; ?>"
                                        title="Delete artwork">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer with Creation Date -->
                    <div class="card-footer bg-transparent border-top-0 py-2">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo formatDate($artwork['created_at']); ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-palette display-4 text-muted mb-3"></i>
                    <h5 class="card-title">No Artworks Found</h5>
                    <p class="card-text text-muted">You haven't added any artworks yet.</p>
                    <a href="<?php echo SITE_URL; ?>/dashboard/upload.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Upload First Artwork
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Artwork pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

    <!-- Edit Artwork Modal -->
    <div class="modal fade" id="editArtworkModal" tabindex="-1" aria-labelledby="editArtworkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArtworkModalLabel">Edit Artwork</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">                <!-- Image Display at Top -->
                <div class="text-center mb-4">
                    <img id="editCurrentImage" src="/placeholder.svg" class="img-fluid rounded" style="max-height: 300px; width: auto;" alt="Artwork Preview">
                    <div class="mt-2">
                        <label for="editImage" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-camera me-1"></i> Change Image
                            <input type="file" id="editImage" name="image" accept="image/*" class="d-none">
                        </label>
                    </div>
                </div>

                <form id="editArtworkForm" enctype="multipart/form-data">
                    <input type="hidden" id="editArtworkId" name="artwork_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <!-- Main Form Fields in Two Columns -->
                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editTitle" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editCategory" class="form-label">Category*</label>
                                <select class="form-select" id="editCategory" name="category" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editPrice" class="form-label">Price ($)*</label>
                                <input type="number" class="form-control" id="editPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selling Options -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="editIsForSale" name="is_for_sale" checked>
                                        <label class="form-check-label" for="editIsForSale">Available for direct sale</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="editIsForAuction" name="is_for_auction">
                                        <label class="form-check-label" for="editIsForAuction">Put up for auction</label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Note: Auction items cannot be available for direct sale.</small>
                        </div>
                    </div>
                    
                    <!-- Auction Settings (Full Width Below) -->
                    <div id="auctionFields" class="card border-0 shadow-sm mb-3" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Auction Settings</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="editStartingBid" class="form-label">Starting Price ($)*</label>
                                        <input type="number" class="form-control" id="editStartingBid" name="starting_price" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="editReservePrice" class="form-label">Reserve Price ($)</label>
                                        <input type="number" class="form-control" id="editReservePrice" name="reserve_price" step="0.01" min="0">
                                        <small class="text-muted">Minimum acceptable price</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="editBidIncrement" class="form-label">Bid Increment ($)</label>
                                        <input type="number" class="form-control" id="editBidIncrement" name="bid_increment" step="0.01" min="0.01" value="1.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editAuctionStart" class="form-label">Start Date/Time*</label>
                                        <input type="datetime-local" class="form-control" id="editAuctionStart" name="start_time">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editAuctionEnd" class="form-label">End Date/Time*</label>
                                        <input type="datetime-local" class="form-control" id="editAuctionEnd" name="end_time">
                                        <small class="text-muted">Minimum duration: 24 hours</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveArtworkChanges">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
    </div>

<!-- Add JavaScript to handle the auction toggle and form interactions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const isForAuctionCheckbox = document.getElementById('editIsForAuction');
    const isForSaleCheckbox = document.getElementById('editIsForSale');
    const auctionFields = document.getElementById('auctionFields');
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchArtworks');
    const categoryFilter = document.getElementById('filterCategory');
    const artworksGrid = document.getElementById('artworksGrid');
    const editArtworkForm = document.getElementById('editArtworkForm');
    const deleteArtworkModal = document.getElementById('deleteArtworkModal');

    // Initialize page state
    initAuctionToggle();
    attachEventListeners();

    // Auction/Sale Toggle Logic
    function initAuctionToggle() {
        if (isForAuctionCheckbox) {
            isForAuctionCheckbox.addEventListener('change', handleAuctionToggle);
            // Initialize state on page load
            handleAuctionToggle();
        }
    }

    function handleAuctionToggle() {
        // Show/hide auction fields based on checkbox state
        auctionFields.style.display = isForAuctionCheckbox.checked ? 'block' : 'none';
        
        // If auction is enabled, disable direct sale
        if (isForAuctionCheckbox.checked) {
            isForSaleCheckbox.checked = false;
            isForSaleCheckbox.disabled = true;
            
            // Set default auction dates if they're empty
            const now = new Date();
            const endDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000); // 7 days from now
            
            const startField = document.getElementById('editAuctionStart');
            const endField = document.getElementById('editAuctionEnd');
            
            if (!startField.value) {
                startField.value = now.toISOString().slice(0, 16);
            }
            
            if (!endField.value) {
                endField.value = endDate.toISOString().slice(0, 16);
            }
        } else {
            // Re-enable direct sale when auction is disabled
            isForSaleCheckbox.disabled = false;
        }
    }

    // Edit Artwork Handler
    function handleEditArtwork(e) {
        e.preventDefault();
        const artworkId = this.getAttribute('data-id');
        
        fetch(`../api/artworks.php?action=get&id=${artworkId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Failed to load artwork');
                
                const artwork = data.artwork;
                
                // Populate basic fields
                document.getElementById('editArtworkId').value = artwork.artwork_id;
                document.getElementById('editTitle').value = artwork.title;
                document.getElementById('editCategory').value = artwork.category || artwork.category_name;
                document.getElementById('editPrice').value = artwork.price;
                document.getElementById('editDescription').value = artwork.description;
                
                // Handle image
                if (artwork.image_url) {
                    document.getElementById('editCurrentImage').src = '<?php echo UPLOAD_URL; ?>' + artwork.image_url;
                }
                
                // Handle sale status
                document.getElementById('editIsForSale').checked = artwork.is_for_sale == 1;
                
                // Handle auction fields
                const isForAuction = artwork.is_for_auction == 1 || 
                                   (artwork.auction_id && artwork.auction_status !== 'cancelled');
                
                document.getElementById('editIsForAuction').checked = isForAuction;
                
                if (isForAuction) {
                    // Fill in auction details
                    if (artwork.starting_price) {
                        document.getElementById('editStartingBid').value = artwork.starting_price;
                    }
                    
                    if (artwork.reserve_price) {
                        document.getElementById('editReservePrice').value = artwork.reserve_price;
                    }
                    
                    // Format dates for datetime-local inputs
                    if (artwork.start_time) {
                        document.getElementById('editAuctionStart').value = 
                            new Date(artwork.start_time).toISOString().slice(0, 16);
                    }
                    
                    if (artwork.end_time) {
                        document.getElementById('editAuctionEnd').value = 
                            new Date(artwork.end_time).toISOString().slice(0, 16);
                    }
                }
                
                // Trigger auction toggle to update the form state
                handleAuctionToggle();
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editArtworkModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading artwork: ' + error.message);
            });
    }

    // Save Artwork Changes
    function saveArtworkChanges(e) {
        const form = document.getElementById('editArtworkForm');
        const formData = new FormData(form);
        
        // Add checkbox values properly
        formData.set('is_for_sale', document.getElementById('editIsForSale').checked ? '1' : '0');
        formData.set('is_for_auction', document.getElementById('editIsForAuction').checked ? '1' : '0');
        
        // Add action
        formData.append('action', 'update');
        
        // Validate auction fields if auction is enabled
        if (document.getElementById('editIsForAuction').checked) {
            const startingPrice = document.getElementById('editStartingBid').value;
            const startTime = document.getElementById('editAuctionStart').value;
            const endTime = document.getElementById('editAuctionEnd').value;
            
            if (!startingPrice || startingPrice <= 0) {
                alert('Please enter a valid starting price for the auction');
                return;
            }
            
            if (!startTime) {
                alert('Please set an auction start date');
                return;
            }
            
            if (!endTime) {
                alert('Please set an auction end date');
                return;
            }
            
            if (new Date(endTime) <= new Date(startTime)) {
                alert('Auction end date must be after the start date');
                return;
            }
        }
        
        // Show loading state
        this.disabled = true;
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        
        fetch('../api/artworks.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editArtworkModal')).hide();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating artwork: ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = originalText;
        });
    }

    // Delete Artwork Handlers
    function handleDeleteClick() {
        const artworkId = this.getAttribute('data-id');
        document.getElementById('deleteArtworkId').value = artworkId;
        new bootstrap.Modal(deleteArtworkModal).show();
    }

    function confirmDeleteArtwork() {
        const artworkId = document.getElementById('deleteArtworkId').value;
        
        // Show loading state
        this.disabled = true;
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
        fetch('../api/artworks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&artwork_id=${artworkId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(deleteArtworkModal).hide();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting artwork: ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = originalText;
        });
    }

    // Search and Filter Handlers
    function searchArtworks() {
        const searchTerm = searchInput.value;
        const categoryId = categoryFilter.value;
        
        fetch(`../api/artworks.php?action=search&term=${searchTerm}&category=${categoryId}&artist_id=<?php echo $_SESSION['user_id']; ?>`)
            .then(response => response.json())
            .then(artworks => {
                artworksGrid.innerHTML = '';
                
                if (artworks.length > 0) {
                    artworks.forEach(artwork => {
                        artworksGrid.innerHTML += createArtworkCard(artwork);
                    });
                } else {
                    artworksGrid.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                <p class="mb-0">No artworks found matching your search criteria.</p>
                            </div>
                        </div>
                    `;
                }
                
                attachEventListeners();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error searching artworks: ' + error.message);
            });
    }

    function createArtworkCard(artwork) {
        return `
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <img src="<?php echo UPLOAD_URL; ?>${artwork.image_url}" class="card-img-top artwork-thumbnail" alt="${artwork.title}">
                    <div class="card-body">
                        <h5 class="card-title">${artwork.title}</h5>
                        <p class="card-text text-muted">${artwork.category}</p>
                        <p class="card-text fw-bold">$${parseFloat(artwork.price).toFixed(2)}</p>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-sm btn-outline-primary edit-artwork" data-id="${artwork.artwork_id}">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-outline-danger delete-artwork" data-id="${artwork.artwork_id}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Added: ${new Date(artwork.created_at).toLocaleDateString()}</small>
                    </div>
                </div>
            </div>
        `;
    }

    // Event Listener Management
    function attachEventListeners() {
        // Edit buttons
        document.querySelectorAll('.edit-artwork').forEach(button => {
            button.removeEventListener('click', handleEditArtwork);
            button.addEventListener('click', handleEditArtwork);
        });
        
        // Delete buttons
        document.querySelectorAll('.delete-artwork').forEach(button => {
            button.removeEventListener('click', handleDeleteClick);
            button.addEventListener('click', handleDeleteClick);
        });
        
        // Save changes button
        const saveBtn = document.getElementById('saveArtworkChanges');
        if (saveBtn) {
            saveBtn.removeEventListener('click', saveArtworkChanges);
            saveBtn.addEventListener('click', saveArtworkChanges);
        }
        
        // Confirm delete button
        const confirmDeleteBtn = document.getElementById('confirmDeleteArtwork');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.removeEventListener('click', confirmDeleteArtwork);
            confirmDeleteBtn.addEventListener('click', confirmDeleteArtwork);
        }
        
        // Search and filter
        if (searchButton) {
            searchButton.removeEventListener('click', searchArtworks);
            searchButton.addEventListener('click', searchArtworks);
        }
        
        if (categoryFilter) {
            categoryFilter.removeEventListener('change', searchArtworks);
            categoryFilter.addEventListener('change', searchArtworks);
        }
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>

