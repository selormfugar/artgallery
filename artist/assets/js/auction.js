document.addEventListener('DOMContentLoaded', function() {
    // Toggle auction fields
    const auctionToggle = document.getElementById('editIsForAuction');
    const auctionFields = document.getElementById('auctionFields');
    
    if (auctionToggle && auctionFields) {
        auctionToggle.addEventListener('change', function() {
            auctionFields.style.display = this.checked ? 'block' : 'none';
            
            if (this.checked) {
                // Set default auction times (now + 7 days)
                const now = new Date();
                const endDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
                
                document.getElementById('editAuctionStart').value = formatDateTimeLocal(now);
                document.getElementById('editAuctionEnd').value = formatDateTimeLocal(endDate);
            }
        });
    }
    
    // Format date for datetime-local input
    function formatDateTimeLocal(date) {
        return date.toISOString().slice(0, 16);
    }
    
    // Load artwork data for editing
    document.querySelectorAll('.edit-artwork').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            
            fetch(`../api/artworks.php?action=get&id=${artworkId}`)
                .then(response => response.json())
                .then(artwork => {
                    // Populate form fields
                    document.getElementById('editArtworkId').value = artwork.artwork_id;
                    document.getElementById('editTitle').value = artwork.title;
                    document.getElementById('editCategory').value = artwork.category_id;
                    document.getElementById('editPrice').value = artwork.price;
                    document.getElementById('editDescription').value = artwork.description;
                    document.getElementById('editCurrentImage').src = artwork.image_url 
                        ? `<?= UPLOAD_URL ?>${artwork.image_url}` 
                        : '../assets/img/placeholder.svg';
                    document.getElementById('editIsForSale').checked = artwork.is_for_sale == 1;
                    
                    // Handle auction data
                    const hasAuction = artwork.auction_id && artwork.auction_status !== 'cancelled';
                    document.getElementById('editIsForAuction').checked = hasAuction;
                    
                    if (hasAuction) {
                        document.getElementById('editStartingPrice').value = artwork.starting_price;
                        document.getElementById('editReservePrice').value = artwork.reserve_price || '';
                        document.getElementById('editAuctionStart').value = formatDateTimeLocal(new Date(artwork.auction_start));
                        document.getElementById('editAuctionEnd').value = formatDateTimeLocal(new Date(artwork.auction_end));
                    }
                    
                    // Show/hide auction fields
                    auctionFields.style.display = hasAuction ? 'block' : 'none';
                    
                    // Show modal
                    new bootstrap.Modal(document.getElementById('editArtworkModal')).show();
                });
        });
    });
    
    // Save artwork changes
    document.getElementById('saveArtworkChanges')?.addEventListener('click', function() {
        const form = document.getElementById('editArtworkForm');
        const formData = new FormData(form);
        const spinner = document.getElementById('saveSpinner');
        
        spinner.classList.remove('d-none');
        this.disabled = true;
        
        fetch('../api/artworks.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred: ' + error.message);
        })
        .finally(() => {
            spinner.classList.add('d-none');
            this.disabled = false;
        });
    });
    
    // View bid history
    document.querySelectorAll('.view-bids').forEach(button => {
        button.addEventListener('click', function() {
            const auctionId = this.getAttribute('data-auction-id');
            const modal = new bootstrap.Modal(document.getElementById('bidHistoryModal'));
            
            fetch(`../api/bids.php?auction_id=${auctionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('bidHistoryBody');
                        tbody.innerHTML = '';
                        
                        data.bids.forEach(bid => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="${bid.avatar || '../assets/img/user-default.png'}" 
                                             class="rounded-circle me-2" width="30" height="30">
                                        ${bid.username}
                                    </div>
                                </td>
                                <td class="fw-bold">$${bid.amount.toFixed(2)}</td>
                                <td>${new Date(bid.bid_time).toLocaleString()}</td>
                                <td>
                                    ${bid.is_winning 
                                        ? '<span class="badge bg-success">Winning</span>' 
                                        : '<span class="badge bg-secondary">Outbid</span>'}
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                        
                        modal.show();
                    } else {
                        alert('Error loading bid history: ' + data.message);
                    }
                });
        });
    });
});