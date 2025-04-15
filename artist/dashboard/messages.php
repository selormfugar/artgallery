<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Check if user is logged in and is an artist
requireArtist();

// Get conversations
global $db;
$conversations = $db->select("
   SELECT 
    CASE 
        WHEN m.sender_id = ? THEN m.receiver_id
        ELSE m.sender_id
    END AS user_id,
    CONCAT(u.firstname, ' ', u.lastname) AS full_name,
    MAX(m.created_at) AS last_message_time,
    COUNT(CASE WHEN m.receiver_id = ? AND m.seen = 0 THEN 1 END) AS unread_count
FROM messages m
JOIN users u ON (
    CASE 
        WHEN m.sender_id = ? THEN m.receiver_id
        ELSE m.sender_id
    END = u.user_id
)
WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.archived = 0
GROUP BY 
    CASE 
        WHEN m.sender_id = ? THEN m.receiver_id
        ELSE m.sender_id
    END,
    u.firstname,
    u.lastname
ORDER BY last_message_time DESC
", 
    [$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]
);

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Messages</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Conversations</h6>
                    <button class="btn btn-sm btn-outline-primary" id="newMessageBtn">
                        <i class="fas fa-plus"></i> New Message
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="conversationsList">
                        <?php if (count($conversations) > 0): ?>
                            <?php foreach ($conversations as $conversation): ?>
                                <a href="#" class="list-group-item list-group-item-action conversation-item" data-user-id="<?php echo $conversation['user_id']; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative">
                                                <img src="<?php echo !empty($conversation['profile_image']) ? UPLOAD_URL . $conversation['profile_image'] : SITE_URL . '/uploads/avatars/default.png'; ?>" 
                                                     alt="<?php echo $conversation['full_name']; ?>" 
                                                     class="rounded-circle me-2" 
                                                     width="40" height="40">
                                                <?php if ($conversation['unread_count'] > 0): ?>
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        <?php echo $conversation['unread_count']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo $conversation['full_name']; ?></h6>
                                                <small class="text-muted">
                                                    <?php 
                                                    $lastMessageTime = strtotime($conversation['last_message_time']);
                                                    $now = time();
                                                    $diff = $now - $lastMessageTime;
                                                    
                                                    if ($diff < 60) {
                                                        echo 'Just now';
                                                    } elseif ($diff < 3600) {
                                                        echo floor($diff / 60) . ' min ago';
                                                    } elseif ($diff < 86400) {
                                                        echo floor($diff / 3600) . ' hours ago';
                                                    } else {
                                                        echo date('M j', $lastMessageTime);
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <p class="mb-0">No conversations yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary" id="conversationTitle">Select a conversation</h6>
                </div>
                <div class="card-body">
                    <div id="messageContainer" class="mb-3" style="height: 400px; overflow-y: auto;">
                        <div class="text-center p-5">
                            <i class="fas fa-comments fa-3x text-gray-300 mb-3"></i>
                            <p>Select a conversation to view messages or start a new one.</p>
                        </div>
                    </div>
                    
                    <form id="messageForm" class="d-none">
                        <input type="hidden" id="receiverId" name="receiver_id">
                        <div class="input-group">
                            <textarea class="form-control" id="messageContent" name="content" placeholder="Type your message..." rows="2" required></textarea>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- New Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newMessageModalLabel">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newMessageForm">
                        <div class="mb-3">
                            <label for="recipientSelect" class="form-label">Recipient:</label>
                            <select class="form-select" id="recipientSelect" name="receiver_id" required>
                                <option value="">Select a recipient</option>
                                <?php
                                // Get list of buyers who have purchased from this artist
                                $buyers = $db->select("
                                    SELECT DISTINCT u.user_id, u.email
                                    FROM users u
                                    JOIN orders o ON u.user_id = o.buyer_id
                                    JOIN artworks a ON o.artwork_id = a.artwork_id
                                    WHERE a.artist_id = ? AND u.archived = 0
                                    ORDER BY u.email", 
                                    [$_SESSION['artist_id']]
                                );
                                
                                foreach ($buyers as $buyer) {
                                    echo '<option value="' . $buyer['user_id'] . '">' . $buyer['full_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newMessageContent" class="form-label">Message:</label>
                            <textarea class="form-control" id="newMessageContent" name="content" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendNewMessageBtn">Send Message</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationItems = document.querySelectorAll('.conversation-item');
    const messageContainer = document.getElementById('messageContainer');
    const conversationTitle = document.getElementById('conversationTitle');
    const messageForm = document.getElementById('messageForm');
    const receiverId = document.getElementById('receiverId');
    const messageContent = document.getElementById('messageContent');
    
    // New Message Modal
    const newMessageBtn = document.getElementById('newMessageBtn');
    const sendNewMessageBtn = document.getElementById('sendNewMessageBtn');
    
    let currentUserId = null;
    let messageInterval = null;
    
    // Load conversation when clicking on a conversation item
    conversationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Set active conversation
            conversationItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Get user ID
            currentUserId = this.getAttribute('data-user-id');
            const email = this.querySelector('h6').textContent;
            
            // Update conversation title
            conversationTitle.textContent = `Conversation with ${email}`;
            
            // Show message form
            messageForm.classList.remove('d-none');
            receiverId.value = currentUserId;
            
            // Load messages
            loadMessages(currentUserId);
            
            // Set up auto-refresh
            if (messageInterval) {
                clearInterval(messageInterval);
            }
            
            messageInterval = setInterval(() => {
                loadMessages(currentUserId, true);
            }, 10000); // Refresh every 10 seconds
        });
    });
    
    // Load messages function
    function loadMessages(userId, silent = false) {
    fetch(`../api/messages.php?action=conversation&user_id=${userId}`)
        .then(response => response.json())
        .then(messages => {
            if (!silent) {
                messageContainer.innerHTML = '';
            }

            if (messages.length === 0) {
                messageContainer.innerHTML = `
                    <div class="text-center p-5">
                        <p>No messages yet. Start the conversation.</p>
                    </div>`;
                return;
            }

            const messageHtml = messages.map(msg => {
                const isSender = msg.sender_id == <?php echo $_SESSION['user_id']; ?>;
                return `
                    <div class="mb-2 d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'}">
                        <div class="p-2 rounded ${isSender ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 75%;">
                            <div>${msg.content}</div>
                            <small class="text-muted d-block mt-1 text-end">${new Date(msg.created_at).toLocaleString()}</small>
                        </div>
                    </div>`;
            }).join('');

            messageContainer.innerHTML = messageHtml;
            messageContainer.scrollTop = messageContainer.scrollHeight;
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!messageContent.value.trim()) {
            return;
        }
        
        const formData = new FormData(this);
        formData.append('action', 'send');
        
        fetch('../api/messages.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear message input
                messageContent.value = '';
                
                // Reload messages
                loadMessages(currentUserId);
            } else {
                alert('Error sending message: ' + data.message);
            }
        });
    });
    
    // New Message Modal
    newMessageBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('newMessageModal'));
        modal.show();
    });
    
    // Send New Message
    sendNewMessageBtn.addEventListener('click', function() {
        const form = document.getElementById('newMessageForm');
        const recipientId = document.getElementById('recipientSelect').value;
        const content = document.getElementById('newMessageContent').value;
        
        if (!recipientId || !content.trim()) {
            alert('Please select a recipient and enter a message.');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'send');
        formData.append('receiver_id', recipientId);
        formData.append('content', content);
        
        fetch('../api/messages.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('newMessageModal')).hide();
                
                // Clear form
                document.getElementById('newMessageContent').value = '';
                
                // Reload conversations list
                window.location.reload();
            } else {
                alert('Error sending message: ' + data.message);
            }
        });
    });
    
    // Update unread count in sidebar
    function updateUnreadCount() {
        fetch('../api/messages.php?action=list')
            .then(response => response.json())
            .then(data => {
                let unreadCount = 0;
                
                data.messages.forEach(message => {
                    if (message.receiver_id == <?php echo $_SESSION['user_id']; ?> && message.seen == 0) {
                        unreadCount++;
                    }
                });
                
                // Update sidebar badge
                const sidebarBadge = document.querySelector('.nav-link[href*="messages.php"] .badge');
                if (sidebarBadge) {
                    if (unreadCount > 0) {
                        sidebarBadge.textContent = unreadCount;
                        sidebarBadge.classList.remove('d-none');
                    } else {
                        sidebarBadge.classList.add('d-none');
                    }
                }
            });
    }
    
    // Initial unread count update
    updateUnreadCount();
});
</script>

<?php include_once '../includes/footer.php'; ?>

