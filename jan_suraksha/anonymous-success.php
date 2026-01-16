<?php
require_once __DIR__ . '/config.php';
$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';

// Security: Verify tracking ID format
if ($tracking_id && !preg_match('/^ANON-\d{4}-[A-F0-9]{6}$/', $tracking_id)) {
    $tracking_id = '';
}

// If tracking ID is missing or invalid after validation, redirect user
if (empty($tracking_id)) {
    header('Location: file-complaint.php');
    exit;
}
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/anonymous.css">

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

.success-container {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.tracking-id-box {
    background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    border-radius: 12px;
    padding: 2rem;
    color: white;
    text-align: center;
    margin: 1.5rem 0;
}

.tracking-id-display {
    font-size: 2rem;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    letter-spacing: 2px;
    padding: 1rem;
    background-color: rgba(255,255,255,0.2);
    border-radius: 8px;
    margin: 1rem 0;
    word-break: break-all;
}

.copy-btn {
    background-color: rgba(255,255,255,0.9);
    color: #0d6efd;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.copy-btn:hover {
    background-color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.copy-btn.copied {
    background-color: #28a745;
    color: white;
}

.warning-box {
    background-color: #fff3cd;
    border: 2px solid #ffc107;
    border-left: 6px solid #ffc107;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 1.5rem 0;
}

.success-icon {
    font-size: 5rem;
    color: #28a745;
    animation: scaleIn 0.5s ease-in-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .tracking-id-display {
        font-size: 1.25rem;
        letter-spacing: 1px;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
}
</style>

<main id="page-content" class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="success-container p-4 p-md-5">
                
                <!-- Success Icon -->
                <div class="text-center">
                    <i class="bi bi-shield-check success-icon"></i>
                    <h1 class="h2 mt-3 mb-2">✅ Anonymous Complaint Filed Successfully</h1>
                    <p class="text-muted">Your identity is protected. Your report has been submitted securely.</p>
                </div>

                <?php if($tracking_id): ?>
                    <!-- Tracking ID Display -->
                    <div class="tracking-id-box">
                        <h3 class="h5 mb-3">Your Anonymous Tracking ID</h3>
                        <div class="tracking-id-display" id="tracking-id-text">
                            <?= e($tracking_id) ?>
                        </div>
                        <button type="button" class="copy-btn" id="copy-btn" onclick="copyTrackingId()">
                            <i class="bi bi-clipboard me-2"></i>Copy Tracking ID
                        </button>
                    </div>

                    <!-- Critical Warning -->
                    <div class="warning-box">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h5 class="mb-2"><strong>⚠️ IMPORTANT: Save This ID Securely</strong></h5>
                                <ul class="mb-0">
                                    <li><strong>You cannot recover this ID later</strong></li>
                                    <li>Take a screenshot or write it down</li>
                                    <li>Use this ID to track your complaint status</li>
                                    <li>Keep it private - anyone with this ID can view your complaint</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="#" onclick="downloadTrackingId(); return false;" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i>Download as Text File
                        </a>
                        <a href="track-status.php" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Track This Complaint
                        </a>
                        <a href="file-complaint.php" class="btn btn-outline-secondary">
                            <i class="bi bi-file-earmark-plus me-2"></i>File Another Complaint
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Return to Home
                        </a>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>What Happens Next?</h6>
                        <ul class="small mb-0">
                            <li>Your anonymous complaint has been submitted to authorities</li>
                            <li>Investigation will begin based on the information provided</li>
                            <li>Track progress anytime using your tracking ID</li>
                            <li>Updates will be reflected in the tracking system</li>
                        </ul>
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger mt-4">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Error:</strong> No tracking ID provided. Please try filing the complaint again.
                    </div>
                    <a class="btn btn-primary" href="file-complaint.php">File Complaint</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
// Copy tracking ID to clipboard
function copyTrackingId() {
    const trackingIdText = document.getElementById('tracking-id-text').textContent.trim();
    const copyBtn = document.getElementById('copy-btn');
    
    navigator.clipboard.writeText(trackingIdText).then(() => {
        // Success feedback
        copyBtn.innerHTML = '<i class="bi bi-check2 me-2"></i>Copied!';
        copyBtn.classList.add('copied');
        
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="bi bi-clipboard me-2"></i>Copy Tracking ID';
            copyBtn.classList.remove('copied');
        }, 2000);
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = trackingIdText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        copyBtn.innerHTML = '<i class="bi bi-check2 me-2"></i>Copied!';
        copyBtn.classList.add('copied');
        
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="bi bi-clipboard me-2"></i>Copy Tracking ID';
            copyBtn.classList.remove('copied');
        }, 2000);
    });
}

// Download tracking ID as text file
function downloadTrackingId() {
    const trackingIdText = document.getElementById('tracking-id-text').textContent.trim();
    const content = `Anonymous Complaint Tracking ID\n\n` +
                   `Tracking ID: ${trackingIdText}\n\n` +
                   `IMPORTANT INSTRUCTIONS:\n` +
                   `- Keep this ID secure and private\n` +
                   `- Use this ID to track your complaint at: ${window.location.origin}/track-status.php\n` +
                   `- You cannot recover this ID if lost\n` +
                   `- Do not share this ID publicly\n\n` +
                   `Date Generated: ${new Date().toLocaleString()}\n` +
                   `Portal: Jan Suraksha - Crime Reporting Portal`;
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Anonymous_Complaint_${trackingIdText}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>

<?php include 'footer.php'; ?>
