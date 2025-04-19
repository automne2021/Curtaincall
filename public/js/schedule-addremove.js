document.addEventListener('DOMContentLoaded', function() {
    // Calculate duration automatically when start or end times change
    const startTimeInput = document.getElementById('start_time_0');
    const endTimeInput = document.getElementById('end_time_0');
    
    function updateDuration() {
        if (startTimeInput && endTimeInput && startTimeInput.value && endTimeInput.value) {
            const startParts = startTimeInput.value.split(':');
            const endParts = endTimeInput.value.split(':');
            
            const startMinutes = (parseInt(startParts[0]) * 60) + parseInt(startParts[1]);
            const endMinutes = (parseInt(endParts[0]) * 60) + parseInt(endParts[1]);
            
            // Handle cases where the show goes past midnight
            const durationMinutes = endMinutes < startMinutes ? 
                (24 * 60 - startMinutes) + endMinutes : 
                endMinutes - startMinutes;
            
            console.log(`Duration: ${durationMinutes} minutes`);
        }
    }
    
    if (startTimeInput) startTimeInput.addEventListener('change', updateDuration);
    if (endTimeInput) endTimeInput.addEventListener('change', updateDuration);
    
    // Multiple schedules functionality
    const schedulesContainer = document.getElementById('schedules-container');
    const addScheduleBtn = document.getElementById('addScheduleBtn');
    
    if (!schedulesContainer || !addScheduleBtn) return;
    
    // Counter for adding new schedules
    let scheduleCount = document.querySelectorAll('.schedule-item').length;
    
    // Function to add a new schedule
    addScheduleBtn.addEventListener('click', function() {
        const newScheduleItem = document.createElement('div');
        newScheduleItem.className = 'schedule-item mb-3 border-bottom pb-3';
        newScheduleItem.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="date_${scheduleCount}" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" 
                        id="date_${scheduleCount}" name="schedules[${scheduleCount}][date]" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="start_time_${scheduleCount}" class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" 
                        id="start_time_${scheduleCount}" name="schedules[${scheduleCount}][start_time]" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="end_time_${scheduleCount}" class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" 
                        id="end_time_${scheduleCount}" name="schedules[${scheduleCount}][end_time]" required>
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-schedule">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        `;
        schedulesContainer.appendChild(newScheduleItem);
        
        // Add event listeners to the new schedule's time inputs
        const newScheduleStartTime = document.getElementById(`start_time_${scheduleCount}`);
        const newScheduleEndTime = document.getElementById(`end_time_${scheduleCount}`);
        
        if (newScheduleStartTime && newScheduleEndTime) {
            function updateNewDuration() {
                if (newScheduleStartTime.value && newScheduleEndTime.value) {
                    const startParts = newScheduleStartTime.value.split(':');
                    const endParts = newScheduleEndTime.value.split(':');
                    
                    const startMinutes = (parseInt(startParts[0]) * 60) + parseInt(startParts[1]);
                    const endMinutes = (parseInt(endParts[0]) * 60) + parseInt(endParts[1]);
                    
                    const durationMinutes = endMinutes < startMinutes ? 
                        (24 * 60 - startMinutes) + endMinutes : 
                        endMinutes - startMinutes;
                    
                    console.log(`Schedule ${scheduleCount} duration: ${durationMinutes} minutes`);
                }
            }
            
            newScheduleStartTime.addEventListener('change', updateNewDuration);
            newScheduleEndTime.addEventListener('change', updateNewDuration);
        }
        
        scheduleCount++;
    });
    
    // Event delegation to handle remove buttons
    schedulesContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-schedule')) {
            const scheduleItem = e.target.closest('.schedule-item');
            scheduleItem.remove();
        }
    });
});