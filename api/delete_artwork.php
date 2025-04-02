<?php
header('Content-Type: application/json');
include './config/db_connection.php'; // Include your database connection file

$artworkId = $_GET['id'];
$sql = "UPDATE artworks SET archived = 1 WHERE artwork_id = $artworkId";
if ($conn->query($sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?>