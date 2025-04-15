<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

global $db;
$pdo = $db->getConnection();
$artistId = $_SESSION['user_id'];

// Handle form submission for new portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_portfolio_item'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
    
    // Handle image upload
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadPortfolioImage($_FILES['image'], $artistId);
        
        if ($uploadResult['success']) {
            $stmt = $pdo->prepare("INSERT INTO portfolio_items 
                                  (artist_id, title, description, image_url, category, tags) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $artistId, 
                $title, 
                $description, 
                $uploadResult['path'], 
                $category,
                $tags
            ]);
            $success = "Portfolio item added successfully!";
        } else {
            $error = $uploadResult['error'];
        }
    } else {
        $error = "Please select an image to upload";
    }
}

// Get existing portfolio items
$stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE artist_id = ? AND archived = 0 ORDER BY created_at DESC");
$stmt->execute([$artistId]);
$portfolioItems = $stmt->fetchAll();

// Get categories for dropdown
$categories = $pdo->query("SELECT name FROM categories WHERE archived = 0")->fetchAll(PDO::FETCH_COLUMN);

include '../includes/header.php';
?>

<div class="artist-dashboard">
    <h1>My Portfolio</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="dashboard-tabs">
        <a href="portfolio.php" class="tab-btn active">Portfolio</a>
        <a href="subscriptions.php" class="tab-btn">Subscriptions</a>
        <a href="stats.php" class="tab-btn">Statistics</a>
    </div>

    <div class="portfolio-section">
        <h2>Add New Portfolio Item</h2>
        <form method="post" enctype="multipart/form-data" class="portfolio-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags (comma separated)</label>
                    <input type="text" id="tags" name="tags">
                </div>
            </div>
            
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <small>Max size: 5MB. Allowed formats: JPG, PNG, GIF</small>
            </div>
            
            <button type="submit" name="add_portfolio_item" class="btn-primary">Add to Portfolio</button>
        </form>
    </div>

    <div class="portfolio-items">
        <h2>My Portfolio Items</h2>
        
        <?php if (empty($portfolioItems)): ?>
            <p>You haven't added any portfolio items yet.</p>
        <?php else: ?>
            <div class="items-grid">
                <?php foreach ($portfolioItems as $item): ?>
                    <div class="portfolio-item" data-id="<?= $item['item_id'] ?>">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p class="category"><?= htmlspecialchars($item['category']) ?></p>
                        <div class="item-actions">
                            <button class="btn-edit">Edit</button>
                            <button class="btn-delete">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission for new portfolio item
    const portfolioForm = document.querySelector('.portfolio-form');
    if (portfolioForm) {
        portfolioForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            
            fetch('/api/create_portfolio_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new item to the grid without reload
                    const itemsGrid = document.querySelector('.items-grid');
                    const newItem = createPortfolioItemElement(data.item);
                    
                    if (itemsGrid) {
                        // If grid exists, prepend new item
                        itemsGrid.prepend(newItem);
                    } else {
                        // Create grid if it doesn't exist
                        const portfolioItems = document.querySelector('.portfolio-items');
                        const grid = document.createElement('div');
                        grid.className = 'items-grid';
                        grid.appendChild(newItem);
                        portfolioItems.appendChild(grid);
                        
                        // Remove "no items" message if present
                        const noItemsMsg = portfolioItems.querySelector('p');
                        if (noItemsMsg) noItemsMsg.remove();
                    }
                    
                    // Reset form
                    portfolioForm.reset();
                    showAlert('Portfolio item added successfully!', 'success');
                } else {
                    showAlert(data.error || 'Failed to add portfolio item', 'error');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }
    
    // Delete portfolio item
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete')) {
            const item = e.target.closest('.portfolio-item');
            const itemId = item.dataset.id;
            
            if (confirm('Are you sure you want to delete this portfolio item?')) {
                fetch(`/api/delete_portfolio_item.php?id=${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.remove();
                        showAlert('Portfolio item deleted successfully', 'success');
                        
                        // Check if grid is now empty
                        const itemsGrid = document.querySelector('.items-grid');
                        if (itemsGrid && itemsGrid.children.length === 0) {
                            const portfolioItems = document.querySelector('.portfolio-items');
                            const noItemsMsg = document.createElement('p');
                            noItemsMsg.textContent = 'You haven\'t added any portfolio items yet.';
                            portfolioItems.appendChild(noItemsMsg);
                        }
                    } else {
                        showAlert(data.error || 'Failed to delete portfolio item', 'error');
                    }
                })
                .catch(error => {
                    showAlert('Network error: ' + error.message, 'error');
                });
            }
        }
    });
    
    // Edit portfolio item (opens modal)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-edit')) {
            const item = e.target.closest('.portfolio-item');
            const itemId = item.dataset.id;
            
            fetch(`/api/get_portfolio_item.php?id=${itemId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openEditModal(data.item);
                } else {
                    showAlert(data.error || 'Failed to load portfolio item', 'error');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'error');
            });
        }
    });
    
    // Helper function to create portfolio item element
    function createPortfolioItemElement(item) {
        const div = document.createElement('div');
        div.className = 'portfolio-item';
        div.dataset.id = item.id;
        
        div.innerHTML = `
            <img src="${item.image_url}" alt="${item.title}">
            <h3>${item.title}</h3>
            <p class="category">${item.category}</p>
            <div class="item-actions">
                <button class="btn-edit">Edit</button>
                <button class="btn-delete">Delete</button>
            </div>
        `;
        
        return div;
    }
    
    // Helper function to show alerts
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.artist-dashboard');
        if (container) {
            container.prepend(alertDiv);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }
    
    // Helper function to open edit modal
    function openEditModal(item) {
        // Implementation would create and show a modal with the item data
        // This would be similar to the create form but pre-populated
        console.log('Edit item:', item);
    }
});
</script>

<?php include '../includes/footer.php'; ?>