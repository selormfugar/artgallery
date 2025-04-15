<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate new password
    if (strlen($newPassword) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = "New passwords do not match";
    }

    global $db;
    $pdo = $db->getConnection();
    $errors = [];
    
    // Verify current password
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $currentHash = $stmt->fetchColumn();
    
    if (!password_verify($currentPassword, $currentHash)) {
        $errors[] = "Current password is incorrect";
    }
    
    if (!empty($errors)) {
        $_SESSION['password_errors'] = $errors;
        header("Location: ../dashboard/settings.php?error=" . urlencode(implode(", ", $errors)) . "#password-tab");
        exit;
    }
    
    // Update password
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->execute([$newHash, $userId]);
    
    header("Location: ../dashboard/settings.php?success=Password+updated+successfully#password-tab");
    exit;
}

header("Location: ../dashboard/settings.php");
exit;