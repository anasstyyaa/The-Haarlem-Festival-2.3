<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="auth-card">
  <h1>Reset Password</h1>

  <?php if (!empty($error)) : ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (!empty($success)) : ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <?php if (!empty($errors) && is_array($errors)) : ?>
    <?php foreach ($errors as $msg) : ?>
      <p class="error"><?= htmlspecialchars($msg) ?></p>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (empty($success)) : ?>
    <form method="POST" action="/resetPassword">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

      <input
        id="password"
        type="password"
        name="password"
        placeholder="New password"
        required
      >

      <input
        id="password_confirm"
        type="password"
        name="password_confirm"
        placeholder="Confirm new password"
        required
      >

      <button type="submit">Reset password</button>
    </form>

    <a class="auth-link" href="/login">Back to login</a>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>