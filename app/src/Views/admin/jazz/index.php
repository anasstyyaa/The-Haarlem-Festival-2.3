<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<?php
$artistMap = [];
foreach ($artists as $artist) {
    $artistMap[$artist->getId()] = $artist->getName();
}

$venueMap = [
    1 => 'Patronaat - Main Hall',
    2 => 'Patronaat - Second Hall',
    3 => 'Patronaat - Third Hall',
    4 => 'Grote Markt'
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-music-note-beamed me-2"></i>Manage Jazz</h2>
</div>

<!-- Artists Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Artists</h4>
    <a href="/admin/jazz/create" class="btn btn-primary">Add New Artist</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Artist</th>
                    <th>Short Description</th>
                    <th>Image</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($artists)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No artists found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($artist->getName()) ?></strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($artist->getShortDescription() ?? '') ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($artist->getImageUrl()): ?>
                                    <img src="<?= htmlspecialchars($artist->getImageUrl()) ?>" alt="Artist" class="img-thumbnail" style="height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted small">No image set</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/jazz/edit/<?= $artist->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <a href="/admin/jazz/delete/<?= $artist->getId() ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this artist?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Events Section -->
<div class="d-flex justify-content-between align-items-center mt-5 mb-3">
    <h4 class="mb-0">Jazz Events</h4>
    <a href="/admin/jazz/events/create" class="btn btn-primary">Add New Event</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Artist</th>
                    <th>Venue</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No jazz events found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($artistMap[$event->getArtistId()] ?? 'Unknown Artist') ?></td>
                            <td><?= htmlspecialchars($venueMap[$event->getJazzVenueId()] ?? 'Unknown Venue') ?></td>
                            <td><?= date('d M H:i', strtotime($event->getStartDateTime())) ?></td>
                            <td><?= date('H:i', strtotime($event->getEndDateTime())) ?></td>
                            <td>€<?= htmlspecialchars(number_format($event->getPrice(), 2)) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/jazz/events/edit/<?= $event->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="/admin/jazz/events/delete/<?= $event->getId() ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>