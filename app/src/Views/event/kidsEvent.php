<?php 
use App\ViewModels\KidsEventViewModel;
use App\ViewModels\PageElementViewModel;
use App\ViewModels\ExtraKidsEventViewModel;
/** @var KidsEventViewModel $vmKids */
/** @var PageElementViewModel $vm */
/** @var ExtraKidsEventViewModel $extraViewModel */

?>
<?php require __DIR__ . '/../partials/header.php'; ?>
   
</head>
<body>
    <?php foreach ($vm->getSections() as $section => $elements): ?>
    
    <div class="section<?= htmlspecialchars($section) ?>">
        
        <?php foreach ($elements as $element): ?>
            <?= $element->render(); ?>
        <?php endforeach; ?>

    </div>

<?php endforeach; ?>

<div class="kids-background">

<div class="container py-5">


<!-- EXTRA KIDS EVENTS -->
<section class="mb-5">
    <h1 class="text-center mb-4">Extra Kids Events</h1>

    <div class="row g-4">
        <?php foreach ($extraViewModel->events as $event): ?>
            <div class="col-md-4">
                <div class="card card-custom shadow-sm h-100 kids-card">

                    <?php if ($event['image']): ?>
                        <img src="<?= htmlspecialchars($event['image']) ?>" alt="event image">
                    <?php endif; ?>

                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>

                        <a href="/extrakids/<?= $event['id'] ?>" class="btn btn-sm btn-dark mt-2">
                            View Details
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<!-- KIDS EVENTS TABLE -->
<div class="card card-custom shadow-sm">
    <div class="card-header bg-dark text-white text-center">
        <h4 class="mb-0">Kids Events</h4>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Day</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Spots Left</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($vmKids->kidsEvents as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event->getDay()) ?></td>
                        <td><?= htmlspecialchars(date('H:i', strtotime($event->getStartTime()))) ?></td>
                        <td><?= htmlspecialchars(date('H:i', strtotime($event->getEndTime()))) ?></td>
                        <td><?= htmlspecialchars($event->getType()) ?></td>
                        <td><?= htmlspecialchars($event->getLocation()) ?></td>
                        <td><?= htmlspecialchars($event->getLimit()) ?></td>

                        <td class="text-center">
                            <form method="POST" action="/addTicket" class="d-flex justify-content-center gap-2">

                                <input type="hidden" name="event_id" value="<?= $event->getId() ?>">
                                <input type="hidden" name="event_type" value="kids">

                                <input 
                                    type="number" 
                                    name="number_of_people" 
                                    value="1" 
                                    min="1" 
                                    max="<?= $event->getLimit() ?>" 
                                    class="form-control form-control-sm"
                                    style="width:70px;"
                                >

                                <?php if ($event->getLimit() > 0): ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        Add
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-secondary" disabled>
                                        Sold Out
                                    </button>
                                <?php endif; ?>

                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


</div>
</div>


</body>
</html>
