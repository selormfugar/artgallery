<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth_check.php';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to manage your wishlist']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$artwork_id = $data['artwork_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Check current state
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND artwork_id = ?");
$stmt->execute([$user_id, $artwork_id]);
$is_in_wishlist = $stmt->fetchColumn() > 0;

if ($is_in_wishlist) {
    // Remove from wishlist
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND artwork_id = ?");
    $stmt->execute([$user_id, $artwork_id]);
    echo json_encode(['status' => 'success', 'action' => 'removed', 'count' => getWishlistCount($pdo, $user_id)]);
} else {
    // Add to wishlist
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, artwork_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $artwork_id]);
    echo json_encode(['status' => 'success', 'action' => 'added', 'count' => getWishlistCount($pdo, $user_id)]);
}

function getWishlistCount($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}