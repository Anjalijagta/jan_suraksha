<?php
require_once __DIR__ . '/config.php';
$code = isset($_GET['code']) ? $_GET['code'] : '';

// Fetch complaint details from database if code exists
$complaint = null;
$user = null;
if ($code) {
    $stmt = $mysqli->prepare('SELECT c.*, u.fullname, u.mobile, u.email FROM complaints c LEFT JOIN users u ON c.user_id = u.id WHERE c.complaint_code = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $complaint = $result->fetch_assoc();
        $stmt->close();
    }
}
?>
<?php include 'header.php'; ?>

<!-- Print Receipt Styles -->
<link rel="stylesheet" href="/css/print-receipt.css">

<!-- QRCode.js Library for QR Code Generation (local first, CDN fallback) -->
<script src="js/qrcode.min.js"></script>
<script>
    // Fallback to CDN if local QRCode.js failed to load for any reason
    if (typeof QRCode === 'undefined') {
        (function () {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
            script.defer = true;
            document.head.appendChild(script);
        }());
    }
</script>

<!-- Print Receipt Script -->
<script src="/js/print-receipt.js" defer></script>

<style>
    /* For pages with custom backgrounds, override body background */
body {
    background-color: var(--color-bg) !important;
    background-image: var(--custom-bg, none) !important;
}

/* Update hardcoded colors to use CSS vars */
.text-primary { color: var(--color-primary) !important; }
.btn-primary { 
    background-color: var(--color-primary); 
    border-color: var(--color-primary); 
}
.btn-primary:hover {
    background-color: color-mix(in srgb, var(--color-primary) 90%, black);
    border-color: color-mix(in srgb, var(--color-primary) 80%, black);
}

</style>


<main id="page-content" class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="success-container p-4 p-md-5" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                
                <!-- Success Icon -->
                <div class="text-center mb-4">
                    <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: #28a745;"></i>
                    <h2 class="mb-4 mt-3">Complaint Submitted Successfully</h2>
                </div>

                <?php if($code && $complaint): ?>
                    <div class="alert alert-success">
                        Your complaint has been submitted successfully.<br>
                        <strong>Complaint ID: <?=e($code)?></strong><br>
                        Please save this ID to track your complaint status.
                    </div>

                    <!-- Complaint Summary -->
                    <div class="p-3 bg-light rounded mb-4">
                        <h5 class="mb-3"><i class="bi bi-file-text me-2"></i>Complaint Summary</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Complaint Type:</strong><br>
                                <?= e($complaint['crime_type']) ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Status:</strong><br>
                                <span class="badge bg-warning"><?= e($complaint['status']) ?></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Location:</strong><br>
                                <?= e($complaint['location']) ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Date Filed:</strong><br>
                                <?= date('d M Y, h:i A', strtotime($complaint['date_filed'])) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" id="printReceiptBtn" class="btn btn-primary">
                            <i class="bi bi-printer me-2"></i>Print Receipt
                        </button>
                        <a class="btn btn-outline-primary" href="track-status.php">
                            <i class="bi bi-search me-2"></i>Track Complaint
                        </a>
                        <a class="btn btn-outline-secondary" href="file-complaint.php">
                            <i class="bi bi-file-earmark-plus me-2"></i>File Another
                        </a>
                        <a class="btn btn-outline-secondary" href="index.php">
                            <i class="bi bi-house me-2"></i>Return to Home
                        </a>
                    </div>

                    <!-- Hidden Receipt Data Container for JavaScript -->
                    <div id="receiptData" style="display: none;"
                         data-tracking-id="<?= e($code) ?>"
                         data-complaint-type="<?= e($complaint['crime_type']) ?>"
                         data-location="<?= e($complaint['location']) ?>"
                         data-status="<?= e($complaint['status']) ?>"
                         data-submission-date="<?= e($complaint['date_filed']) ?>"
                         data-complaint-id="<?= e($code) ?>"
                         data-incident-date="<?= e($complaint['date_filed']) ?>"
                         data-user-name="<?= e($complaint['fullname'] ?? 'Not Available') ?>"
                         data-user-mobile="<?= e($complaint['mobile'] ?? 'Not Available') ?>"
                         data-user-email="<?= e($complaint['email'] ?? '') ?>"
                         data-is-anonymous="false">
                    </div>

                    <!-- Hidden Receipt Container for Printing -->
                    <div class="receipt-container" style="display: none;">
                        <!-- Receipt content will be dynamically generated by JavaScript -->
                    </div>

                <?php elseif($code): ?>
                    <div class="alert alert-success">
                        Your complaint has been submitted successfully.<br>
                        <strong>Complaint ID: <?=e($code)?></strong><br>
                        Please save this ID to track your complaint status.
                    </div>
                    <a class="btn btn-primary" href="index.php">Return to Home</a>
                <?php else: ?>
                    <div class="alert alert-warning">No complaint ID provided.</div>
                    <a class="btn btn-primary" href="file-complaint.php">File a Complaint</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
