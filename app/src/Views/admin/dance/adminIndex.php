<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-music-note-beamed me-2"></i>Manage Dance</h2>
</div>

<!-- Success / status messages -->
<?php if (isset($_GET['status']) && $_GET['status'] === 'created'): ?>
    <div class="alert alert-success">Artist created successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
    <div class="alert alert-success">Artist updated successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
    <div class="alert alert-success">Artist deleted successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'event-created'): ?>
    <div class="alert alert-success">Dance event created successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'event-updated'): ?>
    <div class="alert alert-success">Dance event updated successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'event-deleted'): ?>
    <div class="alert alert-success">Dance event deleted successfully.</div>
<?php endif; ?>

<!-- Artists Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Artists</h4>
    <a href="/admin/dance/create" class="btn btn-primary">Add New Artist</a>
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
                        <td colspan="4" class="text-center py-4 text-muted">No dance artists found in database.</td>
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
                                    <img
                                        src="<?= htmlspecialchars($artist->getImageUrl()) ?>"
                                        alt="Artist"
                                        class="img-thumbnail"
                                        style="height: 60px; width: 60px; object-fit: cover;"
                                    >
                                <?php else: ?>
                                    <span class="text-muted small">No image set</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/dance/edit/<?= $artist->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <a href="/admin/dance/delete/<?= $artist->getId() ?>"
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
    <h4 class="mb-0">Dance Events</h4>
    <a href="/admin/dance/events/create" class="btn btn-primary">Add New Event</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Venue</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Price</th>
                    <th>Capacity</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No dance events found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event->getDisplayTitle() ?: 'No title') ?></td>
                            <td><?= htmlspecialchars($event->getVenueName() ?: 'Unknown Venue') ?></td>
                            <td><?= htmlspecialchars(date('d M Y H:i', strtotime($event->getStartDateTime()))) ?></td>
                            <td>
                                <?= $event->getEndDateTime()
                                    ? htmlspecialchars(date('d M Y H:i', strtotime($event->getEndDateTime())))
                                    : '-' ?>
                            </td>
                            <td>€<?= htmlspecialchars(number_format($event->getPrice(), 2)) ?></td>
                            <td><?= htmlspecialchars((string)$event->getCapacity()) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- use getId(), not getDanceEventID() -->
                                    <a href="/admin/dance/events/edit/<?= $event->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <a href="/admin/dance/events/delete/<?= $event->getId() ?>"
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