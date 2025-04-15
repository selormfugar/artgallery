<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';


?>
<!DOCTYPE html>
<html lang="en">
<?php
include_once 'includes/header.php';

?>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
include_once 'includes/sidebar.php';

?>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Artwork Moderation</h2>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pending Artwork Submissions</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover" id="artworks-table">
                            <thead>
                                <tr>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Artist</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Artwork Detail Modal -->
    <div class="modal fade" id="artwork-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Artwork Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img id="artwork-image" src="" alt="" class="img-fluid mb-3">
                        </div>
                        <div class="col-md-6">
                            <h4 id="artwork-title"></h4>
                            <p><strong>Artist:</strong> <span id="artwork-artist"></span></p>
                            <p><strong>Description:</strong> <span id="artwork-description"></span></p>
                            <p><strong>Category:</strong> <span id="artwork-category"></span></p>
                            <p><strong>Price:</strong> $<span id="artwork-price"></span></p>
                            <p><strong>Submitted:</strong> <span id="artwork-date"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="approve-btn">Approve</button>
                    <button type="button" class="btn btn-danger" id="reject-btn">Reject</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejection-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Artwork</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection-reason" class="form-label">Reason for rejection:</label>
                        <textarea class="form-control" id="rejection-reason" rows="4" placeholder="Please provide a clear reason for rejecting this artwork..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirm-reject-btn">Confirm Rejection</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="notification-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-message"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        const artworkModal = new bootstrap.Modal(document.getElementById('artwork-modal'));
        const rejectionModal = new bootstrap.Modal(document.getElementById('rejection-modal'));
        const notificationToast = new bootstrap.Toast(document.getElementById('notification-toast'));
        
        let currentArtworkId = null;
        
        // Load pending artworks
        function loadPendingArtworks() {
            $.ajax({
                url: 'api/moderation.php?action=get_pending_artworks',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const artworks = response.data;
                        const tableBody = $('#artworks-table tbody');
                        tableBody.empty();
                        
                        if (artworks.length === 0) {
                            tableBody.append('<tr><td colspan="6" class="text-center">No artworks pending moderation</td></tr>');
                            return;
                        }
                        
                        artworks.forEach(artwork => {
                            const row = `
                                <tr>
                                    <td><img src="../images/${artwork.image_url}" class="artwork-thumbnail" alt="${escapeHtml(artwork.title)}"></td>

                                    <td>${escapeHtml(artwork.title)}</td>
                                    <td>${escapeHtml(artwork.firstname + ' ' + artwork.lastname)}</td>
                                    <td>${formatDate(artwork.created_at)}</td>
                                    <td><span class="badge badge-pending">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-artwork-btn" data-id="${artwork.artwork_id}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    } else {
                        showNotification(response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('Error loading artworks: ' + error, 'error');
                }
            });
        }
        
        // View artwork details
        $(document).on('click', '.view-artwork-btn', function() {
            currentArtworkId = $(this).data('id');
            
            $.ajax({
                url: `api/moderation.php?action=get_artwork_details&artwork_id=${currentArtworkId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const artwork = response.data;
                        
                        $('#artwork-image').attr('src', '../images/' + artwork.image_url);
                        $('#artwork-title').text(artwork.title);
                        $('#artwork-artist').text(artwork.firstname + ' ' + artwork.lastname);
                        $('#artwork-description').text(artwork.description);
                        $('#artwork-category').text(artwork.category);
                        $('#artwork-price').text(artwork.price);
                        $('#artwork-date').text(formatDate(artwork.created_at));
                        
                        artworkModal.show();
                    } else {
                        showNotification(response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('Error loading artwork details: ' + error, 'error');
                }
            });
        });
        
        // Approve artwork
        $('#approve-btn').click(function() {
            if (confirm('Are you sure you want to approve this artwork?')) {
                $.ajax({
                    url: 'api/moderation.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'approve_artwork',
                        artwork_id: currentArtworkId
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.data.message, 'success');
                            artworkModal.hide();
                            loadPendingArtworks();
                        } else {
                            showNotification(response.error, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showNotification('Error approving artwork: ' + error, 'error');
                    }
                });
            }
        });
        
        // Reject artwork
        $('#reject-btn').click(function() {
            artworkModal.hide();
            rejectionModal.show();
        });
        
        // Confirm rejection
        $('#confirm-reject-btn').click(function() {
            const reason = $('#rejection-reason').val().trim();
            
            if (!reason) {
                alert('Please provide a reason for rejection');
                return;
            }
            
            $.ajax({
                url: 'api/moderation.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'reject_artwork',
                    artwork_id: currentArtworkId,
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        rejectionModal.hide();
                        $('#rejection-reason').val('');
                        loadPendingArtworks();
                    } else {
                        showNotification(response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('Error rejecting artwork: ' + error, 'error');
                }
            });
        });
        
        // Show notification
        function showNotification(message, type = 'success') {
            const toast = $('#notification-toast');
            toast.removeClass('bg-success bg-danger');
            
            if (type === 'error') {
                toast.addClass('bg-danger text-white');
            } else {
                toast.addClass('bg-success text-white');
            }
            
            $('#toast-message').text(message);
            notificationToast.show();
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;")
                       .replace(/</g, "&lt;")
                       .replace(/>/g, "&gt;")
                       .replace(/"/g, "&quot;")
                       .replace(/'/g, "&#039;");
        }
        
        // Helper function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        // Initial load
        loadPendingArtworks();
    });
    </script>
</body>
</html>