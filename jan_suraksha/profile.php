<?php
require_once __DIR__ . '/config.php';
// The config.php file already starts the session.
if(empty($_SESSION['user_id'])){ header('Location: login.php'); exit; }

// --- YOUR PHP LOGIC (UNCHANGED) ---
$user_id = (int)$_SESSION['user_id'];
// fetch user
$stmt = $mysqli->prepare('SELECT name,email,mobile,created_at FROM users WHERE id=?');
$stmt->bind_param('i',$user_id); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc();
// fetch complaints
$cs = $mysqli->prepare('SELECT id,complaint_code,crime_type,date_filed,status FROM complaints WHERE user_id=? ORDER BY date_filed DESC');
$cs->bind_param('i',$user_id); $cs->execute(); $complaints = $cs->get_result();
?>
<?php include 'header.php'; ?>

<style>
    .card-custom {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: none;
    }
    .profile-info-list .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }
    .complaint-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    .complaint-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .complaint-id {
        font-weight: 600;
        color: #0d6efd;
    }
    .complaint-meta {
        color: #6c757d;
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
        <div class="col-md-10 col-lg-8">
            
            <h1 class="h3 mb-4">My Profile</h1>

            <!-- Profile Information Card -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Profile Information</h5>
                    <ul class="list-group list-group-flush profile-info-list">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Name:</strong>
                            <span><?= e($user['name']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Email:</strong>
                            <span><?= e($user['email']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Mobile:</strong>
                            <span><?= e($user['mobile']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Member Since:</strong>
                            <span><?= date('M d, Y', strtotime(e($user['created_at']))) ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Complaint History Card -->
            <div class="card card-custom">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Complaint History</h5>
                    
                    <?php if ($complaints->num_rows > 0): ?>
                        <?php while($row = $complaints->fetch_assoc()): ?>
                            <div class="complaint-card">
                                <div class="complaint-header">
                                    <span class="complaint-id"><?= e($row['complaint_code']) ?></span>
                                    <?php
                                    $status_text = e($row['status']);
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
                                </div>
                                <div class="complaint-body">
                                    <p class="mb-1"><strong>Crime Type:</strong> <?= e($row['crime_type']) ?></p>
                                    <p class="complaint-meta small mb-0">
                                        Filed On: <?= date('M d, Y', strtotime(e($row['date_filed']))) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">You have not filed any complaints yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
