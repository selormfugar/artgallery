<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artist Dashboard - Subscription Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
 
 <style>
    .sidebar {
      height: 100vh;
      position: fixed;
      background-color: #fff;
      border-right: 1px solid #e0e0e0;
    }
    
    .nav-link {
      color: #666666;
      padding: 0.7rem 1rem;
      border-radius: 0.25rem;
    }
    
    .nav-link.active {
      background-color: #f0f7ff;
      color: #2076d2;
      font-weight: 500;
      border-left: 4px solid #2076d2;
    }
    
    .subnav-link {
      padding-left: 2rem;
      font-size: 0.9rem;
    }
    
    .subnav-link.active {
      color: #2076d2;
      font-weight: 500;
    }
    
    .status-card {
      background-color: #fff;
      border-radius: 0.5rem;
    }
    
    .metric-card {
      background-color: #f8f9fa;
      border-radius: 0.25rem;
    }
    
    .form-check-input:checked {
      background-color: #2076d2;
      border-color: #2076d2;
    }
    
    .form-switch .form-check-input {
      width: 3em;
      height: 1.5em;
    }
    
    .tier-card {
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .tier-card.disabled {
      opacity: 0.7;
    }
    
    .add-tier-card {
      border: 2px dashed #dee2e6;
      border-radius: 0.5rem;
      background-color: #fff;
    }
    
    .badge-pro {
      background-color: #FFC107;
      color: #333;
      padding: 0.2rem 0.5rem;
      font-size: 0.7rem;
      border-radius: 1rem;
    }
    
    .nav-badge {
      background-color: #ff5252;
      color: white;
      font-size: 0.7rem;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
    }
    
    .artist-avatar {
      width: 60px;
      height: 60px;
      background-color: #e0e0e0;
      border-radius: 50%;
    }
    
    .main-content {
      margin-left: 250px;
      padding: 1.5rem;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
      content: ">";
    }
    
    @media (max-width: 992px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>

</head>
<body>
  <!-- Header -->
  <header class="navbar navbar-expand-lg bg-white border-bottom py-3">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold" href="#">Artist Dashboard</a>
      
      <div class="d-flex ms-auto">
        <form class="d-none d-md-flex me-3">
          <div class="input-group">
            <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
            <button class="btn btn-outline-secondary" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
        
        <div class="d-flex align-items-center">
          <div class="position-relative me-3">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              2
            </span>
          </div>
          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-person"></i>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="#">Profile</a></li>
              <li><a class="dropdown-item" href="#">Account Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Sign out</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav id="sidebar" class="col-lg-3 col-xl-2 d-md-block sidebar collapse">
        <div class="position-sticky pt-4">
          <div class="d-flex align-items-center mb-4 px-3">
            <div class="artist-avatar me-3"></div>
            <div>
              <h6 class="mb-0 fw-bold">Sarah Mitchell</h6>
              <p class="text-muted mb-0">Digital Artist</p>
            </div>
          </div>
          
          <hr class="my-3">
          
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link d-flex justify-content-between" href="#">
                Dashboard
                <span class="badge-pro">PRO</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Artworks</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Orders</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="#">Subscriptions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link subnav-link" href="#">→ Overview</a>
            </li>
            <li class="nav-item">
              <a class="nav-link subnav-link active" href="#">→ Manage Tiers</a>
            </li>
            <li class="nav-item">
              <a class="nav-link subnav-link" href="#">→ Subscribers</a>
            </li>
            <li class="nav-item">
              <a class="nav-link subnav-link" href="#">→ Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex justify-content-between" href="#">
                Messages
                <span class="nav-badge">3</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Settings</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Help Center</a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="col-lg-9 col-xl-10 ms-sm-auto px-md-4 main-content">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mt-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Subscriptions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Tiers</li>
          </ol>
        </nav>
        
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Subscription Management</h1>
        </div>
        
        <!-- Subscription Status Card -->
        <div class="card status-card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Subscription Status</h5>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="enableSubscriptions" checked>
                <label class="form-check-label ms-2" for="enableSubscriptions">Enable Subscriptions</label>
              </div>
            </div>
            
            <div class="row g-3">
              <div class="col-md-6 col-lg-3">
                <div class="metric-card p-3">
                  <div class="text-muted small">ACTIVE SUBSCRIBERS</div>
                  <div class="fs-4 fw-bold">24</div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="metric-card p-3">
                  <div class="text-muted small">MONTHLY REVENUE</div>
                  <div class="fs-4 fw-bold">$187.60</div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="metric-card p-3">
                  <div class="text-muted small">AVG. DURATION</div>
                  <div class="fs-4 fw-bold">4.2 mo</div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="metric-card p-3">
                  <div class="text-muted small">CONVERSION RATE</div>
                  <div class="fs-4 fw-bold">3.8%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Available Tiers Section -->
        <h4 class="mb-2">Available Subscription Tiers</h4>
        <p class="text-muted mb-4">Enable and customize the subscription tiers you want to offer to your followers</p>
        
        <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
          <!-- Basic Monthly Tier -->
          <div class="col">
            <div class="card tier-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="card-title">Basic Monthly</h5>
                  <div class="d-flex align-items-center">
                    <span class="me-2">ENABLED</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="basicMonthlyToggle" checked>
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
                  <p class="card-text">Support my art journey and get 10% off all my creations, plus monthly updates</p>
                </div>
                
                <div class="mt-4">
                  <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editTierModal">Edit Description</button>
                  <button class="btn btn-outline-primary">Preview</button>
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
                    <span class="me-2 text-muted">DISABLED</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="premiumMonthlyToggle">
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
                  <p class="card-text text-muted">No custom description set</p>
                </div>
                
                <div class="mt-4">
                  <button class="btn btn-outline-secondary me-2" disabled>Edit Description</button>
                  <button class="btn btn-outline-secondary" disabled>Preview</button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Basic Annual Tier -->
          <div class="col">
            <div class="card tier-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="card-title">Basic Annual</h5>
                  <div class="d-flex align-items-center">
                    <span class="me-2">ENABLED</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="basicAnnualToggle" checked>
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
                  <p class="card-text">Save with a yearly subscription and enjoy 12% off my art all year</p>
                </div>
                
                <div class="mt-4">
                  <button class="btn btn-primary me-2">Edit Description</button>
                  <button class="btn btn-outline-primary">Preview</button>
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
        
        <!-- Subscription Analytics Preview -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Subscription Analytics</h5>
            <a href="#" class="btn btn-sm btn-outline-primary">View Detailed Analytics</a>
          </div>
          <div class="card-body">
            <div class="placeholder-img">
              <img src="/api/placeholder/800/320" alt="Analytics placeholder" class="img-fluid rounded">
            </div>
          </div>
        </div>
      </main>
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
          <p class="text-muted">Basic Monthly - $4.99/month</p>
          
          <div class="mb-4">
            <label for "tierDescription" class="form-label fw-bold">Custom Tier Description</label>
            <p class="text-muted small">This appears on your profile and tier selection page (150 chars max)</p>
            <textarea class="form-control" id="tierDescription" rows="3">Support my art journey and get 10% off all my creations, plus monthly updates</textarea>
          </div>
          
          <div class="mb-4">
            <label for="welcomeMessage" class="form-label fw-bold">Subscriber Welcome Message</label>
            <p class="text-muted small">Sent when someone subscribes to this tier (250 chars max)</p>
            <textarea class="form-control" id="welcomeMessage" rows="4">Thank you for supporting my work! Your subscription helps me create more amazing art. Check your email for your first discount code!</textarea>
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
          <button type="button" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Toggle functionality for tier cards
    document.getElementById('premiumMonthlyToggle').addEventListener('change', function() {
      const tierCard = this.closest('.tier-card');
      const editBtn = tierCard.querySelector('.btn-outline-secondary:first-of-type');
      const previewBtn = tierCard.querySelector('.btn-outline-secondary:last-of-type');
      const statusText = this.parentElement.previousElementSibling;
      
      if (this.checked) {
        tierCard.classList.remove('disabled');
        editBtn.classList.replace('btn-outline-secondary', 'btn-primary');
        previewBtn.classList.replace('btn-outline-secondary', 'btn-outline-primary');
        editBtn.disabled = false;
        previewBtn.disabled = false;
        statusText.textContent = 'ENABLED';
        statusText.classList.remove('text-muted');
      } else {
        tierCard.classList.add('disabled');
        editBtn.classList.replace('btn-primary', 'btn-outline-secondary');
        previewBtn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        editBtn.disabled = true;
        previewBtn.disabled = true;
        statusText.textContent = 'DISABLED';
        statusText.classList.add('text-muted');
      }
    });
  </script>
</body>
</html>