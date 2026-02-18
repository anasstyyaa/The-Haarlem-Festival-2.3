<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --brand:#D89A3D;
      --brand-hover:#C8852F;
    }
    .btn-brand{
      background:var(--brand);
      border-color:var(--brand);
      color:#fff;
    }
    .btn-brand:hover,
    .btn-brand:focus{
      background:var(--brand-hover);
      border-color:var(--brand-hover);
      color:#fff;
    }
    .brand-line{
      height:4px;
      width:70px;
      background:var(--brand);
      border-radius:10px;
      margin:10px auto 0 auto;
    }
    .card{
      border-radius:18px;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">

        <div class="card shadow-sm border-0">
          <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
              <h1 class="h3 fw-bold mb-2">Forgot your password?</h1>
              <div class="brand-line"></div>
              <p class="text-muted mt-3 mb-0">
                Enter your email and we’ll send you a reset link.
              </p>
            </div>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger rounded-3" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
              <div class="alert alert-success rounded-3" role="alert">
                <?= $success /* success contains HTML link during testing */ ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="/api/forgot-password" class="needs-validation" novalidate>
              <div class="mb-3">
                <label class="form-label fw-semibold">Email address</label>
                <input
                  type="email"
                  name="email"
                  class="form-control form-control-lg rounded-3"
                  placeholder="you@example.com"
                  required
                >
                <div class="invalid-feedback">Please enter your email.</div>
              </div>

              <button type="submit" class="btn btn-brand btn-lg w-100 rounded-3">
                Send reset link
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
