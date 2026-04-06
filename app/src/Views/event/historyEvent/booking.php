<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="history-booking-page">
    <div class="history-booking-page__container">

        <div class="history-booking-page__left">
            <div class="history-booking-card">
                <h1 class="history-booking-card__title">Book Your History Tour</h1>
                <p class="history-booking-card__intro">
                    Select a day, time, and language to reserve your place on the Haarlem history tour.
                </p>

                <div class="history-booking-card__section">
                    <h2 class="history-booking-card__heading">1. Select Day</h2>
                    <div id="historyDayContainer" class="history-booking-card__options"></div>
                </div>

                <div id="historyTimeSection" class="history-booking-card__section" style="display:none;">
                    <h2 class="history-booking-card__heading">2. Select Time</h2>
                    <div id="historyTimeContainer" class="history-booking-card__options"></div>
                </div>

                <div id="historyLanguageSection" class="history-booking-card__section" style="display:none;">
                    <h2 class="history-booking-card__heading">3. Select Language</h2>
                    <div id="historyLanguageContainer" class="history-booking-card__options"></div>
                </div>

                <div id="historySelectedSessionContainer" class="history-booking-card__selected" style="display:none;">
                    <h2 class="history-booking-card__heading">Selected Tour</h2>
                    <div class="history-booking-card__summary">
                        <div><strong>Date:</strong> <span id="historySelectedDate"></span></div>
                        <div><strong>Time:</strong> <span id="historySelectedTime"></span></div>
                        <div><strong>Language:</strong> <span id="historySelectedLanguage"></span></div>
                    </div>
                </div>

                <div id="historyTicketSection" class="history-booking-card__tickets" style="display:none;">
                    <h2 class="history-booking-card__heading">4. Select Tickets</h2>

                    <form method="POST" action="/history/book" class="history-booking-card__form">
                        <input type="hidden" name="eventId" id="historyEventIdInput" value="">

                        <div class="history-booking-card__ticket-row">
                            <label for="historyIndividualCount">Individual Ticket (€17.50)</label>
                            <input type="number" id="historyIndividualCount" name="individualCount" min="0" value="0">
                        </div>

                        <div class="history-booking-card__ticket-row">
                            <label for="historyFamilyCount">Family Ticket (€60.00)</label>
                            <input type="number" id="historyFamilyCount" name="familyCount" min="0" value="0">
                        </div>

                        <button type="submit" class="history-booking-card__submit">
                            Add to Personal Program
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="history-booking-page__right">
            <div class="history-route-card">
                <h2 class="history-route-card__title">Tour Route</h2>
                <p class="history-route-card__subtitle">
                    Follow the story of Haarlem through its most iconic places.
                </p>

                <?php if (empty($stops)): ?>
    <div class="history-route-card__empty">
        No route information available.
    </div>
<?php else: ?>
    <div class="history-route-timeline">
        <?php foreach ($stops as $stop): ?>
            <div class="history-route-timeline__item">
                <div class="history-route-timeline__marker">
                    <?= htmlspecialchars((string)$stop['stopOrder']) ?>
                </div>

                <div class="history-route-timeline__content">
                    <h3>
                        Stop <?= htmlspecialchars((string)$stop['stopOrder']) ?>
                        — <?= htmlspecialchars($stop['venueName']) ?>
                    </h3>

                    <?php if (!empty($stop['location'])): ?>
                        <p class="history-route-timeline__location">
                            <span class="history-route-timeline__location-icon">📍</span>
                            <?= htmlspecialchars($stop['location']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
    window.historyBookingSessions = <?= json_encode(array_map(function ($session) {
        return [
            'eventId' => $session->getEventId(),
            'slotDate' => $session->getSlotDate(),
            'startTime' => $session->getStartTime(),
            'language' => $session->getLanguage(),
            'duration' => $session->getDuration(),
            'minAge' => $session->getMinAge(),
            'capacity' => $session->getCapacity(),
            'priceIndividual' => $session->getPriceIndividual(),
            'priceFamily' => $session->getPriceFamily(),
        ];
    }, $sessions)) ?>;
</script>

<script src="/js/history/history-booking.js"></script>
<?php include __DIR__ . '/../../partials/flashMessage.php'; ?>
<?php require __DIR__ . '/../../partials/footer.php'; ?>