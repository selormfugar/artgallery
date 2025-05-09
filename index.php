<?php

session_start();
// After successful login in your login.php:
if (isset($_SESSION['user_id'])) {
    echo '<script>checkPendingWishlistItem();</script>';
    
    // Or if you're redirecting back:
    if (isset($_GET['redirect'])) {
        header("Location: " . urldecode($_GET['redirect']));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
 <!-- header -->
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery | Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
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
<body>
    <!-- navbar -->
    <?php require_once 'includes/navbar.php'; ?>
    
    <!-- Hero Section with Carousel -->
    <section class="hero-section">
        <div class="hero-overlay text-center">
            <h1 class="text-6xl font-bold mb-6">Discover & Collect Unique Art</h1>
            <p class="text-xl mb-8">Explore exclusive artworks from talented artists worldwide</p>
            <div class="flex justify-center gap-4">
                <a href="gallery.php" class="btn btn-primary">Browse Artworks</a>
                <a href="register.php" class="btn" style="background-color: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">Become an Artist</a>
            </div>
        </div>
    </section>
 
    <div class="container mx-auto px-4 py-8">
        <!-- Theme toggle floating button -->
        <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>
    <!-- Category Section -->
    <section class="section-container">
        <h2 class="section-heading">Popular Categories</h2>
        <p class="section-subtext">Explore by artistic style and medium</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12">
            <div class="category-card bg-gray-800 p-6 rounded-lg text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                   <!-- replace with icons  -->
                <img src="images/abstract.jpg" alt="Abstract Art" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold">Abstract</h3>
                <p class="text-gray-400 mt-2">20+ artworks</p>
            </div>
            
            <div class="category-card bg-gray-800 p-6 rounded-lg text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                <img src="images/portrait.jpg" alt="Abstract Art" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold">Portraits</h3>
                <p class="text-gray-400 mt-2">85+ artworks</p>
            </div>
            
            <div class="category-card bg-gray-800 p-6 rounded-lg text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                <img src="images/landscape.jpg" alt="Abstract Art" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold">Landscapes</h3>
                <p class="text-gray-400 mt-2">50+ artworks</p>
            </div>
            
            <div class="category-card bg-gray-800 p-6 rounded-lg text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                <img src="images/modern.jpg" alt="Abstract Art" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold">Modern Art</h3>
                <p class="text-gray-400 mt-2">95+ artworks</p>
            </div>
        </div>
    </section>

    <!-- Featured Artworks -->
    <section class="section-container" id="featured-artworks">
        <h2 class="section-heading">Featured Artworks</h2>
        <p class="section-subtext">Curated selection of this month's highlights</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12" id="artworks-grid">
            <!-- Artwork cards will be loaded here via AJAX -->
        </div>
        
        <div class="text-center mt-10">
            <a href="gallery.php" class="btn btn-primary inline-block">View All Artworks</a>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section-container bg-gray-800 rounded-lg">
        <h2 class="section-heading">Why Choose Us</h2>
        <p class="section-subtext">The trusted platform for artists and collectors</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="text-center p-6">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-b8895c flex items-center justify-center">
                <img src="images/payment.jpg" alt="Secure Payments" class="w-50 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold mb-2">Secure Payments</h3>
                <p class="text-gray-400">Encrypted transactions with buyer protection</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-b8895c flex items-center justify-center">
                <img src="images/verified icon.png" alt="Verified Artist" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold mb-2">Verified Artists</h3>
                <p class="text-gray-400">All artists undergo strict verification</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-b8895c flex items-center justify-center">
                <img src="images/copyright.png" alt="Abstract Art" class="w-16 h-16 rounded-full object-cover">
                </div>
                <h3 class="text-xl font-semibold mb-2">Copyright Protection</h3>
                <p class="text-gray-400">Watermarking and digital rights management</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section-container">
        <h2 class="section-heading">What Our Community Says</h2>
        <p class="section-subtext">Hear from artists and collectors</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
            <div class="bg-gray-800 p-8 rounded-lg">
                <div class="flex items-center mb-4">
                    <img src="images/art7.jpg" alt="Testimonial" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">James Wilson</h4>
                        <p class="text-gray-400 text-sm">Collector since 2018</p>
                    </div>
                </div>
                <p class="italic text-gray-300">"I've discovered amazing emerging artists on this platform that I wouldn't have found elsewhere. The purchase process is seamless and secure."</p>
                <div class="flex mt-4 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <div class="bg-gray-800 p-8 rounded-lg">
                <div class="flex items-center mb-4">
                    <img src="images/art10.jpg" alt="Testimonial" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Sophia Martinez</h4>
                        <p class="text-gray-400 text-sm">Artist</p>
                    </div>
                </div>
                <p class="italic text-gray-300">"As an artist, this platform has given me global exposure. The commission rates are fair and I've connected with serious collectors."</p>
                <div class="flex mt-4 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
        </div>
    </section>
</div>
    <?php require_once 'includes/footer.php'; ?>

    <script>
    // Load featured artworks on page load
    document.addEventListener('DOMContentLoaded', loadFeaturedArtworks);

    function loadFeaturedArtworks() {
        fetch('api/get_featured_artworks.php')
        .then(response => response.json())
        .then(artworks => {
            const grid = document.getElementById('artworks-grid');
            grid.innerHTML = ''; // Clear existing content
            
            artworks.forEach(artwork => {
                const card = `
                    <div class="artwork-card rounded-lg overflow-hidden">
                        <img src="images/${artwork.image_url}" alt="${artwork.title}" class="w-full h-64 object-cover">
                        <div class="artwork-overlay">
                            <h3 class="text-xl font-bold">"${artwork.title}"</h3>
                            <p class="text-gray-300">by ${artwork.artist_name}</p>
                            <p class="text-b8895c font-bold mt-2">$${artwork.price}</p>
                            <a href="artwork-details.php?id=${artwork.artwork_id}" class="btn btn-primary mt-4 inline-block">View Details</a>
                        </div>
                    </div>
                `;
                grid.innerHTML += card;
            });
        })
        .catch(error => console.error('Error loading featured artworks:', error));
    }

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
    
        // Search Bar Functionality
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const query = document.getElementById('search-input').value.trim();
            if(query) {
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
        });

        
    </script> <!-- Theme toggle floating button -->
        <button id="theme-toggle" class="theme-toggle fixed bottom-6 right-6 z-50 shadow-lg">
            <i class="fas fa-moon text-white" id="theme-icon"></i>
        </button>

</body>
</html>