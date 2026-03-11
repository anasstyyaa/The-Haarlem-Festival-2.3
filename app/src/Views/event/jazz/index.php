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
        <a href="/jazz?day=all" class="<?= $selectedDay === 'all' ? 'active' : '' ?>">All</a>
        <a href="/jazz?day=thu" class="<?= $selectedDay === 'thu' ? 'active' : '' ?>">Thu 23</a>
        <a href="/jazz?day=fri" class="<?= $selectedDay === 'fri' ? 'active' : '' ?>">Fri 24</a>
        <a href="/jazz?day=sat" class="<?= $selectedDay === 'sat' ? 'active' : '' ?>">Sat 25</a>
        <a href="/jazz?day=sun" class="<?= $selectedDay === 'sun' ? 'active' : '' ?>">Sun 26</a>
    </div>

    <div class="lineup-grid">
        <?php if (empty($lineup)): ?>
            <p>No artists found for this day.</p>
        <?php else: ?>
            <?php foreach ($lineup as $item): ?>
                <?php
                    $artist = $item['artist'];
                    $events = $item['events'];

                    $lowestPrice = null;
                    foreach ($events as $event) {
                        $price = (float)$event['Price'];
                        if ($lowestPrice === null || $price < $lowestPrice) {
                            $lowestPrice = $price;
                        }
                    }
                ?>

                <div class="artist-card">
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
                                        <?= date('D d M', strtotime($event['StartDateTime'])) ?>
                                    </span>
                                    ·
                                    <span>
                                        <?= date('H:i', strtotime($event['StartDateTime'])) ?>-<?= date('H:i', strtotime($event['EndDateTime'])) ?>
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

<?php require __DIR__ . '/../../partials/footer.php'; ?>