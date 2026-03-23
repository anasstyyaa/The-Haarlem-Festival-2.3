<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="container py-5 text-center">
    <div class="card shadow border-0 p-5 mt-5">
        <div class="mb-4">
            <span style="font-size: 5rem;">⏳</span>
        </div>
        <h1 class="fw-bold">Payment Not Completed</h1>
        <h4 class="alert-heading">Your tickets are reserved!</h4>
            <p class="mb-0">
                We have saved your selection for the next <strong>24 hours</strong>. 
                Check your email (<strong><?= htmlspecialchars($_SESSION['user']['email'] ?? 'your inbox') ?></strong>) 
                for a direct link to complete your payment whenever you're ready.
            </p>
        <div class="mt-4">
            <a href="/personalProgram" class="btn btn-outline-secondary btn-lg">View My Program</a>
            <a href="/" class="btn btn-primary btn-lg">Return Home</a>
        </div>
    </div>
</div>


<?php require __DIR__ . '/../partials/footer.php'; ?>