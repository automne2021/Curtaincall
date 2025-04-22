<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="pagination-info">
        Kết quả <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?>-<?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?>
        trên <?= $pagination['total'] ?> mục
    </div>

    <?php if ($pagination['last_page'] > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
                <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $base_url ?>&page=1" aria-label="First">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= $base_url ?>&page=<?= $pagination['current_page'] - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                // Calculate range of page numbers to display
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['last_page'], $pagination['current_page'] + 2);

                // Always show at least 5 pages if available
                if ($end - $start + 1 < 5) {
                    if ($start == 1) {
                        $end = min($start + 4, $pagination['last_page']);
                    } elseif ($end == $pagination['last_page']) {
                        $start = max(1, $end - 4);
                    }
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $base_url ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $base_url ?>&page=<?= $pagination['current_page'] + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= $base_url ?>&page=<?= $pagination['last_page'] ?>" aria-label="Last">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>