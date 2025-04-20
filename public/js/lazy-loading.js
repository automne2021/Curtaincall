document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loading-spinner');
    const playsContainer = document.querySelector('.row-cols-2.row-cols-md-4');
    const loadMoreContainer = document.getElementById('load-more-container');

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

        let triggerOffset;

        if (window.innerWidth <= 576) {
            triggerOffset = 1000; 
        } else if (window.innerWidth <= 768) {
            triggerOffset = 900;
        } else if (window.innerWidth <= 992) {
            triggerOffset = 500;
        } else {
            triggerOffset = 300;
        }

        // Calculate the position where loading should trigger (near bottom of page)
        const scrollPosition = window.scrollY + window.innerHeight;
        const triggerPosition = document.body.offsetHeight - triggerOffset;

        if (scrollPosition >= triggerPosition) {
            loadMorePlays();
        }
    }

    function loadMorePlays() {
        isLoading = true;
        const nextPage = currentPage + 1;

        loadingSpinner.classList.remove('d-none');

        setTimeout(() => {
            let url = `api/get_plays.php?page=${nextPage}&sort=${sortField}&dir=${sortDir}`;
            if (theaterId) {
                url += `&theater_id=${theaterId}`;
            }

            console.log('Fetching:', url); 

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.classList.add('d-none');

                    if (data.plays && data.plays.length > 0) {
                        data.plays.forEach(play => {
                            const playCard = createPlayCard(play, theaterId === '');
                            playsContainer.appendChild(playCard);
                        });

                        setTimeout(() => {
                            const newCards = document.querySelectorAll('.play-card.new-card');
                            newCards.forEach(card => {
                                card.classList.remove('new-card');
                                card.classList.add('show');
                            });
                        }, 50);

                        currentPage = nextPage;
                        allLoaded = currentPage >= data.pagination.total_pages || data.plays.length < 8; 
                    } else {
                        allLoaded = true; 
                        if (currentPage === 1 && playsContainer.querySelectorAll('.play-card').length === 0) {
                            const noMorePlays = document.createElement('div');
                            noMorePlays.className = 'col-12 text-center my-4';
                            noMorePlays.innerHTML = '<p>Không còn vở kịch nào</p>';
                            playsContainer.appendChild(noMorePlays);
                        }
                    }

                    setTimeout(() => {
                        isLoading = false;
                    }, 500);
                })
                .catch(error => {
                    console.error('Error loading more plays:', error);
                    loadingSpinner.classList.add('d-none');
                    isLoading = false;
                });
        }, 1000); 
    }

    // Helper function to create a play card
    function createPlayCard(play) {
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = `
            <a href="index.php?route=play/view&play_id=${play.play_id}" class="play-card-link">
                <div class="play-card new-card">
                    <img src="${play.image}" class="card-img-top" loading="lazy" alt="${play.title}">
                    <div class="card-body">
                        <h5 class="card-title">[${play.theater_name}] ${play.title}</h5>
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