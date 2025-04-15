<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$artworkId = $_GET['id'];
$sql = "SELECT * FROM artworks WHERE artwork_id = $artworkId";
$result = $conn->query($sql);

$artwork = $result->fetch_assoc();
echo json_encode($artwork);
$conn->close();
?>