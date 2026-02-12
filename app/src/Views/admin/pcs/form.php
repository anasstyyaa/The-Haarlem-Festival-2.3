<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../components/card_start.php'; ?>



<?php
$isEdit = isset($pc) && $pc !== null;
$title = $isEdit ? 'Edit PC' : 'Add PC';
$action = $isEdit ? '/admin/pcs/edit/' . (int)$pc->id : '/admin/pcs/create';
?>

<h1><?= htmlspecialchars($title) ?></h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($action) ?>" class="mt-3">

    <div class="mb-3">
        <label class="form-label">PC Name</label>
        <input class="form-control" name="name" required
               value="<?= htmlspecialchars($isEdit ? ($pc->name ?? '') : '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Specs</label>
        <textarea class="form-control" name="specs" rows="3"><?= htmlspecialchars($isEdit ? ($pc->specs ?? '') : '') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Price per hour</label>
        <input class="form-control" name="price_per_hour"
               value="<?= htmlspecialchars($isEdit ? (string)($pc->price_per_hour ?? '') : '') ?>">
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="/admin/pcs" class="btn btn-secondary">Back</a>
</form>
<?php require __DIR__ . '/../../components/card_end.php'; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
