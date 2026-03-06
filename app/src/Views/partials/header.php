<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Haarlem Festival</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <span>🔍</span>
    <span>🌐</span>
    <a href="/personal-program" class="icon-link" title="Personal program">👤</a>

    <?php if (!isset($_SESSION['user'])): ?>
      <a href="/login" class="login-btn">Login</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user'])): ?>
      <a href="/logout" class="login-btn">Logout</a>
    <?php endif; ?>

    
    
  </div>
</header>

<div class="container">
