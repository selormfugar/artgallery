<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in as a buyer to place bids']);
    exit;
}

// Validate input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['auction_id']) || !isset($input['amount'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$auction_id = (int)$input['auction_id'];
$amount = (float)$input['amount'];
$user_id = $_SESSION['user_id'];

// Get auction details using Database class methods
$db = new Database(); // Use your Database class directly
$auction = $db->selectOne(
    "SELECT a.*, art.title as artwork_title
     FROM auctions a
     JOIN artworks art ON a.artwork_id = art.artwork_id
     WHERE a.auction_id = ? AND a.archived = 0",
    [$auction_id]
);

if (!$auction) {
    echo json_encode(['status' => 'error', 'message' => 'Auction not found']);
    exit;
}

// Check if auction is active
if ($auction['status'] !== 'active') {
    echo json_encode(['status' => 'error', 'message' => 'This auction is not currently active']);
    exit;
}

// Check auction time
$now = new DateTime();
$end_time = new DateTime($auction['end_time']);
if ($now > $end_time) {
    // Update auction status if it's ended
    $db->update('auctions', ['status' => 'ended'], 'auction_id = ?', [$auction_id]);
   
    echo json_encode(['status' => 'error', 'message' => 'This auction has ended']);
    exit;
}

// Get current highest bid and bidder
$currentHighestBid = $db->selectOne(
    "SELECT * FROM bids 
     WHERE auction_id = ? AND is_winning = 1 
     ORDER BY amount DESC LIMIT 1",
    [$auction_id]
);

// Check if user is already the highest bidder
if ($currentHighestBid && $currentHighestBid['user_id'] == $user_id) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'You are already the highest bidder. Please wait until someone outbids you.'
    ]);
    exit;
}

// Get the current highest bid amount
$highestBid = $auction['current_bid'] ?? $auction['starting_price'];

// Validate bid amount
if ($amount <= $highestBid) {
    echo json_encode(['status' => 'error', 'message' => 'Bid amount must be higher than current bid']);
    exit;
}

// Check reserve price if set
if ($auction['reserve_price'] && $amount < $auction['reserve_price']) {
    echo json_encode(['status' => 'error', 'message' => 'Bid amount must meet or exceed reserve price']);
    exit;
}

try {
    // Get PDO connection for transaction handling
    $pdo = $db->getConnection();
    $pdo->beginTransaction();
   
    // Update auction with new bid
    $db->update('auctions', ['current_bid' => $amount], 'auction_id = ?', [$auction_id]);
   
    // Record the bid
    $bid_data = [
        'auction_id' => $auction_id,
        'user_id' => $user_id,
        'amount' => $amount,
        'bid_time' => date('Y-m-d H:i:s'),
        'is_winning' => 1
    ];
   
    $new_bid_id = $db->insert('bids', $bid_data);
   
    // Set all other bids for this auction as not winning
    $db->query(
        "UPDATE bids SET is_winning = 0 WHERE auction_id = ? AND bid_id != ?",
        [$auction_id, $new_bid_id]
    );
   
    $pdo->commit();
   
    echo json_encode([
        'status' => 'success',
        'message' => 'Bid placed successfully',
        'new_bid' => $amount,
        'artwork_title' => $auction['artwork_title']
    ]);
   
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Failed to place bid: ' . $e->getMessage()]);
}
?>