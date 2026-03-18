<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="container">
  <div class="auth-card">

    <h1>Reset Password</h1>

    <?php if (!empty($error)) : ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/resetPassword" class="auth-form">

      <input 
        type="hidden"
        name="token"
        value="<?= htmlspecialchars($token ?? '') ?>"
      >

      <div class="form-group">
        <label for="password">New Password</label>
        <input 
          id="password"
          name="password"
          type="password"
          placeholder="Enter new password"
          required
        >
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirm Password</label>
        <input 
          id="password_confirm"
          name="password_confirm"
          type="password"
          placeholder="Confirm password"
          required
        >
      </div>

      <button type="submit">Reset Password</button>

    </form>

    <a class="auth-link" href="/login">Back to Login</a>

  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>