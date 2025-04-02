<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';


if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$folders = getUserCollectionFolders($user_id);

foreach ($folders as $folder): ?>
<div class="col-md-3 col-lg-2 mb-3">
    <div class="card folder-card h-100 shadow-sm">
        <div class="card-body text-center">
            <a href="#" class="stretched-link folder-link" data-folder-id="<?php echo $folder['folder_id']; ?>"></a>
            <i class="fas fa-folder fa-3x text-warning mb-2"></i>
            <h6 class="card-title mb-1"><?php echo $folder['folder_name']; ?></h6>
            <small class="text-muted"><?php echo $folder['item_count']; ?> items</small>
        </div>
    </div>
</div>
<?php endforeach; ?>