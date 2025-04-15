<?php
// Configure session cookie
// session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$currentRole = $_SESSION['role'] ?? 'guest'; // Default to guest if not set

?>

<nav class="nav-menu">
    <!-- Branding Section -->
    <div class="flex items-center">
        <a href="index.php" class="text-2xl font-bold">M</a>
        <span class="text-sm ml-2">Art Gallery</span>
    </div>

    <!-- Main Navigation Links -->
    <div class="main-links">
        <a href="index.php">Home</a>
        <a href="gallery.php">Gallery</a>
        
        <?php if ($isLoggedIn): ?>
            <!-- Logged-in User Links -->
            <a href="auction.php">Auctions</a>
            <a href="shop.php">Shop</a>
            <!-- <a href="events.php">Events</a> -->
            <a href="subscription.php">Subscription</a>
        <?php else: ?>
            <!-- Guest Links -->
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        <?php endif; ?>
    </div>

    <!-- User Controls Section -->
    <div class="user-controls">
        <?php if ($isLoggedIn): ?>
            <!-- Profile Link -->
            <a href="<?= strtolower($currentRole) ?>/dashboard" 
               class="profile-link px-4 py-2 rounded hover:bg-gray-200">
               <i class="fas fa-user-circle mr-2"></i>Profile
            </a>
            
            <!-- Logout Link -->
            <a href="logout.php" class="logout-link px-4 py-2 rounded hover:bg-gray-200">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
            
        <?php else: ?>
            <!-- Login/Register Links -->
            <a href="login.php" class="login-btn px-4 py-2 rounded hover:bg-gray-200">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </a>
            <a href="register.php" class="register-btn px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">
                <i class="fas fa-user-plus mr-2"></i>Register
            </a>
        <?php endif; ?>
        
        <!-- Theme Toggle -->
  <!-- Theme toggle floating button -->
<button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
    <i class="fas fa-moon text-white" id="theme-icon"></i>
</button>

    </div>
</nav>
  