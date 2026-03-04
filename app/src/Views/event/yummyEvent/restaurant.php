<?php 

$bodyClass = 'restaurant'; //lets me write CSS that only applies to that page.
require __DIR__ . '/../../partials/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/yummy">Yummy</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($restaurant->getName()) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold"><?= htmlspecialchars($restaurant->getName()) ?></h1>
            <p class="text-muted mb-4">📍 <?= htmlspecialchars($restaurant->getLocation()) ?></p>
            
            <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" class="img-fluid rounded mb-5 shadow-sm" alt="...">

            <div class="restaurant-rich-content">
                <?php 
                   // Use raw echo here because WYSIWYG stores HTML tags. 
                   // DO NOT use htmlspecialchars() on the 'long_description' 
                   // or it will show <h1>Tags</h1> as text.
                   echo $restaurant->getLongDescription(); 
                ?>
            </div>
            </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5>Reserve Now</h5>
                    <p class="small text-muted">Secure your spot at <?= htmlspecialchars($restaurant->getName()) ?></p>
                    <a href="/yummy/reservation/<?= $restaurant->getId() ?>" class="btn btn-primary w-100">
                        Book Table
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>