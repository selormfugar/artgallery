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
            <a href="events.php">Events</a>
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
        <button onclick="toggleMode()" class="theme-toggle px-4 py-2 rounded hover:bg-gray-200">
            <i class="fas fa-moon mr-2"></i>Theme
        </button>
    </div>
</nav>
<!-- <style>
    .nav-menu {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .main-links {
        display: flex;
        gap: 1.5rem;
    }
    
    .main-links a {
        padding: 0.5rem 0;
        transition: color 0.3s;
    }
    
    .main-links a:hover {
        color: #3b82f6;
    }
    
    .user-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    a {
        text-decoration: none;
        color: inherit;
    }
    
    .register-btn {
        transition: background-color 0.3s;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-menu {
            flex-direction: column;
            gap: 1rem;
        }
        
        .main-links, .user-controls {
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }
    }
</style>