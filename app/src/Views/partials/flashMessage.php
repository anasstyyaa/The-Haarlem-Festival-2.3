<div class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999; min-width: 350px; max-width: 90%;">
    
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0 mb-3" role="alert">
            <div class="d-flex align-items-center pe-4">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-bold">Success!</h6>
                    <p class="mb-0 small text-dark"><?= $_SESSION['flash_success']; ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0 mb-3" role="alert">
            <div class="d-flex align-items-center pe-4">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-bold">Action Failed</h6>
                    <p class="mb-0 small text-dark"><?= $_SESSION['error']; ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

</div>

<script src="/js/flashMessage.js"></script>