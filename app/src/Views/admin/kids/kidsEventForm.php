<?php
/** @var \App\Models\KidsEventModel|null $event */
$isEdit = $event && $event->getId();
?>

<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<h2 style="text-align:center;">
    <?= $isEdit ? 'Edit Kids Event' : 'Create Kids Event' ?>
</h2>

<form method="POST" action="/admin/kids-events/save" style="width:300px;margin:0 auto;">

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $event->getId() ?>">
    <?php endif; ?>

    <label>Day:</label>
    <input type="text" name="day" required
        value="<?= $isEdit ? htmlspecialchars($event->getDay()) : '' ?>">

    <br><br>

    <label>Start Time:</label>
    <input type="time" name="startTime" required
        value="<?= $isEdit ? htmlspecialchars($event->getStartTime()) : '' ?>">

    <br><br>

    <label>End Time:</label>
    <input type="time" name="endTime" required
        value="<?= $isEdit ? htmlspecialchars($event->getEndTime()) : '' ?>">

    <br><br>

    <button type="submit">
        <?= $isEdit ? 'Update' : 'Create' ?>
    </button>
</form>

<div style="text-align:center;margin-top:20px;">
    <a href="/admin/kidsPage">Back</a>
</div>