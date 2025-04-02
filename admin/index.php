<?php
// Main entry point - redirects to login or dashboard
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (isLoggedIn()) {
    // Redirect to dashboard
    header('Location: ' . SITE_URL . '/admin/index.php');
} else {
    // Not logged in - redirect to login page
    header('Location: ' . SITE_URL . '/login.php');
}

exit;
?>

