<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$folder_name = isset($_POST['folder_name']) ? trim($_POST['folder_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$is_public = isset($_POST['is_public']) ? 1 : 0;

if (empty($folder_name)) {
    echo json_encode(['success' => false, 'message' => 'Collection name is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO collection_folders 
        (user_id, folder_name, description, is_public) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $folder_name, $description, $is_public]);
    
    echo json_encode(['success' => true, 'folder_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    error_log("Error creating collection: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error creating collection']);
}
?>