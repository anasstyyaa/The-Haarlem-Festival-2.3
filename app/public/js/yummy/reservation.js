function initReservationForm(sessionsByDate) {
    const dateSelect = document.getElementById('dateSelect');
    const section = document.getElementById('timeSlotSection');
    const buttonContainer = document.getElementById('timeSlotButtons');
    const sessionInput = document.getElementById('selectedSessionId');
    const peopleInput = document.getElementById('peopleInput');
    const submitBtn = document.getElementById('submitBtn');

    if (!dateSelect) return;

    dateSelect.addEventListener('change', function() {
        const selectedDate = this.value;
        buttonContainer.innerHTML = '';
        sessionInput.value = '';
        section.classList.add('d-none');

        if (selectedDate && sessionsByDate[selectedDate]) {
            section.classList.remove('d-none');
            
            sessionsByDate[selectedDate].forEach(session => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-dark time-slot-btn flex-grow-1 py-2';
                
                const timeStr = session.startTime ? session.startTime.substring(0, 5) : "00:00";
                const slots = session.availableSlots ?? session.available_slots ?? 0;

                btn.innerHTML = `
                    <strong>${timeStr}</strong><br>
                    <small class="slot-text">${slots} left</small>
                `;
                
                if (slots <= 0) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'btn-light');
                    btn.classList.remove('btn-outline-dark');
                }

                btn.onclick = function() {
                    document.querySelectorAll('#timeSlotButtons .btn').forEach(b => {
                        b.classList.remove('btn-dark');
                        b.classList.add('btn-outline-dark');
                        const st = b.querySelector('.slot-text');
                        if(st) st.classList.replace('text-white-50', 'text-muted');
                    });

                    this.classList.remove('btn-outline-dark');
                    this.classList.add('btn-dark');
                    const selectedText = this.querySelector('.slot-text');
                    if(selectedText) selectedText.classList.replace('text-muted', 'text-white-50');
                    
                    sessionInput.value = session.id;

                    if (peopleInput) {
                        peopleInput.max = slots;
                        
                        if (parseInt(peopleInput.value) > slots) {
                            peopleInput.value = slots;
                        }
                    }
                };

                buttonContainer.appendChild(btn);
            });
        }
    });
}