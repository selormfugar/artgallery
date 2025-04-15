<?php
require_once 'includes/config.php';
require_once 'includes/auth_check.php';

// After successful login in your login.php:
if (isset($_SESSION['user_id'])) {
    // echo '<script>checkPendingWishlistItem();</script>';
    
    // Or if you're redirecting back:
    if (isset($_GET['redirect'])) {
        header("Location: " . urldecode($_GET['redirect']));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">

    <!-- Navigation Menu -->
    <?php require_once 'includes/navbar.php'; ?>
 <!-- Theme toggle floating button -->
 <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>

    <!-- Gallery Section -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <!-- Filter Controls -->
        <div class="mb-8 p-6 rounded-lg shadow-md" style="background: rgba(184, 137, 92, 0.1);">
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <!-- Category Filter -->
        <div class="relative flex-grow md:flex-grow-0 md:w-48">
            <select id="category-filter" class="w-full px-4 py-2 pr-8 rounded-md bg-white bg-opacity-90 border border-[#b8895c] text-gray-800 hover:border-[#9a6b42] focus:border-[#9a6b42] focus:ring-1 focus:ring-[#9a6b42] transition-all duration-200 cursor-pointer appearance-none">
                <option value="">All Categories</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM categories WHERE archived = 0");
                while ($category = $stmt->fetch()) {
                    echo "<option value='{$category['category_id']}'>{$category['name']}</option>";
                }
                ?>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <i class="fas fa-chevron-down text-[#b8895c]"></i>
            </div>
        </div>

        <!-- Price Filter -->
        <div class="relative flex-grow md:flex-grow-0 md:w-48">
            <select id="price-filter" class="w-full px-4 py-2 pr-8 rounded-md bg-white bg-opacity-90 border border-[#b8895c] text-gray-800 hover:border-[#9a6b42] focus:border-[#9a6b42] focus:ring-1 focus:ring-[#9a6b42] transition-all duration-200 cursor-pointer appearance-none">
                <option value="">Any Price</option>
                <option value="0-500">Under $500</option>
                <option value="500-1000">$500 - $1000</option>
                <option value="1000-5000">$1000 - $5000</option>
                <option value="5000-">Over $5000</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <i class="fas fa-chevron-down text-[#b8895c]"></i>
            </div>
        </div>

        <!-- Sort Filter -->
        <div class="relative flex-grow md:flex-grow-0 md:w-48">
            <select id="sort-filter" class="w-full px-4 py-2 pr-8 rounded-md bg-white bg-opacity-90 border border-[#b8895c] text-gray-800 hover:border-[#9a6b42] focus:border-[#9a6b42] focus:ring-1 focus:ring-[#9a6b42] transition-all duration-200 cursor-pointer appearance-none">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="popular">Most Popular</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <i class="fas fa-chevron-down text-[#b8895c]"></i>
            </div>
        </div>

        <!-- Search Input -->
        <div class="relative flex-grow">
            <input type="text" id="search-input" placeholder="Search artworks..." 
                class="w-full px-4 py-2 pl-10 rounded-md bg-white bg-opacity-90 border border-[#b8895c] text-gray-800 hover:border-[#9a6b42] focus:border-[#9a6b42] focus:ring-1 focus:ring-[#9a6b42] transition-all duration-200 placeholder-gray-500">
            <i class="fas fa-search absolute left-3 top-3 text-[#b8895c]"></i>
        </div>

        <!-- Reset Button -->
        <button id="reset-filters" class="px-4 py-2 rounded-md bg-transparent border border-[#b8895c] text-[#b8895c] hover:bg-[#b8895c] hover:text-white transition-all duration-200 whitespace-nowrap">
            <i class="fas fa-sync-alt mr-2"></i> Reset
        </button>
    </div>

    <!-- Active Filters Display -->
    <div id="active-filters" class="mt-4 flex flex-wrap gap-2 hidden">
        <span class="text-sm text-gray-700 mr-2">Active filters:</span>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="mt-4 text-center hidden">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#b8895c]"></div>
        <span class="ml-2 text-[#b8895c]">Filtering artworks...</span>
    </div>
</div>

        <!-- Artworks Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8" id="gallery-grid">
            <!-- Artworks will be loaded here via AJAX -->
        </div>
        <div class="pagination mt-8 flex justify-center items-center gap-2">
            <?php
            // Get total number of artworks
            $total_count = $pdo->query("SELECT COUNT(*) FROM artworks WHERE archived = 0")->fetchColumn();
            $items_per_page = 12;
            $total_pages = ceil($total_count / $items_per_page);
            
            // Generate pagination buttons
            for ($i = 1; $i <= $total_pages; $i++) {
            echo "<button class='pagination-btn" . ($i === 1 ? " active" : "") . "' data-page='$i'>$i</button>";
            }
            ?>
        </div>
        <style>
            .gallery-item {
            position: relative;
            aspect-ratio: 1 / 1.2;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
            }

            .gallery-item:hover img {
            transform: scale(1.05);
            }

            .gallery-item .overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 1rem;
            transition: bottom 0.3s ease;
            }

            .gallery-item:hover .overlay {
            bottom: 0;
            }

            /* Pagination styles */
            .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            }

            .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            }

            .pagination-btn:hover {
            background: #f3f4f6;
            }

            .pagination-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            }


            @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.animate-pulse {
    animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.transition-all {
    transition-property: all;
}

.duration-300 {
    transition-duration: 300ms;
}

.ease-in-out {
    transition-timing-function: ease-in-out;
}
        </style>
    
        <!-- Notification container (place this in your layout file) -->
<div id="notification-container" class="fixed bottom-4 right-4 z-50 w-80 max-w-full"></div>

<!-- Cart count element (example) -->
<!-- <a href="/cart" class="relative">
    <span>Cart</span>
    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
</a> -->
        <!-- Load More Button -->
        <div class="text-center mt-8">
            <button id="load-more" class="bg-gray-800 text-white px-6 py-3 rounded hover:bg-gray-700 transition">
                Load More Artworks
            </button>
        </div>
    </section>

    <!-- After jQuery but before your other scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="<?php echo SITE_URL; ?>/assets/js/wishlist.js"></script>
        <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
        <script>
            $(document).ready(function() {
                // Variables for pagination and loading
                let currentPage = 1;
                let isLoading = false;
                let hasMore = true;
                
                // Filter state object
                let filters = {
                    category: '',
                    price: '',
                    sort: 'newest',
                    search: ''
                };
                
                // Initialize
                loadArtworks();
                updateActiveFilters();
                
                // Debounce function
                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this, args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            func.apply(context, args);
                        }, wait);
                    };
                }
                
                // Load artworks function
                function loadArtworks(reset = false) {
                    if (isLoading || !hasMore) return;
                    
                    isLoading = true;
                    $('#loading').removeClass('hidden');
                    $('#loading-indicator').removeClass('hidden');
                    
                    if (reset) {
                        currentPage = 1;
                        $('#gallery-grid').empty();
                        hasMore = true;
                    }
                    
                    // Update filters from UI
                    filters = {
                        category: $('#category-filter').val(),
                        price: $('#price-filter').val(),
                        sort: $('#sort-filter').val(),
                        search: $('#search-input').val().trim()
                    };
                    
                    // Add pagination parameter
                    const requestData = {
                        ...filters,
                        page: currentPage
                    };
                    
                    $.ajax({
                        url: 'api/get_artworks.php',
                        type: 'GET',
                        data: requestData,
                        dataType: 'json',
                        success: function(data) {
                            if (data.artworks.length === 0) {
                                if (currentPage === 1) {
                                    $('#gallery-grid').html(
                                        '<div class="col-span-full text-center py-8">No artworks found matching your criteria.</div>'
                                    );
                                }
                                hasMore = false;
                                $('#load-more').hide();
                            } else {
                                displayArtworks(data.artworks);
                                currentPage++;
                                if (!data.has_more) {
                                    hasMore = false;
                                    $('#load-more').hide();
                                } else {
                                    $('#load-more').show();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error loading artworks:", error);
                            $('#gallery-grid').html(
                                '<div class="col-span-full text-center py-8 text-red-500">Error loading artworks. Please try again.</div>'
                            );
                        },
                        complete: function() {
                            isLoading = false;
                            $('#loading').addClass('hidden');
                            $('#loading-indicator').addClass('hidden');
                        }
                    });
                }

                // add to cart function
                function addToCart(artworkId) {
                    $.ajax({
                        url: 'api/purchase_artwork.php', // Fixed typo in URL (was 'purchare_artwork.php')
                        type: 'POST',
                        data: { artwork_id: artworkId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showNotification({
                                    type: 'success',
                                    title: 'Purchase Successful',
                                    message: response.message,
                                    autoClose: true,
                                    duration: 5000
                                });
                                
                                // Optional: Update cart count in UI
                                updateCartCount();
                                
                            } else {
                                showNotification({
                                    type: 'error',
                                    title: 'Purchase Failed',
                                    message: response.message || 'Failed to complete purchase',
                                    autoClose: true,
                                    duration: 5000
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            showNotification({
                                type: 'error',
                                title: 'Server Error',
                                message: 'Error communicating with server. Please try again.',
                                autoClose: true,
                                duration: 5000
                            });
                            console.error('AJAX Error:', status, error);
                        }
                    });
                }

                // Enhanced notification system with animations and stacking
                function showNotification({ type = 'info', title, message, autoClose = true, duration = 5000 }) {
                    const container = document.getElementById('notification-container');
                    if (!container) {
                        console.error('Notification container not found');
                        return;
                    }

                    const notification = document.createElement('div');
                    
                    // Base classes
                    let classes = 'p-4 rounded-lg shadow-lg border-l-4 mb-2 transform transition-all duration-300 ease-in-out ';
                    
                    // Start hidden and slide in
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    
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
                            <button onclick="this.parentElement.parentElement.remove()" 
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                &times;
                            </button>
                        </div>
                    `;
                    
                    container.appendChild(notification);
                    
                    // Trigger animation
                    setTimeout(() => {
                        notification.style.opacity = '1';
                        notification.style.transform = 'translateX(0)';
                    }, 10);
                    
                    if (autoClose) {
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateX(100%)';
                            setTimeout(() => notification.remove(), 300);
                        }, duration);
                    }
                }

                // Display artworks in grid
                function displayArtworks(artworks) {
                    artworks.forEach(artwork => {
                        // Fix image path - use the full URL from your config
                        const imagePath = '<?php echo SITE_URL; ?>/images/' + artwork.image_url;
                        
                        const artworkHtml = `
                            <div class="gallery-item">
                                <img src="${imagePath}" alt="${artwork.title.replace(/"/g, '&quot;')}" onerror="this.src='<?php echo SITE_URL; ?>/images/art10.jpg'">
                                <div class="overlay">
                                    <h3 class="text-xl font-bold mb-2">${artwork.title.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</h3>
                                    <p class="mb-2">${artwork.description ? artwork.description.substring(0, 100).replace(/</g, '&lt;').replace(/>/g, '&gt;') + (artwork.description.length > 100 ? '...' : '') : 'No description available'}</p>
                                    <p class="text-sm mb-4">By: ${artwork.artist_name.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                                    <div class="flex space-x-2">
                                        <a href="artwork_detail.php?id=${artwork.artwork_id}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">View Details</a>
                                        ${artwork.is_for_auction ? 
                                            `<a href="auction_detail.php?id=${artwork.artwork_id}" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition">View Auction</a>` : 
                                            `<button class="purchase-btn bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition" 
                    data-artwork-id="${artwork.artwork_id}">
                Purchase Now
            </button>`                            } 
                                    </div>
                                </div>
                                <div class="artwork-info">
                                    <p class="font-bold mt-2">$${parseFloat(artwork.price).toFixed(2)}</p>
                                </div>
                            </div>
                        `;
                        $('#gallery-grid').append(artworkHtml);
                    });
                }
            
                $(document).ready(function() {
                    // Event delegation for purchase buttons (works for dynamically added elements)
                    $(document).on('click', '.purchase-btn', function() {
                        const artworkId = $(this).data('artwork-id');
                        purchaseArtwork(artworkId);
                    });
                });


                // purchase artwork function
                function purchaseArtwork(artworkId) {
                    console.log('Attempting to purchase artwork ID:', artworkId); // Debugging
                    
                    $.ajax({
                        url: 'api/purchase_artwork.php',
                        type: 'POST',
                        data: { artwork_id: artworkId },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Purchase response:', response); // Debugging
                            if (response.status === 'success') {
                                showNotification({
                                    type: 'success',
                                    title: 'Purchase Successful',
                                    message: response.message,
                                    autoClose: true
                                });
                                
                                if (response.order_id) {
                                    // Optionally redirect to order confirmation
                                    // window.location.href = `/order-confirmation.php?id=${response.order_id}`;
                                }
                                
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
                        error: function(xhr, status, error) {
                            console.error('Purchase error:', status, error); // Debugging
                            showNotification({
                                type: 'error',
                                title: 'Error',
                                message: 'Error communicating with server',
                                autoClose: true
                            });
                        }
                    });
                }
                    
                // Function to update active filters display
                function updateActiveFilters() {
                    $('#active-filters').html('<span class="text-sm text-gray-700 mr-2">Active filters:</span>');
                    let hasActiveFilters = false;

                    // Category filter
                    if ($('#category-filter').val()) {
                        const selectedCategory = $('#category-filter option:selected').text();
                        addActiveFilterBadge('Category: ' + selectedCategory, 'category');
                        hasActiveFilters = true;
                    }

                    // Price filter
                    if ($('#price-filter').val()) {
                        const priceText = $('#price-filter option:selected').text();
                        addActiveFilterBadge('Price: ' + priceText, 'price');
                        hasActiveFilters = true;
                    }

                    // Sort filter (if not default)
                    if ($('#sort-filter').val() !== 'newest') {
                        const sortText = $('#sort-filter option:selected').text();
                        addActiveFilterBadge('Sort: ' + sortText, 'sort');
                        hasActiveFilters = true;
                    }

                    // Search term
                    if ($('#search-input').val().trim()) {
                        addActiveFilterBadge('Search: "' + $('#search-input').val().trim() + '"', 'search');
                        hasActiveFilters = true;
                    }

                    // Show/hide active filters container
                    $('#active-filters').toggle(hasActiveFilters);
                }

                // Function to add a filter badge
                function addActiveFilterBadge(text, type) {
                    const badge = $(`
                        <div class="flex items-center bg-[#b8895c] bg-opacity-20 text-[#9a6b42] px-3 py-1 rounded-full text-sm">
                            ${text}
                            <button data-type="${type}" class="ml-2 text-[#9a6b42] hover:text-[#7a542f] focus:outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                    $('#active-filters').append(badge);
                }

                // Load more button click
                $('#load-more').click(function() {
                    loadArtworks();
                });
                
                // Filter change events
                $('#category-filter, #price-filter, #sort-filter').change(function() {
                    updateActiveFilters();
                    loadArtworks(true);
                });
                
                // Search input with debounce
                $('#search-input').on('input', debounce(function() {
                    updateActiveFilters();
                    loadArtworks(true);
                }, 300));
                
                // Reset all filters
                $('#reset-filters').click(function() {
                    $('#category-filter').val('');
                    $('#price-filter').val('');
                    $('#sort-filter').val('newest');
                    $('#search-input').val('');
                    updateActiveFilters();
                    loadArtworks(true);
                });
                
                // Remove individual filter
                $('#active-filters').on('click', 'button[data-type]', function() {
                    const filterType = $(this).data('type');
                    
                    switch(filterType) {
                        case 'category':
                            $('#category-filter').val('');
                            break;
                        case 'price':
                            $('#price-filter').val('');
                            break;
                        case 'sort':
                            $('#sort-filter').val('newest');
                            break;
                        case 'search':
                            $('#search-input').val('');
                            break;
                    }
                    
                    updateActiveFilters();
                    loadArtworks(true);
                });
                });


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
    
</script>
    <?php require_once 'includes/footer.php'; ?>

</body>
</html>