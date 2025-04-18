<main class="container-fluid px-4">
    <?php include 'views/booking/breadcrumb.php'; ?>
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="section-title text-center">Book Tickets</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="<?= $play['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($play['title']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($play['title']) ?></h5>
                    <p class="card-text"><?= $play['description'] ?></p>
                    <p><strong>Theater:</strong> <?= htmlspecialchars($theater['name']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($theater['location']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Select a Schedule</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($schedules)): ?>
                        <div class="alert alert-info">
                            No upcoming schedules available for this play.
                        </div>
                    <?php else: ?>
                        <form action="index.php?route=booking/selectSeats" method="POST">
                            <input type="hidden" name="play_id" value="<?= $play['play_id'] ?>">

                            <div class="form-group mb-4">
                                <label for="schedule" class="form-label">Select Date and Time:</label>
                                <select name="schedule_date" id="schedule_date" class="form-select mb-3" required>
                                    <option value="">Select a date</option>
                                    <?php
                                    $dates = [];
                                    foreach ($schedules as $schedule) {
                                        $date = $schedule['date'];
                                        if (!in_array($date, $dates)) {
                                            $dates[] = $date;
                                            $formatted_date = date('l, F j, Y', strtotime($date));
                                            echo "<option value=\"{$date}\">{$formatted_date}</option>";
                                        }
                                    }
                                    ?>
                                </select>

                                <div id="time-slots" class="d-none">
                                    <?php foreach ($dates as $date): ?>
                                        <div class="time-slot" data-date="<?= $date ?>">
                                            <label class="form-label">Select Time:</label>
                                            <div class="btn-group d-flex flex-wrap" role="group">
                                                <?php foreach ($schedules as $schedule): ?>
                                                    <?php if ($schedule['date'] === $date): ?>
                                                        <input type="radio" class="btn-check" name="schedule_time"
                                                            id="time_<?= $schedule['start_time'] ?>"
                                                            value="<?= $schedule['start_time'] ?>" required>
                                                        <label class="btn btn-outline-primary mb-2 me-2"
                                                            for="time_<?= $schedule['start_time'] ?>">
                                                            <?= date('g:i A', strtotime($schedule['start_time'])) ?> -
                                                            <?= date('g:i A', strtotime($schedule['end_time'])) ?>
                                                        </label>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Continue to Select Seats</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateSelect = document.getElementById('schedule_date');
        const timeSlots = document.getElementById('time-slots');

        dateSelect.addEventListener('change', function() {
            const selectedDate = this.value;

            if (selectedDate) {
                // Show the time-slots div
                timeSlots.classList.remove('d-none');

                // Hide all time slot divs
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.style.display = 'none';
                });

                // Show only the time slot div for the selected date
                const selectedSlot = document.querySelector(`.time-slot[data-date="${selectedDate}"]`);
                if (selectedSlot) {
                    selectedSlot.style.display = 'block';
                }
            } else {
                timeSlots.classList.add('d-none');
            }
        });
    });
</script>