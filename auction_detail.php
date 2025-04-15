<?php
require_once 'includes/db.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Validate auction ID
$auction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($auction_id <= 0) {
    header("Location: gallery.php");
    exit;
}

if (!isset($db)) {
    die("Database connection not available");
}
$pdo = $db->getConnection();

// Get auction details with artwork and artist info
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        aw.title, 
        aw.description, 
        aw.image_url,
        aw.artist_id, 
        aw.created_at,
        CONCAT(u.firstname, ' ', u.lastname) AS artist_name,
        u.firstname, 
        u.lastname,
        u.email AS artist_email,
        ua.bio AS artist_bio,
        (SELECT COUNT(*) FROM bids WHERE auction_id = a.auction_id AND archived = 0) AS bid_count,
        (SELECT amount FROM bids WHERE auction_id = a.auction_id AND archived = 0 ORDER BY amount DESC LIMIT 1) AS highest_bid
    FROM auctions a
    JOIN artworks aw ON a.artwork_id = aw.artwork_id
    JOIN users u ON aw.artist_id = u.user_id
        JOIN artists ua ON ua.artist_id = u.user_id
    WHERE a.auction_id = ? AND a.archived = 0
");
$stmt->execute([$auction_id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if auction exists and is not archived
if (!$auction) {
    header("Location: gallery.php?error=auction_not_found");
    exit;
}

// Get current highest bid (without exposing user_id)
$bidStmt = $pdo->prepare("
    SELECT b.amount, b.bid_time, 
           CONCAT(LEFT(u.firstname, 1), LEFT(u.lastname, 1)) AS initials,
           CONCAT(u.firstname, ' ', u.lastname) AS bidder_name
    FROM bids b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.auction_id = ? AND b.is_winning = 1 AND b.archived = 0
    ORDER BY b.amount DESC
    LIMIT 1
");
$bidStmt->execute([$auction_id]);
$current_bid = $bidStmt->fetch(PDO::FETCH_ASSOC);

// Get bid history (without exposing user_id)
$historyStmt = $pdo->prepare("
    SELECT b.amount, b.bid_time,
           CONCAT(LEFT(u.firstname, 1), LEFT(u.lastname, 1)) AS initials,
           CONCAT(u.firstname, ' ', u.lastname) AS bidder_name
    FROM bids b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.auction_id = ? AND b.archived = 0
    ORDER BY b.amount DESC
    LIMIT 10
");
$historyStmt->execute([$auction_id]);
$bid_history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate time remaining
$end_time = new DateTime($auction['end_time']);
$now = new DateTime();
$time_remaining = $now->diff($end_time);
$is_ended = $now > $end_time;

// Calculate next minimum bid
$current_price = $current_bid ? $current_bid['amount'] : $auction['starting_price'];
$next_min_bid = $current_price + ($current_price < 100 ? 10 : ($current_price < 1000 ? 50 : 100));
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <?php require_once 'includes/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Theme toggle floating button -->
        <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>

        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 text-sm">
                <li><a href="gallery.php" class="text-gray-600 hover:text-primary dark:text-gray-300">Gallery</a></li>
                <li class="text-gray-500 dark:text-gray-400">/</li>
                <li class="text-primary font-medium"><?= htmlspecialchars($auction['title']) ?></li>
            </ol>
        </nav>
        
        <!-- Auction header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white font-serif mb-2"><?= htmlspecialchars($auction['title']) ?></h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">by <?= htmlspecialchars($auction['artist_name']) ?></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Artwork Image and Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Artwork Image -->
                <div class="artwork-container">
                    <?php if (!$is_ended): ?>
                    <div class="auction-badge">
                        <i class="fas fa-gavel mr-1"></i> LIVE AUCTION
                    </div>
                    <?php endif; ?>
                    <img src="<?= SITE_URL ?>/images/<?= htmlspecialchars($auction['image_url']) ?>" 
                         alt="<?= htmlspecialchars($auction['title']) ?>" 
                         class="artwork-image">
                </div>
                
                 <!-- Auction Status -->
                 <?php if ($is_ended): ?>
                    <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 p-4 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> This auction has ended
                    </div>
                <?php endif; ?>
                
                <!-- Bid Panel -->
                <div class="bid-panel dark:bg-gray-800">
                    <div class="bid-stats">
                        <div class="bid-stat dark:bg-gray-700">
                            <div class="bid-stat-label">Current Bid</div>
                            <div class="bid-stat-value">$<?= number_format($current_bid ? $current_bid['amount'] : $auction['starting_price'], 2) ?></div>
                        </div>
                        
                        <div class="bid-stat dark:bg-gray-700">
                            <div class="bid-stat-label">Starting Price</div>
                            <div class="bid-stat-value">$<?= number_format($auction['starting_price'], 2) ?></div>
                        </div>
                        
                        <div class="bid-stat dark:bg-gray-700">
                            <div class="bid-stat-label">Bids</div>
                            <div class="bid-stat-value"><?= $auction['bid_count'] ?></div>
                        </div>
                        
                        <div class="bid-stat dark:bg-gray-700">
                            <div class="bid-stat-label">Time Left</div>
                            <div class="bid-stat-value countdown" id="time-remaining">
                                <?php 
                                if ($is_ended) {
                                    echo 'Ended';
                                } else {
                                    echo $time_remaining->d > 0 ? $time_remaining->d . 'd ' : '';
                                    echo $time_remaining->h . 'h ' . $time_remaining->i . 'm';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($auction['reserve_price']): ?>
                    <div class="mb-6 text-center">
                        <span class="reserve-badge <?= ($current_bid && $current_bid['amount'] >= $auction['reserve_price']) ? 'reserve-met' : 'reserve-not-met' ?>">
                            Reserve: $<?= number_format($auction['reserve_price'], 2) ?>
                            <i class="fas <?= ($current_bid && $current_bid['amount'] >= $auction['reserve_price']) ? 'fa-check' : 'fa-times' ?> ml-1"></i>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Bid Form -->
                    <?php if (!$is_ended): ?>
                        <?php if (isLoggedIn() && $_SESSION['role'] === 'buyer'): ?>
                        <form id="bid-form" class="mb-6">
                            <input type="hidden" name="auction_id" value="<?= $auction_id ?>">
                            
                            <label for="bid-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Bid ($)
                            </label>
                            <input type="number" id="bid-amount" name="amount" 
                                   min="<?= $next_min_bid ?>" 
                                   step="0.01"
                                   value="<?= $next_min_bid ?>"
                                   required
                                   class="dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Minimum bid: $<?= number_format($next_min_bid, 2) ?>
                            </p>
                            
                            <button type="submit" class="btn-bid">
                                <i class="fas fa-gavel mr-2"></i> Place Bid
                            </button>
                        </form>
                        <?php elseif (!isLoggedIn()): ?>
                            <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100 p-4 rounded-lg">
                                <i class="fas fa-exclamation-circle mr-2"></i> 
                                You must <a href="login.php" class="text-primary font-semibold hover:underline">log in</a> as a buyer to place bids.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <!-- Current Highest Bidder -->
                    <?php if ($current_bid): ?>
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                        <h3 class="font-bold text-blue-800 dark:text-blue-100 mb-2">Current Highest Bidder</h3>
                        <div class="flex items-center">
                            <div class="bidder-avatar">
                                <?= $current_bid['initials'] ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200"><?= htmlspecialchars($current_bid['bidder_name']) ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('M j, g:i A', strtotime($current_bid['bid_time'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column - Bidding Panel -->
            <div class="space-y-6">
                  <!-- Artwork Details -->
                  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-bold mb-6 font-serif text-gray-900 dark:text-white">
                        <i class="fas fa-info-circle text-primary mr-2"></i> Artwork Details
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-label">Title</span>
                            <span class="detail-value"><?= htmlspecialchars($auction['title']) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Artist</span>
                            <span class="detail-value"><?= htmlspecialchars($auction['artist_name']) ?></span>
                        </div>
                        
                        <?php if ($auction['created_at']): ?>
                        <div class="detail-item">
                            <span class="detail-label">Year Created</span>
                            <span class="detail-value"><?= htmlspecialchars($auction['created_at']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="detail-item">
                            <span class="detail-label">Description</span>
                            <span class="detail-value"><?= htmlspecialchars($auction['description']) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- About the Artist -->
                <div class="artist-card dark:bg-gray-800">
                    <div class="artist-avatar">
                        <?= substr($auction['artist_name'], 0, 1) . substr($auction['artist_name'], strpos($auction['artist_name'], ' ') + 1, 1) ?>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-2 font-serif text-gray-900 dark:text-white">About the Artist</h3>
                        <h4 class="text-xl mb-4 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($auction['artist_name']) ?></h4>
                        <p class="text-gray-600 dark:text-gray-300"><?= htmlspecialchars($auction['artist_bio'] ?: 'No biography available') ?></p>
                        <?php if ($auction['artist_email']): ?>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($auction['artist_email']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bid-panel dark:bg-gray-800">
                    <h2 class="text-2xl font-bold mb-6 font-serif text-gray-900 dark:text-white">
                        <i class="fas fa-history text-primary mr-2"></i> Bid History
                    </h2>
                    
                    <?php if (count($bid_history) > 0): ?>
                        <div class="space-y-4">
                            <?php foreach ($bid_history as $bid): ?>
                            <div class="bid-history-item">
                                <div class="flex items-center">
                                    <div class="bidder-avatar">
                                        <?= $bid['initials'] ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-200"><?= htmlspecialchars($bid['bidder_name']) ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= date('M j, g:i A', strtotime($bid['bid_time'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="bid-amount">$<?= number_format($bid['amount'], 2) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-comment-slash text-4xl mb-4"></i>
                            <p>No bids have been placed yet</p>
                            <?php if (!$is_ended): ?>
                            <p class="text-sm mt-1">Be the first to place a bid!</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Dark/Light mode toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const html = document.documentElement;
    
    // Check for saved user preference or use system preference
    const savedTheme = localStorage.getItem('theme') || 
                      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    // Apply the saved theme
    if (savedTheme === 'dark') {
        html.classList.add('dark');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    } else {
        html.classList.remove('dark');
        themeIcon.classList.replace('fa-sun', 'fa-moon');
    }
    
    // Toggle theme on button click
    themeToggle.addEventListener('click', () => {
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
        }
    });
    
    // Update time remaining every second
    function updateTimeRemaining() {
        const endTime = new Date("<?= $auction['end_time'] ?>");
        const now = new Date();
        const diff = endTime - now;
        
        if (diff <= 0) {
            $('#time-remaining').text('Auction ended');
            return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        let timeStr = '';
        if (days > 0) timeStr += days + 'd ';
        timeStr += hours.toString().padStart(2, '0') + 'h ' + 
                   minutes.toString().padStart(2, '0') + 'm ' + 
                   seconds.toString().padStart(2, '0') + 's';
        
        $('#time-remaining').text(timeStr);
    }

    setInterval(updateTimeRemaining, 1000);
    updateTimeRemaining();

    // Handle bid form submission
    $('#bid-form').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            auction_id: $('input[name="auction_id"]').val(),
            amount: parseFloat($('#bid-amount').val())
        };
        
        // Client-side validation
        if (formData.amount < <?= $next_min_bid ?>) {
            showNotification(`Bid must be at least $<?= number_format($next_min_bid, 2) ?>`, 'error');
            return;
        }
        
        $.ajax({
            url: 'api/place_bid.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            beforeSend: function() {
                $('.btn-bid').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification('Bid placed successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + response.message, 'error');
                    $('.btn-bid').prop('disabled', false).html('<i class="fas fa-gavel mr-2"></i> Place Bid');
                }
            },
            error: function(xhr) {
                let message = 'Error communicating with server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification(message, 'error');
                $('.btn-bid').prop('disabled', false).html('<i class="fas fa-gavel mr-2"></i> Place Bid');
            }
        });
    });

    function showNotification(message, type) {
        const container = $('#notification-container');
        const notification = $(`
            <div class="fixed top-4 right-4 z-50 w-80 transform transition-all duration-300 translate-x-64 opacity-0">
                <div class="p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'}">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    ${message}
                </div>
            </div>
        `);
        
        container.append(notification);
        setTimeout(() => {
            notification.removeClass('translate-x-64 opacity-0').addClass('translate-x-0 opacity-100');
        }, 10);
        
        setTimeout(() => {
            notification.removeClass('translate-x-0 opacity-100').addClass('translate-x-64 opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Auto-focus bid amount field when page loads
    $(document).ready(function() {
        $('#bid-amount').focus();
    });
    </script>

    <div id="notification-container"></div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>