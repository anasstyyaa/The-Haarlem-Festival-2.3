<?php 
use App\ViewModels\KidsEventViewModel;
use App\ViewModels\PageElementViewModel;
use App\ViewModels\ExtraKidsEventViewModel;
/** @var KidsEventViewModel $vmKids */
/** @var PageElementViewModel $vm */
/** @var ExtraKidsEventViewModel $extraViewModel */

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



        /* ===== HERO / PAGE TITLE ===== */

.kids-events h1 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 40px;
    letter-spacing: 1px;
}

/* ===== GRID ===== */
.kids-events {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.kids-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    width: 100%;
}

/* ===== CARD ===== */
.kids-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
}

.kids-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

/* IMAGE */
.kids-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

/* CONTENT */
.kids-card h3 {
    font-size: 1.4rem;
    margin: 15px;
}

.kids-card p {
    font-size: 0.95rem;
    margin: 0 15px 20px;
    color: #555;
    line-height: 1.5;
}

/* BUTTON */
.kids-card a {
    margin: 0 15px 20px;
    padding: 10px;
    text-align: center;
    background: #ff7a18;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s ease;
}

.kids-card a:hover {
    background: #e56710;
}.section3 {
    display: flex;
    flex-wrap: wrap;              
    align-items: center;
    justify-content: space-between;

    background-color: #FEDBCE;
    color: #4b1608; 

    padding: 50px;
    gap: 20px;
}

/* TITLE (always first element) */
.section3 > :first-child {
    width: 100%;                  
    color: #8E3D18;
    font-size: 1.8rem;
    margin-bottom: -130px;
}

/* TEXT */
.section3 p {
    flex: 1;
    font-size: 1rem;
    line-height: 1.5;
    margin: 0;
}

/* IMAGE */
.section3 img {
    width: 500px;
    max-width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px;
}
.section2 {
    background-color: #4b1608;
    padding-bottom: 50px;
    padding-top: 50px;
    padding-left: 100px;
    display: grid;
    grid-template-columns: auto auto auto;
    align-items: center;
    gap: 20px;
}

/* left group */
.section2 p:nth-child(1) { grid-column: 1; grid-row: 1; }
.section2 p:nth-child(2) { grid-column: 1; grid-row: 2; }

/* image */
.section2 img { 
    grid-column: 2; 
    grid-row: 1 / span 2;
    width: 600px;
    height: auto;
    border-radius: 10px;
}

/* right group */
.section2 p:nth-child(4) { grid-column: 3; grid-row: 1; }
.section2 p:nth-child(5) { grid-column: 3; grid-row: 2; }

.section2 p {
    background-color: #FEC8C1;
    padding: 15px;
    border-radius: 10px;
    margin: 0;
    max-width: 220px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
<section class="kids-events">
    <h1>Extra Kids Events</h1>

    <div class="kids-grid">
        <?php foreach ($extraViewModel->events as $event): ?>
            <div class="kids-card">
                <?php if ($event['image']): ?>
                    <img src="<?= htmlspecialchars($event['image']) ?>">
                <?php endif; ?>

                <h3><?= htmlspecialchars($event['name']) ?></h3>


                <a href="/extrakids/<?= $event['id'] ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
    <h2 style="text-align:center;">Kids Events</h2>
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Type</th>
                <th>Location</th>
                <th>Spots Left</th>
                <th>Action</th>
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
                    <td>
                         <form method="POST" action="/addTicket">
            <input type="hidden" name="event_id" value="<?= $event->getId() ?>">
            <input type="hidden" name="event_type" value="kids">

            <input 
                type="number" 
                name="number_of_people" 
                value="1" 
                min="1" 
                max="<?= $event->getLimit() ?>" 
                style="width:50px;"
            >

            <?php if ($event->getLimit() > 0): ?>
                <button type="submit">Add Ticket</button>
            <?php else: ?>
                <button type="button" disabled>Sold Out</button>
            <?php endif; ?>
        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
