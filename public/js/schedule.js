document.addEventListener('DOMContentLoaded', function() {
    const dateSelect = document.getElementById('schedule_date');
    const timeSlots = document.getElementById('time-slots');
    
    if (dateSelect) {
        dateSelect.addEventListener('change', function() {
            const selectedDate = this.value;
            
            if (selectedDate) {
                timeSlots.classList.remove('d-none');
                
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.style.display = 'none';
                });
                
                const selectedSlot = document.querySelector(`.time-slot[data-date="${selectedDate}"]`);
                if (selectedSlot) {
                    selectedSlot.style.display = 'block';
                }
            } else {
                timeSlots.classList.add('d-none');
            }
        });
    }
    
    if (window.location.hash === '#scheduleSection') {
        setTimeout(function() {
            const scheduleSection = document.getElementById('scheduleSection');
            if (scheduleSection) {
                scheduleSection.scrollIntoView({ behavior: 'smooth' });
            }
        }, 500);
    }
    
    const bookNowBtn = document.querySelector('.play-actions .btn[href="#scheduleSection"]');
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scheduleSection = document.getElementById('scheduleSection');
            if (scheduleSection) {
                scheduleSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});