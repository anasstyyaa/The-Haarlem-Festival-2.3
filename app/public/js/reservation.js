document.addEventListener('DOMContentLoaded', () => {
  const startInput = document.getElementById('start_time');
  const endInput   = document.getElementById('end_time');
  const priceEl    = document.getElementById('totalPrice');

  if (!startInput || !endInput || !priceEl) return;

  function parseDate(value) {
    const normalized = String(value).trim().replace(' ', 'T');
    const d = new Date(normalized);
    return isNaN(d.getTime()) ? null : d;
  }

  function updatePrice() {
    if (!startInput.value || !endInput.value) {
      priceEl.textContent = '€0.00';
      return;
    }

    const start = parseDate(startInput.value);
    const end   = parseDate(endInput.value);

    if (!start || !end || end <= start) {
      priceEl.textContent = '€0.00';
      return;
    }

    const hours = (end - start) / (1000 * 60 * 60);
    const rate  = Number(window.PRICE_PER_HOUR ?? 0);
    const total = hours * rate;

    priceEl.textContent = '€' + total.toFixed(2);
  }

  startInput.addEventListener('input', updatePrice);
  endInput.addEventListener('input', updatePrice);
  startInput.addEventListener('change', updatePrice);
  endInput.addEventListener('change', updatePrice);

  updatePrice();
});
