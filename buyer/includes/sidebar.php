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
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
  <div class="position-sticky pt-3">
      <div class="text-center mb-4">
          <img src="<?php echo SITE_URL; ?>/assets/img/logo.png" alt="Logo" class="img-fluid" style="max-width: 120px;">
          <h5 class="text-white mt-2">Buyer Dashboard</h5>
      </div>
      
      <ul class="nav flex-column">
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/index.php">
                  <i class="fas fa-home me-2"></i>
                  Home
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'collection.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/collection.php">
                  <i class="fas fa-palette me-2"></i>
                  My Collection
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'wishlist.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/wishlist.php">
                  <i class="fas fa-heart me-2"></i>
                  Wishlist
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'purchases.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/purchases.php">
                  <i class="fas fa-shopping-cart me-2"></i>
                  Purchase History
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'messages.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/messages.php">
                  <i class="fas fa-envelope me-2"></i>
                  Messages
                  <?php if ($unreadMessages > 0): ?>
                      <span class="badge bg-danger rounded-pill ms-2"><?php echo $unreadMessages; ?></span>
                  <?php endif; ?>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/buyer/dashboard/settings.php">
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

