<?php
require_once 'helpers/sort_helpers.php';
?>

<main class="container-fluid px-4 mt-4">
    <!-- Hot Performances Carousel -->
    <div class="row mb-5">
        <div class="col-12">
            <div id="hotPerformancesCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators mt-8">
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <?php
                    $slide = 0;
                    $total_slides = ceil($hot_plays->num_rows / 2);

                    for ($i = 0; $i < $total_slides; $i++):
                        // Reset result pointer to the beginning of this slide
                        $hot_plays->data_seek($i * 2);
                    ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <div class="row">
                                <?php for ($j = 0; $j < 2 && $play = $hot_plays->fetch_assoc(); $j++): ?>
                                    <div class="col-md-6">
                                        <div class="banner-card">
                                            <a href="index.php?route=play/view&play_id=<?= $play['play_id'] ?>">
                                                <img src="<?= $play['image'] ?>" class="d-block w-100 rounded" alt="<?= $play['title'] ?>">
                                                <div class="banner-overlay">
                                                    <button class="btn btn-sm">Xem chi tiết</button>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev carousel-custom-control" type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next carousel-custom-control" type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon text-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- UpComing Performances Section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">UPCOMING PERFORMANCES</h3>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <a href="index.php?route=play/view&play_id=<?= $row['play_id'] ?>" class="play-card-link">
                        <div class="play-card">
                            <img src="<?= $row['image'] ?>" class="card-img-top" loading="lazy" alt="<?= $row['title'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['title'] ?></h5>
                                <p class="fw-bold mb-1">Từ <?= number_format($row['min_price'], 0, ',', '.') ?>đ</p>
                                <p class="date-info"><i class="bi bi-calendar-event me-2"></i><?= date("d \\t\h\á\\n\g m, Y", strtotime($row['date'])) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Không có vở diễn nào được tìm thấy.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- "Load More" button section -->
    <div class="row" id="load-more-row">
        <div class="col-12 text-center my-3" id="load-more-container">
            <div class="spinner-border text-primary d-none" id="loading-spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</main>

<script>
    // Configuration object to pass PHP variables to JavaScript
    const playConfig = {
        currentPage: <?= $page ?? 1 ?>,
        totalPages: <?= $total_pages ?? 1 ?>,
        theaterId: '<?= $theater_id ?? '' ?>',
        sortField: '<?= $sort_field ?? 'date' ?>',
        sortDir: '<?= $sort_dir ?? 'desc' ?>'
    };
</script>
<script src="public/js/lazy-loading.js"></script>