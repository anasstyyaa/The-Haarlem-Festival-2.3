<?php
/** @var App\ViewModels\TicketViewModel[] $viewTickets */
/** @var float $grandTotal */
?>

<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="festival-dark-page">
    <div class="container py-5 mt-5 program-container">
        
        <div class="row align-items-center mb-5 p-4 border-b-peach">
            <div class="col-md-8">
                <h1 class="hero-overlay-title text-light-peach">My Personal Program</h1>
                <p class="text-light-peach opacity-80">Review your selection and prepare for the festival.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge rounded-pill fs-6" style="background-color: #E5A290; color: #3B0B00;">
                    <i class="bi bi-ticket-perforated me-1"></i> <?= count($viewTickets) ?> Tickets
                </span>
            </div>
        </div>

        <?php if (empty($viewTickets)): ?>
            <div class="festival-dark-card text-center py-5">
                <i class="bi bi-cart-x fs-1 text-peach opacity-50 mb-3"></i>
                <h4 class="text-light-peach">Your program is empty</h4>
                <a href="/" class="more-btn d-inline-block w-auto px-4 mt-3">Browse Events</a>
            </div>
        <?php else: ?>
            <div class="festival-dark-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-dark-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Event</th>
                                <th>Location</th>
                                <th>Schedule</th>
                                <th class="text-center">Price</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viewTickets as $ticket): ?>
                                <tr class="border-b-peach-light">
                                    <?php include __DIR__ . '/../partials/programItem.php'; ?>

                                    <td class="text-center">
                                        <div class="fw-bold text-light-peach">€<?= number_format($ticket->totalPrice, 2) ?></div>
                                        <?php if ($ticket->guestCount > 1): ?>
                                            <small class="text-peach opacity-75" style="font-size: 0.7rem;">
                                                €<?= number_format($ticket->unitPrice, 2) ?> x <?= $ticket->guestCount ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <div class="d-flex align-items-center rounded-pill px-2 py-1" style="background: rgba(229, 162, 144, 0.1); border: 1px solid rgba(229, 162, 144, 0.3);">
                                                <form method="POST" action="/updateTicketQuantity" class="m-0">
                                                    <input type="hidden" name="program_item_id" value="<?= $ticket->programItemId ?>">
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" class="btn btn-link btn-sm p-0 text-peach" <?= $ticket->guestCount <= 1 ? 'disabled' : '' ?>>
                                                        <i class="bi bi-dash-circle fs-5"></i>
                                                    </button>
                                                </form>

                                                <span class="mx-2 fw-bold text-light-peach" style="min-width: 20px; text-align: center;"><?= $ticket->guestCount ?></span>

                                                <form method="POST" action="/updateTicketQuantity" class="m-0">
                                                    <input type="hidden" name="program_item_id" value="<?= $ticket->programItemId ?>">
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" class="btn btn-link btn-sm p-0 text-peach">
                                                        <i class="bi bi-plus-circle-fill fs-5"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <form method="POST" action="/removeTicket" class="m-0" onsubmit="return confirm('Remove this item?');">
                                                <input type="hidden" name="ticket_id" value="<?= $ticket->programItemId ?>">
                                                <button type="submit" class="btn btn-sm text-danger border-0 p-0 opacity-75 hover-opacity-100">
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

            <div class="mt-5 text-end">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="mb-4">
                        <span class="text-peach fs-5">Total to pay:</span>
                        <span class="fw-bold fs-2 ms-2 text-light-peach">€<?= number_format($grandTotal, 2) ?></span>
                    </div>
                    <form action="/checkout" method="POST">
                        <button type="submit" class="more-btn d-inline-block w-auto px-5 py-3 shadow-lg">
                            Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="festival-dark-card text-center p-4">
                        <p class="text-peach mb-3">Please log in to finalize your purchase.</p>
                        <a href="/login" class="more-btn d-inline-block w-auto px-4">Login to Checkout</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>