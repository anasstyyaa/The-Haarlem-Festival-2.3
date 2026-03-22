<?php require __DIR__ . '/../partials/header.php'; ?>

<div style="text-align:center; padding:50px;">
    <h2>Scan Ticket</h2>

    <p>Scan QR code using camera:</p>

    <video id="preview" style="width:300px; border:1px solid black;"></video>

    <br><br>

    <p>Or paste token manually:</p>

    <form method="GET" action="/scan">
        <input type="text" name="token" placeholder="Enter ticket token" style="padding:10px; width:300px;">
        <br><br>
        <button type="submit" style="padding:10px 20px;">Scan</button>
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const html5QrCode = new Html5Qrcode("preview");

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            let cameraId = devices[0].id;

            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: 250
                },
                (decodedText) => {
                    window.location.href = "/scan?token=" + decodedText;
                },
                (errorMessage) => {
                }
            );
        }
    }).catch(err => {
        console.error(err);
    });
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>