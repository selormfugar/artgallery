<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$artwork_id = isset($_POST['artwork_id']) ? (int)$_POST['artwork_id'] : 0;
$collection_id = isset($_POST['collection_id']) ? (int)$_POST['collection_id'] : 0;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

if ($artwork_id <= 0 || $collection_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid artwork or collection']);
    exit;
}

// Verify user owns the collection
$stmt = $pdo->prepare("SELECT 1 FROM collection_folders WHERE folder_id = ? AND user_id = ?");
$stmt->execute([$collection_id, $user_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Collection not found']);
    exit;
}

// Check if artwork exists
$stmt = $pdo->prepare("SELECT 1 FROM artworks WHERE artwork_id = ?");
$stmt->execute([$artwork_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Artwork not found']);
    exit;
}

try {
    // Check if already in collection
    $stmt = $pdo->prepare("
        SELECT 1 FROM collection_folder_items 
        WHERE folder_id = ? AND collection_id IN (
            SELECT collection_id FROM user_collections 
            WHERE artwork_id = ? AND user_id = ?
        )
    ");
    $stmt->execute([$collection_id, $artwork_id, $user_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Artwork already in this collection']);
        exit;
    }
    
    // Add to user_collections if not already there
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO user_collections 
        (user_id, artwork_id, notes) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $artwork_id, $notes]);
    
    $collection_id = $stmt->rowCount() > 0 ? $pdo->lastInsertId() : 
        $pdo->query("SELECT collection_id FROM user_collections WHERE user_id = $user_id AND artwork_id = $artwork_id")->fetchColumn();
    
    // Add to folder
    $stmt = $pdo->prepare("
        INSERT INTO collection_folder_items 
        (folder_id, collection_id) 
        VALUES (?, ?)
    ");
    $stmt->execute([$collection_id, $collection_id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Error adding to collection: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding to collection']);
}
?>