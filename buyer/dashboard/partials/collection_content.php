<?php
// Ensure buyer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../includes/config.php';

// Get buyer's collection
$buyer_id = $_SESSION['user_id'];
$sql = "SELECT a.*, t.created_at 
        FROM artworks a 
        INNER JOIN orders t ON a.artwork_id = t.artwork_id 
        WHERE t.buyer_id = ?
        ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$buyer_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="collection-container">
    <h2>My Art Collection</h2>
    
    <?php if (count($result) > 0): ?>
        <div class="artwork-grid">
            <?php foreach ($result as $artwork): ?>
                <div class="artwork-card">
                    <img src="<?php echo htmlspecialchars($artwork['image_url']); ?>" alt="<?php echo htmlspecialchars($artwork['title']); ?>">
                    <h3><?php echo htmlspecialchars($artwork['title']); ?></h3>
                    <p>Artist: <?php echo htmlspecialchars($artwork['artist_name']); ?></p>
                    <p>Purchased: <?php echo date('M d, Y', strtotime($artwork['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-items">Your collection is empty. Start collecting beautiful artworks!</p>
    <?php endif; ?>
</div>

