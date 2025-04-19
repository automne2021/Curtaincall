<main class="container-fluid px-4 mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="theater-title">Kết quả tìm kiếm</h2>
            <div class="search-container mt-3 mb-4">
                <form id="search-page-form" class="d-flex search-page-form" role="search">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input
                            id="search-page-input"
                            class="form-control border-start-0 ps-0"
                            type="search"
                            name="query"
                            placeholder="Tìm kiếm vở diễn, nhà hát..."
                            aria-label="Search"
                            value="<?= htmlspecialchars($query) ?>"
                            autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <?php if (!empty($query)): ?>
                <div class="search-results-info">
                    <p>Kết quả tìm kiếm cho <strong>"<?= htmlspecialchars($query) ?>"</strong></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="search-results-container" class="row row-cols-1 row-cols-md-4 g-3">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $play): ?>
                <div class="col">
                    <a href="index.php?route=play/view&play_id=<?= $play['play_id'] ?>" class="play-card-link">
                        <div class="play-card">
                            <img src="<?= $play['image'] ?>" class="card-img-top" alt="<?= $play['title'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= '[' . $play['theater_name'] . '] ' . $play['title'] ?></h5>
                                <p class="fw-bold mb-1">Từ <?= number_format($play['min_price'], 0, ',', '.') ?>đ</p>
                                <p class="date-info"><i class="bi bi-calendar-event me-2"></i>
                                    <?php if (!empty($play['date'])): ?>
                                        <?= date("d \\t\h\á\\n\g m, Y", strtotime($play['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($query)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Không tìm thấy vở diễn nào phù hợp với yêu cầu tìm kiếm.
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-secondary">
                    <i class="bi bi-info-circle me-2"></i> Vui lòng nhập từ khóa để tìm kiếm vở diễn.
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-page-form');
        const searchInput = document.getElementById('search-page-input');
        const searchResultsContainer = document.getElementById('search-results-container');
        
        let typingTimer;
        const doneTypingInterval = 500; // Time in ms
        
        // Search form submission
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            
            if (query.length >= 2) {
                searchPlays(query);
                
                // Update URL without page reload
                const url = new URL(window.location);
                url.searchParams.set('query', query);
                window.history.pushState({}, '', url);
            }
        });
        
        // Live search as user types
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            const query = searchInput.value.trim();
            
            if (query.length >= 2) {
                typingTimer = setTimeout(() => searchPlays(query), doneTypingInterval);
            }
        });
        
        function searchPlays(query) {
            // Show loading state
            searchResultsContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Make AJAX request
            fetch(`index.php?route=search/ajaxSearchFull&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update results container
                        searchResultsContainer.innerHTML = data.html;
                        
                        // Update search results info
                        const searchResultsInfo = document.querySelector('.search-results-info p');
                        if (searchResultsInfo) {
                            searchResultsInfo.innerHTML = `Kết quả tìm kiếm cho <strong>"${query}"</strong>`;
                        }
                    } else {
                        searchResultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại sau.</div></div>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại sau.</div></div>';
                });
        }
    });
</script>