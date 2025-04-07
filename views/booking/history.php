<main class="container-fluid px-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="section-title text-center">Your Booking History</h2>
        </div>
    </div>
    
    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> You don't have any bookings yet.
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Browse Plays</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($bookings as $booking): ?>
                <!-- Add payment success highlight class -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 <?= (isset($_GET['payment_success']) && $_GET['payment_success'] == 1 && $booking['status'] == 'Paid') ? 'paid-highlight' : '' ?>">
                        <div class="card-header d-flex justify-content-between align-items-center <?= $booking['status'] == 'Paid' ? 'paid-header' : '' ?>">
                            <h5 class="mb-0">
                                Booking #<?= $booking['booking_id'] ?>
                                <?php if($booking['status'] == 'Paid'): ?>
                                    <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                <?php endif; ?>
                            </h5>
                            <span class="badge bg-<?= $booking['status'] == 'Paid' ? 'success' : ($booking['status'] == 'Pending' ? 'warning' : 'danger') ?>">
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
                            
                            <?php if($booking['status'] == 'Paid'): ?>
                                <div class="paid-ticket-label">
                                    <i class="bi bi-ticket-perforated-fill"></i> Confirmed Ticket
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] === 'Pending'): ?>
                                <p class="card-text text-danger">
                                    <i class="bi bi-exclamation-circle"></i> Expires at: <?= date('g:i A, F j, Y', strtotime($booking['expires_at'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <?php if ($booking['status'] === 'Pending'): ?>
                                <a href="index.php?route=payment/process&booking_id=<?= $booking['booking_id'] ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-credit-card"></i> Complete Payment
                                </a>
                                <a href="index.php?route=booking/cancel&id=<?= $booking['booking_id'] ?>" 
                                class="btn btn-outline-danger btn-sm ms-2"
                                onclick="return confirm('Are you sure you want to cancel this booking?');">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            <?php elseif ($booking['status'] === 'Expired'): ?>
                                <a href="index.php?route=booking/cancel&id=<?= $booking['booking_id'] ?>" 
                                class="btn btn-outline-secondary btn-sm"
                                onclick="return confirm('Remove this expired booking from your history?');">
                                    <i class="bi bi-trash"></i> Remove
                                </a>
                            <?php elseif ($booking['status'] === 'Paid'): ?>
                                <a href="index.php?route=booking/download-ticket&id=<?= $booking['booking_id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i> Download Ticket
                                </a>
                            <?php endif; ?>
                            
                            <span class="float-end text-muted small">
                                Booked on <?= date('M j, Y', strtotime($booking['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php 
    // Check if there are any pending bookings
    $has_pending = false;
    foreach ($bookings as $booking) {
        if ($booking['status'] === 'Pending') {
            $has_pending = true;
            break;
        }
    }
    ?>

    <?php if ($has_pending): ?>
    <script>
    // Auto-refresh the page every 30 seconds if there are pending bookings
    setTimeout(function() {
        location.reload();
    }, 30000);
    </script>
    <?php endif; ?>
    
    <!-- Add payment success toast notification -->
    <?php if(isset($_GET['payment_success']) && $_GET['payment_success'] == 1): ?>
    <div class="payment-toast" id="paymentSuccessToast">
        <div class="toast-header">
            <p class="toast-title"><i class="bi bi-check-circle-fill"></i> Payment Successful</p>
            <button type="button" class="close-toast" onclick="document.getElementById('paymentSuccessToast').style.display='none';">&times;</button>
        </div>
        <div class="toast-body">
            Your payment has been processed and your tickets are confirmed!
        </div>
    </div>

    <script>
    // Auto-hide the toast after 5 seconds
    setTimeout(function() {
        const toast = document.getElementById('paymentSuccessToast');
        if (toast) {
            toast.style.display = 'none';
        }
    }, 5000);
    </script>
    <?php endif; ?>
</main>