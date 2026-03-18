function changeQty(inputId, change) {
    const input = document.getElementById(inputId);
    if (!input) return;

    let currentValue = parseInt(input.value) || 1;

    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 20;

    let newValue = currentValue + change;

    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;

    input.value = newValue;

    updateButtons(input);
}

function updateButtons(input) {
    const wrapper = input.closest('.ticket-quantity-controls');
    if (!wrapper) return;

    const minusBtn = wrapper.querySelector('.qty-btn:first-child');
    const plusBtn = wrapper.querySelector('.qty-btn:last-child');

    const value = parseInt(input.value);
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 20;

    if (minusBtn) minusBtn.disabled = value <= min;
    if (plusBtn) plusBtn.disabled = value >= max;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ticket-quantity-input').forEach(input => {
        updateButtons(input);
    });
});