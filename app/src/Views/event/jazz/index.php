<?php require __DIR__ . '/../../partials/header.php'; ?>

<section class="jazz-hero">
    <img src="/assets/images/jazz-lineup.jpg" alt="Haarlem Jazz">
    <div class="hero-overlay">
        <h1>HAARLEM JAZZ</h1>
        <p>
            This July, Haarlem comes alive with soul, rhythm, and fresh talent.
            Haarlem Jazz invites you to a week of unforgettable summer nights
            filled with music, community, and pure good vibes!
        </p>
    </div>
</section>

<section class="jazz-about">
   <div class="about-container">

        <h2>About Haarlem Jazz</h2>

        <p>
            Haarlem Jazz is bringing the summer vibes to the streets, and this year’s line up is overflowing with talent!
        </p>

        <p>
            Whether you’re a jazz lover or just here for the good vibes, there’s a band waiting to surprise you.
            Join us on the 23rd, 24th, 25th, and 26th of July for unforgettable days of live performances and nonstop energy!
        </p>

        <p>
            Grab your friends, explore the city, and get lost in the music. This is the kind of event you’ll talk about long after summer ends.
        </p>

        <p class="about-highlight">
            Ready to join the fun?
        </p>

    </div>
</section>

<section class="jazz-lineup">
    <div class="lineup-header">
        <h2>2026 Line Up</h2>
    </div>

    <div class="lineup-filters">
        <button class="filter-btn active" data-day="all">All</button>
        <button class="filter-btn" data-day="thu">Thu 23</button>
        <button class="filter-btn" data-day="fri">Fri 24</button>
        <button class="filter-btn" data-day="sat">Sat 25</button>
        <button class="filter-btn" data-day="sun">Sun 26</button>
    </div>

    <div class="lineup-grid">
        <?php if (empty($lineup)): ?>
            <p>No artists found.</p>
        <?php else: ?>
            <?php foreach ($lineup as $item): ?>
                <?php
                    $artist = $item['artist'];
                    $events = $item['events'];

                    $days = [];
                    foreach ($events as $event) {
                        $days[] = strtolower(date('D', strtotime($event->getStartDateTime())));
                    }
                    $days = array_unique($days);
                    $dataDays = implode(' ', $days);
                ?>

                <div class="artist-card" data-days="<?= htmlspecialchars($dataDays) ?>">
                    <div class="artist-image">
                        <?php if ($artist->getImageUrl()): ?>
                            <img src="<?= htmlspecialchars($artist->getImageUrl()) ?>" alt="<?= htmlspecialchars($artist->getName()) ?>">
                        <?php else: ?>
                            <div class="no-image">No image</div>
                        <?php endif; ?>
                    </div>

                    <div class="artist-info">
                        <h3><?= htmlspecialchars($artist->getName()) ?></h3>

                        <p class="artist-description">
                            <?= htmlspecialchars($artist->getShortDescription() ?? '') ?>
                        </p>

                        <div class="artist-events">
                            <?php foreach ($events as $event): ?>
                                <div class="event-line">
                                    <span>
                                        <?= date('D d M', strtotime($event->getStartDateTime())) ?>
                                    </span>
                                    ·
                                    <span>
                                        <?= date('H:i', strtotime($event->getStartDateTime())) ?>-<?= date('H:i', strtotime($event->getEndDateTime())) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="artist-bottom">
                            <a href="/jazz/<?= $artist->getId() ?>" class="more-btn">More...</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="jazz-passes">
    <div class="passes-container">
        <h2>All Access Passes</h2>
        <p class="passes-intro">
            Make the most of the Haarlem Festival with our special ticket options. Choose a day pass for flexible access on your preferred day, or go for the full festival pass to enjoy all three days without missing a moment! <br> 
            The all-access option offers the complete festival experience, giving you the freedom to explore more performances, activities, and the unique atmosphere Haarlem has to offer!
        </p>

        <div class="passes-list">
            <?php foreach ($passes as $pass): ?>
                <div class="pass-card">

                    <!-- LEFT IMAGE -->
                    <div class="pass-image">
                        <img src="<?= htmlspecialchars($pass->getImageUrl() ?: '/assets/img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($pass->getTitle()) ?>">
                    </div>

                    <!-- MIDDLE TEXT -->
                    <div class="pass-info">
                        <h3><?= htmlspecialchars($pass->getTitle()) ?></h3>
                        <p><?= htmlspecialchars(strip_tags($pass->getDescription() ?? '')) ?></p>
                        <div class="pass-price">€<?= number_format($pass->getPrice(), 2) ?></div>
                    </div>

                    <!-- RIGHT ACTION -->
                    <div class="pass-action">
                        <form method="POST" action="/addTicket" class="ticket-form">

                            <input type="hidden" name="event_id" value="<?= $pass->getId() ?>">
                            <input type="hidden" name="event_type" value="jazzpass">

                            <div class="ticket-quantity-wrapper">
                            <div class="ticket-quantity-controls">
                                <button type="button" class="qty-btn" onclick="changeQty('qty-pass-<?= $pass->getId() ?>', -1)">−</button>
                                <input 
                                    id="qty-pass-<?= $pass->getId() ?>"
                                    class="ticket-quantity-input"
                                    type="number"
                                    name="number_of_people"
                                    value="1"
                                    min="1"
                                    max="20"
                                    required
                                >
                                <button type="button" class="qty-btn" onclick="changeQty('qty-pass-<?= $pass->getId() ?>', 1)">+</button>
                            </div>

                            <button class="jazz-ticket-button" type="submit">
                                Add to Personal Program
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
<script src="/js/jazzFilter.js"></script>
<script src="/js/ticketCounter.js"></script>
<script src="/js/flashMessage.js"></script>