<?php 
use App\ViewModels\KidsEventViewModel;
use App\ViewModels\PageElementViewModel;
use App\Models\TextModel;
use App\Models\ImageModel;
/** @var KidsEventViewModel $vmKids */
/** @var PageElementViewModel $vm */
use App\ViewModels\ExtraKidsEventViewModel;
/** @var ExtraKidsEventViewModel $extraViewModel */
?>
<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<style>
table {
    border-collapse: collapse;
    width: 85%;
    margin: 30px auto;
}

th, td {
    border: 1px solid #aaa;
    padding: 10px;
    text-align: center;
}

th {
    background: #f2f2f2;
}

.actions{
    display:flex;
    justify-content:center;
    gap:8px;
}

.edit-btn{
    background:#4CAF50;
    color:white;
    border:none;
    padding:6px 12px;
}

.delete-btn{
    background:#e53935;
    color:white;
    border:none;
    padding:6px 12px;
}

.add-btn{
    margin:20px auto;
    display:block;
    padding:10px 20px;
}

.section-title{
    text-align:center;
    margin-top:40px;
}
td img {
    max-width: 150px;
    height: auto;
    display: block;
    margin: 0 auto;
}
</style>

</head>
<body>

<h1 style="text-align:center;">Admin Page Manager</h1>

<a href="/admin/sections/create">
    <button class="add-btn">Add Section</button>
</a>


<?php foreach ($vm->getSections() as $section => $elements): ?>

<h2 class="section-title">
    Section: <?= htmlspecialchars($section) ?>
</h2>

<table>
<thead>
<tr>
    <th>Element ID</th>
    <th>Type</th>
    <th>Preview</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($elements as $element): ?>

<tr>
<td><?= htmlspecialchars($element->getId()) ?></td>

<td><?= htmlspecialchars(get_class($element)) ?></td>

<td>
    <?= $element->render(); ?>
</td>

<td>
<div class="actions">
<?php if($element instanceof TextModel){ ?>
 <a href="/admin/elements/edit/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <?php } elseif($element instanceof ImageModel){?>
                                   <a href="/admin/elements/editImg/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a> <?php } ?>

<form method="POST" action="/admin/elements/delete"
onsubmit="return confirm('Delete this element?')">
<input type="hidden" name="id" value="<?= $element->getId() ?>">
<button class="delete-btn">Delete</button>
</form>

</div>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<div style="text-align:center;">
<form method="GET" action="/admin/elements/create">
<input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
<button>Add Element to Section</button>
</form>
</div>

<?php endforeach; ?>



<h2 style="text-align:center;">Kids Events</h2>
<div class="edit-btn">
   <a href="/admin/kids-events/create" class="edit-btn">
    Add Kids Event
</a>
</div>
<table>
<thead>
<tr>
<th>ID</th>
<th>Day</th>
<th>Start</th>
<th>End</th>
<th>Type</th>
<th>Location</th>
<th>Limit</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($vmKids->kidsEvents as $event): ?>

<tr>
<td><?= htmlspecialchars($event->getId()) ?></td>
<td><?= htmlspecialchars($event->getDay()) ?></td>
<td><?= htmlspecialchars($event->getStartTime()) ?></td>
<td><?= htmlspecialchars($event->getEndTime()) ?></td>
<td><?= htmlspecialchars($event->getType()) ?></td>
<td><?= htmlspecialchars($event->getLocation()) ?></td>
<td><?= htmlspecialchars($event->getLimit()) ?></td>
<td>
<div class="actions">

<a href="/admin/kids-events/edit/<?= $event->getId() ?>"  class="edit-btn">
Edit
</a>

<form method="POST" action="/admin/kids-events/delete"
onsubmit="return confirm('Delete this event?')">
<input type="hidden" name="id" value="<?= $event->getId() ?>">
<button class="delete-btn">Delete</button>
</form>

</div>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>
<h2 style="text-align:center;">Extra Kids Events</h2>

<div class="edit-btn">
   <a href="/admin/extrakids/create" class="edit-btn">
        Add Extra Kids Event
   </a>
</div>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Image</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($extraViewModel->events as $event): ?>

<tr>
<td><?= htmlspecialchars($event['id']) ?></td>
<td><?= htmlspecialchars($event['name']) ?></td>
<td><?= htmlspecialchars($event['description']) ?></td>

<td>
    <?php if (!empty($event['image'])): ?>
        <img src="<?= htmlspecialchars($event['image']) ?>" alt="event image">
    <?php else: ?>
        No image
    <?php endif; ?>
</td>

<td>
<div class="actions">

    <a href="/admin/extrakids/edit/<?= $event['id'] ?>" class="edit-btn">
        Edit
    </a>

    <form method="POST" action="/admin/extrakids/delete"
          onsubmit="return confirm('Delete this extra event?')">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        <button class="delete-btn">Delete</button>
    </form>

</div>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</body>
</html>