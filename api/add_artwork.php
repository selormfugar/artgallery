<?php
header('Content-Type: application/json');
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'];
$description = $data['description'];
$image_url = $data['image_url'];

$sql = "INSERT INTO artworks (title, description, image_url) VALUES ('$title', '$description', '$image_url')";
if ($conn->query($sql) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?>