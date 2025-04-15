<?php
require_once 'includes/config.php';
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
             body {
            font-family: 'Playfair Display', serif;
            background-color: #121212;
            color: #ffffff;
            transition: background 0.5s ease-in-out, color 0.5s ease-in-out;
        }
        dark-mode {
            background-color: #0b0b0b;
            color: #f8f8f8;
        }
        .dark-mode .bg-white {
            background-color: #1a1a1a !important;
        }
        .dark-mode .text-gray-800 {
            color: #f8f8f8 !important;
        }
        .dark-mode .border-gray-200 {
            border-color: #333 !important;
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .dark-mode .product-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .price-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #d1a054;
            cursor: pointer;
            border-radius: 50%;
        }
        .hero-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/shop-hero.jpg');
            background-size: cover;
            background-position: center;
        }
        .dark-mode .hero-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('images/shop-hero.jpg');
        }
 
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        body {
            font-family: 'Playfair Display', serif;
            background-color: #121212;
            color: #ffffff;
            transition: background 0.5s ease-in-out, color 0.5s ease-in-out;
        }
        .light-mode {
            background-color: #ffffff;
            color: #121212;
        }
        .nav-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .nav-menu a {
            margin: 0 15px;
            font-size: 18px;
            font-weight: bold;
            transition: color 0.3s;
        }
        .nav-menu a:hover {
            color: #b8895c;
        }
        .profile-icon {
            font-size: 24px;
            cursor: pointer;
            color: white;
            transition: color 0.3s;
        }
        .profile-icon:hover {
            color: #b8895c;
        }
        .hero-section {
            position: relative;
            width: 100%;
            height: 100vh;
            background: url('images/art6.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-overlay {
            padding: 60px;
            border-radius: 10px;
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary {
            background-color: #b8895c;
            color: white;
        }
        .btn-primary:hover {
            background-color: #9a6b42;
        }
        .section-container {
            max-width: 1200px;
            margin: auto;
            padding: 80px 20px;
            text-align: center;
        }
        .section-heading {
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #ffffff;
        }
        .section-subtext {
            font-size: 20px;
            font-style: italic;
            color: #b8895c;
        }
        .search-bar {
            display: flex;
            width: 300px;
            margin-left: 20px;
        }
        .search-bar input {
            flex-grow: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #b8895c;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .category-card {
            transition: transform 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-10px);
        }
        .artwork-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .artwork-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .artwork-overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 20px;
            transition: bottom 0.3s ease;
        }
        .artwork-card:hover .artwork-overlay {
            bottom: 0;
        }
                /* Add these styles */
          /* Theme toggle button */
          .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .theme-toggle:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }
        
        .theme-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(184, 137, 92, 0.5);
        }
        
    </style>
</head>
<body class="dark-mode">

    <!-- Navigation Menu -->
    <?php require_once 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-bg h-96 flex items-center justify-center text-center px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">Art Shop</h1>
            <p class="text-xl text-gray-300">Discover unique artworks and museum collectibles</p>
        </div>
    </section>

    

    <!-- Shop Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-1/4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm sticky top-4">
                    <h3 class="text-xl font-bold mb-6">Filters</h3>
                    
                    <!-- Price Range -->
                    <div class="mb-8">
                        <h4 class="font-medium mb-4">Price Range</h4>
                        <div class="flex justify-between mb-2">
                            <span>$0</span>
                            <span>$950</span>
                        </div>
                        <input type="range" min="0" max="950" value="950" class="w-full price-range">
                        <div class="flex justify-between mt-2">
                            <span class="text-sm text-gray-500">Min: $0</span>
                            <span class="text-sm text-gray-500">Max: $950</span>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-8">
                        <h4 class="font-medium mb-4">Categories</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <input type="checkbox" id="brochure" class="mr-2 rounded text-amber-600 focus:ring-amber-500">
                                <label for="brochure">Brochure</label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="lifestyle" class="mr-2 rounded text-amber-600 focus:ring-amber-500">
                                <label for="lifestyle">Lifestyle</label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="museum" class="mr-2 rounded text-amber-600 focus:ring-amber-500">
                                <label for="museum">Museum</label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="souvenirs" class="mr-2 rounded text-amber-600 focus:ring-amber-500">
                                <label for="souvenirs">Souvenirs</label>
                            </li>
                        </ul>
                    </div>
                    
                    <button class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300">
                        Apply Filters
                    </button>
                </div>
            </aside>

            <!-- Main Shop Content -->
            <div class="lg:w-3/4">
                <!-- Theme toggle floating button -->
     <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>

                <!-- Shop Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <p class="text-gray-600 dark:text-gray-400 mb-4 md:mb-0">Showing 1–12 of 15 results</p>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600 dark:text-gray-400">Sort by:</span>
                        <select class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg px-4 py-2 focus:ring-amber-500 focus:border-amber-500">
                            <option>Default</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest</option>
                            <option>Popular</option>
                        </select>
                    </div>
                </div>
                
                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="artwork-grid">
                    <!-- Artworks will be loaded here via AJAX -->
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-xl h-80"></div>
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-xl h-80"></div>
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-xl h-80"></div>
                </div>
                
                <!-- Loading More Indicator -->
                <div id="loading-indicator" class="text-center py-8 hidden">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-amber-600"></div>
                </div>
                        <!-- Notification container (place this in your layout file) -->
<div id="notification-container" class="fixed bottom-4 right-4 z-50 w-80 max-w-full"></div>

                <!-- No Results Message (hidden by default) -->
                <div id="no-results" class="text-center py-12 hidden">
                    <h3 class="text-xl font-medium mb-2">No art piece on sale</h3>
                    <p class="text-gray-600 dark:text-gray-400">Try adjusting your filters or search criteria</p>
                </div>
            </div>
        </div>
    </div> 
        <style>
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
    <!-- Footer -->
    <?php require_once 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Initialize variables
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;
        let filters = {
            price_max: 950,
            categories: []
        };

        // DOM Elements
        const artworkGrid = document.getElementById('artwork-grid');
        const loadingIndicator = document.getElementById('loading-indicator');
        const noResults = document.getElementById('no-results');
        const priceRange = document.querySelector('.price-range');
        
        // Display artworks function
function displayArtworks(artworks) {
    // Clear loading placeholders
    if (currentPage === 1) {
        artworkGrid.innerHTML = '';
    }
    
    if (artworks.length === 0 && currentPage === 1) {
        noResults.classList.remove('hidden');
        return;
    }
    
    noResults.classList.add('hidden');
    
    artworks.forEach(artwork => {
        const card = document.createElement('div');
        card.className = 'product-card bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm';
        card.innerHTML = `
            <div class="relative aspect-square overflow-hidden">
                <img src="<?php echo SITE_URL; ?>/images/${artwork.image_url}" alt="${artwork.title}" 
                     class="w-full h-full object-cover transition duration-300 hover:scale-105">
                <button onclick="addToCart(${artwork.artwork_id})" 
                        class="absolute bottom-4 right-4 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-medium text-lg mb-1 truncate">${artwork.title}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-3">${artwork.artist_name || 'Unknown Artist'}</p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-amber-600">$${artwork.price}</span>
                    <a href="artwork_detail.php?id=${artwork.artwork_id}" class="text-sm bg-amber-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full">
                        View Details
                    </a>
                </div>
            </div>
        `;
        artworkGrid.appendChild(card);
    });
}
        // Load artworks function
        function loadArtworks(reset = false) {
            if (isLoading || !hasMore) return;
            
            isLoading = true;
            
            if (reset) {
                currentPage = 1;
                hasMore = true;
                loadingIndicator.classList.remove('hidden');
            } else {
                artworkGrid.insertAdjacentHTML('beforeend', '<div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-xl h-80 sm:col-span-2 lg:col-span-3"></div>');
            }
            
            // Get current filter values
            filters = {
                price_max: priceRange.value,
                categories: Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(el => el.id),
                sort: document.querySelector('select').value,
                page: currentPage
            };
            
            // Simulate API call (replace with actual fetch)
            setTimeout(() => {
                // This would be your actual fetch call:
                fetch('api/get_sales.php?' + new URLSearchParams(filters))
                    .then(response => response.json())
                    .then(data => {
                        if (reset) {
                            artworkGrid.innerHTML = '';
                        }
                        displayArtworks(data.artworks);
                        hasMore = data.has_more;
                        currentPage++;
                    })
                
               
                
                // Remove loading placeholder
                const placeholders = document.querySelectorAll('.animate-pulse');
                placeholders.forEach(el => el.remove());
            }, 1000);
        }
// Add to Cart function with notification
function addToCart(artworkId) {
   

    $.ajax({
        url: 'api/purchase_artwork.php',
        type: 'POST',
        data: { artwork_id: artworkId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showNotification({
                    type: 'success',
                    title: 'Artwork Purchased',
                    message: response.message || 'Artwork purchased successfully',
                    autoClose: true,
                    duration: 3000
                });
                
                
            } else {
                showNotification({
                    type: 'error',
                    title: 'You already purchased this artwork',
                    message: response.message || 'Could not purchase artwork',
                    autoClose: true,
                    duration: 5000
                });
            }
        },
        error: function(xhr, status, error) {
            showNotification({
                type: 'error',
                title: 'Error',
                message: 'Failed to connect to server. Please try again.',
                autoClose: true,
                duration: 5000
            });
            console.error('Add to cart error:', error);
        },
        complete: function() {
            // Reset button state
            buttons.forEach(btn => {
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>`;
                btn.disabled = false;
            });
        }
    });
}

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

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            loadArtworks();
            
            // Filter change events
            priceRange.addEventListener('input', () => {
                document.querySelector('span:last-child').textContent = `Max: $${priceRange.value}`;
            });
            
            document.querySelector('select').addEventListener('change', () => loadArtworks(true));
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', () => loadArtworks(true));
            });
            
            document.querySelector('.price-range').addEventListener('change', () => loadArtworks(true));
            document.querySelector('button').addEventListener('click', () => loadArtworks(true));
            
            // Infinite scroll
            window.addEventListener('scroll', () => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
                    loadArtworks();
                }
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
</body>
</html>

<!-- Api-key-Stripe1 -->