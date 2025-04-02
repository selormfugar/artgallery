<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'artmarketplace');

// Application configuration
define('SITE_NAME', 'Artist Dashboard');
define('SITE_URL', 'http://localhost/artgallery');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/artgallery/images/');
define('UPLOAD_URL', SITE_URL . '/images/');

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');

define('MAX_UPLOAD_SIZE', 5000000); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

