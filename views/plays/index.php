<?php
require_once 'helpers/sort_helpers.php';
?>

<main class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="theater-title"><?= htmlspecialchars($theater_name) ?></h2>
            <p class="theater-subtitle">
                <?php if (!empty($theater_id)): ?>
                    <a href="index.php?route=play" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> All Theaters
                    </a>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Add sorting options here -->
    <div class="row mb-4">
        <div class="col">
            <div class="sorting-container">
                <div class="sort-label me-2"><i class="bi bi-sort-alpha-down me-1"></i> Sort by:</div>
                <div class="sort-options d-flex flex-wrap gap-2">
                    <a href="<?= getSortUrl('date') ?>" class="sort-option <?= isSortActive('date') ?>">
                        Date <?= getSortIcon('date') ?>
                    </a>
                    <a href="<?= getSortUrl('name') ?>" class="sort-option <?= isSortActive('name') ?>">
                        Name <?= getSortIcon('name') ?>
                    </a>
                    <a href="<?= getSortUrl('price') ?>" class="sort-option <?= isSortActive('price') ?>">
                        Price <?= getSortIcon('price') ?>
                    </a>
                </div>
            </div>
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
                                <?php if (empty($theater_id)): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-building"></i> <?= $row['theater_name'] ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <?php if (!empty($theater_id)): ?>
                        No plays found for this theater.
                    <?php else: ?>
                        No plays currently available.
                    <?php endif; ?>
                </div>
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