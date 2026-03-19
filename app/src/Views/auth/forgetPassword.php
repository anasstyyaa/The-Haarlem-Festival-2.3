<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="container">
  <div class="auth-card">

    <h1>Forgot Password</h1>

    <?php if (!empty($error)) : ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

   <?php if (!empty($success)) : ?>
  <div class="success-box">
    <h2>✅ Email Sent!</h2>
    <p><?= htmlspecialchars($success) ?></p>
    <p class="small-text">
      Please check your inbox and spam folder.
    </p>
  </div>
<?php endif; ?>

    <form method="POST" action="/forgetPassword" class="auth-form">

      <div class="form-group">
        <label for="email">Email Address</label>
        <input 
          id="email"
          name="email"
          type="email"
          placeholder="example@gmail.com"
          value="<?= htmlspecialchars($email ?? '') ?>"
          required
        >
      </div>

      <button type="submit">Send Reset Link</button>

    </form>

    <a class="auth-link" href="/login">Back to Login</a>

  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>