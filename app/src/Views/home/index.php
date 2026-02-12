<?php
$bodyClass = 'bg-dark text-white';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container py-4">

  <div class="home-hero rounded-4 p-4 p-md-5 mb-4 text-white">
    <div class="row align-items-center g-4">
      <div class="col-md-8">
        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-pill home-pill">
          <span class="small">Internet Café</span>
          <span class="small home-muted">•</span>
          <span class="small" id="statusBadge">Loading…</span>
        </div>

        <h1 class="display-6 fw-bold mb-2">Reserve a PC in seconds.</h1>
        <p class="lead home-muted mb-4">
          Book your PC on any day and time you like!
        </p>

        <div class="d-flex flex-wrap gap-2">
          <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a class="btn btn-warning" href="/admin/pcs">Admin: Manage PCs</a>
            <a class="btn btn-warning" href="/admin/reservations">Admin: Reservations</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-md-4">
        <div class="home-card rounded-4 p-3 shadow-sm">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="small home-muted">Login status</div>
              <div id="loginStatus" class="fw-semibold">—</div>
            </div>
          </div>

          <hr style="border-color: rgba(255,255,255,.12);">

          <div class="small home-muted">Local time</div>
          <div class="fs-5 fw-semibold" id="clock">--:--:--</div>

          <div class="small home-muted mt-3">Tip</div>
          <div class="small" id="tipBox">—</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="home-card rounded-4 p-3 h-100 shadow-sm">
        <h5 class="mb-2">Availability</h5>
        <p class="home-muted mb-3">View all available PCs that can be reserved.</p>
        <a class="btn btn-sm btn-outline-light" href="/pcs">View PCs</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="home-card rounded-4 p-3 h-100 shadow-sm">
        <h5 class="mb-2">Reservations</h5>
        <p class="home-muted mb-3">Create reservations by selecting a start and end time.</p>
        <a class="btn btn-sm btn-outline-light" href="/my-reservations">My Reservations</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="home-card rounded-4 p-3 h-100 shadow-sm">
        <h5 class="mb-2">Security</h5>
        <p class="home-muted mb-3">Manage your account details and keep your profile information up to date.</p>
        <?php if (empty($_SESSION['user_id'])): ?>
          <a class="btn btn-sm btn-outline-light" href="/register">Create account</a>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-light" href="/profile">Update profile</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<script>
  // PHP → JS bridge
  window.APP_CONTEXT = {
    isLoggedIn: <?= json_encode(!empty($_SESSION['user_id'])) ?>,
    userName: <?= json_encode($_SESSION['user_name'] ?? null) ?>,
    userRole: <?= json_encode($_SESSION['user_role'] ?? null) ?>
  };
</script>

<script src="/js/home.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
