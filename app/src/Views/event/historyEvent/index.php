<?php require __DIR__ . '/../../partials/header.php'; ?>

<?php

use App\ViewModels\PageElementViewModel;

/** @var PageElementViewModel $pageVM */

$sections = $pageVM->getSections();
$heroElements = $sections[4] ?? [];
?>

<div class="history-page">

    <?php if (!empty($heroElements)): ?>
        <section class="history-section4">
            <?php foreach ($heroElements as $element): ?>
                <?= $element->render(); ?>
            <?php endforeach; ?>

            <a href="/history/booking" class="history-section4__button">Start planning</a>
        </section>
    <?php endif; ?>

    <section class="history-page__tour-section">
        <div class="history-page__container history-page__tour-grid">
            <div class="history-page__tour-text">
                <h2 class="history-page__section-title">Historic Walking Tour</h2>
                <p>
                    Begin your journey at Bavo Church, where the heart of Haarlem’s history comes into view.
                    From there, this guided walking tour takes you through the city’s oldest streets, lively courtyards,
                    and landmarks that tell its cultural and artistic past.
                </p>
                <p>
                    Local guides share interesting stories about Haarlem during the Dutch Golden Age, and explain how
                    everyday life looked in earlier centuries.
                </p>

                <a href="/history/booking" class="history-page__primary-button">
                    Plan your tour
                </a>
            </div>

            <div class="history-page__tour-details">
                <div class="history-page__detail-item">2.5 hours</div>
                <div class="history-page__detail-item">Max 12 people</div>
                <div class="history-page__detail-item">From €17.50</div>
                <div class="history-page__detail-item">Minimum 12 years old</div>
                <div class="history-page__detail-item">1 drink included</div>
                <div class="history-page__detail-item">English / Dutch / Mandarin</div>
            </div>
        </div>
    </section>

    <section class="history-page__venues-section">
        <div class="history-page__container">
            <h2 class="history-page__section-title">What You’ll See</h2>

            <div class="history-page__venues-list">
                <?php foreach ($venues as $venue): ?>
                    <article class="history-page__venue-card">
                        <img
                            src="<?= htmlspecialchars($venue->getImgURL() ?? '/assets/images/history/hero.png') ?>"
                            alt="<?= htmlspecialchars($venue->getAltText() ?? $venue->getVenueName()) ?>"
                            class="history-page__venue-image">

                        <div class="history-page__venue-content">
                            <h3><?= htmlspecialchars($venue->getVenueName()) ?></h3>
                            <p><?= htmlspecialchars($venue->getDetails() ?? 'More info coming soon.') ?></p>

                            <a href="/history/<?= $venue->getVenueId() ?>" class="history-page__secondary-button">
                                More info
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="history-page__bottom-section">
        <div class="history-page__container history-page__bottom-grid">

            <div class="history-page__schedule-block">
                <h2 class="history-page__section-title">Tour Schedule</h2>
                <p>Overview of all available tour times and languages.</p>
                <p class="history-page__schedule-note">
                    Languages: EN (English) - NL (Dutch) - 中文 (Mandarin)
                </p>

                <?php
                $groupedSchedule = [];

                foreach ($sessions as $session) {
                    $day = date('D d', strtotime($session->getSlotDate()));
                    $time = substr($session->getStartTime(), 0, 5);

                    $lang = match ($session->getLanguage()) {
                        'English' => 'EN',
                        'Dutch' => 'NL',
                        'Mandarin' => '中文',
                        default => $session->getLanguage()
                    };

                    if (!isset($groupedSchedule[$day])) {
                        $groupedSchedule[$day] = [];
                    }

                    if (!isset($groupedSchedule[$day][$time])) {
                        $groupedSchedule[$day][$time] = [];
                    }

                    $groupedSchedule[$day][$time][] = $lang;
                }
                ?>

                <table class="history-page__schedule-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Languages</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedSchedule as $day => $times): ?>
                            <tr class="schedule-spacer">
                                <td colspan="3"></td>
                            </tr>

                            <?php $firstRow = true; ?>
                            <?php foreach ($times as $time => $languages): ?>
                                <tr>
                                    <td><?= $firstRow ? htmlspecialchars($day) : '' ?></td>
                                    <td><?= htmlspecialchars($time) ?></td>
                                    <td><?= htmlspecialchars(implode(' - ', $languages)) ?></td>
                                </tr>
                                <?php $firstRow = false; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="/history/booking" class="history-page__primary-button">
                    Start planning
                </a>
            </div>

            <div class="history-page__nearby">
                <h2 class="history-page__section-title">More to discover nearby</h2>

                <img
                    src="/assets/images/history/grotemarkt.png"
                    alt="Grote Markt"
                    class="history-page__nearby-image">

                <p>
                    The history tour begins at the Grote Markt, where many other festival events take place nearby.
                    Jazz and dance performances, Yummy food events, and Magic@Tylers for kids are all within walking
                    distance, making it easy to plan a full day in the city center.
                </p>

                <div class="history-page__nearby-buttons">
                    <a href="/jazz" class="history-page__secondary-button">Explore Jazz</a>
                    <a href="/dance" class="history-page__secondary-button">Explore Dance!</a>
                    <a href="/kidsEvent" class="history-page__secondary-button">Explore Magic@Tylers</a>
                    <a href="/yummy" class="history-page__secondary-button">Explore Yummy</a>
                </div>
            </div>

        </div>
    </section>

</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>