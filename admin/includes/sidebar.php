<?php
// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="<?php echo SITE_URL; ?>/assets/img/logo-light.png" alt="Logo" class="img-fluid" style="max-width: 120px;">
            <h5 class="text-white mt-2">Admin Dashboard</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item"></li>
                <a class="nav-link <?php echo ($currentPage == 'users.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/users.php">
                    <i class="fas fa-users me-2"></i>
                    User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'moderation.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/moderation.php">
                    <i class="fas fa-check-circle me-2"></i>
                    Artwork Moderation
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'reports.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/reports.php">
                    <i class="fas fa-chart-bar me-2"></i>
                    Reports & Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'transactions.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/transactions.php">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'flagged.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/flagged.php">
                    <i class="fas fa-flag me-2"></i>
                    Flagged Content
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/settings.php">
                    <i class="fas fa-cog me-2"></i>
                    System Settings
                </a>
            </li>
        </ul>
        
        <hr class="text-light">
        
        <div class="px-3 mt-4">
            <div class="d-flex align-items-center text-white">
                <img src="<?php echo SITE_URL; ?>/assets/img/admin-avatar.jpg" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                <span><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></span>
            </div>
            <div class="mt-2">
                <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

