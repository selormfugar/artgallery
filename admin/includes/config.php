<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'artmarketplace');

// Application configuration
define('SITE_NAME', 'Art Marketplace Admin');
define('SITE_URL', 'http://localhost/artgallery');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/artgallery/images/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');

// Admin roles and permissions
define('ROLE_SUPER_ADMIN', 1);
define('ROLE_ADMIN', 2);
define('ROLE_MODERATOR', 3);
define('ROLE_ANALYST', 4);

// Permission constants
define('PERM_MANAGE_USERS', 'manage_users');
define('PERM_MODERATE_CONTENT', 'moderate_content');
define('PERM_VIEW_REPORTS', 'view_reports');
define('PERM_MANAGE_TRANSACTIONS', 'manage_transactions');
define('PERM_MANAGE_SETTINGS', 'manage_settings');
define('PERM_VIEW_FLAGGED', 'view_flagged');

// Define role permissions
$GLOBALS['role_permissions'] = [
    ROLE_SUPER_ADMIN => [
        PERM_MANAGE_USERS,
        PERM_MODERATE_CONTENT,
        PERM_VIEW_REPORTS,
        PERM_MANAGE_TRANSACTIONS,
        PERM_MANAGE_SETTINGS,
        PERM_VIEW_FLAGGED
    ],
    ROLE_ADMIN => [
        PERM_MANAGE_USERS,
        PERM_MODERATE_CONTENT,
        PERM_VIEW_REPORTS,
        PERM_MANAGE_TRANSACTIONS,
        PERM_VIEW_FLAGGED
    ],
    ROLE_MODERATOR => [
        PERM_MODERATE_CONTENT,
        PERM_VIEW_FLAGGED
    ],
    ROLE_ANALYST => [
        PERM_VIEW_REPORTS
    ]
];
?>

