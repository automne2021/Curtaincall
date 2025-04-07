<main class="container-fluid px-4">
    <?php include 'views/booking/breadcrumb.php'; ?>
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="section-title text-center">Confirm Booking</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Confirm Your Booking</h3>
                </div>
                <div class="card-body">
                    <div class="booking-summary mb-4">
                        <h4 class="card-title"><?= htmlspecialchars($play['title']) ?></h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><i class="bi bi-geo-alt"></i> <strong>Theater:</strong> <?= htmlspecialchars($theater['name']) ?></p>
                                <p><i class="bi bi-calendar"></i> <strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking_details['schedule_date'])) ?></p>
                                <p><i class="bi bi-clock"></i> <strong>Time:</strong> <?= date('g:i A', strtotime($booking_details['schedule_time'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="bi bi-person"></i> <strong>Booked by:</strong> <?= htmlspecialchars($_SESSION['user']['username']) ?></p>
                                <p><i class="bi bi-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                            </div>
                        </div>
                        
                        <h5>Selected Seats</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Seat</th>
                                        <th>Type</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($selectedSeatsInfo as $seat): ?>
                                        <tr>
                                            <td><?= $seat['seat_id'] ?></td>
                                            <td><?= $seat['seat_type'] ?></td>
                                            <td class="text-end"><?= number_format($seat['price']) ?> VND</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light">
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td class="text-end"><strong><?= number_format($totalPrice) ?> VND</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Please note that your booking will expire in 15 minutes if payment is not completed.
                        </div>
                    </div>
                    
                    <div class="payment-options">
                        <h5 class="mb-3">Payment Methods</h5>
                        <form action="index.php?route=booking/complete" method="POST">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo" checked>
                                <label class="form-check-label" for="momo">
                                    <img src="public/images/payments/momo.png" alt="MoMo" height="20"> MoMo
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="zalopay" value="zalopay">
                                <label class="form-check-label" for="zalopay">
                                    <img src="public/images/payments/zalopay.png" alt="ZaloPay" height="20"> ZaloPay
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="banking" value="banking">
                                <label class="form-check-label" for="banking">
                                    <img src="public/images/payments/bank.png" alt="Banking" height="20"> Internet Banking
                                </label>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php?route=booking/selectSeats&play_id=<?= $play_id ?>&return=true" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Seat Selection
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Proceed to Payment <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>