<?php
header('Content-Type: application/json');
include './config/db_connection.php'; // Include your database connection file

$artworkId = $_GET['id'];
$sql = "SELECT * FROM artworks WHERE artwork_id = $artworkId";
$result = $conn->query($sql);

$artwork = $result->fetch_assoc();
echo json_encode($artwork);
$conn->close();
?>