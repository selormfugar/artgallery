<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

header('Content-Type: text/html');

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Please log in to view your collections</div>';
    exit;
}

$user_id = $_SESSION['user_id'];
$filters = [
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'limit' => 12,
    'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'date_desc',
    'category' => isset($_GET['category']) ? $_GET['category'] : '',
    'artist' => isset($_GET['artist']) ? $_GET['artist'] : '',
    'price_min' => isset($_GET['price_min']) ? (float)$_GET['price_min'] : '',
    'price_max' => isset($_GET['price_max']) ? (float)$_GET['price_max'] : '',
    'collection_type' => isset($_GET['collection_type']) ? $_GET['collection_type'] : 'all',
    'folder_id' => isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null
];

$offset = ($filters['page'] - 1) * $filters['limit'];
$collectionsData = getUserCollections($user_id, $filters['limit'], $offset, $filters);

if ($collectionsData && isset($collectionsData['items']) && count($collectionsData['items']) > 0) {
    if (!isset($_COOKIE['collection_view']) || $_COOKIE['collection_view'] == 'grid') {
        include '../../dashboard/partials/collection_grid.php';
    } else {
        include '../../dashboard/partials/collection_list.php';
    }
} else {
    echo '<div class="alert alert-info">No items found in this collection</div>';
}
?>