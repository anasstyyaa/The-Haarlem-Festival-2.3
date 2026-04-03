<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg border-0"
         role="alert"
         style="z-index: 9999; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg border-0"
         role="alert"
         style="z-index: 9999; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Haarlem Festival</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="<?= isset($bodyClass) ? htmlspecialchars($bodyClass) : '' ?>">

<header class="main-header">
  <link rel="stylesheet" href="/assets/css/dance.css">

  <div class="logo">Haarlem Festival</div>

  <nav class="nav-links">
    <a href="/" class="nav-item">Home</a>
    <a href="/jazz" class="nav-item">Jazz</a>
    <a href="/dance" class="nav-item">Dance</a>
    <a href="/yummy" class="nav-item">Yummy</a>
    <a href="/history" class="nav-item">History</a>
    <a href="/kidsEvent" class="nav-item">Kids</a>

    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'Admin'): ?>
      <a href="/admin/users" class="nav-item border-warning text-warning" style="border-width: 2px;">
        <i class="bi bi-shield-lock me-1"></i> CMS
      </a>
    <?php endif; ?>
  </nav>

  <div class="nav-icons">
    <a href="#" class="icon-link" title="Language">
        <i class="bi bi-globe"></i>
    </a>

    <a href="#" class="icon-link" title="Search">
        <i class="bi bi-search"></i>
    </a>
    
    <a href="/personalProgram" class="icon-link" title="Personal Program">
        <i class="bi bi-ticket-perforated"></i>
    </a>

    <a href="/profile" class="icon-link" title="Profile">
        <i class="bi bi-person-circle"></i>
    </a>

    <?php if (!isset($_SESSION['user'])): ?>
        <a href="/login" class="login-btn">Login</a>
    <?php else: ?>
        <a href="/logout" class="login-btn">Logout</a>
    <?php endif; ?>
</div>
</header>