<main class="container-fluid px-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="section-title text-center">Your Booking History</h2>
        </div>
    </div>
    
    <?php 
    // Filter to show only paid tickets
    $paidBookings = array_filter($bookings, function($booking) {
        return $booking['status'] === 'Paid';
    });
    
    if (empty($paidBookings)): 
    ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> You don't have any confirmed tickets yet.
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Browse Plays</a>
        </div>
    <?php else: ?>
        <div class="row" id="bookings-container">
            <?php 
            // Get current page from query string, default to 1 if not set
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $per_page = 4;
            
            // Calculate total pages
            $total_bookings = count($paidBookings);
            $total_pages = ceil($total_bookings / $per_page);
            
            // Ensure current page is valid
            $page = min($page, $total_pages);
            
            // Calculate starting index for the current page
            $start_index = ($page - 1) * $per_page;
            
            // Get bookings for current page
            $current_page_bookings = array_slice($paidBookings, $start_index, $per_page);
            
            // Get the booking_id parameter safely
            $highlightBookingId = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;
            
            foreach ($current_page_bookings as $booking): 
            ?>
                <!-- Add payment success highlight class -->
                <div class="col-md-6 mb-4 booking-item">
                    <div class="card h-100 <?= (isset($_GET['payment_success']) && $_GET['payment_success'] == 1 && $highlightBookingId == $booking['booking_id']) ? 'paid-highlight' : '' ?>">
                        <div class="card-header d-flex justify-content-between align-items-center paid-header">
                            <h5 class="mb-0">
                                Booking #<?= $booking['booking_id'] ?>
                                <i class="bi bi-check-circle-fill text-success ms-2"></i>
                            </h5>
                            <span class="badge bg-success">
                                <?= $booking['status'] ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($booking['play_title']) ?></h5>
                            <p class="card-text"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($booking['theater_name']) ?></p>
                            <p class="card-text">
                                <i class="bi bi-calendar"></i> <?= date('l, F j, Y', strtotime($booking['schedule_date'])) ?><br>
                                <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-ticket-perforated"></i> Seat <?= $booking['seat_id'] ?> (<?= $booking['seat_type'] ?>)
                            </p>
                            <p class="card-text">
                                <strong>Price:</strong> <?= number_format($booking['price']) ?> VND
                            </p>
                            
                            <div class="paid-ticket-label">
                                <i class="bi bi-ticket-perforated-fill"></i> Confirmed Ticket
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="index.php?route=booking/download-ticket&id=<?= $booking['booking_id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-download"></i> Download Ticket
                            </a>
                            
                            <span class="float-end text-muted small">
                                Booked on <?= date('M j, Y', strtotime($booking['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
        // Setup pagination
        $pagination = [
            'total' => $total_bookings,
            'per_page' => $per_page,
            'current_page' => $page,
            'last_page' => $total_pages
        ];
        
        // Base URL for pagination, preserving payment_success and booking_id parameters
        $base_url = 'index.php?route=booking/history';
        if (isset($_GET['payment_success']) && $_GET['payment_success'] == 1) {
            $base_url .= '&payment_success=1';
        }
        if (isset($_GET['booking_id'])) {
            $base_url .= '&booking_id=' . $_GET['booking_id'];
        }
        
        // Include the pagination component
        include 'views/admin/pagination.php';
        ?>
    <?php endif; ?>
</main>