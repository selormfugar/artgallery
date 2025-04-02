<?php
// Main entry point - redirects to login or dashboard
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (isLoggedIn()) {
    // Check if user is a buyer
    if (isBuyer()) {
        // Redirect to dashboard
        header('Location: ' . SITE_URL . '/buyer/dashboard');
    } else {
        // Not a buyer account - show error and logout
        logout();
        header('Location: ' . SITE_URL . '/login.php?error=not_buyer');
    }
} else {
    // Not logged in - redirect to login page
    header('Location: ' . SITE_URL . '/login.php');
}

exit;
?>

