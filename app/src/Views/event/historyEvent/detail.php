<?php
use App\Models\HistoryVenueModel;

/** @var HistoryVenueModel $venue */

$bodyClass = 'history-detail';
require __DIR__ . '/../../partials/header.php';
?>

<div style="background-color: #3a0d00; min-height: 100vh; color: #f4d9c6;">

    <section class="jazz-hero">
        <img
    src="<?= htmlspecialchars($venue->getDetailImgURL() ?? $venue->getImgURL() ?? '/assets/images/history/hero.png') ?>"
    alt="<?= htmlspecialchars($venue->getDetailAltText() ?? $venue->getVenueName()) ?>"
>

        <div class="hero-overlay">
            <a href="/history" class="jazz-back mb-3">
                <i class="bi bi-arrow-left"></i> Back to History
            </a>

            <h1><?= htmlspecialchars($venue->getVenueName()) ?></h1>

            <p>
                <span class="badge" style="background-color: rgba(244, 217, 198, 0.2); color: #f4d9c6; border: 1px solid #f4d9c6;">
                    History Tour
                </span>

                <?php if ($venue->getLocation()): ?>
                    <span class="ms-3">
                        <i class="bi bi-geo-alt-fill"></i>
                        <?= htmlspecialchars($venue->getLocation()) ?>
                    </span>
                <?php endif; ?>
            </p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="restaurant-detail-description mb-5">
                    <div class="fs-5" style="opacity: 0.9; line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($venue->getDetails() ?? 'No description available.')) ?>
                    </div>
                </div>

                <hr style="border-color: rgba(244, 217, 198, 0.2);" class="my-5">

                <div class="p-4 p-md-5" style="background: #4a1608; border-radius: 24px; border: 1px solid rgba(245, 197, 186, 0.2);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 style="color: #ff8a2a;" class="text-uppercase fw-bold mb-1">Part of the tour</h6>
                            <h2 class="fw-bold mb-2 text-white">Included in the Haarlem History Experience</h2>
                            <p style="color: #f5c5ba;" class="mb-3">
                                <i class="bi bi-signpost-split-fill me-1"></i>
                                Guided visit · Historical storytelling · City landmark
                            </p>
                            <div class="fst-italic" style="color: #f4d9c6; opacity: 0.85;">
                                This location is one of the featured stops in the Haarlem Festival history route.
                                During the guided walk, visitors discover local stories, architecture, and the cultural
                                background that shaped the city through the centuries.
                            </div>
                        </div>

                        <div class="col-md-4 text-center mt-4 mt-md-0">
                            <?php if ($venue->getImgURL()): ?>
                                <img
                                    src="<?= htmlspecialchars($venue->getImgURL()) ?>"
                                    class="rounded-circle shadow-lg"
                                    style="width: 220px; height: 220px; object-fit: cover; border: 4px solid #f5c5ba;"
                                    alt="<?= htmlspecialchars($venue->getVenueName()) ?>"
                                >
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="/history/booking" class="history-detail-tour-btn">
                        Start planning your tour
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>