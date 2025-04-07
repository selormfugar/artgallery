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
        <button onclick="toggleDarkMode()" class="ml-4 p-2 rounded-full focus:outline-none">
  <svg id="light-icon" class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
  </svg>
  <svg id="dark-icon" class="w-6 h-6 text-gray-200 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
  </svg>
</button>
    </div>
</nav>
  