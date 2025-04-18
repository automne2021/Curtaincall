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