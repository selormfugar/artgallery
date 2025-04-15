<?php
require_once 'includes/config.php';
require_once 'includes/auth_check.php';

// Get artwork ID from URL
$artwork_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($artwork_id <= 0) {
    header("Location: gallery.php");
    exit();
}

// Fetch artwork details first
try {
    $stmt = $pdo->prepare("
        SELECT a.*, 
               CONCAT(u.firstname, ' ', u.lastname) as artist_name,
               c.name as category_name,
               ar.bio as artist_bio,
               ar.profile_picture as artist_profile,
               ar.social_links
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        LEFT JOIN categories c ON a.category = c.category_id
        WHERE a.artwork_id = ? AND a.archived = 0
    ");
    $stmt->execute([$artwork_id]);
    $artwork = $stmt->fetch();

    if (!$artwork) {
        header("Location: gallery.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching artwork: " . $e->getMessage());
}

// Check if artwork is in user's wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND artwork_id = ?");
    $stmt->execute([$_SESSION['user_id'], $artwork['artwork_id']]);
    $in_wishlist = $stmt->fetchColumn() > 0;
}

if ($artwork_id <= 0) {
    header("Location: gallery.php");
    exit();
}

// Fetch artwork details
try {
    $stmt = $pdo->prepare("
        SELECT a.*, 
               CONCAT(u.firstname, ' ', u.lastname) as artist_name,
               c.name as category_name,
               ar.bio as artist_bio,
               ar.profile_picture as artist_profile,
               ar.social_links
        FROM artworks a
        JOIN artists ar ON a.artist_id = ar.artist_id
        JOIN users u ON ar.user_id = u.user_id
        LEFT JOIN categories c ON a.category = c.category_id
        WHERE a.artwork_id = ? AND a.archived = 0
    ");
    $stmt->execute([$artwork_id]);
    $artwork = $stmt->fetch();

    if (!$artwork) {
        header("Location: gallery.php");
        exit();
    }

    // Parse social links if they exist
    $social_links = [];
    if (!empty($artwork['social_links'])) {
        $social_links = json_decode($artwork['social_links'], true);
    }

    // Check if artwork is in auction
    $in_auction = false;
    $auction_stmt = $pdo->prepare("
        SELECT * FROM auctions 
        WHERE artwork_id = ? AND status = 'active'
    ");
    $auction_stmt->execute([$artwork_id]);
    $auction = $auction_stmt->fetch();
    $in_auction = ($auction !== false);

} catch (PDOException $e) {
    die("Error fetching artwork: " . $e->getMessage());
}

// Set page title
$page_title = $artwork['title'] . " | Art Gallery";

// Track view (optional)
try {
    $view_stmt = $pdo->prepare("
        INSERT INTO artwork_views (user_id, artwork_id, viewed_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE viewed_at = NOW()
    ");
    $view_stmt->execute([isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null, $artwork_id]);
} catch (PDOException $e) {
    // Silently fail view tracking
    error_log("Error tracking view: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once 'includes/head.php'; ?>

<body class="<?= isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : '' ?>">
    <?php require_once 'includes/navbar.php'; ?>
 <!-- Theme toggle floating button -->
 <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>

    <main class="max-w-7xl mx-auto px-4 py-12">
    <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="gallery.php" class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Gallery</a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-500">/</span>
                        <span class="text-gray-500"><?= htmlspecialchars($artwork['title']) ?></span>
                    </div>
                </li>
            </ol>
        </nav>
       

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 max-w-7xl mx-auto px-4">
    <!-- Artwork Image Section -->
    <div class="rounded-2xl bg-[#1a1a1a] p-6 border border-[#2e2e2e] shadow-[0_10px_30px_rgba(0,0,0,0.3)] transition-all duration-500 hover:shadow-[0_15px_40px_rgba(0,0,0,0.4)]">
    <div class="relative aspect-[4/3] bg-[#121212] rounded-xl overflow-hidden group">
        <!-- Main Artwork Image -->
        <img 
            id="main-artwork-image"
            src="<?= SITE_URL ?>/images/<?= htmlspecialchars($artwork['image_url']) ?>" 
            alt="<?= htmlspecialchars($artwork['title']) ?>"
            class="w-full h-full object-contain transition-all duration-700 opacity-0 scale-95 group-hover:scale-100"
            onload="this.classList.remove('opacity-0', 'scale-95')"
            loading="eager"
            style="max-height: 70vh"
        >
        
        <!-- Subtle Loading Indicator -->
        <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-500" id="image-loading">
            <div class="animate-pulse flex space-x-2">
                <div class="w-3 h-3 bg-[#b8895c] rounded-full"></div>
                <div class="w-3 h-3 bg-[#b8895c] rounded-full" style="animation-delay: 0.2s"></div>
                <div class="w-3 h-3 bg-[#b8895c] rounded-full" style="animation-delay: 0.4s"></div>
            </div>
        </div>
        
        <!-- Auction Badge -->
        <?php if ($in_auction): ?>
        <div class="absolute top-5 left-5 bg-gradient-to-r from-[#b8895c] to-[#9a6b42] text-white text-sm font-medium px-4 py-2 rounded-full shadow-lg flex items-center backdrop-blur-sm backdrop-brightness-75">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727z" clip-rule="evenodd" />
            </svg>
            <span class="font-sans tracking-wide">LIVE AUCTION</span>
        </div>
        <?php endif; ?>
        
        <!-- Subtle Artwork Frame Effect -->
        <div class="absolute inset-0 border-2 border-[#ffffff08] pointer-events-none rounded-xl"></div>
    </div>
    
    <!-- Optional: Thumbnail Navigation (if you add multiple images later) -->
    <!-- <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
        <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
            <button class="w-14 h-14 rounded-lg overflow-hidden border-2 border-transparent hover:border-[#b8895c] transition-all">
                <div class="w-full h-full bg-[#2e2e2e] animate-pulse"></div>
            </button>
        </div>
    </div> -->
</div>

<script>
document.getElementById('main-artwork-image').addEventListener('load', function() {
    document.getElementById('image-loading').classList.add('opacity-0');
    setTimeout(() => {
        document.getElementById('image-loading').style.display = 'none';
    }, 500);
});
</script>

    <!-- Artwork Details Section -->
    <div class="space-y-6 lg:space-y-8">
        <!-- Title and Price -->
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white-900 dark:text-white mb-2"><?= htmlspecialchars($artwork['title']) ?></h1>
            <p class="text-lg text-white-600 dark:text-white-400">by <?= htmlspecialchars($artwork['artist_name']) ?></p>
            
            <div class="flex items-center space-x-4 mt-4">
                <?php if ($in_auction): ?>
                    <div class="flex items-center text-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Ends: <?= date('M j, Y g:i A', strtotime($auction['end_time'])) ?>
                    </div>
                <?php else: ?>
                    <span class="text-3xl font-bold text-white-900 dark:text-white">$<?= number_format($artwork['price'], 2) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <?php if ($in_auction): ?>
                <a href="auction.php?id=<?= $auction['auction_id'] ?>" 
                   class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center space-x-2 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727z" clip-rule="evenodd" />
                    </svg>
                    <span>Place Bid</span>
                </a>
            <?php else: ?>
                <button onclick="purchaseArtwork(<?= $artwork['artwork_id'] ?>)" 
    class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center space-x-2 shadow-md">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
    </svg>
    <span>Purchase Now</span>
</button>

<!-- Notification Container (add to your layout) -->
<div id="notification-container" class="fixed top-4 right-4 z-50 w-80 space-y-2"></div>
            <?php endif; ?>
            
            <button onclick="addToWishlist(<?= $artwork['artwork_id'] ?>)" 
        class="wishlist-btn flex items-center justify-center space-x-2 <?= $in_wishlist ? 'bg-gray-200 dark:bg-gray-600' : 'bg-gray-100 dark:bg-gray-700' ?> hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-3 rounded-lg transition border border-gray-200 dark:border-gray-600 shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="<?= $in_wishlist ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span><?= $in_wishlist ? 'In Wishlist' : 'Add to Wishlist' ?></span>
</button>
        </div>

        <!-- Description & Details -->
        <div class="bg-[#1e1e1e] rounded-xl p-8 border border-[#333] shadow-lg transition-all duration-300 hover:shadow-xl">
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-[#b8895c] mb-4 font-playfair flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Description
        </h3>
        <p class="text-[#e0e0e0] text-lg leading-relaxed italic font-light border-l-4 border-[#b8895c] pl-4 py-2">
            <?= htmlspecialchars($artwork['description'] ?: 'No description available') ?>
        </p>
    </div>
    
    <div>
        <h3 class="text-2xl font-bold text-[#b8895c] mb-4 font-playfair flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Details
        </h3>
        <ul class="space-y-3">
            <li class="flex justify-between py-3 border-b border-[#333]">
                <span class="text-[#b8895c] font-medium">Artist</span>
                <span class="text-[#e0e0e0] font-light"><?= htmlspecialchars($artwork['artist_name']) ?></span>
            </li>
            <li class="flex justify-between py-3 border-b border-[#333]">
                <span class="text-[#b8895c] font-medium">Category</span>
                <span class="text-[#e0e0e0] font-light"><?= htmlspecialchars($artwork['category_name'] ?: 'Uncategorized') ?></span>
            </li>
            <li class="flex justify-between py-3 border-b border-[#333]">
                <span class="text-[#b8895c] font-medium">Created</span>
                <span class="text-[#e0e0e0] font-light"><?= date('F j, Y', strtotime($artwork['created_at'])) ?></span>
            </li>
            <li class="flex justify-between py-3">
                <span class="text-[#b8895c] font-medium">Status</span>
                <span class="text-[#e0e0e0] font-light">
                    <?= $in_auction ? 
                        '<span class="bg-[#b8895c] bg-opacity-20 text-[#b8895c] px-2 py-1 rounded">In Auction</span>' : 
                        '<span class="bg-green-900 bg-opacity-20 text-green-400 px-2 py-1 rounded">Available</span>' 
                    ?>
                </span>
            </li>
        </ul>
    </div>
</div>

<!-- Artist Info -->
<div class="bg-[#1e1e1e] rounded-xl p-8 border border-[#333] shadow-lg mt-8 transition-all duration-300 hover:shadow-xl">
    <h2 class="text-3xl font-bold text-[#b8895c] mb-6 font-playfair flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        About the Artist
    </h2>
    <div class="flex flex-col sm:flex-row gap-8 items-center sm:items-start">
        <div class="flex-shrink-0">
            <?php if (!empty($artwork['artist_profile'])): ?>
                <img src="<?= SITE_URL ?>/images/<?= htmlspecialchars($artwork['artist_profile']) ?>" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-[#b8895c] shadow-lg transition-transform duration-300 hover:scale-105" 
                     alt="<?= htmlspecialchars($artwork['artist_name']) ?>">
            <?php else: ?>
                <div class="w-32 h-32 rounded-full bg-[#333] flex items-center justify-center border-4 border-[#b8895c] shadow-lg">
                    <span class="text-4xl font-semibold text-[#b8895c]">
                        <?= substr($artwork['artist_name'], 0, 1) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-grow text-center sm:text-left">
            <h3 class="text-2xl font-bold text-[#e0e0e0] mb-3 font-playfair"><?= htmlspecialchars($artwork['artist_name']) ?></h3>
            <p class="text-[#aaa] italic mb-6 leading-relaxed border-l-2 border-[#b8895c] pl-4">
                <?= htmlspecialchars($artwork['artist_bio'] ?: 'No biography available') ?>
            </p>
            
            <?php if (!empty($social_links)): ?>
                <div class="flex justify-center sm:justify-start space-x-5">
                    <?php foreach ($social_links as $platform => $link): ?>
                        <?php if (!empty($link)): ?>
                            <a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener noreferrer"
                               class="text-[#aaa] hover:text-[#b8895c] transition-colors duration-300 text-2xl">
                                <span class="sr-only"><?= ucfirst($platform) ?></span>
                                <i class="fab fa-<?= $platform ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    </div>
</div>

        <!-- Related Artworks -->
        <section class="section-container mt-12">
            <h2 class="text-2xl font-bold mb-6 dark:text-white">More from this artist</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="related-artworks">
                <!-- Will be loaded via AJAX -->
            </div>
            <div id="related-loading" class="text-center py-8 hidden">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"></div>
            </div>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <!-- Font Awesome for social icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
// Wishlist functionality
/**
 * Add artwork to wishlist with login check
 * @param {number} artworkId - ID of the artwork to add
 */
function addToWishlist(artworkId) {
    // First check authentication status
    fetch('api/check_auth.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                // User is logged in - proceed with adding to wishlist
                return addToWishlistProcess(artworkId);
            } else {
                // User not logged in - show login modal
                showLoginModal();
                // Store artwork ID for after login
                sessionStorage.setItem('pending_wishlist_item', artworkId);
                throw new Error('Please login to add to wishlist');
            }
        })
        .then(response => {
            if (response.success) {
                showWishlistSuccess(response.message);
                updateWishlistCount(response.wishlist_count);
            } else {
                showWishlistError(response.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Don't show alert for "not logged in" case
            if (!error.message.includes('login')) {
                showWishlistError(error.message);
            }
        });
}

/**
 * Actual wishlist addition process
 * @param {number} artworkId - ID of the artwork to add
 */

// function addToWishlistProcess(artworkId) {
//     // Get artwork ID from URL if not provided
//     if (!artworkId) {
//         const urlParams = new URLSearchParams(window.location.search);
//         artworkId = urlParams.get('id');
//     }

//     return fetch('api/add_to_wishlist.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({
//             artwork_id: artworkId,
//             csrf_token: getCsrfToken()
//         })
//     }).then(response => response.json());
// }


// Function to add artwork to wishlist
function addToWishlistProcess(artworkId) {
    // Get artwork ID from URL if not provided
    // if (!artworkId) {
    //     const urlParams = new URLSearchParams(window.location.search);
    //     artworkId = urlParams.get('id');
    // }
    fetch('api/add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ artwork_id: artworkId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else if (data.error) {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred while processing your request', 'error');
        console.error('Error:', error);
    });
}

/**
 * Show login modal
 */
function showLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.remove('hidden');
        /**
         * Shows the modal by adding the 'flex' class to make it visible
         * The modal is presumably a DOM element that uses flexbox for layout
         * This class addition triggers the display state change from 'none' to 'flex'
         */
        modal.classList.add('flex');
    } else {
        console.error('Login modal not found');
        // Fallback to redirect if modal doesn't exist
        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
    }
}

/**
 * Close login modal
 */
function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/**
 * Get CSRF token from meta tag
 */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

/**
 * Update wishlist count in UI
 * @param {number} count - New wishlist count
 */
function updateWishlistCount(count) {
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = count;
        wishlistCount.classList.remove('hidden');
    }
}

/**
 * Show success notification
 */

// Unified notification function for both success and error messages
function showNotification(message, type) {
    const notification = document.createElement('div');
    
    // Set styles based on notification type
    if (type === 'success') {
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
    } else {
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Show error notification
 */
function showWishlistError(message) {
    // Replace with your preferred notification system
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/**
 * Check for pending wishlist item after login
 */
function checkPendingWishlistItem() {
    const pendingItem = sessionStorage.getItem('pending_wishlist_item');
    if (pendingItem) {
        addToWishlistProcess(pendingItem)
            .then(response => {
                if (response.success) {
                    showWishlistSuccess('Artwork added to your wishlist!');
                    updateWishlistCount(response.wishlist_count);
                }
                sessionStorage.removeItem('pending_wishlist_item');
            });
    }
}

// Initialize login form submission
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeLoginModal();
                    checkPendingWishlistItem();
                    // Refresh page or update UI as needed
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    alert(data.message || 'Login failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Login error occurred');
            });
        });
    }
});
    // Image zoom functionality
    function setupImageZoom() {
        const image = document.getElementById('main-artwork-image');
        const container = image.parentElement;
        
        container.addEventListener('mousemove', (e) => {
            const { left, top, width, height } = container.getBoundingClientRect();
            const x = (e.clientX - left) / width;
            const y = (e.clientY - top) / height;
            
            image.style.transform = `scale(1.5) translate(${(0.5 - x) * 20}%, ${(0.5 - y) * 20}%)`;
        });
        
        container.addEventListener('mouseleave', () => {
            image.style.transform = 'scale(1)';
        });
    }

    // Change main image from thumbnail
    function changeImage(newSrc) {
        const mainImage = document.getElementById('main-artwork-image');
        mainImage.classList.add('loading');
        mainImage.src = newSrc;
    }

    // Load related artworks
    function loadRelatedArtworks() {
        $('#related-loading').removeClass('hidden');
        
        $.ajax({
            url: 'api/get_related_artworks.php',
            type: 'GET',
            data: {
                artist_id: <?= $artwork['artist_id'] ?>,
                exclude_id: <?= $artwork['artwork_id'] ?>
            },
            dataType: 'json',
            success: function(data) {
                if (data.artworks && data.artworks.length > 0) {
                    data.artworks.forEach(artwork => {
                        const artworkHtml = `
                            <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                                <div class="price-tag absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm z-10">
                                    $${parseFloat(artwork.price).toFixed(2)}
                                </div>
                                <a href="artwork_detail.php?id=${artwork.artwork_id}">
                                    <img src="<?= SITE_URL ?>/images/${artwork.image_url}" 
                                         alt="${artwork.title}" 
                                         class="w-full h-48 object-cover transition duration-500 group-hover:scale-105">
                                    <div class="p-4">
                                        <h3 class="font-semibold text-white-900 dark:text-white">${artwork.title}</h3>
                                        <p class="text-sm text-white-600 dark:text-white-300">${artwork.artist_name}</p>
                                    </div>
                                </a>
                            </div>
                        `;
                        $('#related-artworks').append(artworkHtml);
                    });
                } else {
                    $('#related-artworks').html('<p class="col-span-full text-center py-4 text-gray-500">No other artworks by this artist</p>');
                }
            },
            complete: function() {
                $('#related-loading').addClass('hidden');
            }
        });
    }

function purchaseArtwork(artworkId) {
    $.ajax({
        url: 'api/purchase_artwork.php',
        type: 'POST',
        data: { artwork_id: artworkId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showNotification({
                    type: 'success',
                    title: 'Purchase Successful',
                    message: response.message,
                    autoClose: true
                });
                
                // Update UI if needed
                if (response.order_id) {
                    // Optionally redirect to order confirmation
                    // window.location.href = `/order-confirmation.php?id=${response.order_id}`;
                }
                
                // Update any subscription info if applicable
                if (response.subscription_discount_applied) {
                    showNotification({
                        type: 'info',
                        title: 'Discount Applied',
                        message: `Your subscription saved you ${response.discount_amount}!`,
                        autoClose: true
                    });
                }
            } else {
                showNotification({
                    type: 'error',
                    title: 'Purchase Failed',
                    message: response.message || 'Failed to complete purchase',
                    autoClose: true
                });
            }
        },
        error: function() {
            showNotification({
                type: 'error',
                title: 'Error',
                message: 'Error communicating with server',
                autoClose: true
            });
        }
    });
}

// Notification system
function showNotification({ type = 'info', title, message, autoClose = true, duration = 5000 }) {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    // Base classes
    let classes = 'p-4 rounded-lg shadow-lg border-l-4 ';
    
    // Type-specific classes
    switch(type) {
        case 'success':
            classes += 'bg-green-50 border-green-500 text-green-700';
            break;
        case 'error':
            classes += 'bg-red-50 border-red-500 text-red-700';
            break;
        case 'warning':
            classes += 'bg-yellow-50 border-yellow-500 text-yellow-700';
            break;
        default:
            classes += 'bg-blue-50 border-blue-500 text-blue-700';
    }
    
    notification.className = classes;
    notification.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold">${title}</h3>
                <p class="text-sm">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                &times;
            </button>
        </div>
    `;
    
    container.appendChild(notification);
    
    if (autoClose) {
        setTimeout(() => {
            notification.remove();
        }, duration);
    }
}

    // Initialize when page loads
    $(document).ready(function() {
        setupImageZoom();
        loadRelatedArtworks();
        
        // Hide loading indicator when main image loads
        $('#main-artwork-image').on('load', function() {
            $('#image-loading').hide();
        });
        
        // Fallback in case image fails to load
        $('#main-artwork-image').on('error', function() {
            $('#image-loading').hide();
            $(this).attr('src', '<?= SITE_URL ?>/images/placeholder.jpg');
        });
    });
    </script>
</body>
</html>