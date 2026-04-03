<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Time Slot Schedule</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSessionModal">
            <i class="bi bi-calendar-plus me-1"></i> Add Sessions
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Start Time</th>
                        <th>Available Slots</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sessions)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted italic">
                                No time slots defined. Use the "Add Sessions" button to create the schedule.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= date('D, M j, Y', strtotime($session->getDate())) ?></td>
                                <td>
                                    <span class="badge bg-info text-dark fs-6">
                                        <?php 
                                            $start = strtotime($session->getStartTime());
                                            $duration = $restaurant->getSessionDuration() ?? 90; 
                                            $end = $start + ($duration * 60); 
                                            echo date('H:i', $start) . ' - ' . date('H:i', $end);
                                        ?>
                                    </span>
                                </td>
                                <td><?= $session->getAvailableSlots() ?> / <?= $restaurant->getTotalSlots() ?></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editSessionModal<?= $session->getId() ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="/admin/yummy/sessions/delete" method="POST" onsubmit="return confirm('Delete this session?');">
                                            <input type="hidden" name="id" value="<?= $session->getId() ?>">
                                            <input type="hidden" name="restaurant_id" value="<?= $restaurant->getId() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="modal fade" id="editSessionModal<?= $session->getId() ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">Edit Session Slot</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="/admin/yummy/sessions/update" method="POST">
                                                    <input type="hidden" name="session_id" value="<?= $session->getId() ?>">
                                                    <input type="hidden" name="restaurant_id" value="<?= $restaurant->getId() ?>">
                                                    <div class="modal-body text-start">
                                                        <?php                                   
                                                        if (isset($_GET['status']) && $_GET['status'] === 'error' && 
                                                            isset($_GET['session_id']) && $_GET['session_id'] == $session->getId()): 
                                                        ?>
                                                            <div class="alert alert-danger p-2 small">
                                                                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['message'] ?? 'Error updating session') ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Date</label>
                                                            <input type="date" name="session_date" class="form-control" value="<?= $session->getDate() ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Start Time</label>
                                                            <input type="time" name="start_time" class="form-control" value="<?= date('H:i', strtotime($session->getStartTime())) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Available Slots</label>
                                                            <input type="number" name="available_slots" class="form-control" value="<?= $session->getAvailableSlots() ?>" max="<?= $restaurant->getTotalSlots() ?>" min="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>