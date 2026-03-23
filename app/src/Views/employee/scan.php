<?php require __DIR__ . '/../partials/header.php'; ?>

<div style="text-align:center; padding:50px;">
    <h2>Scan Ticket</h2>

    <p>Scan QR code using camera:</p>

    <div id="reader" style="width:300px; margin:0 auto;"></div>

    <br><br>

    <p>Or paste token manually:</p>

    <form method="GET" action="/scan">
        <input type="text" name="token" placeholder="Enter ticket token" style="padding:10px; width:300px;">
        <br><br>
        <button type="submit" style="padding:10px 20px;">Scan</button>
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        window.location.href = "/scan?token=" + encodeURIComponent(decodedText);
    }

    function onScanFailure(error) {
        // ignore scan failures
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: 250 },
        false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>