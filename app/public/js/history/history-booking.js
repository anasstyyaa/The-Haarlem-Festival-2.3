document.addEventListener('DOMContentLoaded', () => {
    const sessions = window.historyBookingSessions || [];

    let selectedDay = null;
    let selectedTime = null;
    let selectedLanguage = null;
    let selectedEvent = null;

    const dayContainer = document.getElementById('historyDayContainer');
    const timeContainer = document.getElementById('historyTimeContainer');
    const languageContainer = document.getElementById('historyLanguageContainer');

    const timeSection = document.getElementById('historyTimeSection');
    const languageSection = document.getElementById('historyLanguageSection');

    const selectedSessionContainer = document.getElementById('historySelectedSessionContainer');
    const ticketSection = document.getElementById('historyTicketSection');

    const selectedDateEl = document.getElementById('historySelectedDate');
    const selectedTimeEl = document.getElementById('historySelectedTime');
    const selectedLanguageEl = document.getElementById('historySelectedLanguage');
    const eventIdInput = document.getElementById('historyEventIdInput');

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
        const days = uniqueValues(sessions.map(session => session.slotDate));
        dayContainer.innerHTML = '';

        days.forEach(day => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'history-booking-pill' + (selectedDay === day ? ' history-booking-pill--active' : '');
            button.textContent = formatDay(day);

            button.addEventListener('click', () => {
                selectedDay = day;
                selectedTime = null;
                selectedLanguage = null;
                selectedEvent = null;
                renderAll();
            });

            dayContainer.appendChild(button);
        });
    }

    function renderTimes() {
        timeContainer.innerHTML = '';

        if (!selectedDay) {
            timeSection.style.display = 'none';
            return;
        }

        timeSection.style.display = 'block';

        const times = uniqueValues(
            sessions
                .filter(session => session.slotDate === selectedDay)
                .map(session => session.startTime.substring(0, 5))
        );

        times.forEach(time => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'history-booking-pill' + (selectedTime === time ? ' history-booking-pill--active' : '');
            button.textContent = time;

            button.addEventListener('click', () => {
                selectedTime = time;
                selectedLanguage = null;
                selectedEvent = null;
                renderAll();
            });

            timeContainer.appendChild(button);
        });
    }

    function renderLanguages() {
        languageContainer.innerHTML = '';

        if (!selectedDay || !selectedTime) {
            languageSection.style.display = 'none';
            return;
        }

        languageSection.style.display = 'block';

        const languages = uniqueValues(
            sessions
                .filter(session =>
                    session.slotDate === selectedDay &&
                    session.startTime.substring(0, 5) === selectedTime
                )
                .map(session => session.language)
        );

        languages.forEach(language => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'history-booking-pill' + (selectedLanguage === language ? ' history-booking-pill--active' : '');
            button.textContent = language;

            button.addEventListener('click', () => {
                selectedLanguage = language;

                selectedEvent = sessions.find(session =>
                    session.slotDate === selectedDay &&
                    session.startTime.substring(0, 5) === selectedTime &&
                    session.language === selectedLanguage
                ) || null;

                renderAll();
            });

            languageContainer.appendChild(button);
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
});