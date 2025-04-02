<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a buyer
requireLogin();

// Get buyer stats
$stats = getBuyerStats($_SESSION['user_id']);

// Get recent purchases
$recentPurchases = getRecentPurchases($_SESSION['user_id']);

// Get recently viewed artworks
$recentlyViewed = getRecentlyViewedArtworks($_SESSION['user_id']);

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Welcome, <?php echo $_SESSION['username']; ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="#" class="btn btn-sm btn-primary me-2">
                <i class="fas fa-shopping-bag me-1"></i> Browse Artwork
            </a>
            <a href="<?php echo SITE_URL; ?>/dashboard/wishlist.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-heart me-1"></i> View Wishlist
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Purchases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_purchases']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Spent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($stats['total_spent']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Wishlist Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['wishlist_count']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_orders']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases and Notifications -->
    <div class="row">
        <!-- Recent Purchases -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Purchases</h6>
                    <a href="<?php echo SITE_URL; ?>/dashboard/purchases.php" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (count($recentPurchases) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Artwork</th>
                                        <th>Artist</th>
                                        <th>Date</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPurchases as $purchase): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo UPLOAD_URL . $purchase['image_url']; ?>" alt="<?php echo $purchase['title']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <span><?php echo $purchase['title']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $purchase['artist_name']; ?></td>
                                            <td><?php echo formatDate($purchase['created_at']); ?></td>
                                            <td><?php echo formatCurrency($purchase['total_price']); ?></td>
                                            <td>
                                                <?php if ($purchase['payment_status'] == 'completed'): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php elseif ($purchase['payment_status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/dashboard/purchase-details.php?id=<?php echo $purchase['order_id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p>You haven't made any purchases yet.</p>
                            <a href="#" class="btn btn-primary">Browse Artwork</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php if ($stats['unread_messages'] > 0): ?>
                            <a href="<?php echo SITE_URL; ?>/dashboard/messages.php" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">New Messages</h6>
                                    <small class="text-primary">View</small>
                                </div>
                                <p class="mb-1">You have <?php echo $stats['unread_messages']; ?> unread message(s).</p>
                                <small class="text-muted">Check your inbox</small>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($stats['pending_orders'] > 0): ?>
                            <a href="<?php echo SITE_URL; ?>/dashboard/purchases.php?status=pending" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Pending Orders</h6>
                                    <small class="text-primary">View</small>
                                </div>
                                <p class="mb-1">You have <?php echo $stats['pending_orders']; ?> pending order(s).</p>
                                <small class="text-muted">Check order status</small>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo SITE_URL; ?>/dashboard/wishlist.php" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Wishlist</h6>
                                <small class="text-primary">View</small>
                            </div>
                            <p class="mb-1">You have <?php echo $stats['wishlist_count']; ?> item(s) in your wishlist.</p>
                            <small class="text-muted">Browse your saved artwork</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Viewed Artwork -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recently Viewed Artwork</h6>
            <a href="#" class="btn btn-sm btn-primary">
                Browse More
            </a>
        </div>
        <div class="card-body">
            <?php if (count($recentlyViewed) > 0): ?>
                <div class="row">
                    <?php foreach ($recentlyViewed as $artwork): ?>
                        <div class="col-md-4 col-lg-2 mb-4">
                            <div class="card h-100">
                                <a href="#" data-artwork-id="<?php echo $artwork['artwork_id']; ?>">
                                    <img src="<?php echo UPLOAD_URL . $artwork['image_url']; ?>" class="card-img-top artwork-thumbnail" alt="<?php echo $artwork['title']; ?>">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $artwork['title']; ?></h6>
                                    <p class="card-text text-muted small"><?php echo $artwork['artist_name']; ?></p>
                                    <p class="card-text fw-bold"><?php echo formatCurrency($artwork['price']); ?></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="#" class="btn btn-sm btn-outline-primary view-artwork" data-id="<?php echo $artwork['artwork_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (isInWishlist($artwork['artwork_id'], $_SESSION['user_id'])): ?>
                                            <button class="btn btn-sm btn-danger remove-from-wishlist" data-id="<?php echo $artwork['artwork_id']; ?>">
                                                <i class="fas fa-heart-broken"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-danger add-to-wishlist" data-id="<?php echo $artwork['artwork_id']; ?>">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-eye fa-3x text-gray-300 mb-3"></i>
                    <p>You haven't viewed any artwork yet.</p>
                    <a href="#" class="btn btn-primary">Browse Artwork</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to wishlist
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            
            fetch('../api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&artwork_id=${artworkId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Change button to remove from wishlist
                    this.classList.remove('btn-outline-danger');
                    this.classList.add('btn-danger');
                    this.classList.remove('add-to-wishlist');
                    this.classList.add('remove-from-wishlist');
                    this.innerHTML = '<i class="fas fa-heart-broken"></i>';
                    
                    // Update wishlist count in stats
                    const wishlistCountElement = document.querySelector('.text-info + .h5');
                    if (wishlistCountElement) {
                        const currentCount = parseInt(wishlistCountElement.textContent);
                        wishlistCountElement.textContent = currentCount + 1;
                    }
                } else {
                    alert('Error adding to wishlist: ' + data.message);
                }
            });
        });
    });
    
    // Remove from wishlist
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            
            fetch('../api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&artwork_id=${artworkId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Change button to add to wishlist
                    this.classList.remove('btn-danger');
                    this.classList.add('btn-outline-danger');
                    this.classList.remove('remove-from-wishlist');
                    this.classList.add('add-to-wishlist');
                    this.innerHTML = '<i class="fas fa-heart"></i>';
                    
                    // Update wishlist count in stats
                    const wishlistCountElement = document.querySelector('.text-info + .h5');
                    if (wishlistCountElement) {
                        const currentCount = parseInt(wishlistCountElement.textContent);
                        wishlistCountElement.textContent = Math.max(0, currentCount - 1);
                    }
                } else {
                    alert('Error removing from wishlist: ' + data.message);
                }
            });
        });
    });
    
    // View artwork
    document.querySelectorAll('.view-artwork, [data-artwork-id]').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const artworkId = this.getAttribute('data-id') || this.getAttribute('data-artwork-id');
            
            // Record view
            fetch('../api/artworks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=view&artwork_id=${artworkId}`
            });
            
            // Redirect to artwork details page
            window.location.href = `<?php echo SITE_URL; ?>/artwork.php?id=${artworkId}`;
        });
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>

