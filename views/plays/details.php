<?php
// filepath: c:\Users\VY\Downloads\curtaincall\views\plays\details.php
?>
<main class="container-fluid px-4">
    <div class="play-details-container">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php?route=play&theater_id=<?= $play['theater_id'] ?>"><?= $play['theater_name'] ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $play['title'] ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Play image -->
            <div class="col-md-5 mb-4">
                <div class="play-image-container">
                    <img src="<?= $play['image'] ?>" class="img-fluid play-detail-img" alt="<?= $play['title'] ?>">
                </div>
            </div>

            <!-- Play details -->
            <div class="col-md-7">
                <h1 class="play-title"><?= $play['title'] ?></h1>

                <div class="play-meta mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>
                                <span class="meta-label">Date:</span>
                                <span class="meta-value"><?= date("d/m/Y", strtotime($play['date'])) ?></span>
                            </div>
                            <?php if (isset($play['start_time']) && isset($play['end_time'])): ?>
                                <div class="meta-item">
                                    <i class="bi bi-clock me-2 text-primary"></i>
                                    <span class="meta-label">Time:</span>
                                    <span class="meta-value"><?= date("H:i", strtotime($play['start_time'])) ?> - <?= date("H:i", strtotime($play['end_time'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="meta-item">
                                <i class="bi bi-building me-2 text-primary"></i>
                                <span class="meta-label">Theater:</span>
                                <span class="meta-value"><?= $play['theater_name'] ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-geo-alt me-2 text-primary"></i>
                                <span class="meta-label">Location:</span>
                                <span class="meta-value"><?= $play['theater_location'] ?? 'Location not available' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="play-description mb-4">
                    <h5 class="section-title">About</h5>
                    <p><?= nl2br($play['description']) ?></p>
                </div>

                <?php if (isset($seats_result) && $seats_result->num_rows > 0): ?>
                    <div class="play-tickets mb-4">
                        <h5 class="section-title">Ticket Information</h5>

                        <div class="ticket-categories">
                            <div class="row">
                                <?php while ($seat = $seats_result->fetch_assoc()): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="ticket-category-card <?= $seat['seat_type'] == 'VIP' ? 'vip' : '' ?>">
                                            <div class="card-header">
                                                <?= $seat['seat_type'] ?> Seats
                                            </div>
                                            <div class="card-body">
                                                <div class="price-tag"><?= number_format($prices[$seat['seat_type']] ?? 0, 0, ',', '.') ?>đ</div>
                                                <div class="availability">
                                                    <span class="availability-text">Availability:</span>
                                                    <span class="seats-remaining"><?= $seat['available_seats'] ?>/<?= $seat['total_seats'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="play-actions text-center">
                    <a href="index.php?route=booking/create&play_id=<?= $play['play_id'] ?>" class="btn btn-primary btn-book">
                        <i class="bi bi-ticket-perforated me-2"></i>Book Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>