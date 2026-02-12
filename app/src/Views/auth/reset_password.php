<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1>Reset Password</h1>

<input type="hidden" id="token" value="<?= htmlspecialchars($token ?? '') ?>">

<div class="mb-3">
    <label class="form-label">New password</label>
    <input id="pw" class="form-control" type="password" placeholder="At least 6 characters">
</div>

<button id="btn" class="btn btn-success">Update password</button>

<div id="msg" class="mt-3"></div>

<script>
document.getElementById('btn').addEventListener('click', async () => {
  const token = document.getElementById('token').value;
  const password = document.getElementById('pw').value;
  const msg = document.getElementById('msg');

  msg.textContent = '';

  const res = await fetch('/api/reset-password', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ token, password })
  });

  const data = await res.json();
  msg.textContent = data.message || 'Done.';
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
