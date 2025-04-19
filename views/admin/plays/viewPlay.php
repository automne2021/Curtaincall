<div class="mb-4 fade-in">
    <a href="<?= BASE_URL ?>index.php?route=admin/plays" class="btn btn-outline-primary back-button">
        <i class="bi bi-arrow-left"></i> Back to Plays
    </a>
</div>

<div class="play-view-container fade-in" style="animation-delay: 0.1s;">
    <div class="play-header">
        <img src="<?= BASE_URL . $play['image'] ?>" class="play-header-image" alt="<?= htmlspecialchars($play['title']) ?>">
        <div class="play-header-overlay"></div>
        <div class="play-header-content">
            <span class="play-id-badge"><?= htmlspecialchars($play['play_id']) ?></span>
            <h1 class="play-title"><?= htmlspecialchars($play['title']) ?></h1>
            <div class="play-theater">
                <i class="bi bi-building"></i> <?= htmlspecialchars($play['theater_name']) ?>
            </div>
        </div>
    </div>
    
    <div class="play-content">
        <div class="play-actions">
            <a href="<?= BASE_URL ?>index.php?route=admin/editPlay&id=<?= $play['play_id'] ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Play
            </a>
            <button type="button" 
                    class="btn btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal" 
                    data-play-id="<?= $play['play_id'] ?>"
                    data-play-title="<?= htmlspecialchars($play['title']) ?>">
                <i class="bi bi-trash"></i> Delete Play
            </button>
        </div>

        <div class="play-section">
            <h2 class="section-title">Performance Schedule</h2>
            <?php if (empty($schedules)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> No schedules have been set for this play.
                </div>
            <?php else: ?>
                <div class="table-responsive mt-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $index => $schedule): 
                                // Calculate duration
                                $start = strtotime($schedule['start_time']);
                                $end = strtotime($schedule['end_time']);
                                $duration = $end - $start;
                                if ($duration < 0) {
                                    $duration += 24 * 3600; // Add 24 hours if ending next day
                                }
                                $hours = floor($duration / 3600);
                                $minutes = floor(($duration % 3600) / 60);
                            ?>
                                <tr class="fade-in" style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;">
                                    <td><?= date('F j, Y (l)', strtotime($schedule['date'])) ?></td>
                                    <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                                    <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                                    <td><?= $hours ?>h <?= $minutes ?>m</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="play-section">
            <h2 class="section-title">Performance Details</h2>
            <div class="play-info">
                <div class="info-card">
                    <i class="bi bi-calendar-event"></i>
                    <div class="info-card-title">Total Performances</div>
                    <div class="info-card-value">
                        <?= count($schedules) ?> performances
                    </div>
                </div>
                
                <?php if (!empty($schedules)): ?>
                <div class="info-card">
                    <i class="bi bi-clock"></i>
                    <div class="info-card-title">Next Performance</div>
                    <div class="info-card-value">
                        <?= date('M j, Y', strtotime($schedules[0]['date'])) ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="info-card">
                    <i class="bi bi-eye"></i>
                    <div class="info-card-title">Views</div>
                    <div class="info-card-value">
                        <?= number_format($play['views'] ?? 0) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="play-section">
            <h2 class="section-title">Description</h2>
            <div class="play-description">
                <?= $play['description'] ?? 'No description available.' ?>
            </div>
        </div>
        
        <div class="play-section">
            <h2 class="section-title">Theater Information</h2>
            <div class="play-info">
                <div class="info-card">
                    <i class="bi bi-building"></i>
                    <div class="info-card-title">Theater ID</div>
                    <div class="info-card-value">
                        <?= htmlspecialchars($theater['theater_id'] ?? 'N/A') ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-geo-alt"></i>
                    <div class="info-card-title">Location</div>
                    <div class="info-card-value">
                        <?= htmlspecialchars($theater['location'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const playId = button.getAttribute('data-play-id');
            const playTitle = button.getAttribute('data-play-title');
            
            document.getElementById('playTitle').textContent = playTitle;
            document.getElementById('confirmDelete').href = `index.php?route=admin/deletePlay&id=${playId}`;
        });
    }
});
</script>