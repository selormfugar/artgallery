<?php

header('Content-Type: application/json');

// Include database connection
require_once '../includes/config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Validate input
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid email format'
    ]);
    exit;
}

if (empty($password)) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Password is required'
    ]);
    exit;
}

// Check if database connection is established
if (!isset($pdo)) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection error'
    ]);
    exit;
}

// Prepare and execute query
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND archived = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("User data from DB: " . print_r($user, true));

    if ($user && password_verify($password, $user['password_hash'])) {
        // Successful login
     // After setting session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['email'] = $user['email'];


// Add debug logging
           // Optional: Update last login time (if column exists)
        // try {
        //     $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        //     $updateStmt->execute([$user['user_id']]);
        // } catch (PDOException $e) {
        //     // Log but don't fail the login if this update fails
        //     error_log("Last login update error: " . $e->getMessage());
        // }

        echo json_encode([
            'status' => 'success', 
            'role' => $user['role'],
            'message' => 'Login successful',
            'firstname' => $user['firstname']
        ]);
    } else {
        // Failed login
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid email or password'
        ]);
    }
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'An unexpected error occurred'
    ]);
}