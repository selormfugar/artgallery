
    
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
        fetch(`../api/artworks.php?action=search&term=${searchTerm}&category=${categoryId}&artist_id=<?php echo $_SESSION['user_id']; ?>`)
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
// In artworks.js
let searchTimeout;
document.getElementById('searchArtworks').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('searchButton').click();
    }, 300);
});