<?php 
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


?> <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Art Gallery</title>
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
        .form-container input, .form-container select {
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
            <button onclick="openModal()" class="bg-gray-600 px-4 py-2 rounded text-white">Register</button>
            <button onclick="toggleMode()" class="bg-gray-600 px-4 py-2 rounded text-white">Toggle Mode</button>
        </div>
    </nav>

    <!-- Register Modal -->
    <div id="registerModal" class="modal flex">
        <div class="modal-content">
            <h2 class="text-3xl font-bold text-white">Create an Account</h2>
            <form id="registerForm" class="form-container">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="text" name="firstname" id="firstname" placeholder="First Name" aria-label="First Name" required>
                <input type="text" name="lastname" id="lastname" placeholder="Last Name" aria-label="Last Name" required>
                <input type="email" name="email" id="email" placeholder="Email" aria-label="Email" required>
                <input type="password" name="password" id="password" placeholder="Password" aria-label="Password" required>
                <!-- <input type="text" name="phone" id="phone" placeholder="Phone Number" aria-label="Phone Number" required> -->

                <label class="block mt-2 text-white text-lg">Select User Role:</label>
                <select name="role" id="role" aria-label="User Role" required>
                    <option value="buyer">Regular User</option>
                    <option value="artist">Artist</option>
                </select>

                <button type="button" id="submitBtn" class="btn mt-4">Register</button>
            </form>
            <p class="text-center mt-4 text-white">
                Already have an account? <a href="login.php" class="text-white-400">Login</a>
            </p>
            <button onclick="closeModal()" class="mt-4 px-4 py-2 rounded text-white">Close</button>
        </div>
    </div>

    <script>
        document.getElementById('submitBtn').addEventListener('click', function() {
            const form = document.getElementById('registerForm');
            const formData = new FormData(form);

            // Validate inputs
            const firstname = formData.get('firstname').trim();
            const lastname = formData.get('lastname').trim();
            const email = formData.get('email').trim();
            const password = formData.get('password').trim();
            // const phone = formData.get('phone').trim();
            const role = formData.get('role');

            if (!firstname || !lastname || !email || !password || !role) {
                alert('All fields are required.');
                return;
            }

            if (!/^\S+@\S+\.\S+$/.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long.');
                return;
            }

            // if (!/^\d{10}$/.test(phone)) {
            //     alert('Phone number must be 10 digits.');
            //     return;
            // }

            // Submit form via AJAX
            fetch('api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Parsed response:', data);
                if (data.success) {
                    alert('Registration successful!');
                    closeModal();
                    form.reset();
                    // Redirect to index.php after success
                    window.location.href = 'index.php';
                } else {
                    alert(data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        function openModal() {
            document.getElementById("registerModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("registerModal").style.display = "none";
            history.back(); // This will redirect to the previous page
        }
        

        function toggleMode() {
            document.body.classList.toggle("light-mode");
        }

        // Close modal when clicking outside content
        window.onclick = function(event) {
            const modal = document.getElementById("registerModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
