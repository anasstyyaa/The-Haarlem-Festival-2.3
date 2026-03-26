<?php
/** @var PageElementViewModel $elements */ 
$titleElement = array_shift($elements); 
$chunks = array_chunk($elements, 3);
?>

<div class="locations-flat">

    <h2 class="locations-title">
        <?= $titleElement->render(); ?>
    </h2>

    <div class="location-grid">
        <?php foreach ($chunks as $group): ?>
            <div class="location-item">

                <?= $group[0]->render(); ?> 
                <?= $group[1]->render(); ?> 
                <?= $group[2]->render(); ?> 

            </div>
        <?php endforeach; ?>
    </div>

</div>