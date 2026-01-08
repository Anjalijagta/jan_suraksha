<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';

// --- PHP LOGIC ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

$criminal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$criminal_id) {
    // LIST VIEW
    $per_page = 12;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $per_page;

    // Get totals for pagination - Using Prepared Statement
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM criminals");
    $stmt->execute();
    $total = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
    $total_pages = $total > 0 ? (int) ceil($total / $per_page) : 1;

    // Fetch paginated criminals and join complaint to get tracking code and current complaint status - Using Prepared Statement
    $stmt = $mysqli->prepare("SELECT cr.*, c.complaint_code, c.status AS complaint_status 
            FROM criminals cr 
            LEFT JOIN complaints c ON c.accused_id = cr.id 
            ORDER BY cr.created_at DESC LIMIT ?, ?");
    $offset_int = (int)$offset;
    $per_page_int = (int)$per_page;
    $stmt->bind_param('ii', $offset_int, $per_page_int);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    if ($res) {
        while ($rr = $res->fetch_assoc()) { $rows[] = $rr; }
    }
} else {
    // DETAIL VIEW
    $success_msg = $error_msg = '';

    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mysqli->begin_transaction();
        try {
            $full_name = trim($_POST['full_name']);
            $father_name = trim($_POST['father_name'] ?? '');
            $aliases = trim($_POST['aliases'] ?? '');
            $dob = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $physical_desc = trim($_POST['physical_description'] ?? '');
            $last_address = trim($_POST['last_known_address'] ?? '');
            $punishment_law = trim($_POST['punishment_law'] ?? '');
            $punishment_desc = trim($_POST['punishment_description'] ?? '');
            $current_status = trim($_POST['current_status'] ?? 'Pending');
            $complaint_id = !empty($_POST['complaint_id']) ? (int)$_POST['complaint_id'] : null;

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

                $storedName = js_secure_upload(
                    $mugshotFile,
                    $allowedMugshotTypes,
                    $destDir,
                    $maxMugshotSize,
                    $uploadError,
                    'mugshot'
                );

                if ($uploadError !== null) {
                    throw new Exception($uploadError . ' Mugshots must be JPG or PNG under 5MB.');
                }

                // Optionally delete old mugshot if it exists
                if (!empty($existing_mugshot) && file_exists($destDir . '/' . $existing_mugshot)) {
                    @unlink($destDir . '/' . $existing_mugshot);
                }

                $new_mugshot = $storedName;
            }

            // Update criminal
            $stmt = $mysqli->prepare("UPDATE criminals SET full_name=?, fathers_name=?, aliases=?, dob=?, physical_description=?, last_known_address=?, punishment_section=?, punishment_description=?, mugshot=?, current_status=? WHERE id=?");
            $stmt->bind_param('ssssssssssi', $full_name, $father_name, $aliases, $dob, $physical_desc, $last_address, $punishment_law, $punishment_desc, $new_mugshot, $current_status, $criminal_id);
            $stmt->execute();

            // Link to complaint if provided
            if ($complaint_id) {
                $stmt2 = $mysqli->prepare("UPDATE complaints SET accused_id = ? WHERE id = ?");
                $stmt2->bind_param('ii', $criminal_id, $complaint_id);
                $stmt2->execute();
            }

            $mysqli->commit();
            $success_msg = "Criminal record updated successfully!";
        } catch (Exception $e) {
            $mysqli->rollback();
            $error_msg = "Error updating record: " . $e->getMessage();
        }
    }

    // Fetch criminal data
    $stmt = $mysqli->prepare("SELECT cr.*, c.id as complaint_id, c.complaint_code FROM criminals cr LEFT JOIN complaints c ON cr.id = c.accused_id WHERE cr.id = ?");
    $stmt->bind_param('i', $criminal_id);
    $stmt->execute();
    $criminal = $stmt->get_result()->fetch_assoc();

    if (!$criminal) { 
        die("Error: Criminal record not found."); 
    }

    // Fetch diary entries if linked
    $diary_entries = [];
    if ($criminal['complaint_id']) {
        $diary_res = $mysqli->query("SELECT * FROM case_diary WHERE complaint_id = " . (int)$criminal['complaint_id'] . " ORDER BY created_at DESC LIMIT 10");
        $diary_entries = $diary_res ? $diary_res->fetch_all(MYSQLI_ASSOC) : [];
    }
}

$current_page = 'criminals.php';
?>

<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $criminal_id ? "Criminal #{$criminal_id}" : 'Criminals' ?> - Jan Suraksha Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --glass-bg: rgba(37, 99, 235, 0.1);
            --glass-border: rgba(37, 99, 235, 0.2);
            /* Dark Theme */
            --bg-body: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            --bg-topbar: rgba(17, 24, 39, 0.95);
            --bg-card: linear-gradient(145deg, rgba(31,41,55,0.8), rgba(17,24,39,0.9));
            --bg-sidebar: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            --bg-sidebar-header: linear-gradient(135deg, #2563eb, #1d4ed8);
            --text-primary: #ffffff;
            --text-secondary: #9ca3af;
            --border-subtle: rgba(55, 65, 81, 0.5);
            --sidebar-nav-bg: linear-gradient(135deg, rgba(37,99,235,0.2), rgba(29,78,216,0.3));
        }

        [data-theme="light"] {
            --bg-body: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            --bg-topbar: rgba(255, 255, 255, 0.95);
            --bg-card: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.8));
            --bg-sidebar: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            --bg-sidebar-header: linear-gradient(135deg, #3b82f6, #2563eb);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-subtle: rgba(148, 163, 184, 0.3);
            --sidebar-nav-bg: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(37,99,235,0.15));
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: var(--bg-body); min-height: 100vh; transition: all 0.3s ease; overflow-x: hidden; }

        /* Sidebar */
        .sidebar {
            width: 280px; max-width: 280px; min-height: 100vh; background: var(--bg-sidebar);
            backdrop-filter: blur(20px); border-right: 1px solid var(--border-subtle);
            box-shadow: 5px 0 25px rgba(0,0,0,0.1); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            z-index: 1040; position: fixed; top: 0; left: 0;
        }
        .sidebar-header { background: var(--bg-sidebar-header); color: white; padding: 1.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-nav .nav-link {
            color: var(--text-primary); padding: 0.875rem 1.5rem; margin: 0.125rem 1rem;
            border-radius: 12px; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); font-weight: 500;
            display: flex; align-items: center; text-decoration: none;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            background: var(--sidebar-nav-bg); color: var(--text-primary); transform: translateX(4px);
            box-shadow: 0 8px 25px rgba(37,99,235,0.2);
        }
        .sidebar-nav .nav-link i {
            background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            width: 24px; margin-right: 0.75rem; flex-shrink: 0;
        }
        .sidebar-nav .nav-link.text-danger { color: #ef4444 !important; }
        .sidebar-nav .nav-link.text-danger:hover { background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(220,38,38,0.3)) !important; color: #ef4444 !important; }

        /* Main Content */
        .main-content { margin-left: 280px; min-width: 0; flex: 1; transition: all 0.3s ease; }

        /* Topbar */
        .topbar { 
            background: var(--bg-topbar); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-subtle);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; padding: 0.75rem 1.5rem;
            z-index: 1030; position: sticky; top: 0;
        }

        /* Cards */
        .criminal-card {
            background: var(--bg-card); backdrop-filter: blur(20px); border: 1px solid rgba(37,99,235,0.3);
            border-radius: 20px; overflow: hidden; color: var(--text-primary);
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); height: 100%; display: flex; flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .criminal-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .criminal-card .card-header {
            background: linear-gradient(135deg, rgba(37,99,235,0.15), rgba(29,78,216,0.1));
            border-bottom: 1px solid var(--border-subtle); color: var(--text-primary); padding: 1.25rem 1.5rem; margin-bottom: 0;
        }
        .criminal-card .card-body { padding: 2rem; flex: 1; display: flex; flex-direction: column; }

        /* Criminal Photo */
        .criminal-photo { width: 120px; height: 120px; border-radius: 16px; object-fit: cover;
            border: 3px solid rgba(37,99,235,0.3); transition: all 0.3s ease; flex-shrink: 0; }
        .criminal-photo:hover { transform: scale(1.05); border-color: rgba(37,99,235,0.6); }
        .criminal-photo-lg { width: 140px; height: 140px; }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;
            backdrop-filter: blur(10px);
        }
        .status-pending { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .status-progress { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .status-resolved { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }

        /* Forms */
        .form-control, .form-select {
            background: rgba(255,255,255,0.1); border: 1px solid var(--border-subtle); color: var(--text-primary);
            backdrop-filter: blur(10px); border-radius: 12px; padding: 0.875rem 1rem; min-height: 52px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15); border-color: rgba(37,99,235,0.5);
            box-shadow: 0 0 0 0.25rem rgba(37,99,235,0.1); color: var(--text-primary);
        }
        .form-label { color: var(--text-secondary); font-weight: 500; margin-bottom: 0.5rem; }

        /* Theme Toggle */
        .theme-toggle {
            border: 2px solid var(--border-subtle) !important; color: var(--text-primary) !important;
            padding: 0.5rem 0.75rem !important; border-radius: 50px !important; font-size: 1.1rem !important;
            transition: all 0.3s ease !important; background: rgba(255,255,255,0.1) !important;
            backdrop-filter: blur(10px) !important; flex-shrink: 0;
        }
        .theme-toggle:hover { border-color: rgba(37,99,235,0.5) !important; background: rgba(37,99,235,0.1) !important; transform: scale(1.05); }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0 !important; }
            main { padding: 1.5rem 1rem !important; }
        }
        .sidebar.active { transform: translateX(0) !important; }

        /* Pagination */
        .pagination .page-link {
            background: var(--bg-card); border: 1px solid var(--border-subtle); color: var(--text-primary);
            border-radius: 10px; margin: 0 2px; padding: 0.5rem 0.875rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary-gradient); border-color: transparent; color: white;
        }

        /* Search */
        .search-input { background: rgba(255,255,255,0.1); border: 1px solid var(--border-subtle); border-radius: 12px; }

        /* Detail Page Specific */
        .diary-entry {
            background: rgba(37,99,235,0.1); border-left: 4px solid #2563eb; padding: 1.25rem;
            border-radius: 0 12px 12px 0; margin-bottom: 1rem; backdrop-filter: blur(10px);
        }
        .diary-entry:hover { background: rgba(37,99,235,0.15); }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar vh-100 position-fixed" id="adminSidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check fs-3 me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <div>
                    <h4 class="mb-0 fw-bold">Jan Suraksha</h4>
                    <small class="opacity-75">Command Center</small>
                </div>
            </div>
        </div>
        <nav class="nav flex-column sidebar-nav mt-4">
            <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a class="nav-link" href="cases.php">
                <i class="bi bi-file-earmark-shield-fill"></i> Complaints
            </a>
            <a class="nav-link active" href="criminals.php">
                <i class="bi bi-person-lines-fill"></i> Criminals
            </a>
            <a class="nav-link" href="settings.php">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow-1 main-content">
        <!-- Topbar -->
        <header class="topbar px-4 py-3">
            <button class="btn btn-outline-light btn-sm d-lg-none me-3" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="d-flex align-items-center ms-auto">
                <button id="theme-toggle" class="btn theme-toggle me-3" aria-label="Toggle light/dark theme" title="Toggle Theme">
                    <i class="bi bi-sun-fill sun-icon"></i>
                    <i class="bi bi-moon-fill moon-icon"></i>
                </button>
                <span class="badge bg-primary fs-6 me-2">Admin</span>
                <div class="dropdown ms-3">
                    <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="color: var(--text-primary) !important;">
                        <i class="bi bi-person-circle fs-5 me-2"></i>
                        <span class="fw-semibold"><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="background: var(--bg-card); border: 1px solid var(--border-subtle); color: var(--text-primary);">
                        <li><a class="dropdown-item" href="profile.php" style="color: var(--text-primary);"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color: var(--border-subtle);"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-5">
            <div class="container-fluid">
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-5" role="alert" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; border-radius: 16px;">
                        <?= htmlspecialchars($success_msg) ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-radius: 16px;">
                        <?= htmlspecialchars($error_msg) ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!$criminal_id): ?>
                    <!-- LIST VIEW -->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h2 class="mb-0 fw-bold" style="color: var(--text-primary);">
                            <i class="bi bi-person-lines-fill me-2" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                            Criminals List (<?= count($rows) ?> of <?= $total ?>)
                        </h2>
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group search-input" style="max-width: 400px;">
                                <span class="input-group-text bg-transparent border-0" style="background: transparent !important; border: none !important;"><i class="bi bi-search text-secondary"></i></span>
                                <input id="criminalSearch" type="search" class="form-control border-0 ps-0" placeholder="Search by name, alias or FIR..." style="background: transparent !important; border: none !important;">
                            </div>
                            <a href="cases.php" class="btn btn-primary px-4">
                                <i class="bi bi-plus-circle me-2"></i>New Case
                            </a>
                        </div>
                    </div>

                    <?php if (!empty($rows)): ?>
                        <div class="row g-4">
                            <?php foreach($rows as $r): 
                                $status = !empty($r['complaint_status']) ? $r['complaint_status'] : ($r['current_status'] ?: 'Pending');
                                $tracking = !empty($r['complaint_code']) ? $r['complaint_code'] : '';
                                $mug = !empty($r['mugshot']) ? '../uploads/mugshots/' . htmlspecialchars($r['mugshot']) : 'https://placehold.co/120x120/21262d/c9d1d9?text=No+Photo';
                            ?>
                                <div class="col-xl-3 col-lg-4 col-md-6" data-name="<?= htmlspecialchars(strtolower($r['full_name'].' '.$r['aliases']), ENT_QUOTES) ?>" data-fir="<?= htmlspecialchars($tracking, ENT_QUOTES) ?>">
                                    <a href="criminals.php?id=<?= $r['id'] ?>" class="criminal-card text-decoration-none">
                                        <div class="card-body text-center">
                                            <img src="<?= $mug ?>" alt="Mugshot" class="criminal-photo mx-auto mb-3 shadow-lg">
                                            <h5 class="fw-bold mb-3" style="color: var(--text-primary); font-size: 1.125rem;"><?= htmlspecialchars($r['full_name']) ?></h5>
                                            <?php if ($r['aliases']): ?>
                                                <p class="text-secondary mb-4 small"><?= htmlspecialchars($r['aliases']) ?></p>
                                            <?php endif; ?>
                                            <div class="mb-4">
                                                <span class="status-badge status-<?= strtolower($status) === 'resolved' ? 'resolved' : (strtolower($status) === 'pending' ? 'pending' : 'progress') ?>">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </div>
                                            <div class="text-muted small">
                                                <?= $tracking ? 'FIR: ' . htmlspecialchars($tracking) : 'No Case Assigned' ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-5" aria-label="Criminal pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>"><i class="bi bi-chevron-left"></i></a></li>
                                    <?php endif; ?>

                                    <?php $start = max(1, $page - 2); $end = min($total_pages, $page + 2);
                                    for ($p = $start; $p <= $end; $p++): ?>
                                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>"><i class="bi bi-chevron-right"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-8" style="padding: 4rem 2rem;">
                            <i class="bi bi-people fs-1 text-muted mb-4" style="opacity: 0.5;"></i>
                            <h4 class="text-muted mb-3" style="color: var(--text-secondary);">No criminals found</h4>
                            <p class="text-muted mb-4" style="color: var(--text-secondary);">Create cases to add criminal records.</p>
                            <a href="cases.php" class="btn btn-primary px-5 py-2 fs-5">Add First Criminal</a>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- DETAIL VIEW -->
                    <div class="row g-4 mb-5">
                        <div class="col-xl-8">
                            <div class="criminal-card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                        <i class="bi bi-person-badge-fill me-2" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        Criminal Details 
                                        <span class="badge bg-primary ms-2">#<?= str_pad($criminal_id, 6, '0', STR_PAD_LEFT) ?></span>
                                    </h5>
                                    <div>
                                        <a href="criminals.php" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="bi bi-arrow-left me-1"></i>Back
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row g-4 mb-4">
                                            <div class="col-md-4 text-center">
                                                <img src="<?= !empty($criminal['mugshot']) ? '../uploads/mugshots/' . htmlspecialchars($criminal['mugshot']) : 'https://placehold.co/140x140/21262d/c9d1d9?text=No+Photo' ?>" 
                                                     alt="Mugshot" class="criminal-photo-lg mx-auto mb-3 shadow-lg">
                                                <input type="file" class="form-control form-control-sm" name="mugshot" accept="image/jpeg,image/png">
                                                <input type="hidden" name="existing_mugshot" value="<?= htmlspecialchars($criminal['mugshot'] ?? '') ?>">
                                                <div class="form-text text-muted">JPG/PNG, max 5MB</div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Full Name *</label>
                                                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($criminal['full_name'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: var(--text-secondary);">Father's Name</label>
                                                        <input type="text" class="form-control" name="father_name" value="<?= htmlspecialchars($criminal['fathers_name'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: var(--text-secondary);">Aliases</label>
                                                        <input type="text" class="form-control" name="aliases" value="<?= htmlspecialchars($criminal['aliases'] ?? '') ?>" placeholder="Comma-separated">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: var(--text-secondary);">Date of Birth</label>
                                                        <input type="date" class="form-control" name="date_of_birth" value="<?= htmlspecialchars($criminal['dob'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Status</label>
                                                        <select class="form-select" name="current_status">
                                                            <option value="Pending" <?= strtolower($criminal['current_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="Investigation" <?= strtolower($criminal['current_status'] ?? '') === 'investigation' ? 'selected' : '' ?>>Investigation</option>
                                                            <option value="Arrested" <?= strtolower($criminal['current_status'] ?? '') === 'arrested' ? 'selected' : '' ?>>Arrested</option>
                                                            <option value="Resolved" <?= strtolower($criminal['current_status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold" style="color: var(--text-primary);">Physical Description</label>
                                                <textarea class="form-control" name="physical_description" rows="3"><?= htmlspecialchars($criminal['physical_description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold" style="color: var(--text-primary);">Last Known Address</label>
                                                <textarea class="form-control" name="last_known_address" rows="3"><?= htmlspecialchars($criminal['last_known_address'] ?? '') ?></textarea>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-5">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold" style="color: var(--text-primary);">Punishment Law/Section</label>
                                                <input type="text" class="form-control" name="punishment_law" value="<?= htmlspecialchars($criminal['punishment_section'] ?? '') ?>" placeholder="IPC 302, etc.">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold" style="color: var(--text-primary);">Punishment Description</label>
                                                <input type="text" class="form-control" name="punishment_description" value="<?= htmlspecialchars($criminal['punishment_description'] ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" style="color: var(--text-secondary);">Link Case (FIR ID)</label>
                                                <input type="number" class="form-control" name="complaint_id" value="<?= htmlspecialchars($criminal['complaint_id'] ?? '') ?>" placeholder="Enter complaint ID">
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary px-5 me-md-2">
                                                <i class="bi bi-check-circle me-2"></i>Update Record
                                            </button>
                                            <a href="criminals.php" class="btn btn-outline-secondary px-5">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="criminal-card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                                        <i class="bi bi-person-circle me-2" style="color: #10b981;"></i>Criminal Profile
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="text-center mb-4">
                                        <div class="status-badge status-<?= strtolower($criminal['current_status'] ?? 'pending') === 'resolved' ? 'resolved' : (strtolower($criminal['current_status'] ?? 'pending') === 'pending' ? 'pending' : 'progress') ?> mb-3 d-inline-block">
                                            <?= htmlspecialchars($criminal['current_status'] ?? 'Pending') ?>
                                        </div>
                                        <?php if ($criminal['complaint_code']): ?>
                                            <div class="badge bg-primary fs-6"><?= htmlspecialchars($criminal['complaint_code']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted mb-3">
                                        <strong>ID:</strong> #<?= str_pad($criminal_id, 6, '0', STR_PAD_LEFT) ?><br>
                                        <?php if ($criminal['dob']): ?>
                                            <strong>DOB:</strong> <?= date('M j, Y', strtotime($criminal['dob'])) ?><br>
                                        <?php endif; ?>
                                        <strong>Added:</strong> <?= date('M j, Y', strtotime($criminal['created_at'])) ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($diary_entries)): ?>
                            <div class="criminal-card h-100 mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                                        <i class="bi bi-journal-text me-2" style="color: #10b981;"></i>Recent Diary (<?= count($diary_entries) ?>)
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-4" style="max-height: 300px; overflow-y: auto;">
                                        <?php foreach(array_slice($diary_entries, 0, 5) as $entry): ?>
                                            <div class="diary-entry mb-3">
                                                <div><?= nl2br(htmlspecialchars($entry['note_text'])) ?></div>
                                                <div class="text-secondary small mt-1"><?= date('M j, Y H:i', strtotime($entry['created_at'])) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('adminSidebar').classList.toggle('active');
});

// Theme Toggle - EXACT same as dashboard
(function() {
    const html = document.documentElement;
    const toggle = document.getElementById('theme-toggle');
    const isDark = localStorage.getItem('admin-theme') !== 'light';

    function applyTheme(dark) {
        html.setAttribute('data-theme', dark ? 'dark' : 'light');
        toggle.setAttribute('aria-pressed', dark);
        localStorage.setItem('admin-theme', dark ? 'dark' : 'light');
    }

    applyTheme(isDark);

    toggle.addEventListener('click', () => {
        const currentIsDark = html.getAttribute('data-theme') === 'dark';
        applyTheme(!currentIsDark);
    });
})();

// Search functionality for list view
if (document.getElementById('criminalSearch')) {
    document.getElementById('criminalSearch').addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('[data-name]').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const fir = item.getAttribute('data-fir') || '';
            const show = q === '' || name.includes(q) || fir.includes(q);
            item.style.display = show ? '' : 'none';
        });
    });
}
</script>
</body>
</html>