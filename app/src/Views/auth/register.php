<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="auth-card">
  <h1>Register</h1>

  <?php if (!empty($error)) : ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="/register">
    <input name="email" placeholder="Email" required>
    <input name="userName" placeholder="Username" required>
    <input name="fullName" placeholder="Full Name" required>
    <input name="phoneNumber" placeholder="Phone Number" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>

  <a class="auth-link" href="/login">Already have an account? Login</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
