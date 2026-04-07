<?php
/** @var App\ViewModels\TicketViewModel[] $viewTickets */
/** @var float $grandTotal */
?>

<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="container program-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Personal Program</h2>
        <span class="badge bg-primary rounded-pill fs-6">
            <?= count($viewTickets) ?> Tickets
        </span>
    </div>

    <?php if (empty($viewTickets)): ?>
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">Your program is empty</h4>
                <p>Looks like you haven't added any tickets yet.</p>
                <a href="/" class="btn btn-primary mt-2">Explore Events</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Event</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Language</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viewTickets as $ticket): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($ticket->image) ?>" class="rounded me-3" style="width:60px; height:60px; object-fit:cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($ticket->title) ?></div>
                                            <small class="text-muted text-uppercase"><?= htmlspecialchars($ticket->category) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($ticket->location) ?></td>
                                <td><?= htmlspecialchars($ticket->date) ?></td>
                                <td><?= htmlspecialchars($ticket->startTime) ?></td>
                                <td><?= htmlspecialchars($ticket->language) ?></td>
                                <td class="text-center">
                                    <div class="fw-bold">€<?= number_format($ticket->totalPrice, 2) ?></div>
                                    <?php if ($ticket->guestCount > 1): ?>
                                        <small class="text-muted">
                                            €<?= number_format($ticket->unitPrice, 2) ?> x <?= $ticket->guestCount ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <div class="d-flex align-items-center bg-light border rounded-pill px-2 py-1">
                                            <form method="POST" action="/updateTicketQuantity" class="m-0">
                                                <input type="hidden" name="program_item_id" value="<?= $ticket->programItemId ?>">
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="btn btn-link btn-sm p-0 qty-hover-btn" <?= $ticket->guestCount <= 1 ? 'disabled' : '' ?>>
                                                    <i class="bi bi-dash-circle fs-5"></i>
                                                </button>
                                            </form>

                                            <span class="mx-2 fw-bold" style="min-width: 20px; text-align: center; color: #000000;">
                                                <?= $ticket->guestCount ?>
                                            </span>

                                            <form method="POST" action="/updateTicketQuantity" class="m-0">
                                                <input type="hidden" name="program_item_id" value="<?= $ticket->programItemId ?>">
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="btn btn-link btn-sm p-0 qty-hover-btn">
                                                    <i class="bi bi-plus-circle-fill fs-5"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <form method="POST" action="/removeTicket" class="m-0" onsubmit="return confirm('Remove this item?');">
                                            <input type="hidden" name="ticket_id" value="<?= $ticket->programItemId ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="bi bi-trash3 fs-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-end">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="mb-3">
                    <span class="text-muted fs-5">Total to pay:</span>
                    <span class="fw-bold fs-4 ms-2">€<?= number_format($grandTotal, 2) ?></span>
                </div>
                <form action="/checkout" method="POST">
                    <button type="submit" class="btn btn-success btn-m px-5 shadow-sm fw-bold">
                        Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>
            <?php else: ?>
                <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>