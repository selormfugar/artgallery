<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        body {
            font-family: 'Playfair Display', serif;
            background-color: #121212;
            color: #ffffff;
        }
        .hero-section {
            background: url('art6.jpg') center/cover no-repeat;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .section-container {
            max-width: 1200px;
            margin: auto;
            padding: 80px 20px;
            text-align: center;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        .image-gallery img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .testimonial-carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .testimonial-slide {
            min-width: 100%;
            text-align: center;
        }
        .testimonial-dot {
            cursor: pointer;
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background: gray;
            display: inline-block;
            border-radius: 50%;
        }
        .testimonial-dot.active {
            background: white;
        }
    </style>
</head>
<body>
<?php   require_once 'includes/navbar.php';   ?>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="text-6xl font-bold">About Us</h1>
        </div>
    </section>

    <!-- About The Museum -->
    <section class="section-container">
        <p class="text-lg italic text-gray-400">About Our House</p>
        <h2 class="text-5xl font-bold text-gray-200">The Museum</h2>
        <p class="text-lg text-gray-400 mt-4 max-w-2xl mx-auto">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </p>

        <div class="flex flex-wrap md:flex-nowrap mt-8">
            <div class="w-full md:w-1/2 text-left pr-10">
                <p class="text-gray-400 text-lg leading-relaxed">
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                    Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
            </div>
            <div class="w-full md:w-1/2 text-left">
                <h3 class="text-2xl font-bold text-white">Opening Hours</h3>
                <p class="text-gray-400 mt-2">Tue – Thu: 09am – 07pm</p>
                <p class="text-gray-400">Fri – Sat: 09am – 05pm</p>
                <p class="text-gray-400">Sun: 08am – 06pm</p>
                <p class="text-gray-400">Mon: Closed</p>

                <h3 class="text-2xl font-bold text-white mt-4">Admissions</h3>
                <p class="text-gray-400">Adults: $25</p>
                <p class="text-gray-400">Children & Students: Free</p>
            </div>
        </div>
    </section>

    <!-- Image Gallery -->
    <section class="section-container">
        <h2 class="text-5xl font-bold text-gray-200">Gallery Highlights</h2>
        <div class="image-gallery">
            <img src="images/art1.jpg" alt="Gallery 1">
            <img src="images/art2.jpg" alt="Gallery 2">
            <img src="images/art3.jpg" alt="Gallery 3">
            <img src="images/art4.jpg" alt="Gallery 4">
            <img src="images/art5.jpg" alt="Gallery 5">
            <img src="images/art6.jpg" alt="Gallery 6">
        </div>
    </section>

    <!-- Testimonial Carousel -->
    <section class="section-container">
        <h2 class="text-5xl font-bold text-gray-200">Visitor Reviews</h2>
        <div class="relative max-w-3xl mx-auto overflow-hidden">
            <div class="testimonial-carousel">
                <div class="testimonial-slide">
                    <p class="text-lg italic text-gray-400">"A wonderful experience. The art was breathtaking!"</p>
                    <h3 class="text-xl font-bold text-gray-300 mt-4">James Carter</h3>
                </div>
                <div class="testimonial-slide">
                    <p class="text-lg italic text-gray-400">"An unforgettable journey through creativity!"</p>
                    <h3 class="text-xl font-bold text-gray-300 mt-4">Sophia Lee</h3>
                </div>
            </div>
        </div>
        <div class="flex justify-center space-x-2 mt-4">
            <span class="testimonial-dot active" onclick="setTestimonial(0)"></span>
            <span class="testimonial-dot" onclick="setTestimonial(1)"></span>
        </div>
    </section>

    <!-- Video Embed -->
    <section class="section-container">
        <h2 class="text-5xl font-bold text-gray-200">Artworks Archive</h2>
        <iframe width="100%" height="500" src="https://player.vimeo.com/video/204646310" frameborder="0" allowfullscreen></iframe>
    </section>

    <!-- Google Maps Embed -->
    <section class="section-container">
        <h2 class="text-5xl font-bold text-gray-200">Visit Us</h2>
        <iframe width="100%" height="400" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.370462774197!2d-74.0025413845961!3d40.73083637932879!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259af5ea1575f%3A0x5aefdf8b2d16d0e6!2sWhitney%20Museum%20of%20American%20Art!5e0!3m2!1sen!2sus!4v1615508659261!5m2!1sen!2sus" allowfullscreen></iframe>
    </section>

    <script>
        function setTestimonial(index) {
            document.querySelector(".testimonial-carousel").style.transform = `translateX(-${index * 100}%)`;
        }
    </script>

</body>
</html>
