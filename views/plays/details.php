<main class="container-fluid px-3">
    <div class="row my-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="index.php?route=play&theater_id=<?= $play['theater_id'] ?>"><?= $play['theater_name'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $play['title'] ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="play-details-container bg-dark">
        <div class="row h-100">
            <!-- Play image -->
            <div class="col-md-8 play-image-column">
                <img src="<?= $play['image'] ?>" class="play-detail-img" alt="<?= $play['title'] ?>">
            </div>

            <!-- Play details -->
            <div class="col-md-4">
                <h4 class="play-title mx-3"><?= '[', $play['theater_name'], '] ', $play['title'] ?></h4>
                <div class="play-meta bg-dark text-white">
                    <div class="row">
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event me-2"></i>
                                <div class="meta-value">
                                    <?php if (!empty($play['date'])): ?>
                                        <?= date("H:i", strtotime($play['start_time'])) ?> - <?= date("H:i", strtotime($play['end_time'])) ?>, <?= date("d \\t\h\á\\n\g m, Y", strtotime($play['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-geo-alt me-2"></i>
                                <div class="meta-value"><?= $play['theater_name'] ?></div>
                                <p class="meta-location"><?= $play['theater_location'] ?? 'Location not available' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="play-actions text-center">
                    <div>
                        <hr class="section-divider">
                        <p class="meta-price mb-1"><span class="text-white">Giá chỉ từ</span> <?= number_format($play['min_price'], 0, ',', '.') ?>đ</p>
                    </div>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="#scheduleSection" class="btn">
                            <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                        </a>
                        <?php $_SESSION['redirect_after_login'] = 'index.php?route=play/view&play_id=' . $play['play_id'] . '#scheduleSection'; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="border-dashed-vertical"></div>
    </div>
    <?php if (!isset($_SESSION['user'])): ?>
        <?php include 'views/auth/login-modal.php'; ?>
    <?php endif; ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="section-title mb-0">Giới thiệu</h5>
                </div>
                <div class="card-body">
                    <div class="description-content">
                        <?= $play['description'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Play schedule -->
    <div class="play-schedule my-4" id="scheduleSection">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="section-title mb-0">Lịch diễn</h5>
            </div>
            <div class="card-body">
                <?php if ($has_schedules): ?>
                    <form action="index.php?route=booking/selectSeats" method="POST" id="scheduleForm">
                        <input type="hidden" name="play_id" value="<?= $play['play_id'] ?>">

                        <div class="form-group">
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="flex-grow-1 me-3 mb-2">
                                    <select name="schedule_time" id="schedule_time" class="form-select" required>
                                        <option value="">-- Chọn lịch diễn --</option>
                                        <?php foreach ($schedules as $schedule): ?>
                                            <?php
                                            $formatted_date = date('d \\t\h\á\\n\g m, Y', strtotime($schedule['date']));
                                            $formatted_time = date('H:i', strtotime($schedule['start_time'])) . ' - ' .
                                                date('H:i', strtotime($schedule['end_time']));
                                            $schedule_value = $schedule['play_id'] . '_' . strtotime($schedule['date'] . ' ' . $schedule['start_time']);
                                            ?>
                                            <option value="<?= $schedule_value ?>">
                                                <?= $formatted_date ?> (<?= $formatted_time ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <button type="submit" class="btn buy-ticket-btn h-100">
                                        <i class="bi bi-ticket-perforated me-2"></i>Mua vé ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Hiện không có lịch diễn nào cho vở kịch này.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateSelect = document.getElementById('schedule_date');
        const timeSlots = document.getElementById('time-slots');

        if (dateSelect) {
            dateSelect.addEventListener('change', function() {
                const selectedDate = this.value;

                if (selectedDate) {
                    // Show the time-slots div
                    timeSlots.classList.remove('d-none');

                    // Hide all time slot divs
                    document.querySelectorAll('.time-slot').forEach(slot => {
                        slot.style.display = 'none';
                    });

                    // Show only the time slot div for the selected date
                    const selectedSlot = document.querySelector(`.time-slot[data-date="${selectedDate}"]`);
                    if (selectedSlot) {
                        selectedSlot.style.display = 'block';
                    }
                } else {
                    timeSlots.classList.add('d-none');
                }
            });
        }
    });
</script>