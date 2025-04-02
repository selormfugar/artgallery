<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        // Place a new bid
        $auctionId = (int)$_POST['auction_id'];
        $amount = (float)$_POST['amount'];
        $userId = $_SESSION['user_id'];
        
        // Validate auction
        $auction = $db->selectOne("
            SELECT * FROM auctions 
            WHERE auction_id = ? AND status = 'active' AND end_time > NOW() AND archived = 0
        ", [$auctionId]);
        
        if (!$auction) {
            throw new Exception("Auction is not active or has ended");
        }
        
        // Validate bid amount
        $minBid = $auction['current_bid'] ?? $auction['starting_price'];
        $minBid += $auction['current_bid'] ? ($auction['current_bid'] * 0.05) : 0; // 5% increment
        
        if ($amount < $minBid) {
            throw new Exception("Bid must be at least " . formatCurrency($minBid));
        }
        
        // Start transaction
        $db->getConnection()->beginTransaction();
        
        try {
            // Insert new bid
            $db->insert('bids', [
                'auction_id' => $auctionId,
                'user_id' => $userId,
                'amount' => $amount,
                'bid_time' => date('Y-m-d H:i:s'),
                'is_winning' => 1
            ]);
            
            // Update previous winning bids
            $db->update('bids', 
                ['is_winning' => 0], 
                'auction_id = ? AND is_winning = 1 AND user_id != ?',
                [$auctionId, $userId]
            );
            
            // Update auction current bid
            $db->update('auctions', [
                'current_bid' => $amount,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'auction_id = ?', [$auctionId]);
            
            $db->getConnection()->commit();
            
            $response['success'] = true;
            $response['message'] = 'Bid placed successfully';
            $response['new_bid'] = $amount;
            $response['next_min_bid'] = $amount + ($amount * 0.05); // 5% increment for next bid
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            throw $e;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['auction_id'])) {
        // Get bid history
        $auctionId = (int)$_GET['auction_id'];
        
        $bids = $db->select("
            SELECT b.*, u.username, u.avatar 
            FROM bids b
            JOIN users u ON b.user_id = u.user_id
            WHERE b.auction_id = ? AND b.archived = 0
            ORDER BY b.amount DESC
            LIMIT 50
        ", [$auctionId]);
        
        $response['success'] = true;
        $response['bids'] = $bids;
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>