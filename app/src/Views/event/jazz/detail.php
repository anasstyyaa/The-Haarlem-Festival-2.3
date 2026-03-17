<?php require __DIR__ . '/../../partials/header.php'; ?>

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

                                <button class="jazz-ticket-button" type="button">
                                    Get Ticket
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</section>

<?php require __DIR__ . '/../../partials/footer.php'; ?>