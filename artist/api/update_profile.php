<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $firstname = htmlspecialchars($_POST['firstname'] ?? '', ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars($_POST['lastname'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
   
    // Initialize database connection
    global $db;
    $pdo = $db->getConnection();
    $errors = [];
   
    // Validate required fields
    if (!$firstname || !$lastname) {
        $errors[] = "First and last name are required";
    }
   
    if (!$email) {
        $errors[] = "Invalid email format";
    }
    
    // Username validation
    if (!$username) {
        $errors[] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $errors[] = "Username must be 3-50 characters and can only contain letters, numbers, and underscores";
    } else {
        // Check if username is already taken by another user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->fetch()) {
            $errors[] = "This username is already taken";
        }
    }
    
    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        $errors[] = "This email is already registered by another user";
    }
   
    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: ../dashboard/settings.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }
   
    try {
        // Get current user data for comparison
        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $currentProfileImage = $stmt->fetchColumn(); // Use fetchColumn instead of fetch
        
        // Handle avatar upload using the uploadImage function
        $avatarPath = null;
        if (!empty($_FILES['avatar']['name'])) {
            $upload = uploadImage($_FILES['avatar']);
            if (!$upload['success']) {
                throw new Exception($upload['message']);
            }
            $avatarPath = $upload['path'];
            
            // Delete old image if exists
            if (!empty($currentProfileImage) && 
                $currentProfileImage != '/uploads/avatars/default.png') {
                $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . $currentProfileImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }
        
        // Update user in database
        if ($avatarPath) {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$username, $firstname, $lastname, $email, $avatarPath, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$username, $firstname, $lastname, $email, $userId]);
        }
        
        // Update session variables
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['username'] = $username;
        
        header("Location: ../dashboard/settings.php?success=Profile+updated+successfully");
        exit;
    } catch (Exception $e) {
        $_SESSION['profile_errors'] = [$e->getMessage()];
        header("Location: ../dashboard/settings.php?error=" . urlencode($e->getMessage()));
        exit;
    } catch (PDOException $e) {
        $_SESSION['profile_errors'] = ["Database error: Unable to update profile."];
        error_log("Profile update error: " . $e->getMessage());
        header("Location: ../dashboard/settings.php?error=Database+error");
        exit;
    }
}
header("Location: ../dashboard/settings.php");
exit;