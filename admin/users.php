<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
// requireLogin();

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get users with pagination
$users = getUsersWithPagination($offset, $perPage);
$totalUsers = getTotalUsersCount();
$totalPages = ceil($totalUsers / $perPage);

// Include header
include_once 'includes/header.php';
include_once 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">User Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshUsers">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportUsers">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">User List</h6>
            <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                    <input type="text" class="form-control" placeholder="Search users..." id="searchUsers">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="form-select form-select-sm" style="width: 120px;" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="artist">Artist</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars(($user['firstname'] . ' ' . $user['lastname']) ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($user['role']) {
                                                'artist' => 'primary',
                                                'admin' => 'danger',
                                                default => 'secondary'
                                            }; 
                                        ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                  
                                    <td><?php echo formatDate($user['created_at']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- <a href="<?php echo SITE_URL; ?>/admin/user-details.php?id=<?php echo $user['user_id']; ?>" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/admin/edit-user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a> -->
                                            <button class="btn btn-<?php echo $user['status'] === 'archived' ? 'secondary' : 'warning'; ?> archive-user" 
        data-id="<?php echo $user['user_id']; ?>" 
        title="Archive" 
        <?php echo $user['is_archived'] === 'archived' ? 'disabled' : ''; ?>>
    <i class="fas fa-archive"></i> 
    <?php echo $user['isarchived'] === 'archived' ? 'Archived' : 'Archive'; ?>
</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="User pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php 
                        // Show first page and ellipsis if needed
                        if ($page > 3): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1">1</a>
                            </li>
                            <?php if ($page > 4): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php 
                        // Show page numbers around current page
                        for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php 
                        // Show last page and ellipsis if needed
                        if ($page < $totalPages - 2): ?>
                            <?php if ($page < $totalPages - 3): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted">
                        Showing <?php echo ($offset + 1) . ' - ' . min($offset + $perPage, $totalUsers); ?> of <?php echo $totalUsers; ?> users
                    </div>
                </nav>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No users found</h4>
                    <p class="text-muted">There are currently no users in the system.</p>
                    <a href="<?php echo SITE_URL; ?>/admin/add-user.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Add New User
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Refresh users
    document.getElementById('refreshUsers').addEventListener('click', function() {
        location.reload();
    });

  // Archive user with confirmation
document.querySelectorAll('.archive-user').forEach(button => {
    button.addEventListener('click', function() {
        if (this.disabled) return;
        
        const userId = this.getAttribute('data-id');
        const userRow = this.closest('tr');
        const userName = userRow.querySelector('td:nth-child(3)').textContent;
        
        Swal.fire({
            title: 'Confirm Archive',
            html: `Are you sure you want to archive <strong>${userName}</strong>?<br>Archived users won't be able to log in.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, archive it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Archiving...';
                this.disabled = true;
                
                fetch('api/users.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        action: 'archive',
        user_id: userId
    })
})
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update UI to show archived state
                        this.innerHTML = '<i class="fas fa-archive"></i> Archived';
                        this.classList.remove('btn-warning');
                        this.classList.add('btn-secondary');
                        
                        Swal.fire({
                            title: 'Archived!',
                            text: 'The user has been archived successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Optionally refresh the page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to archive user');
                    }
                })
                .catch(error => {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error'
                    });
                });
            }
        });
    });
});
    // Delete user with confirmation
    // document.querySelectorAll('.archive-user').forEach(button => {
    //     button.addEventListener('click', function() {
    //         const userId = this.getAttribute('data-id');
    //         const userRow = this.closest('tr');
    //         const userName = userRow.querySelector('td:nth-child(3)').textContent;
            
    //         Swal.fire({
    //             title: 'Confirm Deletion',
    //             html: `Are you sure you want to delete <strong>${userName}</strong>?<br>This action cannot be undone.`,
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#d33',
    //             cancelButtonColor: '#3085d6',
    //             confirmButtonText: 'Yes, delete it!'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 fetch('/api/users.php', {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/x-www-form-urlencoded',
    //                     },
    //                     body: `action=delete&user_id=${userId}`
    //                 })
    //                 .then(response => response.json())
    //                 .then(data => {
    //                     if (data.success) {
    //                         Swal.fire(
    //                             'Deleted!',
    //                             'The user has been deleted.',
    //                             'success'
    //                         ).then(() => {
    //                             userRow.remove();
    //                             // Optionally reload the page to update pagination
    //                             location.reload();
    //                         });
    //                     } else {
    //                         Swal.fire(
    //                             'Error!',
    //                             'Failed to delete user: ' + data.message,
    //                             'error'
    //                         );
    //                     }
    //                 })
    //                 .catch(error => {
    //                     Swal.fire(
    //                         'Error!',
    //                         'An error occurred while deleting the user.',
    //                         'error'
    //                     );
    //                 });
    //             }
    //         });
    //     });
    // });

    // Role filter change
    document.getElementById('roleFilter').addEventListener('change', function() {
        const role = this.value;
        window.location.href = `?role=${role}&page=1`;
    });

    // Search functionality
    document.getElementById('searchUsers').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm.length > 0) {
                window.location.href = `?search=${encodeURIComponent(searchTerm)}&page=1`;
            }
        }
    });
});
</script>

