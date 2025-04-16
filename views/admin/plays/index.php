<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Plays Management</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/createPlay" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Play
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="playsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Theater</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plays as $play): ?>
                    <tr>
                        <td><?= $play['play_id'] ?></td>
                        <td>
                            <?php if ($play['image']): ?>
                                <img src="<?= BASE_URL . $play['image'] ?>" alt="<?= htmlspecialchars($play['title']) ?>" class="admin-thumbnail">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($play['title']) ?></td>
                        <td><?= htmlspecialchars($play['theater_name']) ?></td>
                        <td><?= $play['duration'] ?> minutes</td>
                        <td>
                            <a href="<?= BASE_URL ?>index.php?route=admin/editPlay&id=<?= $play['play_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal" 
                                    data-play-id="<?= $play['play_id'] ?>"
                                    data-play-title="<?= htmlspecialchars($play['title']) ?>">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the play "<span id="playTitle"></span>"?
                <p class="text-danger mt-3">
                    <i class="bi bi-exclamation-triangle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>