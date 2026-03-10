<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addSessionModalLabel">Add Daily Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/yummy/sessions/add" method="POST">
                <input type="hidden" name="restaurant_id" value="<?= $restaurant->getId() ?>">
                
                <div class="modal-body p-4">
                    <?php if (isset($_GET['open_modal']) && $_GET['open_modal'] === 'add' && isset($_GET['message'])): ?>
                        <div class="alert alert-danger small p-2">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['message']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Event Date</label>
                        <input type="date" name="session_date" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Capacity for these slots</label>
                        <input type="number" name="available_slots" class="form-control" value="<?= $restaurant->getTotalSlots() ?>" required>
                    </div>
                    
                    <div class="p-3 bg-light rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase text-muted small fw-bold mb-0">Define Time Slots</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTimeSlot()">
                                <i class="bi bi-plus-lg"></i> Add Slot
                            </button>
                        </div>
                        
                        <div id="time-slots-container">
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white"><i class="bi bi-clock"></i></span>
                                <input type="time" name="times[]" class="form-control" value="17:00" required>
                                <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-text mt-2">Add multiple times to create several sessions for this date at once.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Save All Slots</button>
                </div>
            </form>
        </div>
    </div>
</div>