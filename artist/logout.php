<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Log the user out
logout();

// Redirect to login page
header('Location: ' . SITE_URL . '/login.php');
exit;
?>

