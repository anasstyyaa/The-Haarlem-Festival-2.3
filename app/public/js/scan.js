function onScanSuccess(decodedText, decodedResult) {
    html5QrcodeScanner.clear();
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