<?php
require_once __DIR__ . '/../config.php';
// --- PHP LOGIC ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if(empty($_SESSION['admin_id'])){ header('Location: index.php'); exit; }

$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$complaint_id) {
    // No complaint id — show a criminals index using the admin layout so the UI matches other pages.
    // Pagination setup
    $per_page = 12;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $per_page;

    // Get totals for pagination
    $total = (int) ($mysqli->query("SELECT COUNT(*) AS c FROM criminals")->fetch_assoc()['c'] ?? 0);
    $total_pages = $total > 0 ? (int) ceil($total / $per_page) : 1;

    // Fetch paginated criminals and join complaint to get tracking code and current complaint status
    $sql = "SELECT cr.*, c.complaint_code, c.status AS complaint_status FROM criminals cr LEFT JOIN complaints c ON c.id = cr.complaint_id ORDER BY cr.created_at DESC LIMIT " . (int)$offset . "," . (int)$per_page;
    $res = $mysqli->query($sql);
    $rows = [];
    if ($res) {
        while ($rr = $res->fetch_assoc()) { $rows[] = $rr; }
    }
    ?>
    <!doctype html>
    <html lang="en" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Criminals - Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            :root { --bs-body-bg: #0d1117; --bs-body-color: #c9d1d9; --primary-dark: #161b22; --secondary-dark: #21262d; --border-color: #30363d; }
            /* Light theme overrides */
            html.light-theme { --bs-body-bg: #ffffff;
            --bs-body-color: #212529;
            --primary-dark: #ffffff;
            --secondary-dark: #f4f5f6ff;
            --border-color: #9e9fa0ff; }
            html.disable-transitions *, html.disable-transitions *::before, html.disable-transitions *::after { transition: none !important; -webkit-transition: none !important; }
            /* Ensure text-light becomes dark in light theme */
            html.light-theme .text-light { color: #212529 !important; }
            html:not(.light-theme) .text-light { color: #ffffff !important; }
            .theme-toggle { cursor: pointer; }
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans", sans-serif; }
            .sidebar { width: 260px; background-color: var(--primary-dark); border-right: 1px solid var(--border-color); position: fixed; top: 0; bottom: 0; left: 0; transition: transform 0.3s ease-in-out; z-index: 1040; }
            .sidebar.active { transform: translateX(0); }
            /* Hide the fixed sidebar by default on small screens so offcanvas is primary */
            @media (max-width: 991px) {
                .sidebar { transform: translateX(-100%); }
            }
            .offcanvas-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1035; display:none; }
            .main-content { margin-left: 260px; transition: margin-left 0.3s ease-in-out; padding-bottom: 80px; }
            .card, .form-control, .form-select, table { background-color: var(--primary-dark); border: 1px solid var(--border-color); color: var(--bs-body-color); }
            .table thead th { color: #8b949e; border-bottom: 1px solid var(--border-color); }
            a { color: #58a6ff; }
            /* Mobile-first card layout */
            .criminal-card { border-radius: 12px; padding: 14px; display:block; width:100%; box-shadow: 0 6px 18px rgba(2,6,23,0.6); }
            .criminal-photo { width:56px; height:56px; border-radius:50%; object-fit:cover; flex-shrink:0; }
            @media (max-width: 420px) { .criminal-photo { width:48px; height:48px; } .criminal-card { padding:12px; } }
            .muted-small { color:#8b949e; font-size:0.9rem }
            @media(min-width:992px){
                /* On desktop show table, hide cards */
                .mobile-cards { display:none; }
            }
            @media(max-width:991px){
                /* On mobile hide desktop table */
                .desktop-table { display:none; }
                .main-content { margin-left:0; }
            }
            /* Ensure offcanvas overlay appears above other fixed elements */
            #adminNavOffcanvas, .offcanvas { z-index: 1100 !important; }
            /* make pagination compact on small screens */
            @media(max-width:576px){ .pagination { font-size:0.85rem; } .page-link { padding:0.35rem 0.6rem; } }
        </style>
    </head>
    <body>
    <div class="d-flex">
        <aside class="sidebar vh-100" id="adminSidebar">
            <div class="sidebar-header p-3"><h5 class="mb-0">Command Center</h5></div>
            <div class="p-3">
                <nav class="nav flex-column sidebar-nav">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
                    <a class="nav-link" href="cases.php"><i class="bi bi-shield-check"></i> View Complaints</a>
                    <a class="nav-link active" href="criminals.php"><i class="bi bi-person-badge"></i> Criminals</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>
        </aside>
    <div id="sidebarOverlay" class="offcanvas-overlay d-lg-none" aria-hidden="true"></div>

        <div class="main-content flex-grow-1">
            <header class="topbar p-3 sticky-top d-flex justify-content-between align-items-center">
                <button class="btn btn-dark d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <h5 class="mb-0">Criminals</h5>
                <div class="d-flex align-items-center">
                    <div id="themeToggleWrapper" class="form-check form-switch theme-toggle text-light me-2" title="Toggle light / dark theme">
                        <input class="form-check-input" type="checkbox" id="themeToggle">
                        <label class="form-check-label mb-0 d-flex align-items-center" for="themeToggle"><i id="themeIcon" class="bi bi-moon-stars-fill"></i></label>
                    </div>
                </div>
            </header>

            <main class="p-3">
                <div class="container-fluid">
                    <!-- Offcanvas mobile nav -->
                    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="adminNavOffcanvas" aria-labelledby="adminNavOffcanvasLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="adminNavOffcanvasLabel">Admin Menu</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <nav class="nav flex-column">
                                <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
                                <a class="nav-link" href="cases.php"><i class="bi bi-shield-check"></i> View Complaints</a>
                                <a class="nav-link active" href="criminals.php"><i class="bi bi-person-badge"></i>Criminals</a>
                                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                            </nav>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Manage Criminal Records</h4>
                        <div class="d-none d-lg-block">
                            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary border-secondary text-muted"><i class="bi bi-search"></i></span>
                            <input id="criminalSearch" type="search" class="form-control form-control-lg" placeholder="Search by name, alias or FIR ID" aria-label="Search criminals">
                        </div>
                    </div>

                    <!-- Mobile card list -->
                    <div class="mobile-cards">
                        <?php if (!empty($rows)): ?>
                            <div class="d-grid gap-3">
                                <?php foreach($rows as $r):
                                    $name = $r['full_name'];
                                    $alias = $r['aliases'] ?: '';
                                    $mug = !empty($r['mugshot']) ? '../uploads/mugshots/' . e($r['mugshot']) : 'https://placehold.co/84x84/21262d/c9d1d9?text=';
                                    $tracking = !empty($r['complaint_code']) ? $r['complaint_code'] : ($r['complaint_id'] ? (int)$r['complaint_id'] : '');
                                    $punishment = !empty($r['punishment_section']) ? $r['punishment_section'] : ($r['punishment_description'] ?? '');
                                    $status = !empty($r['complaint_status']) ? $r['complaint_status'] : ($r['current_status'] ?: 'Pending');
                                ?>
                                <a href="<?= !empty($r['complaint_id']) ? 'update-case.php?id='.(int)$r['complaint_id'] : 'criminals.php?id='.(int)$r['id'] ?>" class="d-flex align-items-center criminal-card" data-name="<?= htmlspecialchars(strtolower($name.' '.$alias), ENT_QUOTES) ?>" data-fir="<?= htmlspecialchars($tracking, ENT_QUOTES) ?>" data-status="<?= htmlspecialchars(strtolower($status), ENT_QUOTES) ?>">
                                    <img src="<?= $mug ?>" alt="Mugshot" class="criminal-photo me-3">
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold"><?= e($name) ?></div>
                                                <?php if (!empty($alias)): ?><div class="muted-small">Alias: <?= e($alias) ?></div><?php endif; ?>
                                                <?php if (!empty($r['dob'])): ?><div class="muted-small">DOB: <?= e($r['dob']) ?></div><?php endif; ?>
                                                <div class="muted-small">Punishment: <?= e($punishment ?: '-') ?></div>
                                            </div>
                                            <div class="text-end muted-small">
                                                <div>Tracking: <?= $tracking ? e($tracking) : '&mdash;' ?></div>
                                                <div class="mt-2"><span class="badge rounded-pill bg-<?= strtolower($status) === 'resolved' ? 'success' : (strtolower($status) === 'pending' ? 'warning' : 'secondary') ?>"><?= e($status) ?></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No criminals found.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Desktop table -->
                    <div class="desktop-table mt-3">
                        <div class="card p-3">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Photo</th>
                                                <th>Name</th>
                                                <th>FIR No</th>
                                                <th>Punishment (Law / Section)</th>
                                                <th>Status Update</th>
                                            </tr>
                                        </thead>
                                    <tbody id="criminalTableBody">
                                            <?php
                                            // Use the same $rows array for the desktop table
                                            $i = $offset;
                                            foreach($rows as $r): $i++;
                                                $mug = !empty($r['mugshot']) ? '../uploads/mugshots/' . e($r['mugshot']) : 'https://placehold.co/48x48/21262d/c9d1d9?text=';
                                                $punishment = !empty($r['punishment_section']) ? $r['punishment_section'] : ($r['punishment_description'] ?? '-');
                                                $status = !empty($r['complaint_status']) ? $r['complaint_status'] : ($r['current_status'] ?: 'Pending');
                                                $tracking = !empty($r['complaint_code']) ? $r['complaint_code'] : ($r['complaint_id'] ? (int)$r['complaint_id'] : '');
                                            ?>
                                            <tr data-name="<?= htmlspecialchars(strtolower($r['full_name'].' '.$r['aliases']), ENT_QUOTES) ?>" data-fir="<?= htmlspecialchars($tracking, ENT_QUOTES) ?>" data-status="<?= htmlspecialchars(strtolower($status), ENT_QUOTES) ?>">
                                                <td><?= $i ?></td>
                                                <td><img src="<?= $mug ?>" alt="mug" style="width:48px;height:48px;border-radius:8px;object-fit:cover;"></td>
                                                <td><a href="<?= !empty($r['complaint_id']) ? 'update-case.php?id='.(int)$r['complaint_id'] : 'criminals.php?id='.(int)$r['id'] ?>" class="text-decoration-none text-reset fw-semibold"><?= e($r['full_name']) ?></a></td>
                                                <td class="muted-small"><?= $tracking ? e($tracking) : '&mdash;' ?></td>
                                                <td class="muted-small"><?= e($punishment) ?></td>
                                                <td><span class="badge rounded-pill bg-<?= strtolower($status) === 'resolved' ? 'success' : (strtolower($status) === 'pending' ? 'warning' : 'secondary') ?>"><?= e($status) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-block d-lg-none mt-3 text-center">
                        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
                    </div>

                    <!-- Pagination controls -->
                    <nav aria-label="Criminals pagination" class="mt-3">
                        <ul class="pagination pagination-sm justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">&laquo; Prev</a></li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            for ($p = $start; $p <= $end; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a></li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next &raquo;</a></li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>

                <script>
                (function(){
                    const input = document.getElementById('criminalSearch');
                    if(!input) return;
                    input.addEventListener('input', function(){
                        const q = this.value.trim().toLowerCase();
                        // filter cards
                        document.querySelectorAll('.mobile-cards [data-name]').forEach(item => {
                            const name = item.getAttribute('data-name') || '';
                            const fir = (item.getAttribute('data-fir') || '');
                            const show = q === '' || name.indexOf(q) !== -1 || fir.indexOf(q) !== -1;
                            item.style.display = show ? '' : 'none';
                        });
                        // filter table rows
                        document.querySelectorAll('#criminalTableBody tr').forEach(row => {
                            const name = row.getAttribute('data-name') || '';
                            const fir = (row.getAttribute('data-fir') || '');
                            const show = q === '' || name.indexOf(q) !== -1 || fir.indexOf(q) !== -1;
                            row.style.display = show ? '' : 'none';
                        });
                    });
                })();
                </script>
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




    <?php
    exit;
}

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli->begin_transaction();
    try {
        // 1. Update Case Status & Officer
        $status = $_POST['status'];
        $officer = $_POST['assigned_officer'];
        $stmt1 = $mysqli->prepare("UPDATE complaints SET status = ?, assigned_officer = ? WHERE id = ?");
        $stmt1->bind_param('ssi', $status, $officer, $complaint_id);
        $stmt1->execute();

        // 2. Add New Diary Entry
        $diary_entry = trim($_POST['diary_entry'] ?? '');
        if (!empty($diary_entry)) {
            $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
            if ($admin_id) {
                $stmt2 = $mysqli->prepare("INSERT INTO case_diary (complaint_id, admin_id, note_text) VALUES (?, ?, ?)");
                $stmt2->bind_param('iis', $complaint_id, $admin_id, $diary_entry);
            } else {
                $stmt2 = $mysqli->prepare("INSERT INTO case_diary (complaint_id, note_text) VALUES (?, ?)");
                $stmt2->bind_param('is', $complaint_id, $diary_entry);
            }
            $stmt2->execute();
        }

        // 3. Update or Create Accused/Criminal Record
        $accused_id = (int)($_POST['accused_id'] ?? 0);
        $full_name = trim($_POST['full_name']);
        
        if (!empty($full_name)) {
            $father_name = $_POST['father_name'];
            $aliases = $_POST['aliases'];
            $dob = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $physical_desc = $_POST['physical_description'];
            $last_address = $_POST['last_known_address'];
            $punishment_law = $_POST['punishment_law'];
            $punishment_desc = $_POST['punishment_description'];
            $existing_mugshot = $_POST['existing_mugshot'] ?? '';
            $new_mugshot = $existing_mugshot;

            // Handle mugshot upload using secure helper (strict MIME and extension checks)
            if (isset($_FILES['mugshot']) && $_FILES['mugshot']['error'] !== UPLOAD_ERR_NO_FILE) {
                $mugshotFile = $_FILES['mugshot'];

                $allowedMugshotTypes = [
                    'jpg'  => ['image/jpeg', 'image/pjpeg'],
                    'jpeg' => ['image/jpeg', 'image/pjpeg'],
                    'png'  => ['image/png'],
                ];

                $maxMugshotSize = 5 * 1024 * 1024; // 5MB
                $uploadError = null;
                $destDir = __DIR__ . '/../uploads/mugshots';

                $storedName = js_secure_upload($mugshotFile, $allowedMugshotTypes, $destDir, $maxMugshotSize, $uploadError, 'mugshot');

                if ($uploadError !== null) {
                    throw new Exception($uploadError . ' Mugshots must be JPG or PNG under 5MB.');
                }

                $new_mugshot = $storedName;
            }

            // CORRECTED LOGIC: Execute UPDATE or INSERT within the correct block
            if ($accused_id > 0) { // Update existing criminal
                $stmt3 = $mysqli->prepare("UPDATE criminals SET full_name=?, fathers_name=?, aliases=?, dob=?, physical_description=?, last_known_address=?, punishment_section=?, punishment_description=?, mugshot=? WHERE id=?");
                $stmt3->bind_param('sssssssssi', $full_name, $father_name, $aliases, $dob, $physical_desc, $last_address, $punishment_law, $punishment_desc, $new_mugshot, $accused_id);
                $stmt3->execute();
            } else { // Create new criminal and link to complaint
                $stmt3 = $mysqli->prepare("INSERT INTO criminals (full_name, fathers_name, aliases, dob, physical_description, last_known_address, punishment_section, punishment_description, mugshot, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt3->bind_param('sssssssss', $full_name, $father_name, $aliases, $dob, $physical_desc, $last_address, $punishment_law, $punishment_desc, $new_mugshot);
                $stmt3->execute();
                $new_accused_id = $mysqli->insert_id;
                
                $stmt4 = $mysqli->prepare("UPDATE complaints SET accused_id = ? WHERE id = ?");
                $stmt4->bind_param('ii', $new_accused_id, $complaint_id);
                $stmt4->execute();
            }
        }
        
        $mysqli->commit();
        $success_msg = "Case file updated successfully!";
    } catch (Exception $e) {
        $mysqli->rollback();
        $error_msg = "Error updating case: " . $e->getMessage();
    }
}

// Fetch all data for the page
$stmt = $mysqli->prepare("
    SELECT c.*, 
        cr.full_name AS full_name, 
        cr.fathers_name AS father_name, 
        cr.aliases AS aliases, 
        cr.dob AS date_of_birth, 
        cr.physical_description AS physical_description, 
        cr.last_known_address AS last_known_address, 
        cr.punishment_section AS punishment_section, 
        cr.punishment_description AS punishment_description, 
        cr.mugshot AS avatar
    FROM complaints c
    LEFT JOIN criminals cr ON c.accused_id = cr.id
    WHERE c.id = ?
");
    $stmt->bind_param('i', $complaint_id);
    $stmt->execute();
$case = $stmt->get_result()->fetch_assoc();

if (!$case) { die("Error: Case not found."); }

// Fetch diary entries
$diary_entries = $mysqli->query("SELECT * FROM case_diary WHERE complaint_id = $complaint_id ORDER BY created_at DESC");

$current_page = 'view_complaints.php';
?>
