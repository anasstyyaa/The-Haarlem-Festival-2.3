<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="history-booking">
    <h1 class="history-booking__title">History Tour Schedule</h1>

    <div class="history-booking__section">
        <h2 class="history-booking__heading">Select Day</h2>
        <div id="historyDayContainer" class="history-booking__options"></div>
    </div>

    <div class="history-booking__section">
        <h2 class="history-booking__heading">Select Time</h2>
        <div id="historyTimeContainer" class="history-booking__options"></div>
    </div>

    <div class="history-booking__section">
        <h2 class="history-booking__heading">Select Language</h2>
        <div id="historyLanguageContainer" class="history-booking__options"></div>
    </div>

    <div id="historySelectedSessionContainer" class="history-booking__section" style="display:none;">
        <h2 class="history-booking__heading">Selected Tour</h2>
        <div class="history-booking__summary">
            <strong>Date:</strong> <span id="historySelectedDate"></span><br>
            <strong>Time:</strong> <span id="historySelectedTime"></span><br>
            <strong>Language:</strong> <span id="historySelectedLanguage"></span><br>
        </div>
    </div>

    <div id="historyTicketSection" class="history-booking__section" style="display:none;">
        <h2 class="history-booking__heading">Select Tickets</h2>

        <form method="POST" action="/history/book" class="history-booking__form">
            <input type="hidden" name="eventId" id="historyEventIdInput" value="">

            <div class="history-booking__field">
                <label for="historyIndividualCount">
                    <strong>Individual Ticket (€17.50)</strong>
                </label>
                <input type="number" id="historyIndividualCount" name="individualCount" min="0" value="0">
            </div>

            <div class="history-booking__field">
                <label for="historyFamilyCount">
                    <strong>Family Ticket (€60.00)</strong>
                </label>
                <input type="number" id="historyFamilyCount" name="familyCount" min="0" value="0">
            </div>

            <button type="submit" class="history-booking__submit-btn">
                Add to Personal Program
            </button>
        </form>
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

<script src="/assets/js/history/history-booking.js"></script>

<?php include __DIR__ . '/../../partials/flashMessage.php'; ?>
<?php require __DIR__ . '/../../partials/footer.php'; ?>