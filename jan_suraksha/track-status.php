<?php
require_once __DIR__ . '/config.php';

$status = null;
$err = '';
$isAnonymous = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    if (!$code) {
        $err = 'Please enter a Complaint ID or Anonymous Tracking ID.';
    } else {
        // Check if this is an anonymous tracking ID (format: ANON-YYYY-XXXXXX)
        if (preg_match('/^ANON-\d{4}-[A-F0-9]{6}$/', $code)) {
            // Query by anonymous tracking ID
            $stmt = $mysqli->prepare('SELECT complaint_code, crime_type, status, updated_at, is_anonymous, complainant_name, mobile, location, description FROM complaints WHERE anonymous_tracking_id = ? AND is_anonymous = 1');
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $res = $stmt->get_result();
            $status = $res->fetch_assoc();
            
            if ($status) {
                $isAnonymous = true;
                // Replace complaint_code display with anonymous tracking ID for display
                $status['display_code'] = $code;
            }
        } else {
            // Query by regular complaint code
            $stmt = $mysqli->prepare('SELECT complaint_code, crime_type, status, updated_at, is_anonymous, complainant_name, mobile, location, description FROM complaints WHERE complaint_code = ?');
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $res = $stmt->get_result();
            $status = $res->fetch_assoc();
            
            if ($status) {
                $status['display_code'] = $status['complaint_code'];
                $isAnonymous = ($status['is_anonymous'] == 1);
            }
        }

        if (!$status) {
            $err = 'No record found for this Complaint ID.';
        }
    }
}
?>
<?php include 'header.php'; ?>

<style>
/* Track Status Hero */
.status-hero {
    background: linear-gradient(135deg, var(--color-primary, #0d6efd) 0%, var(--color-primary-light, #0dcaf0) 100%);
    color: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(13, 110, 253, 0.3);
}

/* Status progress timeline */
.progress-timeline {
    position: relative;
    padding: 2rem 0;
}

.progress-step {
    display: flex;
    align-items: center;
    position: relative;
    margin-bottom: 1.5rem;
}

.progress-step:last-child { margin-bottom: 0; }

.progress-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    z-index: 2;
    flex-shrink: 0;
}

.progress-line {
    position: absolute;
    left: 20px;
    top: 50px;
    width: 3px;
    height: calc(100% - 50px);
    background: var(--color-border, #dee2e6);
    z-index: 1;
}

/* Status cards */
.status-card {
    background-color: var(--color-surface, #ffffff);
    border: 1px solid var(--color-border, rgba(0,0,0,0.08));
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.complaint-id-hero {
    font-size: clamp(1.75rem, 6vw, 3rem);
    font-weight: 700;
    background: linear-gradient(135deg, white 0%, rgba(255,255,255,0.9) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Form styling */
.status-form-input {
    background-color: var(--color-surface-subtle, rgba(0,0,0,0.02));
    border: 1px solid var(--color-border, rgba(0,0,0,0.08));
    border-radius: 12px;
    padding: 0.875rem 1.25rem;
}

.status-form-input:focus {
    background-color: var(--color-surface, #ffffff);
    border-color: var(--color-primary, #0d6efd);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
</style>

<style>
/* Theme overrides */
body {
    background-color: var(--color-bg) !important;
    background-image: var(--custom-bg, none) !important;
}

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
        <div class="col-lg-8 col-md-10">
            
            <!-- Hero Section -->
            <div class="status-hero text-center p-5 mb-5">
                <div class="display-4 mb-4">
                    <i class="bi bi-eye"></i>
                </div>
                <h1 class="display-5 fw-bold mb-4">Track Complaint Status</h1>
                <p class="lead mb-0 opacity-90">Enter your Complaint ID to check real-time status and updates</p>
            </div>

            <!-- Status Form -->
            <div class="status-card p-4 mb-5">
                <form method="post" class="mb-0">
                    <div class="mb-4">
                        <label for="code" class="form-label h5 mb-3">
                            <i class="bi bi-card-text me-2 text-primary"></i>Enter Complaint ID or Anonymous Tracking ID
                        </label>
                        <input type="text" class="form-control status-form-input fs-5 py-3" 
                               id="code" name="code" placeholder="e.g., IN/YYYY/12345 or ANON-YYYY-ABC123" required 
                               value="<?= e($_POST['code'] ?? '') ?>">
                    </div>
                    <?php if($err): ?>
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= e($err) ?>
                        </div>
                    <?php endif; ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg py-3 fs-5">
                            <i class="bi bi-search me-2"></i>Check Status
                        </button>
                    </div>
                </form>
            </div>

            <?php if($status): ?>
                <!-- Complaint Hero ID -->
                <div class="text-center mb-5 p-4 bg-light rounded-4">
                    <?php if($isAnonymous): ?>
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="bi bi-shield-lock me-2"></i>🔒 Anonymous Complaint
                            </span>
                        </div>
                    <?php endif; ?>
                    <h2 class="mb-2 text-muted"><?= $isAnonymous ? 'Anonymous Tracking ID' : 'Your Complaint' ?></h2>
                    <div class="complaint-id-hero mb-2"><?= e($status['display_code']) ?></div>
                    <div class="h6 text-muted mb-0">
                        <i class="bi bi-<?= $status['crime_type'] === 'Theft' ? 'bag-check' : 'shield-check' ?> me-2"></i>
                        <?= e($status['crime_type']) ?>
                    </div>
                    <?php if($isAnonymous): ?>
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Personal information is protected for anonymous complaints
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status Progress Tracker -->
                <div class="status-card mb-5">
                    <div class="card-body p-4">
                        <h3 class="h4 mb-4">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Status Progress
                        </h3>
                        
                        <div class="progress-timeline">
                            <div class="progress-step">
                                <div class="progress-circle bg-success text-white">1</div>
                                <div class="ms-4">
                                    <h6 class="mb-1">Filed</h6>
                                    <small class="text-success">Completed</small>
                                </div>
                            </div>
                            
                            <div class="progress-step">
                                <div class="progress-circle bg-info text-white">2</div>
                                <div class="ms-4">
                                    <h6 class="mb-1">Assigned</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            
                            <div class="progress-step">
                                <div class="progress-circle <?= stripos(e($status['status']), 'progress') !== false ? 'bg-primary text-white' : 'bg-secondary' ?>">
                                    <?= stripos(e($status['status']), 'progress') !== false ? '3' : '○' ?>
                                </div>
                                <div class="ms-4">
                                    <h6 class="mb-1">In Progress</h6>
                                    <span class="badge <?= stripos(e($status['status']), 'progress') !== false ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= stripos(e($status['status']), 'progress') !== false ? 'Current' : 'Pending' ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="progress-step">
                                <div class="progress-circle bg-secondary">4</div>
                                <div class="ms-4">
                                    <h6 class="mb-1">Resolved</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Status Details -->
                <div class="status-card">
                    <div class="card-body p-4">
                        <h3 class="h4 mb-4">
                            <i class="bi bi-info-circle me-2 text-primary"></i>Current Status
                        </h3>
                        
                        <?php
                        $status_text = e($status['status']);
                        $status_desc = 'Your complaint is being processed by authorities.';
                        $expected_time = '7-15 working days';
                        
                        if (stripos($status_text, 'submitted') !== false) {
                            $status_desc = 'Your complaint has been received and queued for review.';
                            $expected_time = '24 hours for acknowledgment';
                        } elseif (stripos($status_text, 'progress') !== false || stripos($status_text, 'pending') !== false) {
                            $status_desc = 'Your complaint is assigned and under investigation.';
                            $expected_time = '7-15 working days';
                        } elseif (stripos($status_text, 'resolved') !== false) {
                            $status_desc = 'Your complaint has been resolved successfully.';
                            $expected_time = 'Completed';
                        }
                        
                        $badge_class = 'bg-secondary';
                        if (stripos($status_text, 'progress') !== false || stripos($status_text, 'pending') !== false) {
                            $badge_class = 'bg-warning text-dark';
                        } elseif (stripos($status_text, 'resolved') !== false) {
                            $badge_class = 'bg-success';
                        } elseif (stripos($status_text, 'submitted') !== false) {
                            $badge_class = 'bg-info text-dark';
                        }
                        ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="mb-1"><?= $status_text ?></h5>
                                <p class="text-muted mb-2"><?= $status_desc ?></p>
                                <small class="text-success fw-semibold">
                                    ⏱️ Expected: <?= $expected_time ?>
                                </small>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge fs-5 <?= $badge_class ?> px-3 py-2"><?= $status_text ?></span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Last Updated:</strong><br>
                                <span class="text-muted">
                                    <?php 
                                    $update_date = strtotime(e($status['updated_at']));
                                    if($update_date > 0 && $update_date > 1000000000) {
                                        echo date('d M Y, g:i A', $update_date);
                                    } else {
                                        echo 'Not updated yet';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <a href="file-complaint.php" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle me-2"></i>File New Complaint
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
