<?php
/** @var App\Models\PersonalProgram $program */
$program = $_SESSION['program'] ?? new App\Models\PersonalProgram();
?>

<?php require __DIR__ . '/../partials/header.php'; ?>

<style>
    table {
        border-collapse: collapse;
        width: 70%;
        margin: 20px auto;
    }
    th, td {
        border: 1px solid #aaa;
        padding: 8px 12px;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    button {
        padding: 5px 10px;
        cursor: pointer;
    }
</style>

<h2 style="text-align:center;">My Personal Program Tickets</h2>

<?php if (count($program->getTickets()) === 0): ?>
    <p style="text-align:center;">No tickets added yet.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Event Type</th>
            <th>Sub Event ID</th>
            <th>Number of People</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($program->getTickets() as $index => $ticket): ?>
            <?php $event = $ticket->getEvent(); ?>
            <tr>
    <td><?= $index + 1 ?></td>
    <td><?= htmlspecialchars($event->getEventType()->name) ?></td>
    <td><?= htmlspecialchars($event->getSubEventId()) ?></td>
    <td><?= htmlspecialchars($ticket->getNumberOfPeople()) ?></td>
    <td>
        <form method="POST" action="/removeTicket">
            <input type="hidden" name="ticket_id" value="<?= $ticket->getProgramItemId() ?>">
            <button type="submit">Remove</button>
        </form>
    </td>
</tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
