<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        body {
            font-family: 'Playfair Display', serif;
            background: url('images/art6.jpg') center/cover no-repeat;
            background-size: cover;
            color: #ffffff;
        }
        .light-mode {
            background-color: #ffffff;
            color: #121212;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            color: #000;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.3s ease-in-out;
            background-color: #0b0b0b;
            color: white;
            width: 100%;
        }
        .btn:hover {
            background-color: #0c0c0c;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="w-full fixed top-0 left-0 bg-opacity-90 p-4 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white">M</h1>
        <ul class="flex space-x-6 text-lg text-white">
            <li><a href="index.php" class="hover:underline">Home</a></li>
            <li><a href="events.php" class="hover:underline">Events</a></li>
            <li><a href="shop.php" class="hover:underline">Shop</a></li>
        </ul>
        <div class="flex space-x-4">
            <button onclick="openLoginModal()" class="bg-gray-600 px-4 py-2 rounded text-white">Login</button>
            <button onclick="toggleMode()" class="bg-gray-600 px-4 py-2 rounded text-white">Toggle Mode</button>
        </div>
    </nav>

    <!-- Login Modal -->
    <div id="loginModal" class="modal flex">
        <div class="modal-content">
            <h2 class="text-3xl font-bold text-white">Login</h2>
            <div id="loginModal" class="modal flex">
        <div class="modal-content">
            <h2 class="text-3xl font-bold text-white">Login</h2>
            <form id="loginForm" class="form-container">
                <input type="email" name="email" placeholder="Email" aria-label="Email" required>
                <input type="password" name="password" placeholder="Password" aria-label="Password" required>

                <button type="submit" class="btn mt-4">Login</button>
            </form>
            <p class="text-center mt-4 text-white">
                Don't have an account? <a href="register.php"  class="text-white-400">Register</a>
            </p>
            <button onclick="closeLoginModal()" class="mt-4 px-4 py-2 rounded text-white">Close</button>
        </div>
    </div>

                <input type="password" name="password" placeholder="Password" aria-label="Password" required>

                <button type="submit" class="btn mt-4">Login</button>
            </form>
            <p class="text-center mt-4 text-white">
                Don't have an account? <a href="register.php"  class="text-white-400">Register</a>
            </p>
            <button onclick="closeLoginModal()" class="mt-4 px-4 py-2 rounded text-white">Close</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
       document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
       
        try {
            const response = await axios.post('api/login.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            // Handle successful login
            if (response.data.status === 'success') {
                alert('Login successful!');
                
                // Redirect based on user role
                if (response.data.role === 'admin') {
                    window.location.href = 'admin/index.php';
                }
                else if (response.data.role === 'artist') {
                    window.location.href = 'artist/dashboard';
                 }
                  else {
                    window.location.href = 'index.php';
                }
                
            } else {
                // Display error message
                alert(response.data.message || 'Login failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            
            // More detailed error handling
            if (error.response) {
                // The request was made and the server responded with a status code
                alert(error.response.data.message || 'Login failed');
            } else if (error.request) {
                // The request was made but no response was received
                alert('No response from server. Please check your connection.');
            } else {
                // Something happened in setting up the request
                alert('An unexpected error occurred. Please try again.');
            }
        }
    });
});
   
        function openLoginModal() {
            document.getElementById("loginModal").style.display = "flex";
        }

        function closeLoginModal() {
            document.getElementById("loginModal").style.display = "none";
            history.back(); // This will redirect to the previous page
        }

        function toggleMode() {
            document.body.classList.toggle("light-mode");
        }

        // Close modal when clicking outside content
        window.onclick = function(event) {
            const modal = document.getElementById("loginModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
