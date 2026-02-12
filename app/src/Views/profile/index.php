<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>


<h1>My Profile</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/profile" class="mt-3" style="max-width: 520px;">
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control"
               type="text"
               name="name"
               value="<?= htmlspecialchars($user->name) ?>"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control"
               type="email"
               name="email"
               value="<?= htmlspecialchars($user->email) ?>"
               required>
    </div>

    <button class="btn btn-primary" type="submit">Save changes</button>
</form>
<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
