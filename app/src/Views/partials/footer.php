</main>

<footer class="main-footer">
  © <?= date('Y') ?> Haarlem Festival. All rights reserved.
</footer>

<script src="/js/flashMessage.js"></script>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg border-0"
         role="alert"
         style="z-index: 9999; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg border-0"
         role="alert"
         style="z-index: 9999; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

</body>
</html>