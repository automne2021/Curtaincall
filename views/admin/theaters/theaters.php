<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">Theaters Management</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/createTheater" class="btn btn-primary btn-add">
        <i class="bi bi-plus-circle"></i> <span>Add New Theater</span>
    </a>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-modern" id="theatersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($theaters)): ?>
                        <tr>
                            <td colspan="4" class="text-center empty-table">
                                <i class="bi bi-exclamation-circle"></i>
                                <p>No theaters found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($theaters as $index => $theater): ?>
                            <tr style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;" class="fade-in">
                                <td class="theater-id"><?= htmlspecialchars($theater['theater_id']) ?></td>
                                <td class="theater-title"><?= htmlspecialchars($theater['name']) ?></td>
                                <td><?= htmlspecialchars($theater['location']) ?></td>
                                <td class="text-center action-buttons">
                                    <a href="<?= BASE_URL ?>index.php?route=admin/editTheater&id=<?= $theater['theater_id'] ?>" 
                                    class="btn btn-icon btn-edit" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button data-bs-toggle="modal" data-bs-target="#deleteTheaterModal" 
                                            data-theater-id="<?= $theater['theater_id'] ?>" 
                                            data-theater-name="<?= htmlspecialchars($theater['name']) ?>"
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
<?php if (!empty($theaters) && $pagination['last_page'] > 1): ?>
    <?php include 'views/admin/pagination.php'; ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTheaterModal" tabindex="-1" aria-labelledby="deleteTheaterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTheaterModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger display-5 mb-3"></i>
                    <p>Are you sure you want to delete the theater:</p>
                    <h5 class="fw-bold" id="theaterName"></h5>
                </div>
                <p class="text-danger mt-3 mb-0 text-center">
                    <small><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</small>
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
    const deleteModal = document.getElementById('deleteTheaterModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const theaterId = button.getAttribute('data-theater-id');
            const theaterName = button.getAttribute('data-theater-name');
            
            document.getElementById('theaterName').textContent = theaterName;
            document.getElementById('confirmDelete').href = `index.php?route=admin/deleteTheater&id=${theaterId}`;
        });
    }
});
</script>