<?php
require_once '../includes/config.php';

// Check for pending wishlist items after login
if (isset($_SESSION['user_id'])) {
    echo '<script>checkPendingWishlistItem();</script>';
    
    // Handle redirect if present
    if (isset($_GET['redirect'])) {
        header("Location: " . urldecode($_GET['redirect']));
        exit();
    }
}

$userId = $_SESSION['user_id'] ?? null;
$db = null;
$recommendedArtists = [];
$subscribedArtists = [];

if ($userId) {
    try {
        // Single database connection for both queries
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query 1: Get subscribed artists (simplified with DISTINCT)
        $stmt = $db->prepare("
            SELECT 
                us.subscription_id,
                u.user_id AS artist_id, 
                u.firstname, 
                u.lastname,
                u.profile_image
            FROM user_subscriptions us
            JOIN artist_subscription_settings ass ON us.plan_id = ass.setting_id
            JOIN users u ON ass.artist_id = u.user_id
            WHERE us.user_id = :user_id
            AND us.status = 'active'
            AND us.end_date > NOW()
            ORDER BY u.firstname ASC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $subscribedArtists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query 2: Get recommended artists (not subscribed to)
        $stmt = $db->prepare("
            SELECT 
                u.user_id, 
                u.firstname, 
                u.lastname,
                u.profile_image, 
                ass.setting_id AS plan_id
            FROM users u
            JOIN artist_subscription_settings ass ON u.user_id = ass.artist_id
            WHERE u.user_id NOT IN (
                SELECT ass.artist_id 
                FROM user_subscriptions us
                JOIN artist_subscription_settings ass ON us.plan_id = ass.setting_id
                WHERE us.user_id = :user_id
                AND us.status = 'active'
                AND us.end_date > NOW()
            )
            AND ass.is_enabled = 1
            ORDER BY RAND()
            LIMIT 4
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $recommendedArtists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    } finally {
        // Close connection if it was opened
        $db = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>

<body class="bg-gray-50 dark:bg-gray-900">
    <?php require_once 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="relative py-20 bg-gray-900 text-white">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Your Artist Subscriptions</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Stay updated with exclusive content from your favorite artists.
            </p>
        </div>
    </section>

    <!-- Subscription Management -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 text-center">Your Subscribed Artists</h2>
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    Manage which artists you follow and discover new ones.
                </p>
            </div>

            <!-- Subscribed Artists Grid -->
           

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
    <?php if (empty($subscribedArtists)): ?>
        <div class="col-span-full text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">You haven't subscribed to any artists yet.</p>
            <a href="artists.php" class="mt-4 inline-block px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300">
                Browse Artists
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($subscribedArtists as $artist): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="relative h-48">
                    <img src="<?= htmlspecialchars($artist['profile_image'] ?? 'images/artist8.jpg') ?>" 
                         alt="<?= htmlspecialchars($artist['firstname'] . ' ' . $artist['lastname']) ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6">
                        <h3 class="text-xl font-bold text-white"><?= htmlspecialchars($artist['firstname'] . ' ' . $artist['lastname']) ?></h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        <!-- <?= htmlspecialchars($artist['latest_exhibition'] ?? 'No current exhibitions') ?> -->
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="artist-gallery.php?artist_id=<?= $artist['artist_id'] ?>" 
                           class="text-amber-600 hover:text-amber-700 font-medium">
                            View Updates
                        </a>
                        <!-- <a href="artist-profile.php?id=<?= $artist['user_id'] ?>" 
                           class="text-amber-600 hover:text-amber-700 font-medium">
                            View Updates
                        </a> -->
                        <button class="unsubscribe-button px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition duration-300" 
        onclick="unsubscribeFromArtist(<?= htmlspecialchars($artist['subscription_id']) ?>)">
    Unsubscribe
</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

            <!-- Recommended Artists -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 text-center">Discover New Artists</h2>
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    Expand your collection with these trending creators.
                </p>
            </div>

            <!-- Recommended Artists Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php if (empty($recommendedArtists)): ?>
        <div class="col-span-full text-center py-6">
            <p class="text-gray-600 dark:text-gray-400">You're subscribed to all available artists!</p>
        </div>
    <?php else: ?>
        <?php foreach ($recommendedArtists as $artist): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center hover:shadow-md transition duration-300">
                <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4">
                    <img src="<?= htmlspecialchars($artist['profile_image'] ?? 'images/default-artist.jpg') ?>" 
                         alt="<?= htmlspecialchars($artist['name']) ?>" 
                         class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($artist['firstname']) ?></h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4"><?= htmlspecialchars($artist['description'] ?? 'Artist') ?></p>
                <button class="subscribe-button px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300"
                        data-artist-id="<?= $artist['user_id'] ?>" 
                        data-plan-id="<?= $artist['plan_id'] ?>"
                        onclick="subscribeToArtist(<?= $artist['user_id'] ?>, <?= $artist['plan_id'] ?>)">
                    Subscribe
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
            
        </div>
    </section>

    <?php require_once 'includes/footer.php'; ?>
    <script>
    // Frontend JavaScript for subscription handling
document.addEventListener('DOMContentLoaded', function() {
    // Subscribe button event listener
    const subscribeButtons = document.querySelectorAll('.subscribe-button');
    subscribeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const artistId = this.getAttribute('data-artist-id');
            const planId = this.getAttribute('data-plan-id');
            subscribeToArtist(artistId, planId);
        });
    });

    // Unsubscribe button event listener
    const unsubscribeButtons = document.querySelectorAll('.unsubscribe-button');
    unsubscribeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const subscriptionId = this.getAttribute('data-subscription-id');
            unsubscribeFromArtist(subscriptionId);
        });
    });
});

/**
 * Subscribe to an artist's plan
 * @param {number} artistId - The artist's ID
 * @param {number} planId - The subscription plan ID
 */
function subscribeToArtist(artistId, planId) {
    // Show loading state
    const button = document.querySelector(`[data-artist-id="${artistId}"][data-plan-id="${planId}"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner"></span> Processing...';
    button.disabled = true;

    // AJAX request
    fetch('api/subscribe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            artist_id: artistId,
            plan_id: planId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to show subscribed state
            button.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Subscribed';
            button.classList.remove('bg-amber-600', 'hover:bg-amber-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            // Convert to unsubscribe button
            button.classList.remove('subscribe-button');
            button.classList.add('unsubscribe-button');
            button.setAttribute('data-subscription-id', data.subscription_id);
            
            // Show success message
            showNotification('Successfully subscribed to artist!', 'success');
            
            // Refresh artwork prices if they're on the page
            refreshArtworkPrices(artistId);
        } else {
            // Show error and revert button
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification(data.message || 'Failed to subscribe. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification('An error occurred. Please try again.', 'error');
    });
}

/**
 * Unsubscribe from an artist's plan
 * @param {number} subscriptionId - The subscription ID
 */
function unsubscribeFromArtist(subscriptionId) {
    // Confirm before unsubscribing
    if (!confirm('Are you sure you want to cancel this subscription?')) {
        return;
    }

    // Get button and show loading state
    const button = document.querySelector(`[data-subscription-id="${subscriptionId}"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner"></span> Processing...';
    button.disabled = true;

    // AJAX request
    fetch('api/unsubscribe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subscription_id: subscriptionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to show unsubscribed state
            const artistId = button.getAttribute('data-artist-id');
            const planId = button.getAttribute('data-plan-id');
            
            button.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Subscribe';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-amber-600', 'hover:bg-amber-700');
            
            // Convert back to subscribe button
            button.classList.remove('unsubscribe-button');
            button.classList.add('subscribe-button');
            button.removeAttribute('data-subscription-id');
            
            // Show success message
            showNotification('Successfully unsubscribed from artist.', 'success');
            
            // Refresh artwork prices if they're on the page
            refreshArtworkPrices(artistId);
        } else {
            // Show error and revert button
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification(data.message || 'Failed to unsubscribe. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification('An error occurred. Please try again.', 'error');
    });
}

/**
 * Refresh artwork prices after subscription status changes
 * @param {number} artistId - The artist's ID
 */
function refreshArtworkPrices(artistId) {
    // Only refresh if we're on a page with artwork listings
    const artworkElements = document.querySelectorAll(`.artwork-item[data-artist-id="${artistId}"]`);
    if (artworkElements.length === 0) return;

    fetch(`api/get-artwork-prices.php?artist_id=${artistId}`, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update each artwork's price display
            data.artworks.forEach(artwork => {
                const priceElement = document.querySelector(`.artwork-price[data-artwork-id="${artwork.artwork_id}"]`);
                if (priceElement) {
                    if (artwork.discounted_price < artwork.original_price) {
                        priceElement.innerHTML = `
                            <span class="line-through text-gray-500">$${artwork.original_price}</span>
                            <span class="font-bold text-green-600">$${artwork.discounted_price}</span>
                        `;
                    } else {
                        priceElement.innerHTML = `
                            <span class="font-bold">$${artwork.original_price}</span>
                        `;
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Error refreshing prices:', error);
    });
}

/**
 * Show notification to the user
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error)
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type} fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50`;
    
    // Style based on type
    if (type === 'success') {
        notification.classList.add('bg-green-100', 'border-green-500', 'text-green-700');
    } else if (type === 'error') {
        notification.classList.add('bg-red-100', 'border-red-500', 'text-red-700');
    } else {
        notification.classList.add('bg-blue-100', 'border-blue-500', 'text-blue-700');
    }
    
    notification.innerHTML = message;
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

    if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark-mode');
            document.getElementById('light-icon').classList.add('hidden');
            document.getElementById('dark-icon').classList.remove('hidden');
        }

  function toggleDarkMode() {
    const html = document.documentElement;
    html.classList.toggle('dark-mode');
    
    const isDark = html.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    document.getElementById('light-icon').classList.toggle('hidden');
    document.getElementById('dark-icon').classList.toggle('hidden');
  }
    </script>
</body>
</html>