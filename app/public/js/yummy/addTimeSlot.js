function addTimeSlot() {
    const container = document.getElementById('time-slots-container');
    const newDiv = document.createElement('div');
    newDiv.className = 'input-group mb-2';
    newDiv.innerHTML = `
        <input type="time" name="times[]" class="form-control" required>
        <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">×</button>
    `;
    container.appendChild(newDiv);
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('status') === 'error') {
        const sessionId = urlParams.get('session_id');
        const openModalType = urlParams.get('open_modal');
        let targetModalId = null;

        if (sessionId) {
            targetModalId = 'editSessionModal' + sessionId;
        } else if (openModalType === 'add') {
            targetModalId = 'addSessionModal';
        }

        if (targetModalId) {
            const modalElement = document.getElementById(targetModalId);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
    }
});