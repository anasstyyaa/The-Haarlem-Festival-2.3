<link rel="stylesheet" href="/assets/css/main.css">
<?php require __DIR__. '/../partials/header.php'; ?>
<div class="auth-card">
  <h1>Login</h1>
  <?php if (!empty($error)) : ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
    <form method="POST" action="/login">
        <input name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <a class="auth-link" href="/register">Create account</a>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
