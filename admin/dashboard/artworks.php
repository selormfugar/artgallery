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
$artworks = getArtistArtworks($_SESSION['artist_id'], $limit, $offset);

// Get total count for pagination
global $db;
$totalArtworks = $db->selectOne("SELECT COUNT(*) as count FROM artworks WHERE artist_id = ? AND archived = 0", [$_SESSION['artist_id']]);
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
            <a href="<?php echo SITE_URL; ?>/dashboard/upload.php" class="btn btn-sm btn-primary">
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
    <div class="row" id="artworksGrid">
        <?php if (count($artworks) > 0): ?>
            <?php foreach ($artworks as $artwork): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo UPLOAD_URL . $artwork['image_url']; ?>" class="card-img-top artwork-thumbnail" alt="<?php echo $artwork['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $artwork['title']; ?></h5>
                            <p class="card-text text-muted"><?php echo $artwork['category']; ?></p>
                            <p class="card-text fw-bold"><?php echo formatCurrency($artwork['price']); ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="#" class="btn btn-sm btn-outline-primary edit-artwork" data-id="<?php echo $artwork['artwork_id']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-artwork" data-id="<?php echo $artwork['artwork_id']; ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Added: <?php echo formatDate($artwork['created_at']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">You haven't added any artworks yet. <a href="<?php echo SITE_URL; ?>/dashboard/upload.php">Upload your first artwork</a>.</p>
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
                <div class="modal-body">
                    <form id="editArtworkForm" enctype="multipart/form-data">
                        <input type="hidden" id="editArtworkId" name="artwork_id">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="editTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="editTitle" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editCategory" class="form-label">Category</label>
                                    <select class="form-select" id="editCategory" name="category" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editPrice" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control" id="editPrice" name="price" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <img id="editCurrentImage" src="/placeholder.svg" class="img-fluid rounded" alt="Current Artwork">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editImage" class="form-label">Change Image (optional)</label>
                                    <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveArtworkChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteArtworkModal" tabindex="-1" aria-labelledby="deleteArtworkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteArtworkModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this artwork? This action cannot be undone.</p>
                    <input type="hidden" id="deleteArtworkId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteArtwork">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Artwork
    document.querySelectorAll('.edit-artwork').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const artworkId = this.getAttribute('data-id');
            
            // Fetch artwork details via AJAX
            fetch(`../api/artworks.php?action=get&id=${artworkId}`)
                .then(response => response.json())
                .then(artwork => {
                    // Populate the modal with artwork details
                    document.getElementById('editArtworkId').value = artwork.artwork_id;
                    document.getElementById('editTitle').value = artwork.title;
                    document.getElementById('editCategory').value = artwork.category;
                    document.getElementById('editPrice').value = artwork.price;
                    document.getElementById('editDescription').value = artwork.description;
                    document.getElementById('editCurrentImage').src = '<?php echo UPLOAD_URL; ?>' + artwork.image_url;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editArtworkModal'));
                    modal.show();
                });
        });
    });
    
    // Save Artwork Changes
    document.getElementById('saveArtworkChanges').addEventListener('click', function() {
        const form = document.getElementById('editArtworkForm');
        const formData = new FormData(form);
        formData.append('action', 'update');
        
        // Send AJAX request to update artwork
        fetch('../api/artworks.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                bootstrap.Modal.getInstance(document.getElementById('editArtworkModal')).hide();
                
                // Reload the page to show updated data
                window.location.reload();
            } else {
                alert('Error updating artwork: ' + data.message);
            }
        });
    });
    
    // Delete Artwork
    document.querySelectorAll('.delete-artwork').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            document.getElementById('deleteArtworkId').value = artworkId;
            
            // Show the confirmation modal
            const modal = new bootstrap.Modal(document.getElementById('deleteArtworkModal'));
            modal.show();
        });
    });
    
    // Confirm Delete
    document.getElementById('confirmDeleteArtwork').addEventListener('click', function() {
        const artworkId = document.getElementById('deleteArtworkId').value;
        
        // Send AJAX request to delete artwork
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
                // Close the modal
                bootstrap.Modal.getInstance(document.getElementById('deleteArtworkModal')).hide();
                
                // Reload the page to show updated data
                window.location.reload();
            } else {
                alert('Error deleting artwork: ' + data.message);
            }
        });
    });
    
    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const searchTerm = document.getElementById('searchArtworks').value;
        const categoryId = document.getElementById('filterCategory').value;
        
        // Send AJAX request to search artworks
        fetch(`../api/artworks.php?action=search&term=${searchTerm}&category=${categoryId}&artist_id=<?php echo $_SESSION['artist_id']; ?>`)
            .then(response => response.json())
            .then(artworks => {
                // Clear current grid
                const grid = document.getElementById('artworksGrid');
                grid.innerHTML = '';
                
                if (artworks.length > 0) {
                    // Populate grid with search results
                    artworks.forEach(artwork => {
                        grid.innerHTML += `
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
                    });
                    
                    // Reattach event listeners
                    attachEventListeners();
                } else {
                    grid.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                <p class="mb-0">No artworks found matching your search criteria.</p>
                            </div>
                        </div>
                    `;
                }
            });
    });
    
    // Category filter change
    document.getElementById('filterCategory').addEventListener('change', function() {
        document.getElementById('searchButton').click();
    });
    
    function attachEventListeners() {
        // Reattach edit event listeners
        document.querySelectorAll('.edit-artwork').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const artworkId = this.getAttribute('data-id');
                
                // Fetch artwork details via AJAX
                fetch(`../api/artworks.php?action=get&id=${artworkId}`)
                    .then(response => response.json())
                    .then(artwork => {
                        // Populate the modal with artwork details
                        document.getElementById('editArtworkId').value = artwork.artwork_id;
                        document.getElementById('editTitle').value = artwork.title;
                        document.getElementById('editCategory').value = artwork.category;
                        document.getElementById('editPrice').value = artwork.price;
                        document.getElementById('editDescription').value = artwork.description;
                        document.getElementById('editCurrentImage').src = '<?php echo UPLOAD_URL; ?>' + artwork.image_url;
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('editArtworkModal'));
                        modal.show();
                    });
            });
        });
        
        // Reattach delete event listeners
        document.querySelectorAll('.delete-artwork').forEach(button => {
            button.addEventListener('click', function() {
                const artworkId = this.getAttribute('data-id');
                document.getElementById('deleteArtworkId').value = artworkId;
                
                // Show the confirmation modal
                const modal = new bootstrap.Modal(document.getElementById('deleteArtworkModal'));
                modal.show();
            });
        });
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>

