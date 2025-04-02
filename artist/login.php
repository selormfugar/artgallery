<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard/index.php');
    exit;
}

// Initialize variables
$email = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Attempt to login
        if (login($email, $password)) {
            // Check if user is an artist
            if (isArtist()) {
                // Redirect to dashboard
                header('Location: ' . SITE_URL . '/dashboard/index.php');
                exit;
            } else {
                // Not an artist account
                $error = 'This account does not have artist privileges.';
                logout(); // Log them out
            }
        } else {
            // Invalid credentials
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Login - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .form-signin {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            border-radius: 1rem 1rem 0 0 !important;
            background-color: #4e73df;
            color: white;
        }
    </style>
</head>
<body>
    <main class="form-signin">
        <div class="card">
            <div class="card-header text-center py-3">
                <h2 class="h4 mb-0">Artist Dashboard</h2>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img class="logo" src="<?php echo SITE_URL; ?>/assets/img/logo.png" alt="Logo">
                    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="post">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo $email; ?>" required>
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="remember-me" id="rememberMe" name="remember_me">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    
                    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">Forgot password?</a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3 bg-light">
                <p class="mb-0">Don't have an account? <a href="#" class="text-decoration-none">Contact admin</a></p>
            </div>
        </div>
        
        <p class="mt-4 text-center text-muted">&copy; <?php echo date('Y'); ?> Art Marketplace</p>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

