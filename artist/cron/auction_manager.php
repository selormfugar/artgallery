<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();

// 1. Activate pending auctions that should start
$db->update(
    "auctions",
    ['status' => 'active'],
    "status = 'pending' AND start_time <= NOW() AND archived = 0"
);

// 2. Process completed auctions
$completedAuctions = $db->select(
    "SELECT * FROM auctions 
     WHERE status = 'active' AND end_time <= NOW() AND archived = 0"
);

foreach ($completedAuctions as $auction) {
    // Get the winning bid
    $winningBid = $db->selectOne(
        "SELECT * FROM bids 
         WHERE auction_id = ? 
         ORDER BY amount DESC LIMIT 1",
        [$auction['auction_id']]
    );

    // Update auction status
    $db->update(
        "auctions",
        [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ],
        "auction_id = ?",
        [$auction['auction_id']]
    );

    // If there's a winning bid
    if ($winningBid) {
        // Check if reserve price was met (if set)
        $reserveMet = !$auction['reserve_price'] || 
                      $winningBid['amount'] >= $auction['reserve_price'];
        
        if ($reserveMet) {
            // Create a sale record (you'll need a sales table)
            $db->insert("sales", [
                'artwork_id' => $auction['artwork_id'],
                'buyer_id' => $winningBid['user_id'],
                'seller_id' => $db->selectOne(
                    "SELECT artist_id FROM artworks WHERE artwork_id = ?",
                    [$auction['artwork_id']]
                )['artist_id'],
                'amount' => $winningBid['amount'],
                'sale_type' => 'auction',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Mark artwork as sold
            $db->update(
                "artworks",
                [
                    'is_for_sale' => 0,
                    'is_for_auction' => 0,
                    'price' => $winningBid['amount']
                ],
                "artwork_id = ?",
                [$auction['artwork_id']]
            );
            
            // TODO: Send notifications to buyer and seller
        } else {
            // Reserve not met - notify seller
            // TODO: Send notification
        }
    } else {
        // No bids - notify seller
        // TODO: Send notification
    }
}

// Log execution
file_put_contents(
    __DIR__ . '/auction_manager.log',
    date('Y-m-d H:i:s') . " - Processed auctions\n",
    FILE_APPEND
);