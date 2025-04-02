/**
 * Add artwork to wishlist with login check
 * @param {number} artworkId - ID of the artwork to add
 */
function addToWishlist(artworkId) {
    // Check if user is logged in
    $.ajax({
        url: 'api/check_auth.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.logged_in) {
                // User is logged in - proceed with adding to wishlist
                addToWishlistProcess(artworkId);
            } else {
                // User not logged in - show login modal with redirect back
                showLoginModal(artworkId);
            }
        },
        error: function() {
            alert('Error checking authentication status');
        }
    });
}

/**
 * Actual wishlist addition process
 * @param {number} artworkId - ID of the artwork to add
 */
function addToWishlistProcess(artworkId) {
    $.ajax({
        url: 'api/add_to_wishlist.php',
        type: 'POST',
        data: { 
            artwork_id: artworkId,
            csrf_token: '<?= generate_csrf_token() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showWishlistSuccess(response.message);
                updateWishlistCount(response.wishlist_count);
            } else {
                showWishlistError(response.message);
            }
        },
        error: function() {
            showWishlistError('Error communicating with server');
        }
    });
}

/**
 * Show login modal with redirect back functionality
 * @param {number} artworkId - ID of the artwork to add after login
 */
function showLoginModal(artworkId) {
    // Store artwork ID in sessionStorage for after login
    sessionStorage.setItem('pending_wishlist_item', artworkId);
    
    // Show login modal (implementation depends on your UI)
    $('#loginModal').modal('show');
    
    // Or redirect to login page with return URL
    // window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
}

/**
 * Update wishlist count in UI
 * @param {number} count - New wishlist count
 */
function updateWishlistCount(count) {
    const $wishlistCount = $('#wishlist-count');
    if ($wishlistCount.length) {
        $wishlistCount.text(count);
        $wishlistCount.removeClass('hidden');
    }
}

/**
 * Show success message
 * @param {string} message - Success message
 */
function showWishlistSuccess(message) {
    // Implement your notification system here
    alert(message); // Simple alert for demonstration
}

/**
 * Show error message
 * @param {string} message - Error message
 */
function showWishlistError(message) {
    // Implement your notification system here
    alert('Error: ' + message); // Simple alert for demonstration
}

// Check for pending wishlist items after login
function checkPendingWishlistItem() {
    const pendingItem = sessionStorage.getItem('pending_wishlist_item');
    if (pendingItem) {
        addToWishlistProcess(pendingItem);
        sessionStorage.removeItem('pending_wishlist_item');
    }
}

// Call this after successful login
// Example: In your login success callback:
// checkPendingWishlistItem();