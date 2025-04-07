<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Play</h1>
        <a href="index.php?route=admin/plays" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Plays
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="index.php?route=admin/edit-play&id=<?= $play['play_id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($play['title']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="theater_id" class="form-label">Theater *</label>
                            <select class="form-select" id="theater_id" name="theater_id" required>
                                <option value="">Select Theater</option>
                                <?php foreach ($theaters as $theater): ?>
                                    <option value="<?= $theater['theater_id'] ?>" <?= $theater['theater_id'] == $play['theater_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($theater['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (minutes) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="<?= $play['duration'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="director" class="form-label">Director</label>
                            <input type="text" class="form-control" id="director" name="director" value="<?= htmlspecialchars($play['director']) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="cast" class="form-label">Cast</label>
                    <input type="text" class="form-control" id="cast" name="cast" value="<?= htmlspecialchars($play['cast']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($play['description']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Play Image</label>
                    <?php if (!empty($play['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= $play['image'] ?>" alt="Current Image" class="img-thumbnail" style="max-height: 200px;">
                            <p class="small text-muted">Current image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="form-text text-muted">Leave empty to keep the current image</small>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Play
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>