<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery | Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="nav-menu">
        <div class="flex items-center">
            <a href="index.php" class="text-2xl font-bold">M</a>
            <span class="text-sm ml-2">Art Gallery</span>
        </div>
        <div>
            <a href="index.php">Home</a>
            <a href="gallery.php">Gallery</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>

            <?php if ($isLoggedIn): ?>
                <a href="auction.php">Auctions</a>
                <a href="shop.php">Shop</a>
                <a href="events.php">Events</a>
                <a href="subscription.php">Subscription</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Profile</a>
            <?php endif; ?>
        </div>

        <button onclick="toggleMode()" class="px-4 py-2 rounded">Toggle Mode</button>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay">
            <h2 class="text-6xl font-extrabold">History of Art Department</h2>
            <p class="mt-4 text-lg">Explore exclusive auctions, exhibitions, and rare masterpieces.</p>
            <a href="register.php" class="btn btn-primary">View More</a>
        </div>
    </section>

    <script>
        function toggleMode() {
            document.body.classList.toggle("light-mode");
        }
    </script>
</body>
</html>
