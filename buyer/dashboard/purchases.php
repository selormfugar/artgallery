<?php
// require_once '../../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a buyer
if (!isLoggedIn() || $_SESSION['role'] !== 'buyer') {
    header('Location: ' . SITE_URL . '/login.php');
    exit();
}

// Get buyer ID from session
$buyerId = $_SESSION['user_id'];

// Fetch all purchases for the buyer
$query = "SELECT   o.id as order_id, o.created_at, o.total_price, o.payment_status, a.title, a.image_url, u.name as artist_name
          FROM orders o JOIN artworks a ON o.artwork_id = a.id JOIN users u ON a.artist_id = u.id WHERE o.buyer_id = ? ORDER BY o.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$buyerId]);
    $recentPurchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching purchases: " . $e->getMessage());
    $recentPurchases = [];
}


// Get recent purchases for statistics
$recentPurchasesQuery = "SELECT o.order_id as order_id, o.created_at, o.total_price, o.payment_status, a.title, a.image_url,
 a.artwork_id as artwork_id,u.firstname as artist_name, u.user_id as artist_id,     COUNT(*) OVER() as total_count
FROM orders o
JOIN artworks a ON o.artwork_id = a.artwork_id JOIN users u ON a.artist_id = u.user_id
WHERE o.buyer_id = ? ORDER BY o.created_at DESC LIMIT 5";

try {
    $stmt = $pdo->prepare($recentPurchasesQuery);
    $stmt->execute([$buyerId]);
    $recentIndexPurchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching recent purchases: " . $e->getMessage());
    $recentIndexPurchases = [];
}

// Get purchase statistics
$totalPurchaseQuery = "SELECT COUNT(*) as total, SUM(total_price) as total_spent 
                      FROM orders 
                      WHERE buyer_id = ? AND payment_status = 'completed'";
try {
    $stmt = $pdo->prepare($totalPurchaseQuery);
    $stmt->execute([$buyerId]);
    $purchaseStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $totalPurchases = $purchaseStats['total'] ?? 0;
    $totalSpent = $purchaseStats['total_spent'] ?? 0;
} catch (PDOException $e) {
    error_log("Error fetching purchase statistics: " . $e->getMessage());
    $totalPurchases = 0;
    $totalSpent = 0;
}

// Include header
include_once '../includes/header.php';

?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include_once '../includes/sidebar.php'; ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="container-fluid">
                <h1 class="h2">My Purchases</h1>
            </div>
            <br>
            <div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Purchases</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php  echo $totalPurchases  ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Spent</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalSpent)  ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>

        <!-- Content Row -->
        <!-- <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Purchase History</h6>
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
                                                        <img src="<?php echo UPLOAD_URL . $purchase['image_url']; ?>" 
                                                            alt="<?php echo $purchase['title']; ?>" 
                                                            class="img-thumbnail me-2" 
                                                            style="width: 50px; height: 50px; object-fit: cover;">
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
                                                    <a href="<?php echo SITE_URL; ?>/buyer/dashboard/purchase-details.php?id=<?php echo $purchase['order_id']; ?>" 
                                                    class="btn btn-sm btn-info">
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
                                <a href="<?php echo SITE_URL; ?>/gallery.php" class="btn btn-primary">Browse Artwork</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> -->

            <!--  Display recent purchases section -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Purchases</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Artwork</th>
                                            <th>Artist</th>
                                            <th>Date</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    
            if (count($recentIndexPurchases) > 0) {
                foreach ($recentIndexPurchases as $purchase) {?>
                   <tr>
                        <td><?php echo $counter = isset($counter) ? $counter + 1 : 1; ?></td>
                        <td><?php echo  htmlspecialchars($purchase['title']) ?></td>
                        <td><?php echo htmlspecialchars($purchase['artist_name']) ?></td>
                        <td><?php echo formatDate($purchase['created_at']) ?></td>
                        <td><?php echo formatCurrency($purchase['total_price']) ?></td>
                        <td>
                                                <?php if ($purchase['payment_status'] == 'completed'): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php elseif ($purchase['payment_status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                    </tr><?php
                }
            } else {?>
                <tr><td colspan="5" class="text-center">No purchases made recently</td></tr>';
                <?php 
                     }
                ?>
            </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>



<?php
include_once '../includes/footer.php';
?>
