<?php
// Right side of navbar: auth buttons or user greeting
?>

<?php if (!empty($_SESSION['user_id'])): ?>
    <span class="navbar-text me-3">
        Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>
        (<?= htmlspecialchars($_SESSION['user_role']) ?>)
    </span>
    <a class="btn btn-outline-light btn-sm" href="/logout">Logout</a>
<?php else: ?>
    <a class="btn btn-outline-light btn-sm me-2" href="/login">Login</a>
    <a class="btn btn-light btn-sm" href="/register">Register</a>
<?php endif; ?>
