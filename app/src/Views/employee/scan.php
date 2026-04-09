<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="scan-page">
    <div class="scan-page-card">
        <h2 class="scan-page-title">Scan Ticket</h2>

        <p class="scan-page-text">Scan QR code using camera:</p>
        <div id="reader" class="scan-page-reader"></div>

        <div class="scan-page-divider">or</div>

        <p class="scan-page-text">Paste token manually:</p>

        <form method="GET" action="/scan" class="scan-page-form">
            <input
                type="text"
                name="token"
                placeholder="Enter ticket token"
                class="scan-page-input"
            >
            <button type="submit" class="scan-page-button">Scan</button>
        </form>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="/js/scan.js"></script>

<?php require __DIR__ . '/../partials/footer.php'; ?>