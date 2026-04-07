<?php
use App\ViewModels\PageElementViewModel;

/** @var PageElementViewModel $vm */

require __DIR__ . '/../../partials/adminHeader.php';
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-layout-text-window-reverse me-2"></i>Manage History</h2>
    </div>

    <?php require __DIR__ . '/partials/_page_sections.php'; ?>

    <hr class="my-5">

    <?php require __DIR__ . '/partials/_venues_table.php'; ?>

    <hr class="my-5">

    <?php require __DIR__ . '/partials/_tours_table.php'; ?>

</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>