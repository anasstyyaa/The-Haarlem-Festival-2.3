<?php
/** @var PageElementViewModel[] $elements */
$titleElement = array_shift($elements);
$chunks = array_chunk($elements, 4);
?>

<section class="home-events" id="festival-events">
    <div class="home-container">

        <h2>
            <?= $titleElement->render(); ?>
        </h2>

        <div class="event-feature-list">

            <?php foreach ($chunks as $index => $group): ?>

                <div class="event-feature-card <?= $index % 2 !== 0 ? 'reverse' : '' ?>">

                    <!-- IMAGE -->
                    <?= $group[0]->render() ?? '' ?>

                    <div class="event-feature-content">

                        <!-- TITLE -->
                        <?php if (isset($group[1])): ?>
                            <h3><?= $group[1]->render(); ?></h3>
                        <?php endif; ?>

                        <!-- TEXT -->
                        <?php if (isset($group[2])): ?>
                            <p><?= $group[2]->render(); ?></p>
                        <?php endif; ?>

                        <!-- BUTTON / LINK -->
                        <?php if (isset($group[3])): ?>
                            <?= $group[3]->render(); ?>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>
    </div>
</section>