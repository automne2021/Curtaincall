<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Plays</h1>
        <a href="index.php?route=admin/create-play" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Play
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
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
                                <?php if (!empty($play['image'])): ?>
                                    <img src="<?= $play['image'] ?>" alt="<?= htmlspecialchars($play['title']) ?>" class="img-preview">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($play['title']) ?></td>
                            <td><?= htmlspecialchars($play['theater_name']) ?></td>
                            <td><?= $play['duration'] ?> minutes</td>
                            <td>
                                <div class="btn-group">
                                    <a href="index.php?route=admin/edit-play&id=<?= $play['play_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="index.php?route=admin/delete-play&id=<?= $play['play_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this play?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($plays)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No plays found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>