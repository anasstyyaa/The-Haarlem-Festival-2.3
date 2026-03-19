<?php require __DIR__ . '/../../partials/header.php'; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow" role="alert" style="z-index: 9999; min-width: 300px;">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow" role="alert" style="z-index: 9999; min-width: 300px;">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>


<section class="jazz-detail-page">
    <div class="jazz-detail-container">

        <a href="/jazz" class="jazz-back">← Back</a>

        <?php if ($artist->getImageUrl()): ?>
            <div
                class="artist-hero"
                style="background-image: url('<?= htmlspecialchars($artist->getImageUrl()) ?>');"
            >
                <div class="artist-hero-overlay">
                    <h1 class="artist-hero-title">
                        <?= htmlspecialchars($artist->getName()) ?>
                    </h1>
                </div>
            </div>
        <?php else: ?>
            <div class="artist-hero no-hero-image">
                <div class="artist-hero-overlay">
                    <h1 class="artist-hero-title">
                        <?= htmlspecialchars($artist->getName()) ?>
                    </h1>
                </div>
            </div>
        <?php endif; ?>

        <div class="jazz-detail-description">
            <?= html_entity_decode($artist->getDescription() ?? 'No description available.') ?>
        </div>

        <section class="jazz-detail-tickets">
            <h2 class="tickets-title">Tickets</h2>

            <?php if (empty($events)): ?>
                <p class="jazz-detail-no-events">No performances available.</p>
            <?php else: ?>
                <div class="jazz-ticket-list">
                    <?php foreach ($events as $event): ?>
                        <div class="jazz-ticket-card">
                            <div class="jazz-ticket-left">
                                <div class="jazz-ticket-date">
                                    <?= date('D d.m.Y', strtotime($event->getStartDateTime())) ?>
                                </div>

                                <div class="jazz-ticket-time">
                                    <?= date('H:i', strtotime($event->getStartDateTime())) ?>
                                    -
                                    <?= date('H:i', strtotime($event->getEndDateTime())) ?>
                                </div>

                                <div class="jazz-ticket-venue">
                                    <?= htmlspecialchars($event->getVenueName() ?? '') ?>
                                    <?= !empty($event->getHallName()) ? ', ' . htmlspecialchars($event->getHallName()) : '' ?>
                                </div>
                            </div>

                            <div class="jazz-ticket-right">
                                <div class="jazz-ticket-price">
                                    <?php if ((float)$event->getPrice() == 0.0): ?>
                                        Free
                                    <?php else: ?>
                                        €<?= number_format((float)$event->getPrice(), 2) ?>
                                    <?php endif; ?>
                                </div>

                                <form method="POST" action="/addTicket" class="jazz-ticket-form">
                                    <input type="hidden" name="event_id" value="<?= htmlspecialchars($event->getJazzEventID()) ?>">
                                    <input type="hidden" name="event_type" value="jazz">

                                    <div class="ticket-quantity-wrapper">
                                        <div class="ticket-quantity-controls">
                                            <button type="button" class="qty-btn" onclick="changeQty('qty-<?= $event->getJazzEventID() ?>', -1)">−</button>
                                            <input 
                                                id="qty-<?= $event->getJazzEventID() ?>"
                                                class="ticket-quantity-input"
                                                type="number"
                                                name="number_of_people"
                                                value="1"
                                                min="1"
                                                max="20"
                                                required
                                            >
                                            <button type="button" class="qty-btn" onclick="changeQty('qty-<?= $event->getJazzEventID() ?>', 1)">+</button>
                                        </div>
                                    </div>

                                    <button class="jazz-ticket-button" type="submit">
                                        Add to Personal Program
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</section>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
<script src="/js/ticketCounter.js"></script>
<script src="/js/flashMessage.js"></script>