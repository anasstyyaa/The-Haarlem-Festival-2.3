<?php 
$bodyClass = 'restaurant-detail'; 
require __DIR__ . '/../../partials/header.php';
?>

<div style="background-color: #3a0d00; min-height: 100vh; color: #f4d9c6;">

    <section class="jazz-hero">
        <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" alt="<?= htmlspecialchars($restaurant->getName()) ?>">
        <div class="hero-overlay">
            <a href="/yummy" class="jazz-back mb-3"><i class="bi bi-arrow-left"></i> Back to Restaurants</a>
            <h1><?= htmlspecialchars($restaurant->getName()) ?></h1>
            <p>
                <span class="badge" style="background-color: rgba(244, 217, 198, 0.2); color: #f4d9c6; border: 1px solid #f4d9c6;">
                    <?= htmlspecialchars($restaurant->getCuisine()) ?>
                </span>
                <span class="ms-3"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($restaurant->getLocation()) ?></span>
            </p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="restaurant-detail-description mb-5">
                    <div class="fs-5" style="opacity: 0.9; line-height: 1.8;">
                        <?= $restaurant->getLongDescription(); ?>
                    </div>
                </div>

                <hr style="border-color: rgba(244, 217, 198, 0.2);" class="my-5">

                <?php if (isset($chef) && $chef): ?>
                    <div class="chef-section mb-5 p-4 p-md-5" style="background: #4a1608; border-radius: 24px; border: 1px solid rgba(245, 197, 186, 0.2);">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <?php if ($chef->getImageUrl()): ?>
                                    <img src="<?= htmlspecialchars($chef->getImageUrl()) ?>" 
                                         class="rounded-circle shadow-lg" 
                                         style="width: 220px; height: 220px; object-fit: cover; border: 4px solid #f5c5ba;" 
                                         alt="<?= htmlspecialchars($chef->getName()) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h6 style="color: #ff8a2a;" class="text-uppercase fw-bold mb-1">Executive Chef</h6>
                                <h2 class="fw-bold mb-2 text-white"><?= htmlspecialchars($chef->getName()) ?></h2>
                                <p style="color: #f5c5ba;" class="mb-3">
                                    <i class="bi bi-award-fill me-1"></i> 
                                    <?= $chef->getExperienceYears() ?> Years of Professional Excellence
                                </p>
                                <div class="fst-italic" style="color: #f4d9c6; opacity: 0.8;">
                                    <?= $chef->getDescription() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center mt-5">
                    <div class="col-lg-9">
                        <?php include __DIR__ . '/partials/_reservationForm.php'; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>