<?php
/** @var \App\Models\ExtraKidsEventModel|null $event */
$isEdit = isset($event) && $event && $event->getId();
?>
<?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $event->getId() ?>">
<?php endif; ?>

<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<h2 style="text-align:center;">
    <?= $isEdit ? 'Edit Extra Kids Event' : 'Create Extra Kids Event' ?>
</h2>

<form method="POST" action="/admin/extrakids/save" 
      enctype="multipart/form-data"
      style="width:350px;margin:0 auto;">

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $event->getId() ?>">
    <?php endif; ?>

    <label>Name:</label>
    <input type="text" name="name" required
        value="<?= $isEdit ? htmlspecialchars($event->getName()) : '' ?>">

    <br><br>

    <label>Description:</label>
    <textarea name="description" rows="4" style="width:100%;"><?= 
        $isEdit ? htmlspecialchars($event->getDescription()) : '' 
    ?></textarea>

    <br><br>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*">

    <?php if ($isEdit && $event->getImageUrl()): ?>
        <br><br>
        <p>Current Image:</p>
        <img src="<?= htmlspecialchars($event->getImageUrl()) ?>" 
             style="max-width:100%; height:auto;">
    <?php endif; ?>

    <br><br>

    <button type="submit">
        <?= $isEdit ? 'Update' : 'Create' ?>
    </button>
</form>

<div style="text-align:center;margin-top:20px;">
    <a href="/admin/kidsPage">Back</a>
</div>