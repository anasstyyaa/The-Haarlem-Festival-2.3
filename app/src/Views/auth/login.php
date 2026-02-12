<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>



<h1>Login</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="post" action="/login" class="mt-3" novalidate>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input required type="email" name="email" id="email" class="form-control">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input required type="password" name="password" id="password" class="form-control">
    </div>
<a href="/forgot-password">Forgot your password?</a>

    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>

