<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1>Forgot Password</h1>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input id="email" class="form-control" type="email" placeholder="you@example.com">
</div>

<button id="btn" class="btn btn-primary">Send reset link</button>

<div id="msg" class="mt-3"></div>
<div id="linkBox" class="mt-2"></div>

<script>
document.getElementById('btn').addEventListener('click', async () => {
  const email = document.getElementById('email').value.trim();
  const msg = document.getElementById('msg');
  const linkBox = document.getElementById('linkBox');

  msg.textContent = '';
  linkBox.textContent = '';

  const res = await fetch('/api/forgot-password', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email })
  });

  const data = await res.json();

  msg.textContent = data.message || 'Done.';

  // For demo/testing: show link if returned
  if (data.reset_link) {
    linkBox.innerHTML = `<a href="${data.reset_link}">Open reset link (demo)</a>`;
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
