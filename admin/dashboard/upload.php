<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an artist
requireArtist();

// Get categories
$categories = getCategories();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitizeInput($_POST['category']);
    
    // Validate required fields
    if (empty($title) || empty($category) || $price <= 0) {
        $message = 'Please fill in all required fields.';
        $messageType = 'danger';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = uploadImage($_FILES['image']);
            
            if ($imageName) {
                // Insert artwork into database
                global $db;
                $artworkData = [
                    'artist_id' => $_SESSION['artist_id'],
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'category' => $category,
                    'image_url' => $imageName
                ];
                
                $artworkId = $db->insert('artworks', $artworkData);
                
                if ($artworkId) {
                    $message = 'Artwork uploaded successfully!';
                    $messageType = 'success';
                    
                    // Clear form after successful upload
                    $title = $description = $category = '';
                    $price = 0;
                } else {
                    $message = 'Error saving artwork to database.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'Error uploading image. Please try again.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Please select an image to upload.';
            $messageType = 'danger';
        }
    }
}

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Upload Artwork</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Artwork Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo isset($price) ? $price : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($description) ? $description : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Artwork Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            <div class="form-text">Upload a high-quality image of your artwork. Maximum file size: 5MB. Supported formats: JPG, PNG, GIF.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="/placeholder.svg" alt="Image Preview" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">Upload Artwork</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Upload Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-image text-primary me-2"></i>
                            Use high-quality images that showcase your artwork clearly
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-tag text-primary me-2"></i>
                            Set a competitive price based on size, medium, and complexity
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-align-left text-primary me-2"></i>
                            Write detailed descriptions including dimensions and materials
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-list text-primary me-2"></i>
                            Choose the most appropriate category for your artwork
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-copyright text-primary me-2"></i>
                            Ensure you own the rights to all uploaded content
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you're having trouble uploading your artwork or have questions about the process, check out our <a href="#">Artist FAQ</a> or <a href="#">contact support</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = document.getElementById('imagePreview');
            const previewImage = preview.querySelector('img');
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('d-none');
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Form reset handler
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        document.getElementById('imagePreview').classList.add('d-none');
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>

