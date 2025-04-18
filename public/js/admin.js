document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        });
    }
    
    if (document.querySelector('.action-buttons')) {
        const actionTooltips = document.querySelectorAll('.action-buttons [data-bs-toggle="tooltip"]');
        if (typeof bootstrap !== 'undefined' && actionTooltips.length > 0) {
            actionTooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });
        }
    }
    
    // Initialize delete confirmation modal
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const playId = button.getAttribute('data-play-id');
            const playTitle = button.getAttribute('data-play-title');
            
            document.getElementById('playTitle').textContent = playTitle;
            document.getElementById('confirmDelete').href = `index.php?route=admin/deletePlay&id=${playId}`;
        });
    }
    
    const imageInput = document.getElementById('image');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    
    if (imageInput && imagePreviewContainer) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Check if preview image exists, if not create it
                    let imagePreview = document.getElementById('imagePreview');
                    if (!imagePreview) {
                        imagePreview = document.createElement('img');
                        imagePreview.id = 'imagePreview';
                        imagePreview.classList.add('img-thumbnail', 'mt-2');
                        imagePreview.style.maxHeight = '200px';
                        imagePreviewContainer.appendChild(imagePreview);
                    }
                    
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Initialize charts on dashboard if they exist
    if (typeof Chart !== 'undefined') {
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) {
            new Chart(revenueCanvas, {
                type: 'line',
                data: {
                    labels: revenueChartData.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenueChartData.data,
                        backgroundColor: 'rgba(7, 94, 84, 0.05)',
                        borderColor: '#075E54',
                        borderWidth: 2,
                        pointBackgroundColor: '#075E54',
                        pointRadius: 3,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' đ';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});