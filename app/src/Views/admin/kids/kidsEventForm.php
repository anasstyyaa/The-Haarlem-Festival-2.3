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
    <select name="day">
       <option value="Monday">Monday</option>
       <option value="Tuesday">Tuesday</option>
       <option value="Wednesday">Wednesday</option>
       <option value="Thursday">Thursday</option>
       <option value="Friday">Friday</option>
       <option value="Saturday">Saturday</option>
       <option value="Sunday">Sunday</option>
     </select>

    <br><br>

    <label>Start Time:</label>
    <input type="time" name="startTime" required
        value="<?= $isEdit ? htmlspecialchars($event->getStartTime()) : '' ?>">

    <br><br>

    <label>End Time:</label>
    <input type="time" name="endTime" required
        value="<?= $isEdit ? htmlspecialchars($event->getEndTime()) : '' ?>">

        <br><br>

   <label>Type:</label>
     <select name="type">
       <option value="Teylers Secret">Teylers Secret</option>
       <option value="Lorentz Formula">Lorentz Formula</option>
     </select>

       <br><br>

    <label>Location:</label>
    <input type="text" name="location" required
       value="<?= $isEdit ? htmlspecialchars($event->getLocation()) : '' ?>">

    <br><br>

    <label>Limit:</label>
      <input type="number" name="limit" required min="0"
        value="<?= $isEdit ? $event->getLimit() : 0 ?>">

    <br><br>

    <button type="submit">
        <?= $isEdit ? 'Update' : 'Create' ?>
    </button>
</form>

<div style="text-align:center;margin-top:20px;">
    <a href="/admin/kidsPage">Back</a>
</div>