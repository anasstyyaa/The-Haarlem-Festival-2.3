<?php
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/header.php';
?>

<div class="container">
  <div class="auth-card">
    <h1>Register</h1>

    <?php if (!empty($error)) : ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/register" enctype="multipart/form-data" class="register-form">
      
      <div class="form-grid">

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email"
                  placeholder="example@gmail.com" required>
          </div>

          <div class="form-group">
            <label for="userName">Username</label>
            <input id="userName" name="userName"
                  placeholder="john_doe" required>
          </div>

          <div class="form-group">
            <label for="fullName">Full Name</label>
            <input id="fullName" name="fullName"
                  placeholder="John Doe" required>
          </div>

          <div class="form-group">
            <label for="phoneNumber">Phone Number</label>
            <input id="phoneNumber" name="phoneNumber"
                  placeholder="+31 612345678" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                  placeholder="Password" required>
          </div>

          <div class="form-group">
            <label for="profilePicture">Profile Picture</label>
            <input id="profilePicture" type="file"
                  name="profilePicture" accept="image/*">
          </div>

        </div>
      
        <!-- reCAPTCHA v2 -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="captcha-wrapper">
          <div class="g-recaptcha" data-sitekey="6LfGDHEsAAAAAPBzZo6IgZovYM-uSGLWsBpCU-Di"></div>
        </div>
        <button type="submit">Register</button>
      </form>
      <a class="auth-link" href="/login">Already have an account? Login</a>
  </div>
</div>


<?php require __DIR__ . '/../partials/footer.php'; ?>
