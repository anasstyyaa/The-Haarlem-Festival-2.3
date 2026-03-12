<?php require __DIR__ . '/../../partials/header.php'; ?>

<h1>History Tour Schedule</h1>

<div class="section">
    <h2>Select Day</h2>
    <div id="dayContainer"></div>
</div>

<div class="section">
    <h2>Select Time</h2>
    <div id="timeContainer"></div>
</div>

<div class="section">
    <h2>Select Language</h2>
    <div id="languageContainer"></div>
</div>

<div class="section" id="selectedSessionContainer" style="display:none;">
    <h2>Selected Tour</h2>
    <div style="border:1px solid #ccc; padding:10px; width:fit-content;">
        <strong>Date:</strong> <span id="selectedDate"></span><br>
        <strong>Time:</strong> <span id="selectedTime"></span><br>
        <strong>Language:</strong> <span id="selectedLanguage"></span><br>
    </div>
</div>

<div class="section" id="ticketSection" style="display:none;">
    <h2>Select Tickets</h2>

    <form method="POST" action="/history/book">
        <input type="hidden" name="eventId" id="eventIdInput" value="">

        <div style="margin-bottom: 12px;">
            <label for="individualCount"><strong>Individual Ticket (€17.50)</strong></label><br>
            <input type="number" id="individualCount" name="individualCount" min="0" value="0">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="familyCount"><strong>Family Ticket (€60.00)</strong></label><br>
            <input type="number" id="familyCount" name="familyCount" min="0" value="0">
        </div>

        <button type="submit">Add to Personal Program</button>
    </form>
</div>

<style>
    .pill-btn {
        display: inline-block;
        margin: 6px;
        padding: 8px 14px;
        border-radius: 16px;
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
    }

    .pill-btn.active {
        background: #d88b2b;
        color: white;
        border-color: #d88b2b;
    }
</style>

<script>
    const sessions = <?= json_encode(array_map(function ($session) {
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

    let selectedDay = null;
    let selectedTime = null;
    let selectedLanguage = null;
    let selectedEvent = null;

    const dayContainer = document.getElementById('dayContainer');
    const timeContainer = document.getElementById('timeContainer');
    const languageContainer = document.getElementById('languageContainer');

    const selectedSessionContainer = document.getElementById('selectedSessionContainer');
    const ticketSection = document.getElementById('ticketSection');

    const selectedDateEl = document.getElementById('selectedDate');
    const selectedTimeEl = document.getElementById('selectedTime');
    const selectedLanguageEl = document.getElementById('selectedLanguage');
    const eventIdInput = document.getElementById('eventIdInput');

    function uniqueValues(arr) {
        return [...new Set(arr)];
    }

    function formatDay(day) {
        const date = new Date(day);
        return date.toLocaleDateString('en-GB', {
            weekday: 'short',
            day: '2-digit'
        });
    }

    function renderDays() {
        const days = uniqueValues(sessions.map(s => s.slotDate));
        dayContainer.innerHTML = '';

        days.forEach(day => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pill-btn' + (selectedDay === day ? ' active' : '');
            btn.textContent = formatDay(day);
            btn.onclick = () => {
                selectedDay = day;
                selectedTime = null;
                selectedLanguage = null;
                selectedEvent = null;
                renderAll();
            };
            dayContainer.appendChild(btn);
        });
    }

    function renderTimes() {
        timeContainer.innerHTML = '';
        if (!selectedDay) return;

        const times = uniqueValues(
            sessions
                .filter(s => s.slotDate === selectedDay)
                .map(s => s.startTime.substring(0, 5))
        );

        times.forEach(time => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pill-btn' + (selectedTime === time ? ' active' : '');
            btn.textContent = time;
            btn.onclick = () => {
                selectedTime = time;
                selectedLanguage = null;
                selectedEvent = null;
                renderAll();
            };
            timeContainer.appendChild(btn);
        });
    }

    function renderLanguages() {
        languageContainer.innerHTML = '';
        if (!selectedDay || !selectedTime) return;

        const languages = uniqueValues(
            sessions
                .filter(s =>
                    s.slotDate === selectedDay &&
                    s.startTime.substring(0, 5) === selectedTime
                )
                .map(s => s.language)
        );

        languages.forEach(language => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pill-btn' + (selectedLanguage === language ? ' active' : '');
            btn.textContent = language;
            btn.onclick = () => {
                selectedLanguage = language;
                selectedEvent = sessions.find(s =>
                    s.slotDate === selectedDay &&
                    s.startTime.substring(0, 5) === selectedTime &&
                    s.language === selectedLanguage
                ) || null;
                renderAll();
            };
            languageContainer.appendChild(btn);
        });
    }

    function renderSelectedSession() {
        if (!selectedEvent) {
            selectedSessionContainer.style.display = 'none';
            ticketSection.style.display = 'none';
            eventIdInput.value = '';
            return;
        }

        selectedDateEl.textContent = selectedEvent.slotDate;
        selectedTimeEl.textContent = selectedEvent.startTime.substring(0, 5);
        selectedLanguageEl.textContent = selectedEvent.language;
        eventIdInput.value = selectedEvent.eventId;

        selectedSessionContainer.style.display = 'block';
        ticketSection.style.display = 'block';
    }

    function renderAll() {
        renderDays();
        renderTimes();
        renderLanguages();
        renderSelectedSession();
    }

    renderAll();
</script>