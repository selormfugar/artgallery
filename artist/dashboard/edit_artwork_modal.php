<div class="modal fade" id="editArtworkModal" tabindex="-1" aria-labelledby="editArtworkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArtworkModalLabel">Edit Artwork</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editArtworkForm" enctype="multipart/form-data">
                    <input type="hidden" id="editArtworkId" name="artwork_id">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editTitle" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editCategory" class="form-label">Category*</label>
                                <select class="form-select" id="editCategory" name="category" required>
                                    <?php foreach (getCategories() as $category): ?>
                                        <option value="<?= $category['category_id'] ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editPrice" class="form-label">Price ($)*</label>
                                <input type="number" class="form-control" id="editPrice" name="price" 
                                       step="0.01" min="0" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <img id="editCurrentImage" src="../assets/img/placeholder.svg" 
                                     class="img-fluid rounded mb-2" alt="Current Artwork">
                                <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                            </div>
                            
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editIsForSale" name="is_for_sale" checked>
                                <label class="form-check-label" for="editIsForSale">Available for sale</label>
                            </div>
                            
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editIsForAuction" name="is_for_auction">
                                <label class="form-check-label" for="editIsForAuction">Available for auction</label>
                            </div>
                            
                            <div id="auctionFields" class="border p-3 rounded" style="display: none;">
                                <h6>Auction Settings</h6>
                                <div class="mb-3">
                                    <label for="editStartingPrice" class="form-label">Starting Price ($)*</label>
                                    <input type="number" class="form-control" id="editStartingPrice" 
                                           name="starting_price" step="0.01" min="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editReservePrice" class="form-label">Reserve Price ($)</label>
                                    <input type="number" class="form-control" id="editReservePrice" 
                                           name="reserve_price" step="0.01" min="0">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editAuctionStart" class="form-label">Start Time*</label>
                                        <input type="datetime-local" class="form-control" id="editAuctionStart" name="start_time">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editAuctionEnd" class="form-label">End Time*</label>
                                        <input type="datetime-local" class="form-control" id="editAuctionEnd" name="end_time">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveArtworkChanges">
                    <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>