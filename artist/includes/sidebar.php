<?php
// Get unread message count
$unreadMessages = 0;
if (isLoggedIn()) {
    global $db;
    $result = $db->selectOne("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND seen = 0 AND archived = 0", [$_SESSION['user_id']]);
    $unreadMessages = $result['count'];
}

// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);
// Add JavaScript and CSS for collapsible sidebar
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    });
</script>
<style>
    .sidebar.collapsed {
        width: 60px;
    }
    .sidebar.collapsed .nav-link {
        text-align: center;
        font-size: 0;
    }
    .sidebar.collapsed .nav-link i {
        font-size: 1.5rem;
    }
    .sidebar.collapsed .text-white,
    .sidebar.collapsed .btn-outline-light {
        display: none;
    }
</style>
<button id="sidebarToggle" class="btn btn-light btn-sm position-fixed" style="top: 5px; left: 5px; z-index: 1000;"></button>
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <!-- <img src="<?php echo SITE_URL; ?>/assets/img/logo.png" alt="Logo" class="img-fluid" style="max-width: 120px;"> -->
            <h5 class="text-white mt-2">Artist Dashboard</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'upload.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/upload.php">
                    <i class="fas fa-upload me-2"></i>
                    Upload Artwork
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'artworks.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/artworks.php">
                    <i class="fas fa-palette me-2"></i>
                    My Artworks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'sales.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/sales.php">
                    <i class="fas fa-chart-line me-2"></i>
                    Sales Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'subscription.php.') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/subscription.php">
                    <i class="fas fa-crown me-2"></i>
                    Subscriptions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'messages.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/messages.php">
                    <i class="fas fa-envelope me-2"></i>
                    Messages
                    <?php if ($unreadMessages > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-2"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/artist/dashboard/settings.php">
                    <i class="fas fa-cog me-2"></i>
                    Account Settings
                </a>
            </li>
        </ul>
        
        <hr class="text-light">
        
        <div class="px-3 mt-4">
            <div class="d-flex align-items-center text-white">
                <img src="<?php echo SITE_URL; ?>/assets/img/user-placeholder.jpg" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
            </div>
            <div class="mt-2">
                <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

