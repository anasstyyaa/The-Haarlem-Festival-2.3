<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>


<h1>Register</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/register" class="mt-3" novalidate>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input required type="text" name="name" id="name" class="form-control">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input required type="email" name="email" id="email" class="form-control">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input required type="password" name="password" id="password" class="form-control">
    </div>

    <div class="mb-3">
        <label for="password_repeat" class="form-label">Repeat Password</label>
        <input required type="password" name="password_repeat" id="password_repeat" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>

