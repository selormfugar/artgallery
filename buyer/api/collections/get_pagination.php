<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$filters = [
    'category' => isset($_GET['category']) ? $_GET['category'] : '',
    'artist' => isset($_GET['artist']) ? $_GET['artist'] : '',
    'price_min' => isset($_GET['price_min']) ? (float)$_GET['price_min'] : '',
    'price_max' => isset($_GET['price_max']) ? (float)$_GET['price_max'] : '',
    'collection_type' => isset($_GET['collection_type']) ? $_GET['collection_type'] : 'all',
    'folder_id' => isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null
];

$totalItems = getUserCollectionsCount($user_id, $filters);
$totalPages = ceil($totalItems / 12);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($totalPages > 1): ?>
<nav aria-label="Collection pagination" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="#" aria-label="Previous" data-page="<?php echo $currentPage - 1; ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="#" aria-label="Next" data-page="<?php echo $currentPage + 1; ?>">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>