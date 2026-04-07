<?php $display = $ticket->getEvent()->getDisplayData(); ?>
<tr class="ticket-row-styled">
    <td class="ps-4">
        <div class="d-flex align-items-center py-2">
            <div class="icon-box-peach me-3">
                <i class="bi <?= $display['icon'] ?>"></i>
            </div>
            <div>
                <div class="fw-bold text-light-peach"><?= htmlspecialchars($display['title']) ?></div>
                <div class="text-peach x-small"><?= htmlspecialchars($display['venue']) ?></div>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-light text-dark fw-normal border"><?= $ticket->getNumberOfPeople() ?></span>
    </td>
    <td>
        <?php if ($display['startTime'] === "Festival Period"): ?>
            <span class="badge bg-soft-orange text-peach rounded-pill x-small fw-bold">All Access</span>
        <?php else: ?>
            <div class="fw-medium text-light-peach small"><?= date('D, M j', strtotime($display['startTime'])) ?></div>
            <div class="text-peach x-small"><?= date('H:i', strtotime($display['startTime'])) ?></div>
        <?php endif; ?>
    </td>
</tr>