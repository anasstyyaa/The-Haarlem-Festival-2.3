<?php /** @var App\ViewModels\TicketViewModel $ticket */ ?>

<td class="ps-4">
    <div class="d-flex align-items-center py-2">
        <img src="<?= htmlspecialchars($ticket->image) ?>" class="rounded me-3" style="width:50px; height:50px; object-fit:cover;">
        <div>
            <div class="fw-bold text-light-peach"><?= htmlspecialchars($ticket->title) ?></div>
            <small class="text-peach opacity-75 text-uppercase" style="font-size: 0.7rem;"><?= htmlspecialchars($ticket->category) ?></small>
        </div>
    </div>
</td>

<td>
    <div class="text-light-peach small">
        <i class="bi bi-geo-alt me-1 text-peach"></i><?= htmlspecialchars($ticket->location) ?>
    </div>
</td>

<td>
    <div class="fw-medium text-light-peach small"><?= htmlspecialchars($ticket->date) ?></div>
    <div class="text-peach x-small"><?= htmlspecialchars($ticket->startTime) ?></div>
</td>

<td class="text-center">
    <span class="badge rounded-pill" style="background-color: #E5A290; color: #3B0B00;"><?= $ticket->guestCount ?></span>
</td>