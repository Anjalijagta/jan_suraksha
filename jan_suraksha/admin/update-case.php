<?php
require_once __DIR__ . '/../config.php';
// --- PHP LOGIC (CORRECTED AND FINAL) ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if(empty($_SESSION['admin_id'])){ header('Location: index.php'); exit; }

$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$complaint_id) { die("Error: No complaint ID specified. Please access this page from the 'Cases' list."); }

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli->begin_transaction();
    try {
        // 1. Update Case Status
        $status = $_POST['status'];
        $stmt1 = $mysqli->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $stmt1->bind_param('si', $status, $complaint_id);
        $stmt1->execute();

        // 2. Add New Diary Entry
        $diary_entry = trim($_POST['diary_entry'] ?? '');
        if (!empty($diary_entry)) {
            $admin_id = (int)$_SESSION['admin_id'];
            // schema uses note_text and created_at
            $stmt2 = $mysqli->prepare("INSERT INTO case_diary (complaint_id, admin_id, note_text, created_at) VALUES (?, ?, ?, NOW())");
            $stmt2->bind_param('iis', $complaint_id, $admin_id, $diary_entry);
            $stmt2->execute();
        }

        // 3. Update or Create Accused/Criminal Record
        $accused_id = (int)($_POST['accused_id'] ?? 0);
        $full_name = trim($_POST['full_name']);
        
        if (!empty($full_name)) {
            $father_name = $_POST['father_name'];
            $aliases = $_POST['aliases'];
            $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
            $physical_desc = $_POST['physical_description'];
            $last_address = $_POST['last_known_address'];
            $punishment_law = $_POST['punishment_section'];
            $punishment_desc = $_POST['punishment_description'];
            $existing_mugshot = $_POST['existing_mugshot'] ?? '';
            $new_mugshot = $existing_mugshot;

            if (isset($_FILES['mugshot']) && $_FILES['mugshot']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['mugshot'];
                if ($file['size'] < 5 * 1024 * 1024 && in_array($file['type'], ['image/jpeg', 'image/png'])) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_mugshot = bin2hex(random_bytes(16)) . '.' . $ext;
                    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/mugshots/' . $new_mugshot)) {
                        throw new Exception("Failed to move uploaded mugshot file.");
                    }
                } else { throw new Exception("Invalid mugshot file. Must be JPG/PNG and under 5MB."); }
            }

            if ($accused_id > 0) {
                // Update existing criminal record (do not change complaint_id here)
                $stmt3 = $mysqli->prepare("UPDATE criminals SET full_name=?, fathers_name=?, aliases=?, dob=?, physical_description=?, last_known_address=?, punishment_section=?, punishment_description=?, mugshot=? WHERE id=?");
                if (!$stmt3) throw new Exception('Prepare failed: ' . $mysqli->error);
                $stmt3->bind_param('sssssssssi', $full_name, $father_name, $aliases, $dob, $physical_desc, $last_address, $punishment_law, $punishment_desc, $new_mugshot, $accused_id);
                $stmt3->execute();
            } else {
                // Insert new criminal linked to this complaint via complaint_id
                $stmt3 = $mysqli->prepare("INSERT INTO criminals (complaint_id, full_name, fathers_name, aliases, dob, physical_description, last_known_address, punishment_section, punishment_description, mugshot, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                if (!$stmt3) throw new Exception('Prepare failed: ' . $mysqli->error);
                $stmt3->bind_param('isssssssss', $complaint_id, $full_name, $father_name, $aliases, $dob, $physical_desc, $last_address, $punishment_law, $punishment_desc, $new_mugshot);
                $stmt3->execute();
                $new_accused_id = $mysqli->insert_id;
                // relationship stored in criminals.complaint_id; no complaints table update required
            }
        }
        
        $mysqli->commit();
        header("Location: update-case.php?id=" . $complaint_id . "&success=1");
        exit;

    } catch (Exception $e) {
        $mysqli->rollback();
        $error_msg = "Error updating case: " . $e->getMessage();
    }
}

if(isset($_GET['success']) && $_GET['success'] == 1) {
    $success_msg = "Case file updated successfully!";
}

$stmt = $mysqli->prepare("
    SELECT c.*, cr.id AS criminal_id, cr.full_name, cr.fathers_name, cr.aliases, cr.dob, cr.physical_description, cr.last_known_address, cr.punishment_section, cr.punishment_description, cr.mugshot
    FROM complaints c
    LEFT JOIN criminals cr ON cr.complaint_id = c.id
    WHERE c.id = ?
");
$stmt->bind_param('i', $complaint_id);
$stmt->execute();
$case = $stmt->get_result()->fetch_assoc();

if (!$case) { die("Error: Case not found."); }

// Set display code for anonymous or regular complaint
$case['accused_id'] = $case['criminal_id'] ?? 0;

$diary_entries = $mysqli->query("SELECT * FROM case_diary WHERE complaint_id = $complaint_id ORDER BY created_at DESC");

$current_page = 'cases.php'; // CORRECTED
?>
<!doctype html>  
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Case #<?= e($case['complaint_code']) ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --bs-body-bg: #0d1117; --bs-body-color: #c9d1d9; --primary-dark: #161b22; --secondary-dark: #21262d; --border-color: #30363d; }
        /* Light theme overrides */
        html.light-theme { --bs-body-bg: #ffffff;
            --bs-body-color: #212529;
            --primary-dark: #ffffffff;
            --secondary-dark: #c0c9d0ff;
            --border-color: #b2b2b2ff; }
        /* Disable transitions briefly when toggling to avoid fade */
        html.disable-transitions *, html.disable-transitions *::before, html.disable-transitions *::after { transition: none !important; -webkit-transition: none !important; }
        /* Ensure .text-light switches to dark text in light theme */
        html.light-theme .text-light { color: #212529 !important; }
        html:not(.light-theme) .text-light { color: #ffffff !important; }
        .theme-toggle { cursor: pointer; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans", sans-serif; }
        .main-content { margin-left: 0; }
        @media (min-width: 992px) { .main-content { margin-left: 260px; } }
        .sidebar {
            width: 260px; background-color: var(--primary-dark); border-right: 1px solid var(--border-color);
            position: fixed; top: 0; bottom: 0; left: 0;
            transform: translateX(-100%); transition: transform 0.3s ease-in-out; z-index: 1040;
        }
        @media (min-width: 992px) { .sidebar { transform: translateX(0); } }
        .sidebar.active { transform: translateX(0); }

        .sidebar-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .sidebar-nav .nav-link { color: #8b949e; padding: 0.75rem 1.5rem; display: flex; align-items: center; font-size: 1rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background-color: var(--secondary-dark); color: #c9d1d9; border-radius: 6px; }
        .sidebar-nav .nav-link i { margin-right: 1rem; font-size: 1.2rem; }
        .topbar { background-color: var(--primary-dark); border-bottom: 1px solid var(--border-color); }
        .card, .form-control, .form-select { background-color: var(--primary-dark); border: 1px solid var(--border-color); color: var(--bs-body-color); }
        .form-control::placeholder { color: #8b949e; }
        .form-control:focus, .form-select:focus { background-color: var(--primary-dark); border-color: #58a6ff; box-shadow: none; color: var(--bs-body-color); }
        .info-grid { display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1rem; align-items: center; }
        .info-label { color: #8b949e; font-weight: 500; }
        .mugshot { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar vh-100" id="adminSidebar">
        <div class="sidebar-header"><h5 class="mb-0">Command Center</h5></div>
        <div class="p-3">
            <nav class="nav flex-column sidebar-nav">
                <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
                <a class="nav-link active" href="cases.php"><i class="bi bi-shield-check"></i> View Complaints</a>
                <a class="nav-link" href="criminals.php"><i class="bi bi-person-badge"></i> Criminals</a>
                <a class="nav-link" href="#"><i class="bi bi-people-fill"></i> Manage Users</a>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <header class="topbar p-3 sticky-top d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-dark d-lg-none me-2" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <a href="cases.php" class="text-light d-none d-lg-inline"><i class="bi bi-arrow-left fs-4"></i></a>
            </div>
            <h5 class="mb-0">Case #<?= e($case['complaint_code']) ?></h5>
            <div class="d-flex align-items-center">
                <div id="themeToggleWrapper" class="form-check form-switch theme-toggle text-light me-2" title="Toggle light / dark theme">
                    <input class="form-check-input" type="checkbox" id="themeToggle">
                    <label class="form-check-label mb-0 d-flex align-items-center" for="themeToggle"><i id="themeIcon" class="bi bi-moon-stars-fill"></i></label>
                </div>
            </div>
        </header>

        <main class="p-4">
            <?php if ($success_msg): ?><div class="alert alert-success"><?= $success_msg ?></div><?php endif; ?>
            <?php if ($error_msg): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="accused_id" value="<?= e($case['accused_id']) ?>">
                <input type="hidden" name="existing_mugshot" value="<?= e($case['mugshot']) ?>">

                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-7">
                        <div class="card p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Case Information</h5>
                                <?php if($case['is_anonymous'] == 1): ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-shield-lock"></i> Anonymous
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="info-grid">
                                <div class="info-label">
                                    <?= $case['is_anonymous'] == 1 ? 'Tracking ID:' : 'FIR No:' ?>
                                </div>
                                <div>
                                    <?php 
                                    if($case['is_anonymous'] == 1 && !empty($case['anonymous_tracking_id'])) {
                                        echo e($case['anonymous_tracking_id']);
                                    } else {
                                        echo e($case['complaint_code']);
                                    }
                                    ?>
                                </div>
                                <div class="info-label">Date of Incident:</div><div><?= date('Y-m-d', strtotime($case['date_filed'])) ?></div>
                                <div class="info-label">Complainant:</div>
                                <div>
                                    <?php 
                                    if($case['is_anonymous'] == 1) {
                                        echo '<span class="text-muted fst-italic"><i class="bi bi-lock-fill"></i> Protected (Anonymous)</span>';
                                    } else {
                                        echo e($case['complainant_name']);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="card p-3 mb-4">
                            <h5 class="mb-3">Accused/Suspect Details</h5>
                             <div class="row g-3">
                                <div class="col-12"><label class="form-label">Full Name</label><input type="text" class="form-control" name="full_name" value="<?= e($case['full_name']) ?>" placeholder="Suresh Yadav"></div>
                                <div class="col-12"><label class="form-label">Father's Name</label><input type="text" class="form-control" name="father_name" value="<?= e($case['fathers_name']) ?>" placeholder="Mahesh Yadav"></div>
                                <div class="col-12"><label class="form-label">Aliases</label><input type="text" class="form-control" name="aliases" value="<?= e($case['aliases']) ?>" placeholder="Sonu, Chhotu"></div>
                                <div class="col-12"><label class="form-label">Age / Date of Birth</label><input type="text" class="form-control" name="dob" value="<?= e($case['dob']) ?>" placeholder="Approx. 28 / 1996-05-10"></div>
                                <div class="col-12"><label class="form-label">Physical Description</label><textarea class="form-control" name="physical_description" rows="2"><?= e($case['physical_description']) ?></textarea></div>
                                <div class="col-12"><label class="form-label">Last Known Address</label><textarea class="form-control" name="last_known_address" rows="2"><?= e($case['last_known_address']) ?></textarea></div>
                                <div class="col-12">
                                    <label class="form-label">Mugshot</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= !empty($case['mugshot']) ? '../uploads/mugshots/' . e($case['mugshot']) : 'https://placehold.co/80x80/21262d/c9d1d9?text=Mugshot' ?>" alt="Mugshot" class="mugshot">
                                        <button type="button" class="btn btn-outline-dark" onclick="document.getElementById('mugshot-upload').click();">Upload New</button>
                                        <input type="file" class="d-none" id="mugshot-upload" name="mugshot">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-5">
                        <div class="card p-3 mb-4">
                            <h5 class="mb-3">Case Management</h5>
                            <div class="mb-3">
                                <label class="form-label">Status Update</label>
                                <select class="form-select" name="status">
                                    <option value="Pending" <?= ($case['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= ($case['status'] == 'In Progress') ? 'selected' : '' ?>>Under Investigation</option>
                                    <option value="Resolved" <?= ($case['status'] == 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                                    <option value="Closed" <?= ($case['status'] == 'Closed') ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Internal Case Diary (New Entry)</label>
                                <textarea class="form-control" name="diary_entry" rows="3" placeholder="Add a new time-stamped entry..."></textarea>
                                <div class="form-text">Previous entries are saved automatically.</div>
                                <?php if ($diary_entries->num_rows > 0): ?>
                                <ul class="list-group list-group-flush mt-3" style="max-height: 200px; overflow-y: auto;">
                                    <?php while($entry = $diary_entries->fetch_assoc()): ?>
                                    <li class="list-group-item bg-transparent border-secondary small"><strong><?= date('Y-m-d H:i', strtotime($entry['created_at'])) ?>:</strong><br><?= nl2br(e($entry['note_text'])) ?></li>
                                    <?php endwhile; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card p-3 mb-4">
                            <h5 class="mb-3">Punishment Details</h5>
                            <div class="mb-3"><label class="form-label">Punishment Law/Section</label><input type="text" class="form-control" name="punishment_section" value="<?= e($case['punishment_section']) ?>" placeholder="IPC Section 302 - Murder"></div>
                            <div class="mb-3"><label class="form-label">Punishment Description</label><textarea class="form-control" name="punishment_description" rows="2" placeholder="Enter sentence duration, fine amount, etc."><?= e($case['punishment_description']) ?></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">Update Case File</button>
                </div>
            </form>
        </main>
    </div>
</div>
<script>
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('adminSidebar').classList.toggle('active');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
// Theme toggle: persist preference in localStorage and apply by toggling `light-theme` on <html>
;(function(){
    const toggle = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');
    const htmlEl = document.documentElement;
    const wrapper = document.getElementById('themeToggleWrapper');

    function applyTheme(isLight){
        htmlEl.classList.add('disable-transitions');
        requestAnimationFrame(() => {
            if(isLight){
                htmlEl.classList.add('light-theme');
                htmlEl.setAttribute('data-bs-theme', 'light');
                if(icon) icon.className = 'bi bi-sun-fill';
                if(wrapper) wrapper.classList.remove('text-light');
                if(toggle) toggle.checked = true;
            } else {
                htmlEl.classList.remove('light-theme');
                htmlEl.setAttribute('data-bs-theme', 'dark');
                if(icon) icon.className = 'bi bi-moon-stars-fill';
                if(wrapper) wrapper.classList.add('text-light');
                if(toggle) toggle.checked = false;
            }
            void htmlEl.offsetWidth;
            setTimeout(() => htmlEl.classList.remove('disable-transitions'), 50);
        });
    }

    const stored = localStorage.getItem('js_theme');
    const useLight = stored ? (stored === 'light') : false;
    applyTheme(useLight);

    if(toggle){
        if(wrapper){
            if(useLight) wrapper.classList.remove('text-light'); else wrapper.classList.add('text-light');
        }
        toggle.addEventListener('change', function(){
            const nowLight = !!this.checked;
            localStorage.setItem('js_theme', nowLight ? 'light' : 'dark');
            applyTheme(nowLight);
        });
    }
})();
</script>

