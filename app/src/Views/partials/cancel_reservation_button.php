<?php
/**
 * Expected variables:
 * @var object $reservation   ReservationModel
 */

if ($reservation->status !== 'booked') {
    echo '<span class="text-muted">—</span>';
    return;
}
?>

<form method="post"
      action="/reservations/cancel/<?= (int)$reservation->id ?>"
      class="d-inline"
      onsubmit="return confirm('Cancel this reservation?');">
    <button type="submit" class="btn btn-danger btn-sm">
        Cancel
    </button>
</form>
