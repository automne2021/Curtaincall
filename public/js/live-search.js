document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchHints = document.getElementById('search-hints');
    const searchForm = document.getElementById('live-search');
    
     if (!searchInput || !searchHints || !searchForm) {
        console.error('Search elements not found');
        return;
    }

    // Debounce function to limit API calls
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    function highlightMatch(text, query) {
        if (!query) return text;
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }
    
    // Function to perform the search
    const performSearch = debounce(function() {
        const query = searchInput.value.trim();
        
        // Clear results if empty query
        if (query.length === 0) {
            searchHints.innerHTML = '';
            searchHints.classList.add('d-none');
            return;
        }
        
        if (query.length > 1) {
            fetch(`public/search-api.php?query=${encodeURIComponent(query)}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    searchHints.innerHTML = '';
                    
                    if (data.length > 0) {
                        const resultsList = document.createElement('ul');
                        resultsList.className = 'list-group';

                        data.forEach(play => {
                            const resultItem = document.createElement('li');
                            resultItem.className = 'list-group-item d-flex align-items-center';
                            
                            const iconSpan = document.createElement('span');
                            iconSpan.className = 'me-2 search-icon';
                            iconSpan.innerHTML = '<i class="bi bi-camera-reels"></i>';

                            const link = document.createElement('a');
                            link.href = `index.php?route=play/view&play_id=${play.play_id}`;
                            link.className = 'text-decoration-none flex-grow-1';

                            link.innerHTML = highlightMatch(play.title, query);
                            
                            resultItem.appendChild(iconSpan);
                            resultItem.appendChild(link);
                            resultsList.appendChild(resultItem);
                        });
                        
                        searchHints.appendChild(resultsList);
                        searchHints.classList.remove('d-none');
                    } else {
                        // No results found
                        const noResults = document.createElement('div');
                        noResults.className = 'p-3 text-center';
                        noResults.textContent = 'Không tìm thấy kết quả phù hợp';
                        searchHints.appendChild(noResults);
                        searchHints.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }
    }, 300);
    
    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchHints.contains(e.target)) {
                searchHints.classList.add('d-none');
            }
        });
        
        // Form submission
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (query.length === 0) {
                e.preventDefault();
            }
        });
        
        // Show results if input is focused and has value
        searchInput.addEventListener('focus', function() {
            if (searchInput.value.trim().length >= 2) {
                performSearch();
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchHints = document.getElementById('search-hints');
    const searchForm = document.getElementById('live-search');
    
    let typingTimer;
    const doneTypingInterval = 300; // Time in ms
    
    // Show/hide search hints dropdown
    searchInput.addEventListener('focus', function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            showSearchHints();
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchHints.contains(e.target)) {
            hideSearchHints();
        }
    });
    
    // Live search as user types
    searchInput.addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            typingTimer = setTimeout(() => getSearchHints(query), doneTypingInterval);
        } else {
            hideSearchHints();
        }
    });
    
    // Form submission - redirect to search page
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
        }
    });
    
    function getSearchHints(query) {
        fetch(`index.php?route=search/ajaxSearch&query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderSearchHints(data.results, query);
                }
            })
            .catch(error => console.error('Search error:', error));
    }
    
    function renderSearchHints(results, query) {
        if (results.length === 0) {
            hideSearchHints();
            return;
        }
        
        let html = `<div class="search-hint-header">Kết quả tìm kiếm cho "${query}"</div>`;
        
        results.forEach(play => {
            html += `
            <a href="index.php?route=play/view&play_id=${play.play_id}" class="search-hint-item">
                <div class="search-hint-image">
                    <img src="${play.image}" alt="${play.title}">
                </div>
                <div class="search-hint-content">
                    <div class="search-hint-title">${play.title}</div>
                    <div class="search-hint-theater">${play.theater_name}</div>
                    <div class="search-hint-price">Từ ${new Intl.NumberFormat('vi-VN').format(play.min_price)}đ</div>
                </div>
            </a>
            `;
        });
        
        html += `
        <div class="search-hint-footer">
            <a href="index.php?route=search/index&query=${encodeURIComponent(query)}" class="search-all-btn">
                Xem tất cả kết quả
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        `;
        
        searchHints.innerHTML = html;
        showSearchHints();
    }
    
    function showSearchHints() {
        searchHints.classList.remove('d-none');
    }
    
    function hideSearchHints() {
        searchHints.classList.add('d-none');
    }
});