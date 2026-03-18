<?php
/** @var App\Models\PersonalProgram $program */
$program = $_SESSION['program'] ?? new App\Models\PersonalProgram();
?>

<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="container program-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Personal Program</h2>
        <span class="badge bg-primary rounded-pill fs-6">
            <?= count($program->getTickets()) ?> Tickets
        </span>
    </div>

    <?php if (count($program->getTickets()) === 0): ?>
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
                            <th class="text-center">Guests</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program->getTickets() as $index => $ticket): ?>
                            <?php 
                                $event = $ticket->getEvent();
                                $details = $event->getDetails(); 
                                
                                $title = ($details && method_exists($details, 'getName')) 
                                ? $details->getName() 
                                : "Event " . $event->getSubEventId();
                                $image = ($details && method_exists($details, 'getImageUrl') && $details->getImageUrl())
                                ? $details->getImageUrl()
                                : "/assets/images/placeholder.jpg";
                                $location = ($details && method_exists($details, 'getLocation')) ? $details->getLocation() : "Haarlem";
                                                   
                                $guestCount = $ticket->getNumberOfPeople();
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $image ?>" class="rounded me-3" style="width:60px; height:60px; object-fit:cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($title) ?></div>
                                            <small class="text-muted text-uppercase"><?= $event->getEventType()->name ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($location) ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-3">
                                        <i class="bi bi-people-fill me-1"></i>
                                        <?= htmlspecialchars($guestCount) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="/removeTicket" onsubmit="return confirm('Remove this item from your Personal Program?');">
                                        <input type="hidden" name="ticket_id" value="<?= $ticket->getProgramItemId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-1">                                           
                                            Delete 
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 text-end">
            <a href="/checkout" class="btn btn-success btn-m px-5 shadow-sm fw-bold">
                Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>