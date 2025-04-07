<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artist Subscription Management</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    .tier-card {
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    .tier-card.disabled {
      opacity: 0.6;
    }
    .tier-card:not(.disabled):hover {
      border-color: #0d6efd;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .form-switch .form-check-input {
      width: 3em;
    }
    .duration-section {
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #eee;
    }
    .duration-badge {
      background-color: #f0f8ff;
      color: #0d6efd;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .text-count {
      font-size: 0.8rem;
      color: #6c757d;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Subscription Tiers</h2>
      <button class="btn btn-outline-primary">
        <i class="bi bi-question-circle me-2"></i>Learn About Subscriptions
      </button>
    </div>

    <div class="alert alert-info mb-4">
      <div class="d-flex">
        <div class="me-3">
          <i class="bi bi-info-circle-fill fs-4"></i>
        </div>
        <div>
          <h5>Set Up Your Subscription Tiers</h5>
          <p class="mb-0">Enable the subscription tiers you want to offer, customize descriptions, and preview how they'll appear to your fans.</p>
        </div>
      </div>
    </div>

    <!-- Monthly Subscription Section -->
    <div class="duration-section">
      <span class="duration-badge">
        <i class="bi bi-calendar-month me-2"></i>Monthly Subscriptions
      </span>
      
      <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <!-- Basic Monthly Tier -->
        <div class="col">
          <div class="card tier-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Basic Monthly</h5>
                <div class="d-flex align-items-center">
                  <span class="me-2" id="basicMonthlyStatus">ENABLED</span>
                  <div class="form-check form-switch">
                    <input class="form-check-input tier-toggle" type="checkbox" id="basicMonthlyToggle" checked data-tier-id="1" data-tier-name="Basic Monthly">
                    <label class="form-check-label visually-hidden" for="basicMonthlyToggle">Toggle Basic Monthly</label>
                  </div>
                </div>
              </div>
              
              <h6 class="fw-bold mt-3">$4.99/month</h6>
              <p class="card-text">10% discount on all purchases</p>
              
              <hr>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Platform description:</div>
                <p class="card-text">Basic monthly subscription with exclusive discounts</p>
              </div>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Your custom description:</div>
                <p class="card-text" id="basicMonthlyCustomDesc">Support my art journey and get 10% off all my creations, plus monthly updates</p>
              </div>
              
              <div class="mt-4">
                <button class="btn btn-primary me-2 edit-tier-btn" data-bs-toggle="modal" data-bs-target="#editTierModal" data-tier-id="1" data-tier-name="Basic Monthly" data-tier-price="$4.99/month" data-tier-desc="Support my art journey and get 10% off all my creations, plus monthly updates">Edit Description</button>
                <button class="btn btn-outline-primary preview-tier-btn">Preview</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Premium Monthly Tier -->
        <div class="col">
          <div class="card tier-card h-100 disabled">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Premium Monthly</h5>
                <div class="d-flex align-items-center">
                  <span class="me-2 text-muted" id="premiumMonthlyStatus">DISABLED</span>
                  <div class="form-check form-switch">
                    <input class="form-check-input tier-toggle" type="checkbox" id="premiumMonthlyToggle" data-tier-id="2" data-tier-name="Premium Monthly">
                    <label class="form-check-label visually-hidden" for="premiumMonthlyToggle">Toggle Premium Monthly</label>
                  </div>
                </div>
              </div>
              
              <h6 class="fw-bold mt-3">$9.99/month</h6>
              <p class="card-text">15% discount on all purchases</p>
              
              <hr>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Platform description:</div>
                <p class="card-text">Premium monthly subscription with better discounts and early access</p>
              </div>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Your custom description:</div>
                <p class="card-text text-muted" id="premiumMonthlyCustomDesc">No custom description set</p>
              </div>
              
              <div class="mt-4">
                <button class="btn btn-outline-secondary me-2 edit-tier-btn" disabled data-bs-toggle="modal" data-bs-target="#editTierModal" data-tier-id="2" data-tier-name="Premium Monthly" data-tier-price="$9.99/month" data-tier-desc="">Edit Description</button>
                <button class="btn btn-outline-secondary preview-tier-btn" disabled>Preview</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Yearly Subscription Section -->
    <div class="duration-section">
      <span class="duration-badge">
        <i class="bi bi-calendar-year me-2"></i>Annual Subscriptions
      </span>
      
      <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <!-- Basic Annual Tier -->
        <div class="col">
          <div class="card tier-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Basic Annual</h5>
                <div class="d-flex align-items-center">
                  <span class="me-2" id="basicAnnualStatus">ENABLED</span>
                  <div class="form-check form-switch">
                    <input class="form-check-input tier-toggle" type="checkbox" id="basicAnnualToggle" checked data-tier-id="3" data-tier-name="Basic Annual">
                    <label class="form-check-label visually-hidden" for="basicAnnualToggle">Toggle Basic Annual</label>
                  </div>
                </div>
              </div>
              
              <h6 class="fw-bold mt-3">$49.99/year</h6>
              <p class="card-text">12% discount on all purchases</p>
              
              <hr>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Platform description:</div>
                <p class="card-text">Annual subscription with savings and continuous benefits</p>
              </div>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Your custom description:</div>
                <p class="card-text" id="basicAnnualCustomDesc">Save with a yearly subscription and enjoy 12% off my art all year</p>
              </div>
              
              <div class="mt-4">
                <button class="btn btn-primary me-2 edit-tier-btn" data-bs-toggle="modal" data-bs-target="#editTierModal" data-tier-id="3" data-tier-name="Basic Annual" data-tier-price="$49.99/year" data-tier-desc="Save with a yearly subscription and enjoy 12% off my art all year">Edit Description</button>
                <button class="btn btn-outline-primary preview-tier-btn">Preview</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Premium Annual Tier -->
        <div class="col">
          <div class="card tier-card h-100 disabled">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Premium Annual</h5>
                <div class="d-flex align-items-center">
                  <span class="me-2 text-muted" id="premiumAnnualStatus">DISABLED</span>
                  <div class="form-check form-switch">
                    <input class="form-check-input tier-toggle" type="checkbox" id="premiumAnnualToggle" data-tier-id="4" data-tier-name="Premium Annual">
                    <label class="form-check-label visually-hidden" for="premiumAnnualToggle">Toggle Premium Annual</label>
                  </div>
                </div>
              </div>
              
              <h6 class="fw-bold mt-3">$99.99/year</h6>
              <p class="card-text">20% discount on all purchases</p>
              
              <hr>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Platform description:</div>
                <p class="card-text">Premium annual subscription with maximum benefits and exclusive content</p>
              </div>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Your custom description:</div>
                <p class="card-text text-muted" id="premiumAnnualCustomDesc">No custom description set</p>
              </div>
              
              <div class="mt-4">
                <button class="btn btn-outline-secondary me-2 edit-tier-btn" disabled data-bs-toggle="modal" data-bs-target="#editTierModal" data-tier-id="4" data-tier-name="Premium Annual" data-tier-price="$99.99/year" data-tier-desc="">Edit Description</button>
                <button class="btn btn-outline-secondary preview-tier-btn" disabled>Preview</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lifetime Subscription Section -->
    <div class="duration-section">
      <span class="duration-badge">
        <i class="bi bi-infinity me-2"></i>Lifetime Subscriptions
      </span>
      
      <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <!-- Lifetime Tier -->
        <div class="col">
          <div class="card tier-card h-100 disabled">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Lifetime Support</h5>
                <div class="d-flex align-items-center">
                  <span class="me-2 text-muted" id="lifetimeStatus">DISABLED</span>
                  <div class="form-check form-switch">
                    <input class="form-check-input tier-toggle" type="checkbox" id="lifetimeToggle" data-tier-id="5" data-tier-name="Lifetime Support">
                    <label class="form-check-label visually-hidden" for="lifetimeToggle">Toggle Lifetime Support</label>
                  </div>
                </div>
              </div>
              
              <h6 class="fw-bold mt-3">$249.99 one-time</h6>
              <p class="card-text">25% discount forever on all purchases</p>
              
              <hr>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Platform description:</div>
                <p class="card-text">Lifetime supporter benefits with maximum discounts and special recognition</p>
              </div>
              
              <div class="mb-3">
                <div class="text-muted small mb-1">Your custom description:</div>
                <p class="card-text text-muted" id="lifetimeCustomDesc">No custom description set</p>
              </div>
              
              <div class="mt-4">
                <button class="btn btn-outline-secondary me-2 edit-tier-btn" disabled data-bs-toggle="modal" data-bs-target="#editTierModal" data-tier-id="5" data-tier-name="Lifetime Support" data-tier-price="$249.99 one-time" data-tier-desc="">Edit Description</button>
                <button class="btn btn-outline-secondary preview-tier-btn" disabled>Preview</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Add More Tier Card -->
        <div class="col">
          <div class="card add-tier-card h-100 text-center">
            <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
              <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-plus-lg fs-3 text-secondary"></i>
              </div>
              <h5 class="card-title text-muted">More Subscription Tiers</h5>
              <a href="#" class="link-primary fw-semibold mt-2">View All Tiers</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button Area -->
    <div class="d-flex justify-content-end mt-4">
      <button type="button" class="btn btn-primary" id="saveAllChanges">
        Save All Changes
      </button>
    </div>
  </div>

  <!-- Edit Tier Modal -->
  <div class="modal fade" id="editTierModal" tabindex="-1" aria-labelledby="editTierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTierModalLabel">Edit Tier Description</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted" id="modalTierInfo">Basic Monthly - $4.99/month</p>
          <input type="hidden" id="modalTierId" value="1">
          
          <div class="mb-4">
            <label for="tierDescription" class="form-label fw-bold">Custom Tier Description</label>
            <p class="text-muted small">This appears on your profile and tier selection page (150 chars max)</p>
            <textarea class="form-control" id="tierDescription" rows="3" maxlength="150">Support my art journey and get 10% off all my creations, plus monthly updates</textarea>
            <div class="text-count"><span id="tierDescriptionCount">0</span>/150</div>
          </div>
          
          <div class="mb-4">
            <label for="welcomeMessage" class="form-label fw-bold">Subscriber Welcome Message</label>
            <p class="text-muted small">Sent when someone subscribes to this tier (250 chars max)</p>
            <textarea class="form-control" id="welcomeMessage" rows="4" maxlength="250">Thank you for supporting my work! Your subscription helps me create more amazing art. Check your email for your first discount code!</textarea>
            <div class="text-count"><span id="welcomeMessageCount">0</span>/250</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Display Settings</label>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="showProfile" checked>
              <label class="form-check-label" for="showProfile">
                Show on profile
              </label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="showArtwork" checked>
              <label class="form-check-label" for="showArtwork">
                Show on artwork pages
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="featuredShop">
              <label class="form-check-label" for="featuredShop">
                Featured in shop
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveTierChanges">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Toast -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header bg-success text-white">
        <strong class="me-auto">Success</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        Changes saved successfully!
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Store artist ID (in a real application, this would come from server-side)
      const artistId = 123;
      
      // Character count for textareas
      document.getElementById('tierDescription').addEventListener('input', function() {
        document.getElementById('tierDescriptionCount').textContent = this.value.length;
      });
      
      document.getElementById('welcomeMessage').addEventListener('input', function() {
        document.getElementById('welcomeMessageCount').textContent = this.value.length;
      });
      
      // Set initial counts
      document.getElementById('tierDescriptionCount').textContent = 
        document.getElementById('tierDescription').value.length;
      document.getElementById('welcomeMessageCount').textContent = 
        document.getElementById('welcomeMessage').value.length;
      
      // Toggle functionality for all tier cards
      const toggles = document.querySelectorAll('.tier-toggle');
      
      toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
          const tierId = this.dataset.tierId;
          const tierName = this.dataset.tierName;
          const tierCard = this.closest('.tier-card');
          const editBtn = tierCard.querySelector('.edit-tier-btn');
          const previewBtn = tierCard.querySelector('.preview-tier-btn');
          const statusText = tierCard.querySelector('[id$="Status"]');
          
          if (this.checked) {
            tierCard.classList.remove('disabled');
            editBtn.classList.replace('btn-outline-secondary', 'btn-primary');
            previewBtn.classList.replace('btn-outline-secondary', 'btn-outline-primary');
            editBtn.disabled = false;
            previewBtn.disabled = false;
            statusText.textContent = 'ENABLED';
            statusText.classList.remove('text-muted');
            
            // Save to database (ajax call would go here)
            console.log(`Enabled tier ${tierId} (${tierName}) for artist ${artistId}`);
            
            // In a real application, this would be an AJAX call to update the database
            // Example:
            /*
            fetch('/api/artist-subscription-settings/update', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                artist_id: artistId,
                tier_id: tierId,
                is_enabled: 1
              })
            })
            .then(response => response.json())
            .then(data => console.log('Success:', data))
            .catch(error => console.error('Error:', error));
            */
          } else {
            tierCard.classList.add('disabled');
            editBtn.classList.replace('btn-primary', 'btn-outline-secondary');
            previewBtn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
            editBtn.disabled = true;
            previewBtn.disabled = true;
            statusText.textContent = 'DISABLED';
            statusText.classList.add('text-muted');
            
            // Save to database (ajax call would go here)
            console.log(`Disabled tier ${tierId} (${tierName}) for artist ${artistId}`);
            
            // In a real application, this would be an AJAX call to update the database
            // Example of AJAX call to update is_enabled to 0
          }
        });
      });
      
      // Modal functionality for editing tier descriptions
      const editTierModal = document.getElementById('editTierModal');
      const editButtons = document.querySelectorAll('.edit-tier-btn');
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const tierId = this.dataset.tierId;
          const tierName = this.dataset.tierName;
          const tierPrice = this.dataset.tierPrice;
          const tierDesc = this.dataset.tierDesc || '';
          
          // Set modal values
          document.getElementById('modalTierId').value = tierId;
          document.getElementById('modalTierInfo').textContent = `${tierName} - ${tierPrice}`;
          document.getElementById('tierDescription').value = tierDesc;
          
          // Update character count
          document.getElementById('tierDescriptionCount').textContent = tierDesc.length;
          
          // In a real app, you might fetch welcome message and display settings from the database
        });
      });
      
      // Save tier changes from modal
      document.getElementById('saveTierChanges').addEventListener('click', function() {
        const tierId = document.getElementById('modalTierId').value;
        const customDescription = document.getElementById('tierDescription').value;
        const welcomeMessage = document.getElementById('welcomeMessage').value;
        const showProfile = document.getElementById('showProfile').checked;
        const showArtwork = document.getElementById('showArtwork').checked;
        const featuredShop = document.getElementById('featuredShop').checked;
        
        // Update the displayed description
        const descElement = document.getElementById(`tier${tierId}CustomDesc`) || 
                           document.querySelector(`[data-tier-id="${tierId}"]`).closest('.card').querySelector('[id$="CustomDesc"]');
        
        if (descElement) {
          if (customDescription.trim() === '') {
            descElement.textContent = 'No custom description set';
            descElement.classList.add('text-muted');
          } else {
            descElement.textContent = customDescription;
            descElement.classList.remove('text-muted');
          }
        }
        
        // Update the data attribute on the edit button for future edits
        const editBtn = document.querySelector(`.edit-tier-btn[data-tier-id="${tierId}"]`);
        if (editBtn) {
          editBtn.dataset.tierDesc = customDescription;
        }
        
        // In a real application, this would be an AJAX call to update the database
        console.log(`Saving tier ${tierId} custom description: ${customDescription}`);
        console.log(`Welcome message: ${welcomeMessage}`);
        console.log(`Display settings: Profile ${showProfile}, Artwork ${showArtwork}, Shop ${featuredShop}`);
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(editTierModal);
        modal.hide();
        
        // Show success toast
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      });
      
      // Save all changes button
      document.getElementById('saveAllChanges').addEventListener('click', function() {
        console.log('Saving all tier settings to the database...');
        
        // In a real application, this might collect all the settings and send them in a batch
        // or confirm that all individual changes have been saved
        
        // Show success toast
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      });
      
      // Preview button functionality
      const previewButtons = document.querySelectorAll('.preview-tier-btn');
      previewButtons.forEach(button => {
        button.addEventListener('click', function() {
          const tierCard = this.closest('.card');
          const tierName = tierCard.querySelector('.card-title').textContent;
          const tierPrice = tierCard.querySelector('.fw-bold').textContent;
          const tierDesc = tierCard.querySelector('[id$="CustomDesc"]').textContent;
          
          alert(`Preview of ${tierName}\nPrice: ${tierPrice}\nDescription: ${tierDesc}`);
          
          // In a real application, this might open a modal with a preview of how the tier
          // would appear to subscribers
        });
      });
    });
  </script>
</body>
</html>