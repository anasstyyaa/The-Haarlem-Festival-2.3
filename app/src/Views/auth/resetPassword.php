<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">

        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
              <h1 class="h3 fw-bold mb-2">Reset password</h1>
              <p class="text-muted mb-0">
                Create a new strong password for your account.
              </p>
            </div>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger rounded-3" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="/api/reset-password" class="needs-validation" novalidate>
              <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

              <div class="mb-3">
                <label class="form-label fw-semibold">New password</label>
                <input
                  type="password"
                  name="password"
                  class="form-control form-control-lg rounded-3"
                  placeholder="At least 8 characters"
                  minlength="8"
                  required
                >
                <div class="invalid-feedback">Password must be at least 8 characters.</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Confirm password</label>
                <input
                  type="password"
                  name="password_confirm"
                  class="form-control form-control-lg rounded-3"
                  placeholder="Repeat your password"
                  minlength="8"
                  required
                >
                <div class="invalid-feedback">Please confirm your password.</div>
              </div>

              <button type="submit" class="btn btn-success btn-lg w-100 rounded-3">
                Update password
              </button>
            </form>

            <div class="text-center mt-4">
              <a href="/login" class="text-decoration-none">← Back to login</a>
            </div>

          </div>
        </div>

        <p class="text-center text-muted small mt-4 mb-0">
          Haarlem Festival • Customer Portal
        </p>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (() => {
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
</body>
</html>
