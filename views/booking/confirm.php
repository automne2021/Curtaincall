<main class="container-fluid px-4">
    <?php include 'views/booking/breadcrumb.php'; ?>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Xác nhận đặt vé</h3>
                    <div class="countdown-timer">
                        Đơn hàng sẽ hết hạn trong: <span id="countdown">15:00</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="booking-summary mb-4">
                        <h4 class="card-title"><?= htmlspecialchars($play['title']) ?></h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><i class="bi bi-geo-alt"></i> <strong>Nhà hát:</strong> <?= htmlspecialchars($theater['name']) ?></p>
                                <p><i class="bi bi-calendar"></i> <strong>Ngày:</strong> <?= date('l, d/m/Y', strtotime($booking_details['schedule_date'])) ?></p>
                                <p><i class="bi bi-clock"></i> <strong>Giờ:</strong> <?= date('H:i', strtotime($booking_details['schedule_time'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="bi bi-person"></i> <strong>Đặt bởi:</strong> <?= htmlspecialchars($_SESSION['user']['username']) ?></p>
                                <p><i class="bi bi-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                            </div>
                        </div>
                        
                        <h5>Ghế đã chọn</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Ghế</th>
                                        <th>Loại</th>
                                        <th class="text-end">Giá</th>
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
                                        <td colspan="2"><strong>Tổng tiền</strong></td>
                                        <td class="text-end"><strong><?= number_format($totalPrice) ?> VND</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Đơn hàng sẽ tự động hủy sau 15 phút nếu không hoàn tất thanh toán.
                        </div>
                    </div>
                    
                    <div class="payment-options">
                        <h5 class="mb-3">Phương thức thanh toán</h5>
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
                                <a href="index.php?route=booking/cancelConfirmation" class="btn btn-outline-secondary cancel-btn">
                                    Hủy đơn
                                </a>
                                <button type="submit" class="btn btn-primary pay-btn">
                                    Thanh toán <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận hủy đơn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy đơn đặt vé này không?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quay lại</button>
                <a href="index.php?route=booking/cancelConfirmation" class="btn btn-danger">Hủy đơn</a>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown timer
function startCountdown() {
    let timeLeft = 15 * 60; // 15 minutes in seconds
    const countdownElement = document.getElementById('countdown');
    
    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            alert('Thời gian đặt vé đã hết. Đơn hàng sẽ bị hủy.');
            window.location.href = 'index.php?route=booking/cancelConfirmation';
        }
        
        // Change color as time gets low
        if (timeLeft <= 60) {
            countdownElement.style.color = '#dc3545'; // Red
            countdownElement.classList.add('blinking');
        } else if (timeLeft <= 180) {
            countdownElement.style.color = '#ffc107'; // Yellow
        }
        
        timeLeft--;
    }
    
    // Initial update
    updateCountdown();
    
    // Start timer
    const timerInterval = setInterval(updateCountdown, 1000);
}

// Attach cancel confirmation to cancel button
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.querySelector('.cancel-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmModal = new bootstrap.Modal(document.getElementById('cancelConfirmModal'));
            confirmModal.show();
        });
    }
    
    // Start the countdown
    startCountdown();
});
</script>