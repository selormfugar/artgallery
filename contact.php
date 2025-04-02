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
            background-color: #ffffff;
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
            color: #e63946;
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
        @keyframes slideShow {
            0% { background-image: url('images/art6.jpg'); }
            33% { background-image: url('images/art25.jpg'); }
            66% { background-image: url('images/art26.jpg'); }
            100% { background-image: url('images/art15.jpg'); }
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
    </style>
</head>
<body>

    <!-- Navigation Menu -->
    <?php   require_once 'includes/navbar.php';   ?>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="carousel"></div>
        <div class="hero-content">
            <h1 class="text-5xl font-bold tracking-wide">CONTACT US</h1>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-16 text-center">
        <h2 class="text-4xl font-semibold text-gray-800">Get To Us</h2>
        <p class="text-lg text-gray-600 mt-4">673 12 Constitution Lane Massillon, 10002 New York City</p>
        <p class="text-md text-gray-500 mt-2">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </p>
    </section>

    <!-- Google Map -->
    <section class="w-full">
        <iframe
            class="w-full h-96"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127877.123456789!2d-0.186964!3d5.603717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1020f8b123456789%3A0x123456789abcdef!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1603412345678!5m2!1sen!2sus"
            frameborder="0"
            allowfullscreen=""
            aria-hidden="false"
            tabindex="0">
        </iframe>
    </section>

    <!-- Contact Form & Details -->
    <section class="py-16 max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Contact Form -->
            <div>
                <h3 class="text-3xl font-semibold text-gray-800">Contact</h3>
                <form action="#" method="POST" class="mt-6">
                    <textarea placeholder="Message" class="w-full p-3 border rounded-md bg-gray-100"></textarea>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <input type="text" placeholder="Name" class="w-full p-3 border rounded-md bg-gray-100">
                        <input type="email" placeholder="Email" class="w-full p-3 border rounded-md bg-gray-100">
                    </div>
                    <button type="submit" class="mt-6 px-6 py-3 bg-black text-white font-semibold rounded-md hover:bg-gray-800">
                        Submit
                    </button>
                </form>
            </div>

            <!-- Reservation & Contact Details -->
            <div>
                <h3 class="text-3xl font-semibold text-gray-800">Reservations</h3>
                <p class="text-lg text-gray-600 mt-2">673 12 Constitution Lane Massillon, 05765 New York</p>
                <p class="text-md text-gray-600 mt-2">781-562-9355, 781-727-6090</p>
                <p class="text-md text-gray-600 mt-2">musea@qodeinteractive.com</p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-gray-600 hover:text-black"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-600 hover:text-black"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-600 hover:text-black"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php   require_once 'includes/footer.php';   ?>


    <script>
        function toggleMode() {
            document.body.classList.toggle("light-mode");
        }
    </script>

</body>
</html>
