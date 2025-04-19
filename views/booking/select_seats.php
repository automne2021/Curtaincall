<main class="container-fluid px-4">
    <?php include 'views/booking/breadcrumb.php'; ?>
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4"><?= htmlspecialchars($play['title']) ?></h2>
            <div class="booking-info text-center mb-4">
                <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($_SESSION['booking_details']['schedule_date'])) ?></p>
                <p><strong>Time:</strong> <?= date('g:i A', strtotime($_SESSION['booking_details']['schedule_time'])) ?></p>
                <p><strong>Theater:</strong> <?= htmlspecialchars($theater['name']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Select Your Seats</h4>
                </div>
                <div class="card-body">
                    <!-- Seat map legend -->
                    <div class="seat-legend mb-4">
                        <div class="d-flex flex-wrap justify-content-center">
                            <div class="d-flex align-items-center me-4 mb-2">
                                <div class="seat available me-2"></div> Available
                            </div>
                            <div class="d-flex align-items-center me-4 mb-2">
                                <div class="seat selected me-2"></div> Selected
                            </div>
                            <div class="d-flex align-items-center me-4 mb-2">
                                <div class="seat booked me-2"></div> Booked
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="seat vip me-2"></div> VIP
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="screen">SCREEN</div>
                    </div>
                    
                    <form id="seat-selection-form" action="index.php?route=booking/confirm" method="POST">
                        <div class="seat-map">
                            <?php 
                            // Create a simplified seat map based on seat IDs
                            // This assumes seat IDs are in format like "A1", "B3", etc.
                            
                            // First, organize seats by row
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
                                            $status = $seatAvailability[$seatId] ?? 'Available';
                                            $disabled = ($status !== 'Available') ? 'disabled' : '';
                                            $price = $seatPrices[$seatType];
                                            $seatClass = strtolower($seatType) . ' ' . strtolower($status);
                                        ?>
                                            <div class="seat-container">
                                                <input type="checkbox" name="seats[]" id="seat_<?= $seatId ?>" 
                                                       value="<?= $seatId ?>" class="seat-checkbox" <?= $disabled ?> 
                                                       data-price="<?= $price ?>">
                                                <label for="seat_<?= $seatId ?>" class="seat <?= $seatClass ?>" 
                                                       title="<?= $seatId ?> (<?= number_format($price) ?> VND)">
                                                    <span class="seat-number"><?= substr($seatId, 1) ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <a href="index.php?route=play/view&play_id=<?= $play['play_id'] ?>#scheduleSection" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to Play
                                    </a>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg" disabled id="continue-btn">
                                        Continue to Checkout <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Booking Summary</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($play['title']) ?></h5>
                    <p><?= htmlspecialchars($theater['name']) ?></p>
                    <p><i class="bi bi-calendar"></i> <?= date('l, F j, Y', strtotime($_SESSION['booking_details']['schedule_date'])) ?></p>
                    <p><i class="bi bi-clock"></i> <?= date('g:i A', strtotime($_SESSION['booking_details']['schedule_time'])) ?></p>
                    
                    <hr>
                    
                    <div id="selected-seats-summary">
                        <p>No seats selected</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <h5>Total:</h5>
                        <h5 id="total-price">0 VND</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatCheckboxes = document.querySelectorAll('.seat-checkbox');
    const continueBtn = document.getElementById('continue-btn');
    const selectedSeatsSummary = document.getElementById('selected-seats-summary');
    const totalPriceElement = document.getElementById('total-price');
    
    seatCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });
    
    function updateSummary() {
        const selectedSeats = Array.from(seatCheckboxes).filter(cb => cb.checked);
        
        // Enable/disable continue button
        continueBtn.disabled = selectedSeats.length === 0;
        
        let summaryHTML = '';
        let totalPrice = 0;
        
        if (selectedSeats.length > 0) {
            summaryHTML = '<h6>Selected Seats:</h6><ul class="list-unstyled">';
            
            selectedSeats.forEach(seat => {
                const seatId = seat.value;
                const price = parseInt(seat.dataset.price);
                totalPrice += price;
                
                summaryHTML += `<li><div class="d-flex justify-content-between">
                                <span>Seat ${seatId}</span>
                                <span>${price.toLocaleString()} VND</span>
                              </div></li>`;
            });
            
            summaryHTML += '</ul>';
        } else {
            summaryHTML = '<p>No seats selected</p>';
        }
        
        selectedSeatsSummary.innerHTML = summaryHTML;
        totalPriceElement.textContent = totalPrice.toLocaleString() + ' VND';
    }
});
</script>