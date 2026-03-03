<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="auth-card">
  <h1>Forgot Password</h1>

  <?php if (!empty($error)) : ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (!empty($success)) : ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <form method="POST" action="/forgetPassword">
    <input
      name="email"
      type="email"
      placeholder="Enter your Email"
      value="<?= htmlspecialchars($email ?? '') ?>"
      required
    >
    <button type="submit">Send reset link</button>
  </form>

  <a class="auth-link" href="/login">Back to login</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>