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
 <?php require_once 'includes/head.php'; ?>
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
    
        // Search Bar Functionality
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const query = document.getElementById('search-input').value.trim();
            if(query) {
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
        });

        
    </script>
</body>
</html>