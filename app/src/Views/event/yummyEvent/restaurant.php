<?php 
$bodyClass = 'restaurant-profile'; 
require __DIR__ . '/../../partials/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="d-flex justify-content-left mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/yummy">Yummy</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($restaurant->getName()) ?></li>
        </ol>
    </nav>

    <div class="text-center mb-5">
        <h1 class="display-3 fw-bold"><?= htmlspecialchars($restaurant->getName()) ?></h1>
        <p class="lead text-muted">
            <span class="badge bg-primary me-2"><?= htmlspecialchars($restaurant->getCuisine()) ?></span>
            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($restaurant->getLocation()) ?>
        </p>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" 
                 class="img-fluid rounded-4 shadow-lg w-100" 
                 style="max-height: 600px; object-fit: cover;" 
                 alt="<?= htmlspecialchars($restaurant->getName()) ?>">
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="restaurant-rich-content mb-5 fs-5">
                <?= $restaurant->getLongDescription(); ?>
            </div>

            <hr class="my-5">

            <?php if (isset($chef) && $chef): ?>
                <div class="chef-profile-section p-4 p-md-5 bg-light rounded-4 shadow-sm mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <?php if ($chef->getImageUrl()): ?>
                                <img src="<?= htmlspecialchars($chef->getImageUrl()) ?>" 
                                     class="rounded-circle shadow" 
                                     style="width: 200px; height: 200px; object-fit: cover; border: 5px solid white;" 
                                     alt="<?= htmlspecialchars($chef->getName()) ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow" 
                                     style="width: 200px; height: 200px; font-size: 4rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-primary text-uppercase fw-bold mb-1">Executive Chef</h6>
                            <h2 class="fw-bold mb-2"><?= htmlspecialchars($chef->getName()) ?></h2>
                            <p class="text-muted mb-3">
                                <i class="bi bi-award-fill me-1"></i> 
                                <?= $chef->getExperienceYears() ?> Years of Professional Excellence
                            </p>
                            <div class="chef-bio fst-italic text-secondary">
                                <?= $chef->getDescription() ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center py-4">
                <h3 class="mb-3">Ready to experience <?= htmlspecialchars($restaurant->getName()) ?>?</h3>
                <a href="/yummy/reservation/<?= $restaurant->getId() ?>" class="btn btn-primary btn-lg px-5 shadow">
                    Book a Table Now
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>