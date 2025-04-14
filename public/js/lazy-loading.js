/* === LAZY LOADING === */
document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loading-spinner');
    const playsContainer = document.querySelector('.row-cols-2.row-cols-md-4');
    const loadMoreContainer = document.getElementById('load-more-container');

    // Variables to control loading state
    let currentPage = playConfig.currentPage;
    const totalPages = playConfig.totalPages;
    const theaterId = playConfig.theaterId;
    const sortField = playConfig.sortField;
    const sortDir = playConfig.sortDir; 

    let lastPageCount = 8;
    let isLoading = false;
    let allLoaded = currentPage >= totalPages || lastPageCount < 8;

    // Function to check if we need to load more content
    function checkScroll() {
        if (isLoading || allLoaded || lastPageCount < 8) return;

        // Calculate the position where loading should trigger (near bottom of page)
        const scrollPosition = window.scrollY + window.innerHeight;
        const triggerPosition = document.body.offsetHeight - 300; // 300px before bottom

        if (scrollPosition >= triggerPosition) {
            loadMorePlays();
        }
    }

    // Function to load more plays
    function loadMorePlays() {
        isLoading = true;
        const nextPage = currentPage + 1;

        // Show loading spinner
        loadingSpinner.classList.remove('d-none');

        // Add a small delay to simulate loading
        setTimeout(() => {
            // Build URL with the same parameters as current page
            let url = `api/get_plays.php?page=${nextPage}&sort=${sortField}&dir=${sortDir}`;
            if (theaterId) {
                url += `&theater_id=${theaterId}`;
            }

            console.log('Fetching:', url); // Debug line to check URL

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Hide spinner
                    loadingSpinner.classList.add('d-none');

                    // Append new plays
                    if (data.plays && data.plays.length > 0) {
                        data.plays.forEach(play => {
                            const playCard = createPlayCard(play, theaterId === '');
                            playsContainer.appendChild(playCard);
                        });

                        // Add fade-in animation to new cards
                        setTimeout(() => {
                            const newCards = document.querySelectorAll('.play-card.new-card');
                            newCards.forEach(card => {
                                card.classList.remove('new-card');
                                card.classList.add('show');
                            });
                        }, 50);

                        // Update current page and check if we've loaded all pages
                        currentPage = nextPage;
                        allLoaded = currentPage >= data.pagination.total_pages || data.plays.length < 8; 
                    } else {
                        allLoaded = true; // No more plays to load
                        if (currentPage === 1 && playsContainer.querySelectorAll('.play-card').length === 0) {
                            const noMorePlays = document.createElement('div');
                            noMorePlays.className = 'col-12 text-center my-4';
                            noMorePlays.innerHTML = '<p>Không còn vở kịch nào</p>';
                            playsContainer.appendChild(noMorePlays);
                        }
                    }


                    // Reset loading state after a short delay
                    setTimeout(() => {
                        isLoading = false;
                    }, 500);
                })
                .catch(error => {
                    console.error('Error loading more plays:', error);
                    loadingSpinner.classList.add('d-none');
                    isLoading = false;
                });
        }, 1000); // 1 second delay before loading data
    }

    // Helper function to create a play card
    function createPlayCard(play, showTheaterName) {
        const col = document.createElement('div');
        col.className = 'col';

        col.innerHTML = `
            <a href="index.php?route=play/view&play_id=${play.play_id}" class="play-card-link">
                <div class="play-card new-card">
                    <img src="${play.image}" class="card-img-top" loading="lazy" alt="${play.title}">
                    <div class="card-body">
                        <h5 class="card-title">${play.title}</h5>
                        <p class="fw-bold mb-1">Từ ${play.formatted_price}đ</p>
                        <p class="date-info"><i class="bi bi-calendar-event me-2"></i>${play.formatted_date}</p>
                    </div>
                </div>
            </a>
        `;

        return col;
    }

    // Add scroll event listener with throttling
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(checkScroll, 100);
    });

    // Initial check in case the page is not tall enough to scroll
    setTimeout(checkScroll, 500);
});