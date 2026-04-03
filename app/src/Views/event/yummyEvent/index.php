<?php 
$bodyClass = 'yummy-page'; 
require __DIR__ . '/../../partials/header.php';
?>

<style> 
/* HERO IMAGE */
/* 1. Ensure the parent is the reference point */
.section9 {
    position: relative;
    height: 75vh;
    overflow: hidden; /* Clips image edges */
    display: flex;
    flex-direction: column;
    justify-content: center;
    z-index: 1; /* Creates a stacking context */

    padding-left: 10%; /* Adjust this percentage to find your perfect 1/3 spot */
    padding-right: 8%;
    
    color: white;
    overflow: hidden;
}

/* 2. Force the image to the bottom layer */
.section9 img {
    position: absolute !important;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
    z-index: -2 !important; /* Move behind EVERYTHING */
}

/* 3. Place the overlay between the image and text */
.section9::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(60, 15, 0, 0.7), rgba(60, 15, 0, 0.2));
    z-index: -1 !important; 
}

/* 4. Ensure text/buttons stay on top */
.section9 > * {
    position: relative;
    z-index: 2; 
}

/* Hero text & buttons */
.section9 h1, .section1 p, .section1 a {
  position: relative;
  z-index: 1; /* on top of image */
}

.section9 h1 { font-size: 4rem; margin-bottom: 10px; }
.section9 p { max-width: 600px; margin-bottom: 20px; }

.section9 a:first-of-type {
  background: #f7c9bc;
  color: #4b1608;
}
.section9 a:last-of-type {
  border: 1px solid #fff;
  color: #fff;
}
</style>

<div style="background-color: #3a0d00; min-height: 100vh; color: #f4d9c6;">
    
    <!-- heading & description section -->
    <?php foreach ($vm->getSections() as $section => $elements): ?>
        <div class="section<?= htmlspecialchars($section) ?>">
            
            <?php foreach ($elements as $element): ?>
                <?= $element->render(); ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="container py-5">
        <section class="restaurant-list">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold text-white">Discover Restaurants</h2>
                <span class="text-white-50"><?= count($restaurants) ?> results found</span>
            </div>

            <div class="row g-4">
                <?php if (empty($restaurants)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-white-50">No restaurants are currently listed for this event.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 restaurant-card border-0 shadow-lg" style="background-color: #4a1608; color: white; border-radius: 15px; overflow: hidden;">
                                <div class="ratio ratio-16x9">
                                    <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($restaurant->getName()) ?>"
                                         style="object-fit: cover;">
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge" style="background-color: rgba(244, 217, 198, 0.2); color: #f4d9c6; border: 1px solid #f4d9c6;">
                                            <?= htmlspecialchars($restaurant->getCuisine()) ?>
                                        </span>
                                    </div>

                                    <h5 class="card-title fw-bold mb-1" style="color: #f4d9c6;">
                                        <?= htmlspecialchars($restaurant->getName()) ?>
                                    </h5>
                                    
                                    <p class="small mb-3" style="color: #f5c5ba;">
                                        <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($restaurant->getLocation()) ?>
                                    </p>

                                    <p class="card-text mb-4" style="font-size: 0.9rem; opacity: 0.9;">
                                        <?= htmlspecialchars($restaurant->getDescription()) ?>
                                    </p>

                                    <div class="mt-auto">
                                        <a href="/yummy/restaurant/<?= $restaurant->getId() ?>" class="more-btn">
                                            View Restaurant 
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>