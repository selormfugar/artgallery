<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'list') {
        // Get messages
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        $messages = getMessages($_SESSION['user_id'], $limit, $offset);
        
        // Get total count for pagination
        global $db;
        $totalMessages = $db->selectOne("
            SELECT COUNT(*) as count 
            FROM messages 
            WHERE (sender_id = ? OR receiver_id = ?) AND archived = 0", 
            [$_SESSION['user_id'], $_SESSION['user_id']]
        );
        
        echo json_encode([
            'messages' => $messages,
            'total' => $totalMessages['count'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($totalMessages['count'] / $limit)
        ]);
    } elseif ($action === 'conversation' && isset($_GET['user_id'])) {
        // Get conversation with specific user
        $otherUserId = (int)$_GET['user_id'];
        
        global $db;
        $messages = $db->select("
            SELECT m.*, 
                   u_sender.username as sender_name,
                   u_receiver.username as receiver_name
            FROM messages m
            JOIN users u_sender ON m.sender_id = u_sender.user_id
            JOIN users u_receiver ON m.receiver_id = u_receiver.user_id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
                  AND m.archived = 0
            ORDER BY m.created_at ASC", 
            [$_SESSION['user_id'], $otherUserId, $otherUserId, $_SESSION['user_id']]
        );
        
        // Mark messages as read
        $db->update(
            'messages', 
            ['seen' => 1], 
            'sender_id = ? AND receiver_id = ? AND seen = 0', 
            [$otherUserId, $_SESSION['user_id']]
        );
        
        echo json_encode($messages);
    } else {
        $response['message'] = 'Invalid action.';
        echo json_encode($response);
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'send') {
        // Send message
        $receiverId = (int)$_POST['receiver_id'];
        $content = sanitizeInput($_POST['content']);
        
        if (empty($content)) {
            $response['message'] = 'Message content cannot be empty.';
            echo json_encode($response);
            exit;
        }
        
        global $db;
        $messageData = [
            'sender_id' => $_SESSION['user_id'],
            'receiver_id' => $receiverId,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $messageId = $db->insert('messages', $messageData);
        
        if ($messageId) {
            $response['success'] = true;
            $response['message'] = 'Message sent successfully.';
            $response['data'] = $messageId;
        } else {
            $response['message'] = 'Error sending message.';
        }
        
        echo json_encode($response);
    } elseif ($action === 'mark_read') {
        // Mark message as read
        $messageId = (int)$_POST['message_id'];
        
        global $db;
        $result = $db->update(
            'messages', 
            ['seen' => 1], 
            'message_id = ? AND receiver_id = ?', 
            [$messageId, $_SESSION['user_id']]
        );
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Message marked as read.';
        } else {
            $response['message'] = 'Error marking message as read.';
        }
        
        echo json_encode($response);
    } elseif ($action === 'delete') {
        // Delete message (soft delete)
        $messageId = (int)$_POST['message_id'];
        
        global $db;
        $result = $db->update(
            'messages', 
            ['archived' => 1], 
            'message_id = ? AND (sender_id = ? OR receiver_id = ?)', 
            [$messageId, $_SESSION['user_id'], $_SESSION['user_id']]
        );
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Message deleted successfully.';
        } else {
            $response['message'] = 'Error deleting message.';
        }
        
        echo json_encode($response);
    } else {
        $response['message'] = 'Invalid action.';
        echo json_encode($response);
    }
}
?>

