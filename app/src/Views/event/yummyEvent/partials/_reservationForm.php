<div class="reservation-card border-0 shadow-lg" style="background: #4a1608; border-radius: 20px; overflow: hidden; border: 1px solid rgba(245, 197, 186, 0.1);">
    <div class="card-header py-4 text-center" style="background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(245, 197, 186, 0.1);">
        <h4 class="mb-0 fw-bold" style="color: #f4d9c6;"><i class="bi bi-calendar-check me-2"></i>Book Your Table</h4>
    </div>
    
    <div class="card-body p-4 p-md-5">
        <form action="/addTicket" method="POST" id="reservationForm">
            <input type="hidden" name="event_id" value="<?= $restaurant->getId() ?>">
            <input type="hidden" name="event_type" value="reservation">
            <input type="hidden" name="program_item_id" id="selectedSessionId" required>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold small text-uppercase" style="color: #f5c5ba; letter-spacing: 1px;">1. Select Date</label>
                    <select id="dateSelect" name="reservation_date" class="form-select custom-dark-input" required>
                        <option value="">Choose a date...</option>
                        <?php foreach ($groupedSessions as $date => $daySessions): ?>
                            <option value="<?= $date ?>"><?= date('D, M j', strtotime($date)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold small text-uppercase" style="color: #f5c5ba; letter-spacing: 1px;">2. Party Size</label>
                    <div class="input-group">
                        <span class="input-group-text custom-dark-input border-end-0"><i class="bi bi-people"></i></span>
                        <input type="number" id="peopleInput" name="number_of_people" class="form-control custom-dark-input border-start-0" min="1" value="2" required>
                    </div>
                    <div id="capacityWarning" class="text-danger small mt-1 d-none"></div>
                </div>
            </div>

            <div id="timeSlotSection" class="mb-4 d-none">
                <label class="form-label fw-bold small text-uppercase" style="color: #f5c5ba; letter-spacing: 1px;">3. Select Time</label>
                <div id="timeSlotButtons" class="d-flex flex-wrap gap-2">
                    </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-uppercase" style="color: #f5c5ba; letter-spacing: 1px;">4. Special Requests</label>
                <textarea name="remarks" class="form-control custom-dark-input" rows="2" placeholder="Allergies, birthdays, etc."></textarea>
            </div>

            <div class="mb-4 p-3 rounded-3" style="background: rgba(244, 217, 198, 0.1); border: 1px dashed #f5c5ba;">
                <p class="mb-0 small" style="color: #f4d9c6;">
                    <i class="bi bi-info-circle me-2"></i>Reservation fee: <strong>€<?= number_format($restaurant->getReservationFee(), 2) ?></strong> per person. 
                    <span class="d-block opacity-75 mt-1">Fee is deducted from the final bill at the restaurant.</span>
                </p>
            </div>

            <button type="submit" id="submitBtn" class="more-btn w-100 py-3 fw-bold text-uppercase" style="letter-spacing: 2px;">
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