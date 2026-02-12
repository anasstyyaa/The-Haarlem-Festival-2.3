document.addEventListener('DOMContentLoaded', () => {
  const { isLoggedIn, userName, userRole } = window.APP_CONTEXT;

  const loginStatusEl = document.getElementById('loginStatus');
  const statusBadge = document.getElementById('statusBadge');
  const tipBox = document.getElementById('tipBox');

  // login status
  if (isLoggedIn) {
    loginStatusEl.textContent = `Logged in as ${userName} (${userRole})`;
    statusBadge.textContent = userRole === 'admin' ? 'Admin mode' : 'User mode';
  } else {
    loginStatusEl.textContent = 'Not logged in';
    statusBadge.textContent = 'Guest mode';
  }

  // clock with hours/minutes/seconds
  function updateClock() {
    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const clock = document.getElementById('clock');
    if (clock) {
      clock.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    }
  }
  updateClock();
  setInterval(updateClock, 1000);

  // rotate between 4 tips/facts
  const tips = [
    "Evenings and weekends are the busiest. Book early to secure high-performance PCs.",
    "You can cancel bookings from 'My Reservations'.",
    "Some pcs may temporarily be unavailable incase of maintenance.",
    "Each pc has a different price depending on its specs."
  ];
  let i = 0;
  function rotateTip() {
    tipBox.textContent = tips[i % tips.length];
    i++;
  }
  rotateTip();
  setInterval(rotateTip, 5000);
});
