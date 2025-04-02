<?php
// filepath: c:\xampp\htdocs\artgallery\buyer\dashboard\partials\collection_grid.php
if (!defined('BASE_PATH')) die('Direct access to this file is not allowed');
?>
<div class="row g-4">
    <?php foreach ($collectionsData['items'] as $item): ?>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($item['artwork_image']); ?>" 
                     class="card-img-top artwork-thumbnail" 
                     alt="<?php echo htmlspecialchars($item['artwork_title']); ?>">
                <div class="card-body">
                    <h5 class="card-title text-truncate">
                        <?php echo htmlspecialchars($item['artwork_title']); ?>
                    </h5>
                    <?php
                    // filepath: c:\xampp\htdocs\artgallery\buyer\dashboard\partials\collection_list.php
                    if (!defined('BASE_PATH')) die('Direct access to this file is not allowed');
                    ?>
                    <div class="list-group">
                        <?php foreach ($collectionsData['items'] as $item): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo htmlspecialchars($item['artwork_image']); ?>" 
                                             class="img-fluid artwork-thumbnail" 
                                             alt="<?php echo htmlspecialchars($item['artwork_title']); ?>">
                                    </div>
                                    <div class="col-md-7">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['artwork_title']); ?></h5>
                                        <p class="mb-1">
                                            <small class="text-muted">By <?php echo htmlspecialchars($item['artist_name']); ?></small>
                                        </p>
                                        <p class="mb-1">$<?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="btn-group" role="group">
                                            <a href="artwork.php?id=<?php echo $item['artwork_id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                View Details
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm remove-from-collection"
                                                    data-artwork-id="<?php echo $item['artwork_id']; ?>"
                                                    data-collection-id="<?php echo $item['collection_id']; ?>">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($collectionsData['total_pages'] > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Collection pagination">
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $collectionsData['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo ($filters['page'] == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="#" data-page="<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?></div>
                    <p class="card-text">
                        $<?php echo number_format($item['price'], 2); ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100" role="group">
                        <a href="artwork.php?id=<?php echo $item['artwork_id']; ?>" 
                           class="btn btn-outline-primary btn-sm">
                            View Details
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm remove-from-collection"
                                data-artwork-id="<?php echo $item['artwork_id']; ?>"
                                data-collection-id="<?php echo $item['collection_id']; ?>">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($collectionsData['total_pages'] > 1): ?>
<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Collection pagination">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $collectionsData['total_pages']; $i++): ?>
                <li class="page-item <?php echo ($filters['page'] == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="#" data-page="<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<?php endif; ?></a></div>