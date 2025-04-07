<?php
require_once 'includes/config.php';

// After successful login in your login.php:
if (isset($_SESSION['user_id'])) {
    echo '<script>checkPendingWishlistItem();</script>';
    
    // Or if you're redirecting back:
    if (isset($_GET['redirect'])) {
        header("Location: " . urldecode($_GET['redirect']));
        exit();
    }
}

// First get subscription status (from your previous code)
$isSubscribed = false;
$subscriptionId = null;
$artistId = 1; // The artist ID you're checking against
$planId = 1; // The default subscription plan ID

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT us.subscription_id 
            FROM user_subscriptions us
            JOIN artist_subscription_settings ass ON us.plan_id = ass.setting_id
            WHERE us.user_id = :user_id 
            AND ass.artist_id = :artist_id
            AND us.status = 'active'
            AND us.end_date > NOW()
        ");
        $stmt->execute([':user_id' => $userId, ':artist_id' => $artistId]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($subscription) {
            $isSubscribed = true;
            $subscriptionId = $subscription['subscription_id'];
        }
    } catch (PDOException $e) {
        error_log("Subscription check error: " . $e->getMessage());
    }
}

?>
      
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>

<body class="bg-gray-50 dark:bg-gray-900">
    <?php require_once 'includes/navbar.php'; ?>

    <!-- Artist Hero Section -->
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Artist Portrait -->
                <div class="w-full md:w-1/3 lg:w-1/4">
                    <div class="aspect-square rounded-full overflow-hidden border-4 border-amber-500 shadow-xl">
                        <img src="images/artist-van-gogh.jpg" alt="Vincent Van Gogh" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <!-- Artist Info -->
                <div class="w-full md:w-2/3">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Vincent Van Gogh</h1>
                    <p class="text-xl text-gray-300 mb-6">Post-Impressionist Master (1853-1890)</p>
                    
                    <div class="flex flex-wrap gap-4 mb-8">
                 
<button 
    class="<?= $isSubscribed ? 'unsubscribe-button bg-green-600 hover:bg-green-700' : 'subscribe-button bg-amber-600 hover:bg-amber-700' ?> 
           px-6 py-3 text-white rounded-lg transition duration-300 flex items-center"
    data-artist-id="<?= $artistId ?>" 
    <?= $isSubscribed ? 'data-subscription-id="'.htmlspecialchars($subscriptionId).'"' : 'data-plan-id="'.htmlspecialchars($planId).'"' ?>
    onclick="<?= $isSubscribed ? 'unsubscribeFromArtist('.htmlspecialchars($subscriptionId).')' : 'subscribeToArtist('.htmlspecialchars($artistId).', '.htmlspecialchars($planId).')' ?>">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <?php if ($isSubscribed): ?>
        <!-- Checkmark icon for subscribed state -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        <?php else: ?>
        <!-- Plus icon for unsubscribed state -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        <?php endif; ?>
    </svg>
    <?= $isSubscribed ? 'Subscribed' : 'Subscribe' ?>
</button>
                        <button class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition duration-300 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            Contact Curator
                        </button>
                    </div>
                    
                    <div class="flex flex-wrap gap-6 text-gray-300">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                            <span>Dutch</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>1853-1890</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Netherlands/France</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artist Content Tabs -->
    <section class="sticky top-0 z-10 bg-white dark:bg-gray-800 shadow-sm">
        <div class="container mx-auto px-6">
            <div class="flex overflow-x-auto">
                <button class="tab-button active px-6 py-4 font-medium text-amber-600 border-b-2 border-amber-600">
                    Gallery
                </button>
                <button class="tab-button px-6 py-4 font-medium text-gray-600 dark:text-gray-400 hover:text-amber-500">
                    Biography
                </button>
                <button class="tab-button px-6 py-4 font-medium text-gray-600 dark:text-gray-400 hover:text-amber-500">
                    Exhibitions
                </button>
                <!-- <button class="tab-button px-6 py-4 font-medium text-gray-600 dark:text-gray-400 hover:text-amber-500">
                    Related Artists
                </button> -->
            </div>
        </div>
    </section>

    <!-- Artist Gallery Section -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Gallery Filters -->
            <div class="flex flex-wrap justify-between items-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Featured Works</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600 dark:text-gray-400">Filter by:</span>
                    <select class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        <option>All Periods</option>
                        <option>Early Works (1881-1885)</option>
                        <option>Paris Period (1886-1888)</option>
                        <option>Arles Period (1888-1889)</option>
                        <option>Saint-Rémy & Auvers (1889-1890)</option>
                    </select>
                </div>
            </div>

            <!-- Artwork Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Artwork 1 -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/art2.webp" alt="Starry Night" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">The Starry Night (1889)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 73.7 × 92.1 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">MOMA, New York</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                        </div>
                    </div>
                </div>

                <!-- Artwork 2 -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/sunflower.jpg" alt="Sunflowers" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sunflowers (1888)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 92.1 × 73 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Van Gogh Museum</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                        </div>
                    </div>
                </div>

                <!-- Artwork 3 -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/cafe-terrace.jpg" alt="Café Terrace at Night" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Café Terrace at Night (1888)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 80.7 × 65.3 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Kröller-Müller Museum</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                    <!-- <button 
        class="ar-preview px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg flex items-center"
        data-model="models/sunflowers.usdz"
        data-ios-src="models/sunflowers.usdz"
        data-android-src="models/sunflowers.glb">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10"></path>
        </svg>
        AR View
    </button> -->
</div>

<!-- Model Viewer Library (add to head.php) -->
<!-- <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

<script>
    // AR Preview Handler
    document.querySelectorAll('.ar-preview').forEach(button => {
        button.addEventListener('click', () => {
            if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                // iOS - Quick Look
                window.location.href = button.dataset.iosSrc;
            } else {
                // Android/Desktop - WebAR
                const modelViewer = document.createElement('model-viewer');
                modelViewer.setAttribute('src', button.dataset.androidSrc);
                modelViewer.setAttribute('ar', 'true');
                modelViewer.setAttribute('camera-controls', 'true');
                modelViewer.style.width = '100%';
                modelViewer.style.height = '500px';
                
                // Show in modal
                // (Implement your modal system here)
            }
        });
    });
</script>  -->
</div>
                    </div>
                </div>

                <!-- Artwork 4 -->
                <!-- <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/almond-blossom.jpg" alt="Almond Blossoms" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Almond Blossoms (1890)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 73.5 × 92 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Van Gogh Museum</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                        </div>
                    </div>
                </div> -->

                <!-- Artwork 5 -->
                <!-- <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/self-portrait.jpg" alt="Self-Portrait" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Self-Portrait (1889)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 65 × 54 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Musée d'Orsay</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                        </div>
                    </div>
                </div> -->

                <!-- Artwork 6 -->
                <!-- <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="images/wheatfield.jpg" alt="Wheatfield with Crows" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Wheatfield with Crows (1890)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Oil on canvas, 50.5 × 103 cm</p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Van Gogh Museum</span>
                            <button class="text-amber-600 hover:text-amber-700 font-medium">View Details</button>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Previous
                    </button>
                    <button class="px-4 py-2 bg-amber-600 text-white rounded-lg">1</button>
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        2
                    </button>
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        3
                    </button>
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Next
                    </button>
                </nav>
            </div>
        </div>
    </section>

    <!-- Current Exhibitions Section -->
    <!-- <section class="py-16 px-4 bg-gray-100 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Current Exhibitions Featuring Van Gogh</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden">
                    <div class="aspect-[16/9] overflow-hidden">
                        <img src="images/exhibition-1.jpg" alt="Exhibition" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Van Gogh: The Immersive Experience</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">A 360° digital art exhibition showcasing Van Gogh's masterpieces in a revolutionary light.</p>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Museum of Modern Art, New York</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Through October 15, 2023</p>
                            </div>
                            <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden">
                    <div class="aspect-[16/9] overflow-hidden">
                        <img src="images/exhibition-2.jpg" alt="Exhibition" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Van Gogh and the Olive Groves</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Exploring Van Gogh's fascination with olive trees during his time in Provence.</p>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dallas Museum of Art</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Through December 10, 2023</p>
                            </div>
                            <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    
<!-- Artist subscription plans display -->
<?php
require_once '../includes/config.php';

$artistId = $_GET['artist_id'] ?? 0; // Get artist ID from URL parameter
$subscriptionPlans = [];

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all active subscription plans for this artist
    $stmt = $db->prepare("
        SELECT 
            s.setting_id AS plan_id,
            t.name AS plan_name,
            t.description AS plan_description,
            t.price,
            t.duration_type,
            t.discount_percentage,
            s.custom_description
        FROM artist_subscription_settings s
        JOIN subscription_tiers t ON s.tier_id = t.tier_id
        WHERE s.artist_id = :artist_id
        AND s.is_enabled = 1
        AND t.is_active = 1
        ORDER BY t.price ASC
    ");
    $stmt->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
    $stmt->execute();
    $subscriptionPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<div class="subscription-plans-container p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Support this Artist</h2>
    <p class="text-gray-600 mb-6">Subscribe to get exclusive benefits and discounts on all artwork by this artist.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (empty($subscriptionPlans)): ?>
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No subscription plans available at this time.</p>
            </div>
        <?php else: ?>
            <?php foreach ($subscriptionPlans as $plan): 
                // Determine plan type for styling
                $isPopular = ($plan['duration_type'] === 'yearly');
                $planDescription = $plan['custom_description'] ?? $plan['plan_description'];
                $discount = $plan['discount_percentage'];
            ?>
                <div class="subscription-plan border rounded-lg p-6 hover:shadow-md transition duration-300 <?= $isPopular ? 'border-amber-300 bg-amber-50' : '' ?>">
                    <?php if ($isPopular): ?>
                        <div class="absolute top-0 right-0 bg-amber-500 text-white py-1 px-3 rounded-bl-lg rounded-tr-lg text-xs font-bold">BEST VALUE</div>
                    <?php endif; ?>
                    
                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($plan['plan_name']) ?></h3>
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($planDescription) ?></p>
                    
                    <div class="flex items-baseline mb-4">
                        <span class="text-3xl font-bold">$<?= number_format($plan['price'], 2) ?></span>
                        <span class="text-gray-500 ml-1">
                            /<?= htmlspecialchars($plan['duration_type'] === 'lifetime' ? 'lifetime' : $plan['duration_type']) ?>
                        </span>
                    </div>
                    
                    <ul class="mb-6 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <?= $discount ?>% off all purchases
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Early access to new works
                        </li>
                        <?php if ($plan['duration_type'] === 'yearly' || $plan['duration_type'] === 'lifetime'): ?>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <?= $plan['duration_type'] === 'yearly' ? 'Quarterly' : 'Exclusive' ?> digital art
                            </li>
                        <?php endif; ?>
                        <?php if ($plan['duration_type'] === 'lifetime'): ?>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Special thank-you in artist bio
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <button 
                        class="subscribe-button w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300 flex items-center justify-center" 
                        data-artist-id="<?= $artistId ?>" 
                        data-plan-id="<?= $plan['plan_id'] ?>"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Subscribe
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>



<!-- Artwork display with subscription-aware pricing -->
<!-- <div class="artwork-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
    <div class="artwork-item rounded-lg overflow-hidden shadow-lg bg-white" data-artist-id="123" data-artwork-id="789">
        <img src="/api/placeholder/400/300" alt="Artwork" class="w-full h-64 object-cover">
        <div class="p-4">
            <h3 class="text-xl font-semibold mb-2">Summer Breeze</h3>
            <p class="text-gray-600 mb-4">Oil on canvas, 24" x 36"</p>
            <div class="flex justify-between items-center">
                <div class="artwork-price" data-artwork-id="789">
                    <span class="font-bold">$350.00</span>
                </div>
                <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded transition duration-300">
                    Add to Cart
                </button>
            </div>
        </div>
    </div> -->
    
    <!-- Artwork Item with discounted pricing -->
    <!-- <div class="artwork-item rounded-lg overflow-hidden shadow-lg bg-white" data-artist-id="123" data-artwork-id="790">
        <img src="/api/placeholder/400/300" alt="Artwork" class="w-full h-64 object-cover">
        <div class="p-4">
            <h3 class="text-xl font-semibold mb-2">Ocean Waves</h3>
            <p class="text-gray-600 mb-4">Acrylic on canvas, 18" x 24"</p>
            <div class="flex justify-between items-center">
                <div class="artwork-price" data-artwork-id="790">
                    <span class="line-through text-gray-500">$250.00</span>
                    <span class="font-bold text-green-600">$212.50</span>
                </div>
                <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded transition duration-300">
                    Add to Cart
                </button>
            </div>
            <div class="mt-2">
                <span class="text-xs font-semibold text-green-600">15% subscriber discount applied</span>
            </div>
        </div>
    </div> -->
<!-- </div> -->


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


// Tab switching functionality
        const tabs = document.querySelectorAll('.tab-button');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active', 'text-amber-600', 'border-amber-600'));
                tabs.forEach(t => t.classList.add('text-gray-600', 'dark:text-gray-400'));
                tab.classList.add('active', 'text-amber-600', 'border-amber-600');
                tab.classList.remove('text-gray-600', 'dark:text-gray-400');
                // Here you would add content switching logic
            });
        });
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