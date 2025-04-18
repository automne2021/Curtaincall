<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">Plays Management</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/createPlay" class="btn btn-primary btn-add">
        <i class="bi bi-plus-circle"></i> <span>Add New Play</span>
    </a>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern" id="playsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Theater</th>
                        <th>Duration (min)</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plays)): ?>
                        <tr>
                            <td colspan="5" class="text-center empty-table">
                                <i class="bi bi-exclamation-circle"></i>
                                <p>No plays found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plays as $index => $play): ?>
                        <tr style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;" class="fade-in">
                            <td class="play-id"><?= htmlspecialchars($play['play_id'] ?? 'N/A') ?></td>
                            <td class="play-title"><?= htmlspecialchars($play['title'] ?? 'No Title') ?></td>
                            <td><?= htmlspecialchars($play['theater_name'] ?? 'Unknown Theater') ?></td>
                            <td><?= htmlspecialchars($play['duration'] ?? 'N/A') ?></td>
                            <td class="text-center action-buttons">
                                <a href="<?= BASE_URL ?>index.php?route=admin/editPlay&id=<?= $play['play_id'] ?>" 
                                   class="btn btn-icon btn-edit" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top" 
                                   title="Edit Play">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-icon btn-delete" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        data-play-id="<?= $play['play_id'] ?>"
                                        data-play-title="<?= htmlspecialchars($play['title'] ?? 'Play') ?>"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Delete Play">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php 
            $base_url = BASE_URL . 'index.php?route=admin/plays';
            include 'views/admin/pagination.php'; 
            ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger display-5 mb-3"></i>
                    <p>Are you sure you want to delete the play:</p>
                    <h5 class="fw-bold" id="playTitle"></h5>
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