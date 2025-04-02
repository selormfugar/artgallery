<?php
require_once 'includes/config.php';
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0b0b0b;
            color: #fcf8f8;
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
            color: #0b0b0b;
        }
        .hero-section {
            position: relative;
            width: 100%;
            height: 80vh;
            overflow: hidden;
        }
        .carousel {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            animation: slideShow 20s infinite;
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
        }
        .shop-container {
            max-width: 1300px;
            margin: auto;
            padding: 50px 20px;
            display: flex;
            gap: 30px;
        }
        .sidebar {
            width: 300px;
        }
        .main-content {
            flex: 1;
        }
        .shop-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 500;
        }
        .shop-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        .product-card {
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .product-card img {
            width: 100%;
            border-radius: 5px;
            transition: transform 0.3s ease-in-out;
        }
        .product-card:hover img {
            transform: scale(1.05);
        }
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 16px;
        }
        .featured-section, .tags-section {
            margin-top: 40px;
        }
        .filter-button {
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px;
            background: black;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>

     <!-- Navigation Menu -->
     <?php require_once 'includes/navbar.php'; ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="carousel"></div>
        <div class="hero-content">
            <h1 class="text-5xl font-bold tracking-wide">SHOP</h1>
        </div>
    </section>

<!-- Shop Layout -->
<div class="shop-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h3 class="font-semibold text-xl mb-4">Filter</h3>
        <label class="block">Price: $0 - $950</label>
        <input type="range" min="0" max="950" class="w-full mb-4">
        <button class="filter-button">Apply Filter</button>
        
        <h3 class="font-semibold text-xl mt-6 mb-4">Categories</h3>
        <ul class="text-gray-700">
            <li>Brochure</li>
            <li>Lifestyle</li>
            <li>Museum</li>
            <li>Souvenirs</li>
        </ul>
    </aside>

    <!-- Main Shop Content -->
    <div class="main-content">
        <div class="shop-header">
            <p>Showing 1–12 of 15 results</p>
            <select class="border px-4 py-2">
                <option>Default sorting</option>
                <option>Price: low to high</option>
                <option>Price: high to low</option>
            </select>
        </div>
        <div class="shop-grid">
                       <!-- Artworks will be loaded here via AJAX -->
        </div>
    </div>
</div>

<!-- Footer -->
<?php require_once 'includes/footer.php'; ?>


    <script>
        function toggleMode() {
            document.body.classList.toggle('light-mode');
        }

        // Initialize variables
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;
        let filters = {};

        function displayArtworks(artworks) {
            const grid = document.querySelector('.shop-grid');
            artworks.forEach(artwork => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
                <img src="${artwork.image_url}" alt="${artwork.title}">
                <h3>${artwork.title}</h3>
                <p>$${artwork.price}</p>
            `;
            const imagePath = '<?php echo SITE_URL; ?>/images/' + artwork.image_url;
            card.innerHTML = `
                <img src="${imagePath}" alt="${artwork.title}">
                <h3>${artwork.title}</h3>
                <p>$${artwork.price}</p>
            `;
            card.style.height = '350px'; // Set a fixed height for all cards
            card.style.display = 'flex';
            card.style.flexDirection = 'column';
            card.style.justifyContent = 'space-between';
            // Add styles to ensure consistent card sizing
            const img = card.querySelector('img');
            img.style.width = '100%';
            img.style.height = '200px'; // Fixed height for images
            img.style.objectFit = 'cover'; // Maintain aspect ratio
            
            const textContent = card.querySelector('h3');
            textContent.style.margin = '10px 0';
            textContent.style.fontSize = '1rem';
            textContent.style.overflow = 'hidden';
            textContent.style.textOverflow = 'ellipsis';
            textContent.style.whiteSpace = 'nowrap';
            
            const price = card.querySelector('p');
            price.style.fontWeight = 'bold';
            price.style.marginTop = 'auto';
            
            grid.appendChild(card);
            });
        }

        function loadArtworks(reset = false) {
            if (isLoading || !hasMore) return;
            
            isLoading = true;
            
            if (reset) {
            currentPage = 1;
            document.querySelector('.shop-grid').innerHTML = '';
            hasMore = true;
            }
            
            // Get filter values
            filters = {
            category: document.querySelector('select').value,
            price: document.querySelector('input[type="range"]').value,
            sort: document.querySelector('select').value
            };
            
            // Add pagination parameter
            const requestData = {
            ...filters,
            page: currentPage
            };
            
            fetch('api/get_artworks.php?' + new URLSearchParams(requestData))
            .then(response => response.json())
            .then(data => {
                if (data.artworks.length === 0) {
                if (currentPage === 1) {
                    document.querySelector('.shop-grid').innerHTML = 
                    '<div class="col-span-full text-center py-8">No artworks found matching your criteria.</div>';
                }
                hasMore = false;
                } else {
                displayArtworks(data.artworks);
                currentPage++;
                hasMore = data.has_more;
                }
            })
            .catch(error => {
                console.error("Error loading artworks:", error);
                document.querySelector('.shop-grid').innerHTML = 
                '<div class="col-span-full text-center py-8 text-red-500">Error loading artworks. Please try again.</div>';
            })
            .finally(() => {
                isLoading = false;
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadArtworks();
            
            // Add event listeners for filters
            document.querySelector('input[type="range"]').addEventListener('change', () => loadArtworks(true));
            document.querySelector('select').addEventListener('change', () => loadArtworks(true));
            document.querySelector('.filter-button').addEventListener('click', () => loadArtworks(true));
        });
    </script>

</body>
</html>