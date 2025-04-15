<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Get action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_pending_artworks':
            getPendingArtworks();
            break;
        case 'approve_artwork':
            approveArtwork();
            break;
        case 'reject_artwork':
            rejectArtwork();
            break;
        case 'get_artwork_details':
            getArtworkDetails();
            break;
        default:
            jsonResponse(false, null, 'Invalid action');
    }
} catch (Exception $e) {
    jsonResponse(false, null, $e->getMessage());
}

// Get pending artworks for moderation
function getPendingArtworks() {
    global $db;
    
    $sql = "SELECT a.*, u.firstname, u.lastname 
            FROM artworks a 
            JOIN artists ar ON a.artist_id = ar.artist_id
            JOIN users u ON ar.user_id = u.user_id
            WHERE a.moderation_status = 'pending'
            ORDER BY a.created_at ASC";
    
    $artworks = $db->select($sql);
    
    jsonResponse(true, $artworks);
}

// Approve an artwork
function approveArtwork() {
    global $db;
    
    $artworkId = $_POST['artwork_id'] ?? null;
    $adminId = $_SESSION['user_id'] ?? null;
    
    if (!$artworkId || !$adminId) {
        throw new Exception('Missing required parameters');
    }
    
    $db->beginTransaction();
    
    try {
        // Update artwork status
        $db->update('artworks', 
            ['moderation_status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')], 
            'artwork_id = ?', 
            [$artworkId]
        );
        
        // Get artist ID for logging
        $artistData = $db->selectOne(
            "SELECT ar.user_id, a.title 
             FROM artworks a
             JOIN artists ar ON a.artist_id = ar.artist_id
             WHERE a.artwork_id = ?",
            [$artworkId]
        );
        
        if (!$artistData) {
            throw new Exception('Artist data not found');
        }
        
        // Log admin action
        $db->insert('adminactions', [
            'admin_id' => $adminId,
            'target_user_id' => $artistData['user_id'],
            'action_taken' => 'approve_artwork',
            'reason' => 'Artwork meets guidelines',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Notify artist
        $db->insert('notifications', [
            'user_id' => $artistData['user_id'],
            'message' => 'Your artwork "' . $artistData['title'] . '" has been approved and is now live.',
            'seen' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $db->commit();
        jsonResponse(true, ['message' => 'Artwork approved successfully']);
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

// Reject an artwork
function rejectArtwork() {
    global $db;
    
    $artworkId = $_POST['artwork_id'] ?? null;
    $reason = $_POST['reason'] ?? null;
    $adminId = $_SESSION['user_id'] ?? null;
    
    if (!$artworkId || !$reason || !$adminId) {
        throw new Exception('Missing required parameters');
    }
    
    $db->beginTransaction();
    
    try {
        // Update artwork status
        $db->update('artworks', 
            ['moderation_status' => 'rejected', 'updated_at' => date('Y-m-d H:i:s')], 
            'artwork_id = ?', 
            [$artworkId]
        );
        
        // Get artist ID for logging
        $artistData = $db->selectOne(
            "SELECT ar.user_id, a.title 
             FROM artworks a
             JOIN artists ar ON a.artist_id = ar.artist_id
             WHERE a.artwork_id = ?",
            [$artworkId]
        );
        
        if (!$artistData) {
            throw new Exception('Artist data not found');
        }
        
        // Log admin action
        $db->insert('adminactions', [
            'admin_id' => $adminId,
            'target_user_id' => $artistData['user_id'],
            'action_taken' => 'reject_artwork',
            'reason' => $reason,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Notify artist
        $db->insert('notifications', [
            'user_id' => $artistData['user_id'],
            'message' => 'Your artwork "' . $artistData['title'] . '" was rejected. Reason: ' . $reason,
            'seen' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $db->commit();
        jsonResponse(true, ['message' => 'Artwork rejected successfully']);
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

// Get detailed artwork information
function getArtworkDetails() {
    global $db;
    
    $artworkId = $_GET['artwork_id'] ?? null;
    
    if (!$artworkId) {
        throw new Exception('Artwork ID required');
    }
    
    $sql = "SELECT a.*, u.firstname, u.lastname, u.user_id as artist_user_id
            FROM artworks a
            JOIN artists ar ON a.artist_id = ar.artist_id
            JOIN users u ON ar.user_id = u.user_id
            WHERE a.artwork_id = ?";
    
    $artwork = $db->selectOne($sql, [$artworkId]);
    
    if (!$artwork) {
        throw new Exception('Artwork not found');
    }
    
    jsonResponse(true, $artwork);
}
?>