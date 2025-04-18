<div class="mb-4 fade-in">
    <a href="<?= BASE_URL ?>index.php?route=admin/bookings" class="btn btn-outline-primary back-button">
        <i class="bi bi-arrow-left"></i> Back to Bookings
    </a>
</div>

<div class="booking-view-container fade-in" style="animation-delay: 0.1s;">
    <div class="booking-header">
        <div class="booking-header-content">
            <span class="booking-id-badge">Booking #<?= htmlspecialchars($booking['booking_id']) ?></span>
            <h1 class="booking-title"><?= htmlspecialchars($booking['play_title']) ?></h1>
            <div class="booking-status">
                <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                    <?= htmlspecialchars($booking['status']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="booking-content">
        <div class="booking-section">
            <h2 class="section-title">Booking Information</h2>
            <div class="booking-info">
                <div class="info-card">
                    <i class="bi bi-calendar-event"></i>
                    <div class="info-card-title">Booking Date</div>
                    <div class="info-card-value">
                        <?= date('F j, Y, g:i A', strtotime($booking['created_at'])) ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-calendar-check"></i>
                    <div class="info-card-title">Performance Date</div>
                    <div class="info-card-value">
                        <?= date('F j, Y', strtotime($booking['schedule_date'])) ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-clock"></i>
                    <div class="info-card-title">Show Time</div>
                    <div class="info-card-value">
                        <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                        <?= date('g:i A', strtotime($booking['end_time'])) ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="bi bi-cash-stack"></i>
                    <div class="info-card-title">Amount</div>
                    <div class="info-card-value">
                        <?= number_format($booking['amount']) ?> VND
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="booking-section">
                    <h2 class="section-title">Customer Information</h2>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person"></i> Name:</span>
                            <span class="info-value"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-envelope"></i> Email:</span>
                            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <?php if (!empty($user['phone'])): ?>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-telephone"></i> Phone:</span>
                            <span class="info-value"><?= htmlspecialchars($user['phone']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person-badge"></i> User ID:</span>
                            <span class="info-value"><?= htmlspecialchars($booking['user_id']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="booking-section">
                    <h2 class="section-title">Venue Information</h2>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-film"></i> Play:</span>
                            <span class="info-value"><?= htmlspecialchars($booking['play_title']) ?> (ID: <?= htmlspecialchars($booking['play_id']) ?>)</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-building"></i> Theater:</span>
                            <span class="info-value"><?= htmlspecialchars($booking['theater_name']) ?> (ID: <?= htmlspecialchars($booking['theater_id']) ?>)</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-geo-alt"></i> Location:</span>
                            <span class="info-value"><?= htmlspecialchars($theater['location']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-ticket-perforated"></i> Seat:</span>
                            <span class="info-value"><?= htmlspecialchars($booking['seat_id']) ?> (<?= htmlspecialchars($booking['seat_type']) ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="booking-section">
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
                            <div class="d-flex align-items-center me-4 mb-2">
                                <div class="seat booked me-2"></div> Booked
                            </div>
                            <div class="d-flex align-items-center me-4 mb-2">
                                <div class="seat selected me-2"></div> This Booking
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="screen">SCREEN</div>
                    </div>
                    
                    <div class="seat-map">
                        <?php 
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
                                        // Determine if this is the booked seat or another booked seat
                                        if ($seatId === $booking['seat_id']) {
                                            $seatClass = 'selected'; // This booking's seat
                                        } elseif (in_array($seatId, $bookedSeats)) {
                                            $seatClass = 'booked'; // Other booked seats
                                        } else {
                                            $seatClass = strtolower($seatType); // Available seats
                                        }
                                    ?>
                                        <div class="seat-container">
                                            <div class="seat <?= $seatClass ?>" 
                                                title="<?= $seatId ?> (<?= $seatType ?>)<?= $seatId === $booking['seat_id'] ? ' - Current Booking' : '' ?>">
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
        
        <?php if ($booking['status'] === 'Pending'): ?>
        <div class="booking-section">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                This booking is pending payment and will expire on <?= date('F j, Y, g:i A', strtotime($booking['expires_at'])) ?>.
            </div>
        </div>
        <?php elseif ($booking['status'] === 'Expired'): ?>
        <div class="booking-section">
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-octagon me-2"></i>
                This booking has expired (payment deadline: <?= date('F j, Y, g:i A', strtotime($booking['expires_at'])) ?>).
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>