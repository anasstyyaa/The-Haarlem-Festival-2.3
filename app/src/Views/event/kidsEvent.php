<?php 
use App\ViewModels\KidsEventViewModel;
use App\ViewModels\PageElementViewModel;
/** @var KidsEventViewModel $vmKids */
/** @var PageElementViewModel $vm */

?>
<?php require __DIR__ . '/../partials/header.php'; ?>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
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
</head>
<body>
    <?php foreach ($vm->getSections() as $section => $elements): ?>
    
    <div class="section<?= htmlspecialchars($section) ?>">
        
        <?php foreach ($elements as $element): ?>
            <?= $element->render(); ?>
        <?php endforeach; ?>

    </div>

<?php endforeach; ?>
    <h2 style="text-align:center;">Kids Events</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vmKids->kidsEvents as $event): ?>
 
                <tr>
                    <td><?= htmlspecialchars($event->getId()) ?></td>
                    <td><?= htmlspecialchars($event->getDay()) ?></td>
                    <td><?= htmlspecialchars($event->getStartTime()) ?></td>
                    <td><?= htmlspecialchars($event->getEndTime()) ?></td>
                    <td>
                        <form method="POST" action="/addTicket">
                           
    <input type="hidden" name="event_id" value="<?= $event->getId() ?>">
       <input type="hidden" name="event_type" value="kids">
    <input type="number" name="number_of_people" value="1" min="1" style="width:50px;">


                            <button type="submit">Add Ticket</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
