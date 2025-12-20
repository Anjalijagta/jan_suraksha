<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$status = null; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $code = trim($_POST['code'] ?? '');
    if(!$code) $err = 'Please enter a Complaint ID.';
    else {
        $stmt = $mysqli->prepare('SELECT complaint_code, crime_type, status, updated_at FROM complaints WHERE complaint_code=?');
        $stmt->bind_param('s',$code); $stmt->execute(); $res = $stmt->get_result();
        if($row = $res->fetch_assoc()) $status = $row; else $err = 'No record found for this Complaint ID.';
    }
}
?>
<?php include 'header.php'; ?>

<style>
    body {
        background-image: url(uploads/ppp.jpg);
    }
    .content-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .form-control {
        background-color: #f1f3f5;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.8rem 1rem;
    }
    .form-control:focus {
        background-color: #ffffff;
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    }
    .details-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }
    .details-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .details-row:last-child {
        border-bottom: none;
    }
    .details-label {
        color: #6c757d;
    }
    .details-value {
        font-weight: 500;
    }
</style>
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


<main id="page-content" class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="content-container p-4 p-md-5">
                <h1 class="h3 text-center mb-4">Complaint Status</h1>
                
                <?php if($err): ?>
                    <div class="alert alert-warning"><?= e($err) ?></div>
                <?php endif; ?>

                <form method="post" class="mb-4">
                    <div class="mb-3">
                        <label for="code" class="form-label">Enter Your FIR/Complaint ID</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="e.g., IN/2024/12345" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Check Status</button>
                    </div>
                </form>

                <?php if($status): ?>
                    <div class="mt-5">
                        <h2 class="h4 mb-3">Complaint Details</h2>
                        <div class="details-card">
                            <div class="details-row">
                                <span class="details-label">Complaint ID</span>
                                <span class="details-value"><?= e($status['complaint_code']) ?></span>
                            </div>
                            <div class="details-row">
                                <span class="details-label">Current Status</span>
                                <span class="details-value">
                                    <?php
                                    $status_text = e($status['status']);
                                    $badge_class = 'bg-secondary'; // Default
                                    if (stripos($status_text, 'progress') !== false || stripos($status_text, 'pending') !== false) {
                                        $badge_class = 'bg-warning text-dark';
                                    } elseif (stripos($status_text, 'resolved') !== false || stripos($status_text, 'closed') !== false) {
                                        $badge_class = 'bg-success';
                                    } elseif (stripos($status_text, 'submitted') !== false) {
                                        $badge_class = 'bg-info text-dark';
                                    }
                                    ?>
                                    <span class="badge rounded-pill <?= $badge_class ?>"><?= $status_text ?></span>
                                </span>
                            </div>
                            <div class="details-row">
                                <span class="details-label">Last Updated On</span>
                                <span class="details-value"><?= date('Y-m-d', strtotime(e($status['updated_at']))) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
