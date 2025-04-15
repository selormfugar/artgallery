<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

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

<?php require_once '../../includes/head.php'; ?>

<body class="<?= isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : '' ?>">
    <?php require_once '../../includes/navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:py-12">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 text-sm">
            <li>
                <a href="gallery.php" class="text-gray-500 hover:text-amber-600 transition-colors">Gallery</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-amber-600 font-medium"><?= htmlspecialchars($artwork['title']) ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Artwork Display Section -->
        <div class="relative group">
            <!-- Artwork Frame -->
            <div class="absolute inset-0 bg-gradient-to-br from-amber-900/5 to-stone-800/5 rounded-xl shadow-lg transform group-hover:scale-[1.01] transition-transform duration-500 -z-10"></div>
            
            <!-- Main Artwork Image -->
            <div class="relative aspect-[4/3] bg-stone-100 dark:bg-stone-900 rounded-xl overflow-hidden">
                <img 
                    id="main-artwork-image"
                    src="<?= SITE_URL ?>/images/<?= htmlspecialchars($artwork['image_url']) ?>" 
                    alt="<?= htmlspecialchars($artwork['title']) ?>"
                    class="w-full h-full object-contain transition-opacity duration-300 opacity-0"
                    onload="this.classList.remove('opacity-0')"
                    loading="eager"
                >
                
                <!-- Loading Placeholder -->
                <div class="absolute inset-0 flex items-center justify-center bg-stone-100 dark:bg-stone-900 transition-opacity duration-300" id="image-loading">
                    <div class="animate-pulse flex space-x-2">
                        <div class="w-3 h-3 bg-amber-600 rounded-full"></div>
                        <div class="w-3 h-3 bg-amber-600 rounded-full" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-amber-600 rounded-full" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
                
                <!-- Auction Badge -->
                <?php if ($in_auction): ?>
                <div class="absolute top-4 left-4 bg-gradient-to-r from-amber-700 to-amber-800 text-white text-xs font-medium px-3 py-1 rounded-full shadow-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727z" clip-rule="evenodd" />
                    </svg>
                    LIVE AUCTION
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Optional: Zoom Controls -->
            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex space-x-2">
                <button class="bg-white/90 dark:bg-stone-800/90 p-2 rounded-full shadow-md hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                    </svg>
                </button>
                <button class="bg-white/90 dark:bg-stone-800/90 p-2 rounded-full shadow-md hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Artwork Details Section -->
        <div class="space-y-6">
            <!-- Title and Artist -->
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-stone-900 dark:text-white mb-1"><?= htmlspecialchars($artwork['title']) ?></h1>
                <p class="text-lg text-stone-600 dark:text-stone-400">by <a href="#" class="hover:text-amber-600 transition-colors"><?= htmlspecialchars($artwork['artist_name']) ?></a></p>
                
                <!-- Price/Auction Info -->
                <div class="mt-6">
                    <?php if ($in_auction): ?>
                        <div class="flex items-center space-x-4">
                            <div class="bg-amber-100 dark:bg-amber-900/30 px-4 py-3 rounded-lg">
                                <p class="text-xs text-amber-800 dark:text-amber-200">CURRENT BID</p>
                                <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">$<?= number_format($auction['current_bid'], 2) ?></p>
                            </div>
                            <div class="text-sm">
                                <p class="text-stone-500 dark:text-stone-400">Auction ends:</p>
                                <p class="font-medium"><?= date('M j, Y g:i A', strtotime($auction['end_time'])) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-3xl font-bold text-stone-900 dark:text-white">$<?= number_format($artwork['price'], 2) ?></p>
                    <?php endif; ?>
                </div>
            </div>

           <!-- Description -->
            <div class="pt-4 border-t border-stone-200 dark:border-stone-800">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-white mb-3">Description</h3>
                <p class="text-stone-600 dark:text-stone-400 leading-relaxed">
                    <?= htmlspecialchars($artwork['description'] ?: 'No description available') ?>
                </p>
            </div>

            <!-- Details Accordion -->
            <div class="border border-stone-200 dark:border-stone-800 rounded-lg overflow-hidden">
                <details class="group" open>
                    <summary class="list-none flex justify-between items-center p-4 cursor-pointer bg-stone-50 dark:bg-stone-900/50 hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
                        <h3 class="font-semibold text-stone-900 dark:text-white">Artwork Details</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-stone-500 group-open:rotate-180 transform transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </summary>
                    <div class="p-4 pt-0 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-stone-500 dark:text-stone-400">Artist</span>
                            <span class="font-medium"><?= htmlspecialchars($artwork['artist_name']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500 dark:text-stone-400">Year Created</span>
                            <span class="font-medium"><?= date('Y', strtotime($artwork['created_at'])) ?></span>
                        </div>
                       
                        <div class="flex justify-between">
                            <span class="text-stone-500 dark:text-stone-400">Category</span>
                            <span class="font-medium"><?= htmlspecialchars($artwork['category_name'] ?: 'Uncategorized') ?></span>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Artist Preview -->
            <div class="flex items-center space-x-4 p-4 bg-stone-50 dark:bg-stone-900/30 rounded-lg border border-stone-200 dark:border-stone-800">
                <?php if (!empty($artwork['artist_profile'])): ?>
                    <img src="<?= SITE_URL ?>/images/<?= htmlspecialchars($artwork['artist_profile']) ?>" 
                         class="w-16 h-16 rounded-full object-cover border-2 border-amber-500/30" 
                         alt="<?= htmlspecialchars($artwork['artist_name']) ?>">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-full bg-stone-200 dark:bg-stone-700 flex items-center justify-center border-2 border-amber-500/30">
                        <span class="text-xl font-semibold text-stone-600 dark:text-stone-300">
                            <?= substr($artwork['artist_name'], 0, 1) ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div>
                    <h4 class="font-medium"><?= htmlspecialchars($artwork['artist_name']) ?></h4>
                    <p class="text-sm text-stone-500 dark:text-stone-400">Artist</p>
                    <a href="#" class="text-sm text-amber-600 hover:text-amber-700 transition-colors">View profile →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Artworks -->
    <section class="mt-16 pt-8 border-t border-stone-200 dark:border-stone-800">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-stone-900 dark:text-white">More from this artist</h2>
            <a href="#" class="text-amber-600 hover:text-amber-700 transition-colors text-sm font-medium">View all</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6" id="related-artworks">
            <!-- Will be loaded via AJAX -->
        </div>
        
        <div id="related-loading" class="text-center py-8 hidden">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-amber-600"></div>
        </div>
    </section>
</main>

<script>
document.getElementById('main-artwork-image').addEventListener('load', function() {
    document.getElementById('image-loading').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('image-loading').style.display = 'none';
    }, 300);
});
</script>

<script>
document.getElementById('main-artwork-image').addEventListener('load', function() {
    document.getElementById('image-loading').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('image-loading').style.display = 'none';
    }, 300);
});
</script>
    <?php require_once '../../includes/footer.php'; ?>

    <!-- Font Awesome for social icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>


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
            url: '../../api/get_related_artworks.php',
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