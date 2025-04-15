<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

// Get dashboard stats
$stats = getDashboardStats();

// Get recent users
$recentUsers = getRecentUsers();

// Get recent sales
$recentSales = getRecentSales();

// Get pending moderation
$pendingModeration = getPendingModeration();

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshStats">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportDashboard">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-calendar me-1"></i> This Month
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Week</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
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
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $stats['total_users']; ?></div>
                            <div class="text-xs mt-1">
                                <span class="<?php echo $stats['user_growth'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <i class="fas <?php echo $stats['user_growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                                    <?php echo abs($stats['user_growth']); ?>%
                                </span>
                                from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo formatCurrency($stats['total_revenue']); ?></div>
                            <div class="text-xs mt-1">
                                <span class="<?php echo $stats['revenue_growth'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <i class="fas <?php echo $stats['revenue_growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                                    <?php echo abs($stats['revenue_growth']); ?>%
                                </span>
                                from last period
                            </div>
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
                                Total Artworks</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $stats['total_artworks']; ?></div>
                            <div class="text-xs mt-1">
                                <span class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <?php echo $stats['pending_moderation']; ?> pending
                                </span>
                                moderation
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-palette fa-2x text-gray-300"></i>
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
                                Flagged Content</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $stats['flagged_content']; ?></div>
                            <div class="text-xs mt-1">
                                <span class="text-danger">
                                    <i class="fas fa-flag me-1"></i>
                                    Requires attention
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Time Period:</div>
                            <a class="dropdown-item" href="#" data-period="week">Last Week</a>
                            <a class="dropdown-item" href="#" data-period="month">Last Month</a>
                            <a class="dropdown-item" href="#" data-period="year">Last Year</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">View Options:</div>
                            <a class="dropdown-item" href="#" data-view="category">By Category</a>
                            <a class="dropdown-item" href="#" data-view="artist">By Artist</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Pending Moderation -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Moderation</h6>
                    <a href="<?php echo SITE_URL; ?>/admin/moderation.php" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (count($pendingModeration) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Artwork</th>
                                        <th>Artist</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingModeration as $artwork): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo UPLOAD_URL . $artwork['image_url']; ?>" alt="<?php echo $artwork['title']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <span><?php echo $artwork['title']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $artwork['artist_name']; ?></td>
                                            <td><?php echo formatDate($artwork['created_at']); ?></td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/admin/artwork-details.php?id=<?php echo $artwork['artwork_id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success approve-artwork" data-id="<?php echo $artwork['artwork_id']; ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-artwork" data-id="<?php echo $artwork['artwork_id']; ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>No artworks pending moderation.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (count($recentUsers) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <!-- <th>Username</th> -->
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                        <tr>
                                            <!-- <td><?php echo $user['username']; ?></td> -->
                                            <td><?php echo $user['email']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['role'] == 'artist' ? 'primary' : 'secondary'; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($user['created_at']); ?></td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/admin/user-details.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-info">
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
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <p>No recent users.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
            <a href="<?php echo SITE_URL; ?>/admin/transactions.php" class="btn btn-sm btn-primary">
                View All
            </a>
        </div>
        <div class="card-body">
            <?php if (count($recentSales) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Artwork</th>
                                <th>Buyer</th>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td>#<?php echo $sale['order_id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo UPLOAD_URL . $sale['image_url']; ?>" alt="<?php echo $sale['title']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <span><?php echo $sale['title']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $sale['buyer_name']; ?></td>
                                    <td><?php echo $sale['seller_name']; ?></td>
                                    <td><?php echo formatCurrency($sale['total_price']); ?></td>
                                    <td><?php echo formatDate($sale['created_at']); ?></td>
                                    <td>
                                        <?php if ($sale['payment_status'] == 'completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($sale['payment_status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php elseif ($sale['payment_status'] == 'refunded'): ?>
                                            <span class="badge bg-info">Refunded</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/transaction-details.php?id=<?php echo $sale['order_id']; ?>" class="btn btn-sm btn-info">
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
                    <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                    <p>No recent sales.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                lineTension: 0.3,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 35000],
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return '$' + value;
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += '$' + context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Revenue Pie Chart
    const pieCtx = document.getElementById('revenuePieChart').getContext('2d');
    const revenuePieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paintings', 'Digital Art', 'Sculptures', 'Photography', 'Other'],
            datasets: [{
                data: [40, 20, 15, 15, 10],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                }
            }
        },
    });

    // Quick approve/reject artwork
    document.querySelectorAll('.approve-artwork').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to approve this artwork?')) {
                fetch('../api/moderation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=moderate&artwork_id=${artworkId}&status=approved`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from table
                        this.closest('tr').remove();
                        
                        // Update pending count
                        const pendingElement = document.querySelector('.text-warning');
                        if (pendingElement) {
                            const currentCount = parseInt(pendingElement.textContent.match(/\d+/)[0]);
                            pendingElement.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${currentCount - 1} pending`;
                        }
                    } else {
                        alert('Error approving artwork: ' + data.message);
                    }
                });
            }
        });
    });

    document.querySelectorAll('.reject-artwork').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to reject this artwork?')) {
                fetch('../api/moderation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=moderate&artwork_id=${artworkId}&status=rejected`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from table
                        this.closest('tr').remove();
                        
                        // Update pending count
                        const pendingElement = document.querySelector('.text-warning');
                        if (pendingElement) {
                            const currentCount = parseInt(pendingElement.textContent.match(/\d+/)[0]);
                            pendingElement.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${currentCount - 1} pending`;
                        }
                    } else {
                        alert('Error rejecting artwork: ' + data.message);
                    }
                });
            }
        });
    });

    // Refresh stats
    document.getElementById('refreshStats').addEventListener('click', function() {
        fetch('../api/dashboard.php?action=stats')
            .then(response => response.json())
            .then(data => {
                // Update stats
                document.querySelector('.text-primary + .h5').textContent = data.total_users;
                document.querySelector('.text-success + .h5').textContent = '$' + data.total_revenue.toLocaleString();
                document.querySelector('.text-info + .h5').textContent = data.total_artworks;
                document.querySelector('.text-warning + .h5').textContent = data.flagged_content;
                
                // Update growth indicators
                const userGrowthElement = document.querySelector('.text-primary + .h5 + .text-xs span');
                userGrowthElement.className = data.user_growth >= 0 ? 'text-success' : 'text-danger';
                userGrowthElement.innerHTML = `<i class="fas ${data.user_growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1"></i> ${Math.abs(data.user_growth)}%`;
                
                const revenueGrowthElement = document.querySelector('.text-success + .h5 + .text-xs span');
                revenueGrowthElement.className = data.revenue_growth >= 0 ? 'text-success' : 'text-danger';
                revenueGrowthElement.innerHTML = `<i class="fas ${data.revenue_growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1"></i> ${Math.abs(data.revenue_growth)}%`;
                
                const pendingElement = document.querySelector('.text-info + .h5 + .text-xs span');
                pendingElement.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${data.pending_moderation} pending`;
            });
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>

