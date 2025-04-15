<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

$artistId = $_SESSION['artist_id'];


// Get available subscription tiers
$tiers = $pdo->query("SELECT * FROM subscription_tiers WHERE is_active = 1")->fetchAll();

// Get artist's current subscription plans
$artistPlans = $pdo->prepare("SELECT sp.*, st.name as tier_name 
                             FROM subscription_plans sp
                             JOIN subscription_tiers st ON sp.tier_id = st.tier_id
                             WHERE sp.artist_id = ? AND sp.archived = 0
                             ORDER BY sp.created_at DESC")
                  ->execute([$artistId])
                  ->fetchAll();

// Handle form submission for new subscription plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plan'])) {
    $tierId = filter_input(INPUT_POST, 'tier_id', FILTER_VALIDATE_INT);
    $customDescription = filter_input(INPUT_POST, 'custom_description', FILTER_SANITIZE_STRING);
    
    // Validate tier exists
    $tierExists = $pdo->prepare("SELECT COUNT(*) FROM subscription_tiers WHERE tier_id = ? AND is_active = 1")
                     ->execute([$tierId])
                     ->fetchColumn();
    
    if ($tierExists) {
        $stmt = $pdo->prepare("INSERT INTO subscription_plans 
                              (artist_id, tier_id, name, description, duration_type, price, discount_percentage) 
                              SELECT ?, tier_id, name, ?, duration_type, price, discount_percentage 
                              FROM subscription_tiers 
                              WHERE tier_id = ?");
        $stmt->execute([
            $artistId,
            $customDescription ?: null,
            $tierId
        ]);
        
        $success = "Subscription plan created successfully!";
    } else {
        $error = "Invalid subscription tier selected";
    }
}

include '../includes/header.php';
?>

<div class="artist-dashboard">
    <h1>Subscription Plans</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="dashboard-tabs">
        <a href="portfolio.php" class="tab-btn">Portfolio</a>
        <a href="subscriptions.php" class="tab-btn active">Subscriptions</a>
        <a href="stats.php" class="tab-btn">Statistics</a>
    </div>

    <div class="subscription-section">
        <h2>Create New Subscription Plan</h2>
        <form method="post" class="subscription-form">
            <div class="form-group">
                <label for="tier_id">Subscription Tier</label>
                <select id="tier_id" name="tier_id" required>
                    <?php foreach ($tiers as $tier): ?>
                        <option value="<?= $tier['tier_id'] ?>">
                            <?= htmlspecialchars($tier['name']) ?> - 
                            $<?= number_format($tier['price'], 2) ?> 
                            (<?= $tier['duration_type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="custom_description">Custom Description (Optional)</label>
                <textarea id="custom_description" name="custom_description" rows="3"></textarea>
                <small>Add a personalized message for this subscription level</small>
            </div>
            
            <button type="submit" name="create_plan" class="btn-primary">Create Plan</button>
        </form>
    </div>

    <div class="current-plans">
        <h2>My Current Subscription Plans</h2>
        
        <?php if (empty($artistPlans)): ?>
            <p>You haven't created any subscription plans yet.</p>
        <?php else: ?>
            <div class="plans-grid">
                <?php foreach ($artistPlans as $plan): ?>
                    <div class="subscription-plan">
                        <h3><?= htmlspecialchars($plan['tier_name']) ?></h3>
                        <p class="price">$<?= number_format($plan['price'], 2) ?> 
                            <span class="discount">(<?= $plan['discount_percentage'] ?>% discount)</span>
                        </p>
                        <p class="duration"><?= ucfirst($plan['duration_type']) ?> plan</p>
                        
                        <?php if ($plan['description']): ?>
                            <div class="description"><?= htmlspecialchars($plan['description']) ?></div>
                        <?php endif; ?>
                        
                        <div class="plan-stats">
                            <span><?= getSubscriberCount($pdo, $plan['plan_id']) ?> subscribers</span>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-edit" data-plan-id="<?= $plan['plan_id'] ?>">Edit</button>
                            <button class="btn-delete" data-plan-id="<?= $plan['plan_id'] ?>">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission for new subscription plan
    const subscriptionForm = document.querySelector('.subscription-form');
    if (subscriptionForm) {
        subscriptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';
            
            fetch('/api/create_subscription_plan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new plan to the grid without reload
                    const plansGrid = document.querySelector('.plans-grid');
                    const newPlan = createSubscriptionPlanElement(data.plan);
                    
                    if (plansGrid) {
                        // If grid exists, prepend new plan
                        plansGrid.prepend(newPlan);
                    } else {
                        // Create grid if it doesn't exist
                        const currentPlans = document.querySelector('.current-plans');
                        const grid = document.createElement('div');
                        grid.className = 'plans-grid';
                        grid.appendChild(newPlan);
                        currentPlans.appendChild(grid);
                        
                        // Remove "no plans" message if present
                        const noPlansMsg = currentPlans.querySelector('p');
                        if (noPlansMsg) noPlansMsg.remove();
                    }
                    
                    // Reset form
                    subscriptionForm.reset();
                    showAlert('Subscription plan created successfully!', 'success');
                } else {
                    showAlert(data.error || 'Failed to create subscription plan', 'error');
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
    
    // Delete subscription plan
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete')) {
            const plan = e.target.closest('.subscription-plan');
            const planId = e.target.dataset.planId;
            
            if (confirm('Are you sure you want to delete this subscription plan? Existing subscribers will be unaffected.')) {
                fetch(`/api/delete_subscription_plan.php?id=${planId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        plan.remove();
                        showAlert('Subscription plan deleted successfully', 'success');
                        
                        // Check if grid is now empty
                        const plansGrid = document.querySelector('.plans-grid');
                        if (plansGrid && plansGrid.children.length === 0) {
                            const currentPlans = document.querySelector('.current-plans');
                            const noPlansMsg = document.createElement('p');
                            noPlansMsg.textContent = 'You haven\'t created any subscription plans yet.';
                            currentPlans.appendChild(noPlansMsg);
                        }
                    } else {
                        showAlert(data.error || 'Failed to delete subscription plan', 'error');
                    }
                })
                .catch(error => {
                    showAlert('Network error: ' + error.message, 'error');
                });
            }
        }
    });
    
    // Edit subscription plan (opens modal)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-edit')) {
            const planId = e.target.dataset.planId;
            
            fetch(`/api/get_subscription_plan.php?id=${planId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openEditModal(data.plan);
                } else {
                    showAlert(data.error || 'Failed to load subscription plan', 'error');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'error');
            });
        }
    });
    
    // Helper function to create subscription plan element
    function createSubscriptionPlanElement(plan) {
        const div = document.createElement('div');
        div.className = 'subscription-plan';
        
        div.innerHTML = `
            <h3>${plan.tier_name}</h3>
            <p class="price">$${plan.price.toFixed(2)} 
                <span class="discount">(${plan.discount_percentage}% discount)</span>
            </p>
            <p class="duration">${plan.duration_type.charAt(0).toUpperCase() + plan.duration_type.slice(1)} plan</p>
            
            ${plan.description ? `<div class="description">${plan.description}</div>` : ''}
            
            <div class="plan-stats">
                <span>${plan.subscriber_count || 0} subscribers</span>
            </div>
            
            <div class="plan-actions">
                <button class="btn-edit" data-plan-id="${plan.plan_id}">Edit</button>
                <button class="btn-delete" data-plan-id="${plan.plan_id}">Delete</button>
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
    function openEditModal(plan) {
        // Implementation would create and show a modal with the plan data
        // This would allow editing the custom description
        console.log('Edit plan:', plan);
    }
});
</script>

<?php include '../includes/footer.php'; ?>