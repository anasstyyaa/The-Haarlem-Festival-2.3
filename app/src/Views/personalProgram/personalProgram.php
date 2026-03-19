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
                            <th>Date</th>
                            <th>Time</th>
                            <th>Language</th>
                            <th class="text-center">Guests</th>
                            <th class="text-center">Price</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program->getTickets() as $index => $ticket): ?>
                            <?php
                            $event = $ticket->getEvent();
                            $details = $event->getDetails();

                            $title = "Event " . $event->getSubEventId();
                            $image = "/assets/images/placeholder.jpg";
                            $location = "Haarlem";

                            $date = '';
                            $startTime = '';
                            $language = '';

                            if (is_array($details) && isset($details['artist'])) {
                                $artist = $details['artist'] ?? null;
                                $venueInfo = $details['venueInfo'] ?? null;

                                if ($artist && method_exists($artist, 'getName')) {
                                    $title = $artist->getName();
                                }

                                if ($artist && method_exists($artist, 'getImageUrl') && $artist->getImageUrl()) {
                                    $image = $artist->getImageUrl();
                                }

                                if (!empty($venueInfo['VenueName'])) {
                                    $location = $venueInfo['VenueName'];

                                    if (!empty($venueInfo['HallName'])) {
                                        $location .= ', ' . $venueInfo['HallName'];
                                    }
                                }
                            } elseif ($details) {
                                if (method_exists($details, 'getName')) {
                                    $title = $details->getName();
                                }

                                if (method_exists($details, 'getImageUrl') && $details->getImageUrl()) {
                                    $image = $details->getImageUrl();
                                }

                                if (method_exists($details, 'getLocation') && $details->getLocation()) {
                                    $location = $details->getLocation();
                                }

                                if ($details instanceof \App\Models\Yummy\RestaurantModel) {
                                    $session = $details->getSessionData();
                                    if ($session) {
                                        $date = date('Y-m-d', strtotime($session->getDate()));
                                        $startTime = date('H:i', strtotime($session->getStartTime()));
                                    }
                                }

                                if (method_exists($details, 'getSlotDate') && $details->getSlotDate()) {
                                    $date = date('Y-m-d', strtotime($details->getSlotDate()));
                                }

                                if (method_exists($details, 'getStartTime') && $details->getStartTime()) {
                                    $startTime = date('H:i', strtotime($details->getStartTime()));
                                }

                                if (method_exists($details, 'getLanguage') && $details->getLanguage()) {
                                    $language = $details->getLanguage();
                                }
                            }

                            $guestCount = $ticket->getNumberOfPeople();
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($image) ?>" class="rounded me-3" style="width:60px; height:60px; object-fit:cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($title) ?></div>
                                            <small class="text-muted text-uppercase"><?= htmlspecialchars($event->getEventType()->name) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($location) ?></td>
                                <td><?= htmlspecialchars($date) ?></td>
                                <td><?= htmlspecialchars($startTime) ?></td>
                                <td><?= htmlspecialchars($language) ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-3">
                                        <i class="bi bi-people-fill me-1"></i>
                                        <?= htmlspecialchars((string)$guestCount) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold">€<?= number_format($ticket->getTotalPrice(), 2) ?></div>
                                    <?php if ($ticket->getNumberOfPeople() > 1): ?>
                                        <small class="text-muted">
                                            €<?= number_format($ticket->getUnitPrice(), 2) ?> x <?= $ticket->getNumberOfPeople() ?>
                                        </small>
                                    <?php endif; ?>
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
            <form action="/checkout" method="POST">
                <button type="submit" class="btn btn-success btn-m px-5 shadow-sm fw-bold">
                    Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>