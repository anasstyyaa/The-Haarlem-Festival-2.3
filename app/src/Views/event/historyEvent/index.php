<?php require __DIR__ . '/../../partials/header.php'; ?>

<?php
use App\ViewModels\PageElementViewModel;
/** @var PageElementViewModel $vm */
?>

<div class="history-page">
    <?php foreach ($vm->getSections() as $section => $elements): ?>
        <div class="section<?= htmlspecialchars($section) ?>">
            <?php foreach ($elements as $element): ?>
                <?= $element->render(); ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <section class="history-page__hero history-page__hero--full">
        <div class="history-page__hero-overlay">
            <div class="history-page__container">
                <div class="history-page__hero-content-full">
                    <h1 class="history-page__hero-title">Walk Through Haarlem’s Past and Find Its Stories</h1>
                    <p class="history-page__hero-subtitle">
                        Discover the people, places and moments that shaped Haarlem across the centuries.
                    </p>

                    <a href="/history/booking" class="history-page__primary-button">
                        Start planning
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="history-page__story-section">
        <div class="history-page__container history-page__story-grid">
            <div class="history-page__story-image-wrap">
                <img src="/assets/images/history/Vector.png" alt="Historic Haarlem" class="history-page__story-image">
            </div>

            <div class="history-page__story-text">
                <h2 class="history-page__section-title">A City Shaped by Stories</h2>
                <p>
                    Haarlem has always been shaped by the people who passed through it. What began as a small settlement along the River Spaarne grew into a lively town of merchants, craftsmen, and artists.
                </p>
                <p>
                    Today, its historic streets, churches, and hidden hofjes still carry traces of the city’s past, waiting to share their stories with those who walk its streets.
                </p>

                <h3 class="history-page__subheading">Key Historical Facts</h3>
                <ul class="history-page__facts-list">
                    <li>Haarlem received city rights in 1245</li>
                    <li>A major center during the Dutch Golden Age</li>
                    <li>Home to Frans Hals and many artists</li>
                    <li>Known for hidden hofjes and historic streets</li>
                    <li>Important in the early tulip trade</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="history-page__tour-section">
        <div class="history-page__container history-page__tour-grid">
            <div class="history-page__tour-text">
                <h2 class="history-page__section-title history-page__section-title--dark">Historic Walking Tour</h2>
                <p>
                    Begin your journey at Bavo Church, where the heart of Haarlem’s history comes into view. From there, this guided walking tour takes you through the city’s oldest streets, lively courtyards, and landmarks that tell its cultural and artistic past.
                </p>
                <p>
                    Local guides share interesting stories about Haarlem during the Dutch Golden Age, and explain how everyday life looked in earlier centuries. It is a relaxed and informative way to discover the city.
                </p>

                <a href="/history/booking" class="history-page__primary-button">
                    Plan your tour
                </a>
            </div>

            <div class="history-page__tour-details">
                <div class="history-page__detail-item">2.5 hours</div>
                <div class="history-page__detail-item">Max 12 people</div>
                <div class="history-page__detail-item">From €17.50</div>
                <div class="history-page__detail-item">No stroller allowed</div>
                <div class="history-page__detail-item">Minimum 12 years old</div>
                <div class="history-page__detail-item">1 drink included</div>
                <div class="history-page__detail-item">English / Dutch / Mandarin</div>
            </div>
        </div>
    </section>

    <section class="history-page__timeline-section">
        <div class="history-page__container">
            <div class="history-page__timeline-grid">
                <article class="history-page__timeline-card">
                    <span class="history-page__timeline-year">1572–1573</span>
                    <h3>The Siege of Haarlem</h3>
                    <p>
                        During the Eighty Years’ War, Haarlem endured a long siege by Spanish forces. Despite eventual surrender, the city’s resistance became legendary.
                    </p>
                </article>

                <article class="history-page__timeline-card">
                    <span class="history-page__timeline-year">1600s</span>
                    <h3>Center of Art and Culture</h3>
                    <p>
                        Haarlem became home to renowned painters and writers, including Frans Hals, Jacob van Ruisdael, and Golden Age publishers.
                    </p>
                </article>

                <article class="history-page__timeline-card">
                    <span class="history-page__timeline-year">1630–1637</span>
                    <h3>Tulip Mania</h3>
                    <p>
                        Haarlem was at the epicenter of tulip mania, one of the earliest speculative bubbles in history, connected with the flower trade.
                    </p>
                </article>

                <article class="history-page__timeline-card">
                    <span class="history-page__timeline-year">1700s</span>
                    <h3>Textile Industry Boom</h3>
                    <p>
                        The city’s linen bleaching fields and textile activity brought trade and prosperity, shaping Haarlem’s economy for generations.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="history-page__banner-section">
        <img src="/assets/images/history/tulip.png" alt="Haarlem landscape" class="history-page__banner-image">
    </section>

    <section class="history-page__venues-section">
        <div class="history-page__container">
            <div class="history-page__venues-header">
                <h2 class="history-page__section-title">What You’ll See on the Walk</h2>
                <p>
                    Each stop on our journey reveals a different chapter of Haarlem’s remarkable history.
                </p>
            </div>

            <div class="history-page__venues-list">
                <?php foreach ($venues as $venue): ?>
                    <article class="history-page__venue-card">
                        <div class="history-page__venue-image-wrap">
                            <img
                                src="<?= htmlspecialchars($venue->getImgURL() ?? '/assets/images/history/hero.png') ?>"
                                alt="<?= htmlspecialchars($venue->getAltText() ?? $venue->getVenueName()) ?>"
                                class="history-page__venue-image"
                            >
                        </div>

                        <div class="history-page__venue-content">
                            <h3><?= htmlspecialchars($venue->getVenueName()) ?></h3>
                            <p>
                                <?= htmlspecialchars($venue->getDetails() ?? 'More information about this stop will be added soon.') ?>
                            </p>

                            <button type="button" class="history-page__secondary-button">
                                More info
                            </button>
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

                <?php
                $groupedSchedule = [];

                foreach ($sessions as $session) {
                    $date = $session->getSlotDate();
                    $time = substr($session->getStartTime(), 0, 5);
                    $language = $session->getLanguage();

                    if (!isset($groupedSchedule[$date])) {
                        $groupedSchedule[$date] = [];
                    }

                    if (!isset($groupedSchedule[$date][$time])) {
                        $groupedSchedule[$date][$time] = [];
                    }

                    $groupedSchedule[$date][$time][] = $language;
                }

                $languageMap = [
                    'English' => 'EN',
                    'Dutch' => 'NL',
                    'Mandarin' => '中文'
                ];
                ?>

                <p class="history-page__schedule-note">
                    Languages: EN (English) · NL (Dutch) · 中文 (Mandarin)
                </p>

                <table class="history-page__schedule-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Languages</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedSchedule as $date => $times): ?>
                            <?php
                            $formattedDay = date('D d', strtotime($date));
                            $firstRow = true;
                            ?>
                            <?php foreach ($times as $time => $languages): ?>
                                <tr>
                                    <td><?= $firstRow ? htmlspecialchars($formattedDay) : '' ?></td>
                                    <td><?= htmlspecialchars($time) ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            implode(
                                                ' - ',
                                                array_map(fn($lang) => $languageMap[$lang] ?? $lang, $languages)
                                            )
                                        ) ?>
                                    </td>
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

            <div class="history-page__nearby-block">
                <h2 class="history-page__section-title">More to discover nearby</h2>

                <div class="history-page__nearby-card">
                    <img src="/assets/images/history/grotemarkt.png" alt="Grote Markt" class="history-page__nearby-image">
                    <p>
                        The history tour begins at the Grote Markt, where many other festival events take place nearby. Jazz and dance performances, Yummy food events, and kids activities are all within walking distance.
                    </p>
                </div>

                <div class="history-page__nearby-buttons">
                    <a href="/jazz" class="history-page__secondary-button history-page__secondary-button--link">Explore Jazz</a>
                    <a href="/dance" class="history-page__secondary-button history-page__secondary-button--link">Explore Dance</a>
                    <a href="/kidsEvent" class="history-page__secondary-button history-page__secondary-button--link">Explore Magic@Tylers</a>
                    <a href="/yummy" class="history-page__secondary-button history-page__secondary-button--link">Explore Yummy</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>