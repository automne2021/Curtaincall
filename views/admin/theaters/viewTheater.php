<div class="mb-4 fade-in">
    <a href="<?= BASE_URL ?>index.php?route=admin/theaters" class="btn btn-outline-primary back-button">
        <i class="bi bi-arrow-left"></i> Back to Theaters
    </a>
</div>

<div class="theater-view-container fade-in" style="animation-delay: 0.1s;">
    <div class="theater-header">
        <div class="theater-header-content">
            <span class="theater-id-badge"><?= htmlspecialchars($theater['theater_id']) ?></span>
            <h1 class="theater-title"><?= htmlspecialchars($theater['name']) ?></h1>
            <div class="theater-location">
                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($theater['location']) ?>
            </div>
        </div>
    </div>
    
    <div class="theater-content">
        <div class="theater-actions">
            <a href="<?= BASE_URL ?>index.php?route=admin/editTheater&id=<?= $theater['theater_id'] ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Theater
            </a>
            <button type="button" 
                    class="btn btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteTheaterModal" 
                    data-theater-id="<?= $theater['theater_id'] ?>"
                    data-theater-name="<?= htmlspecialchars($theater['name']) ?>">
                <i class="bi bi-trash"></i> Delete Theater
            </button>
        </div>

        <div class="theater-section">
            <h2 class="section-title">Theater Information</h2>
            <div class="theater-info">
                <div class="info-card">
                    <i class="bi bi-building"></i>
                    <div class="info-card-title">Theater ID</div>
                    <div class="info-card-value"><?= htmlspecialchars($theater['theater_id']) ?></div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-card-heading"></i>
                    <div class="info-card-title">Name</div>
                    <div class="info-card-value"><?= htmlspecialchars($theater['name']) ?></div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-geo-alt"></i>
                    <div class="info-card-title">Location</div>
                    <div class="info-card-value"><?= htmlspecialchars($theater['location']) ?></div>
                </div>
            </div>
        </div>
        
        <div class="theater-section">
            <h2 class="section-title">Seat Map</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Seat map legend -->
                    <div class="seat-legend mb-4">
                        <div class="d-flex flex-wrap justify-content-center">
                            <?php foreach ($seatTypes as $type => $price): ?>
                                <div class="d-flex align-items-center me-4 mb-2">
                                    <div class="seat <?= strtolower($type) ?> me-2"></div> 
                                    <?= $type ?> (<?= number_format($price) ?> VND)
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="screen">SCREEN</div>
                    </div>
                    
                    <div class="seat-map">
                        <?php 
                        // Create a simplified seat map based on seat IDs
                        // Organize seats by row
                        $seatsByRow = [];
                        foreach ($seatMap as $seat) {
                            $seatId = $seat['seat_id'];
                            $row = substr($seatId, 0, 1); // Extract the row letter
                            if (!isset($seatsByRow[$row])) {
                                $seatsByRow[$row] = [];
                            }
                            $seatsByRow[$row][] = $seat;
                        }
                        
                        // Sort rows by letter
                        ksort($seatsByRow);
                        
                        // Display seats by row
                        foreach ($seatsByRow as $row => $seats): 
                        ?>
                            <div class="seat-row mb-2">
                                <div class="row-label"><?= $row ?></div>
                                <div class="d-flex justify-content-center flex-wrap">
                                    <?php 
                                    // Sort seats by number within each row
                                    usort($seats, function($a, $b) {
                                        $numA = intval(substr($a['seat_id'], 1));
                                        $numB = intval(substr($b['seat_id'], 1));
                                        return $numA - $numB;
                                    });
                                    
                                    foreach ($seats as $seat): 
                                        $seatId = $seat['seat_id'];
                                        $seatType = $seat['seat_type'];
                                        $seatClass = strtolower($seatType);
                                    ?>
                                        <div class="seat-container">
                                            <div class="seat <?= $seatClass ?>" title="<?= $seatId ?> (<?= $seatType ?>)">
                                                <span class="seat-number"><?= substr($seatId, 1) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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