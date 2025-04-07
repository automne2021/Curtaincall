<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Theaters</h1>
        <a href="index.php?route=admin/create-theater" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Theater
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($theaters as $theater): ?>
                        <tr>
                            <td><?= $theater['theater_id'] ?></td>
                            <td><?= htmlspecialchars($theater['name']) ?></td>
                            <td><?= htmlspecialchars($theater['location']) ?></td>
                            <td><?= $theater['capacity'] ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="index.php?route=admin/edit-theater&id=<?= $theater['theater_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="index.php?route=admin/delete-theater&id=<?= $theater['theater_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this theater?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($theaters)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No theaters found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>