<?php
// Include database connection
require_once '../includes/db.php';
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form-data input
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'user'; // Default to 'user' if not specified

    // Validate input
    $errors = [];
    if (empty($firstname)) $errors[] = 'First name is required';
    if (empty($lastname)) $errors[] = 'Last name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Password strength check
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    // Allowed roles
    $allowedRoles = ['user', 'artist', 'admin'];
    if (!in_array($role, $allowedRoles)) {
        $role = 'user'; // Default to user if invalid role
    }

    // Check for existing email
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = ?";
        $checkStmt = $pdo->prepare($checkEmailQuery);
        $checkStmt->execute([$email]);
        $emailCount = $checkStmt->fetchColumn();
        
        if ($emailCount > 0) {
            $errors[] = 'Email already registered';
        }

        // If there are errors, return them
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert into database
        $query = "INSERT INTO users (firstname, lastname, email, password_hash, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $firstname,
            $lastname,
            $email,
            $hashedPassword,
            $role
        ]);

        // Retrieve the newly created user
        $userId = $pdo->lastInsertId();
        $query = "SELECT user_id, firstname, lastname, email, role, created_at, archived FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Successful registration response
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user
        ]);
        
    } catch (PDOException $e) {
        // Log the error (in a production environment)
        error_log('Registration error: ' . $e->getMessage());
        
        // Return generic error to client
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred during registration'
        ]);
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed'
    ]);
}