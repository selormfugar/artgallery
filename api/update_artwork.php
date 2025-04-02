<?php
header('Content-Type: application/json');
include './config/db_connection.php'; // Include your database connection file

$artworkId = $_GET['id'];
$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'];
$description = $data['description'];
$image_url = $data['image_url'];

$sql = "UPDATE artworks SET title='$title', description='$description', image_url='$image_url' WHERE artwork_id=$artworkId";
if ($conn->query($sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?>