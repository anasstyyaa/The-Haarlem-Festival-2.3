<div class="card shadow-sm border-0 sticky-top" style="top: 2rem;">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Book a Table</h5>
    </div>
    <div class="card-body p-4">
        <form action="/addTicket" method="POST" id="reservationForm">
            <input type="hidden" name="event_id" value="<?= $restaurant->getId() ?>">
            <input type="hidden" name="event_type" value="reservation">
            <input type="hidden" name="program_item_id" id="selectedSessionId" required>

            <div class="mb-4">
                <label class="form-label fw-bold small text-uppercase text-muted">1. Select Date</label>
                <select id="dateSelect" name="reservation_date" class="form-select border-2" required>
                    <option value="">Choose a date...</option>
                    <?php foreach ($groupedSessions as $date => $daySessions): ?>
                        <option value="<?= $date ?>"><?= date('D, M j', strtotime($date)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="timeSlotSection" class="mb-4 d-none">
                <label class="form-label fw-bold small text-uppercase text-muted">2. Select Time</label>
                <div id="timeSlotButtons" class="d-flex flex-wrap gap-2">
                    </div>
                </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-uppercase text-muted">3. Party Size</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-people"></i></span>
                    <input type="number" id="peopleInput" name="number_of_people" class="form-control" min="1" value="2" required>
                </div>
                <div id="capacityWarning" class="text-danger small mt-1 d-none"></div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-uppercase text-muted">4. Special Requests</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Allergies, birthdays, etc."></textarea>
            </div>

            <div class="alert alert-info py-2 small border-0">
                <i class="bi bi-info-circle me-2"></i>Reservation fee: <strong>€<?= number_format($restaurant->getReservationFee(), 2) ?></strong> per person.
            </div>

           <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                Confirm Reservation <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>

<script src="/js/yummy/reservation.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = <?= json_encode($groupedSessions) ?>;
        if (data && Object.keys(data).length > 0) {
            initReservationForm(data);
        }
    });
</script>

<script>
    console.log("Sessions Loaded:", <?= json_encode($groupedSessions) ?>);
</script>