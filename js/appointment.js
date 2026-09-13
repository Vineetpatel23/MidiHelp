/**
 * MidiHelp - Appointment Booking Interactive Logic
 * Handles date pickers, time slot pill selection, and dynamic AJAX slot checking
 */

document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('appointment_date');
    const doctorIdInput = document.getElementById('doctor_id');
    const timeInput = document.getElementById('appointment_time');
    const slotsContainer = document.getElementById('timeSlotsContainer');

    if (!dateInput || !slotsContainer) return;

    // 1. Prevent past date selection
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

    // Standard available time slots array
    const defaultSlots = [
        "09:00 AM", "09:30 AM", "10:00 AM", "10:30 AM",
        "11:00 AM", "11:30 AM", "02:00 PM", "02:30 PM",
        "03:00 PM", "03:30 PM", "04:00 PM", "04:30 PM"
    ];

    // Listen to date input change
    dateInput.addEventListener('change', function () {
        const selectedDate = this.value;
        const doctorId = doctorIdInput ? doctorIdInput.value : '';

        if (!selectedDate || !doctorId) return;

        // Reset selected time input
        if (timeInput) timeInput.value = '';

        // Show loading state in slots container
        slotsContainer.innerHTML = '<p class="text-muted" style="grid-column: 1/-1; text-align: center;">Checking available time slots...</p>';

        // Fetch booked slots via fetch API
        fetch(`get-booked-slots.php?doctor_id=${doctorId}&date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                const bookedSlots = data.booked_slots || [];
                renderTimeSlots(defaultSlots, bookedSlots);
            })
            .catch(error => {
                console.error('Error fetching booked slots:', error);
                renderTimeSlots(defaultSlots, []);
            });
    });

    function renderTimeSlots(allSlots, bookedSlots) {
        slotsContainer.innerHTML = '';

        if (allSlots.length === 0) {
            slotsContainer.innerHTML = '<p class="text-muted">No available slots for this date.</p>';
            return;
        }

        allSlots.forEach(slot => {
            const isBooked = bookedSlots.includes(slot);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `time-slot-btn ${isBooked ? 'disabled' : ''}`;
            btn.textContent = slot;

            if (isBooked) {
                btn.disabled = true;
                btn.title = 'Slot already booked';
            } else {
                btn.addEventListener('click', function () {
                    // Remove selected class from all sibling buttons
                    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    if (timeInput) {
                        timeInput.value = slot;
                    }
                });
            }

            slotsContainer.appendChild(btn);
        });
    }

    // Initial trigger if date pre-selected
    if (dateInput.value) {
        dateInput.dispatchEvent(new Event('change'));
    }
});
