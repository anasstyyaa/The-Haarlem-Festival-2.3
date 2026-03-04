<h1>History Tour Schedule</h1>

<h2>Select Day</h2>
<?php foreach (array_keys($byDay) as $day): ?>
    <a href="/history?day=<?= $day ?>"
       style="display:inline-block; margin:6px; padding:8px 14px; border-radius:16px; border:1px solid #ccc; text-decoration:none;">
        <?= date('D d', strtotime($day)) ?>
    </a>
<?php endforeach; ?>

<?php if ($selectedDay && isset($byDay[$selectedDay])): ?>
    <h2>Select Time</h2>

    <?php foreach (array_keys($byDay[$selectedDay]) as $time): ?>
        <a href="/history?day=<?= $selectedDay ?>&time=<?= $time ?>"
           style="display:inline-block; margin:6px; padding:8px 14px; border-radius:16px; border:1px solid #ccc; text-decoration:none;">
            <?= $time ?>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($selectedDay && $selectedTime && isset($byDay[$selectedDay][$selectedTime])): ?>
    <h2>Select Language</h2>

    <?php foreach ($byDay[$selectedDay][$selectedTime] as $lang): ?>
        <a href="/history?day=<?= $selectedDay ?>&time=<?= $selectedTime ?>&lang=<?= urlencode($lang) ?>"
           style="display:inline-block; margin:6px; padding:8px 14px; border-radius:16px; border:1px solid #ccc; text-decoration:none;">
            <?= htmlspecialchars($lang) ?>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($selectedSession)): ?>
    <h2>Selected Session</h2>
    <div style="border:1px solid #ccc; padding:10px; width:fit-content;">
        <strong>Date:</strong> <?= htmlspecialchars($selectedSession['slotDate']) ?><br>
        <strong>Time:</strong> <?= substr($selectedSession['startTime'], 0, 5) ?><br>
        <strong>Language:</strong> <?= htmlspecialchars($selectedSession['language']) ?><br>
    </div>
<?php endif; ?>

<?php if (!empty($stops)): ?>
    <h2>Tour Stops</h2>

    <?php foreach ($stops as $stop): ?>
        <div>
            <?= (int)$stop['stopOrder'] ?>.
            <?= htmlspecialchars($stop['venueName']) ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>