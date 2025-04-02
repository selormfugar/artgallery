<?php
if (isset($total_pages)) {
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
?>
    <nav aria-label="Collection pagination">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" <?php echo ($current_page <= 1) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>Previous</a>
            </li>
            
            <!-- Page numbers -->
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <!-- Next button -->
            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" <?php echo ($current_page >= $total_pages) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>Next</a>
            </li>
        </ul>
    </nav>
<?php
}
?>