<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Add New Play</h1>
        <a href="index.php?route=admin/plays" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Plays
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="index.php?route=admin/create-play" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="theater_id" class="form-label">Theater *</label>
                            <select class="form-select" id="theater_id" name="theater_id" required>
                                <option value="">Select Theater</option>
                                <?php foreach ($theaters as $theater): ?>
                                    <option value="<?= $theater['theater_id'] ?>"><?= htmlspecialchars($theater['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (minutes) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="director" class="form-label">Director</label>
                            <input type="text" class="form-control" id="director" name="director">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="cast" class="form-label">Cast</label>
                    <input type="text" class="form-control" id="cast" name="cast">
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Play Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Play
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>