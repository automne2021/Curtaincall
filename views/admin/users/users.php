<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">User Management</h1>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="text-center empty-table">
                                <i class="bi bi-exclamation-circle"></i>
                                <p>No users found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;" class="fade-in">
                                <td class="user-name-cell">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?= BASE_URL . $user['avatar'] ?>" class="user-avatar" alt="<?= htmlspecialchars($user['username']) ?>'s avatar">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="user-name"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></p>
                                        <p class="user-email d-md-none"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell"><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="text-center action-buttons">
                                    <a href="<?= BASE_URL ?>index.php?route=admin/viewUser&id=<?= $user['user_id'] ?>" 
                                        class="btn btn-icon btn-view" data-bs-toggle="tooltip" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button data-bs-toggle="modal" data-bs-target="#deleteUserModal" 
                                            data-user-id="<?= $user['user_id'] ?>" 
                                            data-user-name="<?= htmlspecialchars($user['fullname'] ?? $user['username']) ?>"
                                            class="btn btn-icon btn-delete" data-bs-toggle="tooltip" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include pagination -->
<?php if (!empty($users) && $pagination['last_page'] > 1): ?>
    <?php include 'views/admin/pagination.php'; ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger display-5 mb-3"></i>
                    <p>Are you sure you want to delete this user:</p>
                    <h5 class="fw-bold" id="userName"></h5>
                </div>
                <p class="text-danger mt-3 mb-0 text-center">
                    <small><i class="bi bi-exclamation-triangle"></i> This action cannot be undone. All user data including bookings will be deleted.</small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize delete confirmation modal
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteUserModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            
            document.getElementById('userName').textContent = userName;
            document.getElementById('confirmDelete').href = `index.php?route=admin/deleteUser&id=${userId}`;
        });
    }
});
</script>