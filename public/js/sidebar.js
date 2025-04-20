document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    
    // Check if elements exist
    if (!sidebar || !content || !sidebarCollapse) return;
    
    // Create backdrop element for mobile
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);
    
    // Function to check if we're on mobile
    function isMobile() {
        return window.innerWidth < 768;
    }
    
    /**
     * Set sidebar state explicitly (expanded or collapsed)
     * @param {boolean} collapsed - Whether sidebar should be collapsed
     */
    function setSidebarState(collapsed) {
        if (collapsed) {
            sidebar.classList.add('active');
            content.classList.add('active');
            
            // Show backdrop on mobile when expanding sidebar
            if (isMobile()) {
                backdrop.classList.add('active');
            }
        } else {
            sidebar.classList.remove('active');
            content.classList.remove('active');
            
            // Hide backdrop when collapsing sidebar
            backdrop.classList.remove('active');
        }
        
        // Save to localStorage (only for desktop)
        if (!isMobile()) {
            localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');
        }
    }
    
    /**
     * Toggle sidebar between collapsed and expanded states
     */
    function toggleSidebar() {
        const isCurrentlyCollapsed = sidebar.classList.contains('active');
        setSidebarState(!isCurrentlyCollapsed);
    }
    
    // Add click event to toggle button
    sidebarCollapse.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Load saved preference (only on desktop)
    if (!isMobile() && localStorage.getItem('sidebar-collapsed') === 'true') {
        setSidebarState(true);
    }
    
    // Responsive handling
    function handleResponsive() {
        if (isMobile()) {
            // Always collapse sidebar on mobile
            setSidebarState(true);
            
            // Don't save this state to localStorage
            // since we don't want to affect desktop view
        } else if (localStorage.getItem('sidebar-collapsed') === 'true') {
            // On desktop, respect user preference
            setSidebarState(true);
        } else {
            // Default desktop state is expanded
            setSidebarState(false);
        }
    }
    
    // Close sidebar when clicking outside on mobile
    backdrop.addEventListener('click', function() {
        if (isMobile()) {
            setSidebarState(false);
        }
    });
    
    // Close sidebar when clicking a menu item on mobile
    document.querySelectorAll('#sidebar ul li a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (isMobile()) {
                setTimeout(() => {
                    setSidebarState(false);
                }, 150); // Small delay for better UX
            }
            
            // On desktop, save current state to localStorage
            if (!isMobile()) {
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('active') ? 'true' : 'false');
            }
        });
    });
    
    // Initial responsive check
    handleResponsive();
    
    // Re-check on window resize
    window.addEventListener('resize', handleResponsive);
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});