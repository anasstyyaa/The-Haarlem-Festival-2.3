<?php 
$bodyClass = 'yummy-page'; 
require __DIR__ . '/../../partials/header.php';
?>
<div class="container">

<section class="yummy-hero mb-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 fw-bold mb-3">Yummy</h1>
            <p class="lead text-muted mb-4">
                Experience the culinary heart of the Netherlands. From historic taverns to 
                modern gastronomy, find your perfect table in Haarlem.
            </p>
            
            <div class="banner-container">
                <img src="/assets/images/yummy-banner.jpg" alt="Yummy Event Banner" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<hr class="my-5">

<section class="restaurant-list pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Discover Restaurants</h2>
        <span class="text-muted"><?= count($restaurants) ?> results found</span>
    </div>

    <div class="row g-4">
        <?php if (empty($restaurants)): ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-light border">
                    <p class="mb-0">No restaurants are currently listed for this event.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 restaurant-card border-0 shadow-sm">
                        <div class="ratio ratio-16x9">
                            <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" 
                                 class="card-img-top rounded-top" 
                                 alt="<?= htmlspecialchars($restaurant->getName()) ?>"
                                 style="object-fit: cover;">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <?= htmlspecialchars($restaurant->getCuisine()) ?>
                                </span>
                            </div>

                            <h5 class="card-title fw-bold mb-1">
                                <?= htmlspecialchars($restaurant->getName()) ?>
                            </h5>
                            
                            <p class="small text-muted mb-3">
                                📍 <?= htmlspecialchars($restaurant->getLocation()) ?>
                            </p>

                            <p class="card-text text-secondary mb-4">
                                <?= htmlspecialchars($restaurant->getDescription()) ?>
                            </p>

                            <div class="mt-auto">
                                <a href="/yummy/restaurant/<?= $restaurant->getId() ?>" class="btn btn-dark w-100 mb-2">
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

<?php require __DIR__ . '/../../partials/footer.php'; ?>