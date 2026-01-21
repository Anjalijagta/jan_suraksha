<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

// Handle filters/search
$q          = trim($_GET['q'] ?? '');
$crime_type = trim($_GET['crime_type'] ?? '');
$status = trim($_GET['status'] ?? '');
$anonymous_filter = trim($_GET['anonymous_filter'] ?? '');

$where  = [];
$params = [];
$types  = '';
// Phase 2: Include urgent fields and sort urgent complaints first
$sql = 'SELECT c.id, c.complaint_code, c.complainant_name, c.crime_type, c.status, c.is_anonymous, c.anonymous_tracking_id, 
           c.is_urgent, c.urgent_marked_at, c.created_at
           FROM complaints c';

if($q){ 
    $where[] = '(c.complaint_code LIKE ? OR c.complainant_name LIKE ? OR c.anonymous_tracking_id LIKE ?)'; 
    $params[] = "%$q%"; 
    $params[] = "%$q%"; 
    $params[] = "%$q%"; 
    $types .= 'sss'; 
}
if ($crime_type) {
    $where[]  = 'c.crime_type = ?';
    $params[] = $crime_type;
    $types   .= 's';
}
if ($status) {
    $where[]  = 'c.status = ?';
    $params[] = $status;
    $types   .= 's';
}
if($anonymous_filter === 'anonymous'){ 
    $where[] = 'c.is_anonymous = 1'; 
}
if($anonymous_filter === 'regular'){ 
    $where[] = 'c.is_anonymous = 0'; 
}

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
// Phase 2: Sort urgent complaints to top, then by creation date
$sql .= ' ORDER BY c.is_urgent DESC, c.created_at DESC LIMIT 50';

$stmt = $mysqli->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();

$current_page   = basename($_SERVER['PHP_SELF']);
$total_results  = $res->num_rows;
$active_filters = count(array_filter([$q, $crime_type, $status]));
?>

<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complaints - Jan Suraksha Command Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            /* Dark Theme */
            --bg-body: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            --bg-topbar: rgba(17, 24, 39, 0.95);
            --bg-card: linear-gradient(145deg, rgba(31,41,55,0.8), rgba(17,24,39,0.9));
            --bg-sidebar: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            --bg-sidebar-header: linear-gradient(135deg, #2563eb, #1d4ed8);
            --bg-surface: linear-gradient(145deg, rgba(31,41,55,0.8), rgba(17,24,39,0.9));
            --text-primary: #ffffff;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --border-subtle: rgba(55, 65, 81, 0.5);
            --sidebar-nav-bg: linear-gradient(135deg, rgba(37,99,235,0.2), rgba(29,78,216,0.3));
            --admin-btn-primary-bg: linear-gradient(135deg, #2563eb, #1d4ed8);
            --admin-btn-primary-hover: rgba(37,99,235,0.9);
            --admin-card-shadow: rgba(0,0,0,0.3);
        }

        [data-theme="light"] {
            /* Light Theme */
            --bg-body: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            --bg-topbar: rgba(255, 255, 255, 0.95);
            --bg-card: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.8));
            --bg-sidebar: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            --bg-sidebar-header: linear-gradient(135deg, #3b82f6, #2563eb);
            --bg-surface: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.8));
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #6b7280;
            --border-subtle: rgba(148, 163, 184, 0.3);
            --sidebar-nav-bg: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(37,99,235,0.15));
            --admin-btn-primary-bg: linear-gradient(135deg, #3b82f6, #2563eb);
            --admin-btn-primary-hover: rgba(59,130,246,0.9);
            --admin-card-shadow: rgba(0,0,0,0.1);
        }

        * { font-family: 'Inter', sans-serif; }
        body { 
            background: var(--bg-body);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Layout */
        .admin-shell { display: flex; min-height: 100vh; }
        .admin-sidebar { 
            width: 280px; 
            background: var(--bg-sidebar);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-subtle);
            box-shadow: 5px 0 25px var(--admin-card-shadow);
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .admin-sidebar-header { 
            background: var(--bg-sidebar-header); 
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar-nav .nav-link {
            color: var(--text-primary); 
            padding: 1rem 2rem; 
            margin: 0.25rem 1rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            font-weight: 500;
        }
        .admin-sidebar-nav .nav-link:hover, .admin-sidebar-nav .nav-link.active {
            background: var(--sidebar-nav-bg);
            color: var(--text-primary);
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(37,99,235,0.2);
        }
        .admin-sidebar-nav .nav-link i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            width: 24px;
            margin-right: 1rem;
        }
        .admin-sidebar-nav .nav-link.text-danger { color: #ef4444 !important; }
        .admin-sidebar-nav .nav-link.text-danger:hover {
            background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(220,38,38,0.3)) !important;
            color: #ef4444 !important;
        }

        /* Topbar */
        .admin-topbar { 
            background: var(--bg-topbar);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-subtle);
            box-shadow: 0 4px 20px var(--admin-card-shadow);
        }

        /* Cards & Surfaces */
        .admin-surface, .admin-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            backdrop-filter: blur(20px);
        }
        .admin-card {
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        .admin-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(37,99,235,0.2);
            border-color: rgba(37,99,235,0.3);
        }

        /* Buttons */
        .admin-btn {
            border-radius: 12px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(15px);
        }
        .admin-btn-primary {
            background: var(--admin-btn-primary-bg);
            color: white;
        }
        .admin-btn-primary:hover {
            background: var(--admin-btn-primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37,99,235,0.4);
        }
        .admin-btn-ghost {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
        }
        .admin-btn-ghost:hover {
            background: var(--sidebar-nav-bg);
            border-color: rgba(37,99,235,0.3);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        /* Pills & Badges */
        .admin-pill {
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        .admin-pill-priority-low { background: rgba(34,197,94,0.2); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
        .admin-pill-priority-medium { background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
        .admin-pill-priority-high { background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .admin-pill-status-pending { background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .admin-pill-status-in-progress { background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
        .admin-pill-status-resolved { background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .admin-pill-status-closed { background: rgba(34,197,94,0.2); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }

        /* Phase 2: Urgent Complaint Styles */
        .urgent-badge {
            background: linear-gradient(135deg, #d32f2f, #c62828);
            color: #ffffff;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            box-shadow: 0 4px 12px rgba(211,47,47,0.3);
            animation: urgentPulse 2s ease-in-out infinite;
        }
        .urgent-badge i {
            font-size: 0.875rem;
            animation: urgentShake 0.5s ease-in-out infinite;
        }
        @keyframes urgentPulse {
            0%, 100% { box-shadow: 0 4px 12px rgba(211,47,47,0.3); }
            50% { box-shadow: 0 4px 20px rgba(211,47,47,0.6); }
        }
        @keyframes urgentShake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }
        .urgent-complaint-row {
            background: linear-gradient(145deg, rgba(255,243,243,0.15), rgba(255,230,230,0.1)) !important;
            border-left: 4px solid #d32f2f !important;
            transition: all 0.3s ease;
        }
        .urgent-complaint-row:hover {
            background: linear-gradient(145deg, rgba(255,243,243,0.25), rgba(255,230,230,0.2)) !important;
            transform: translateX(4px);
            box-shadow: 0 8px 25px rgba(211,47,47,0.2);
        }
        [data-theme="light"] .urgent-complaint-row {
            background: linear-gradient(145deg, #fff3f3, #ffe6e6) !important;
        }
        [data-theme="light"] .urgent-complaint-row:hover {
            background: linear-gradient(145deg, #ffe6e6, #ffd6d6) !important;
        }

        /* Stats Bar */
        .admin-stats-bar {
            background: var(--bg-surface);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(20px);
        }

        /* Forms */
        .form-control, .form-select {
            background: rgba(255,255,255,0.1) !important;
            border: 1px solid var(--border-subtle) !important;
            color: var(--text-primary) !important;
            backdrop-filter: blur(10px);
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15) !important;
            border-color: rgba(37,99,235,0.5) !important;
            color: var(--text-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.2);
        }
        .form-control::placeholder { color: var(--text-secondary); }

        /* Theme Toggle */
        .theme-toggle {
            border: 2px solid var(--border-subtle) !important;
            color: var(--text-primary) !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 50px !important;
            font-size: 1.1rem !important;
            transition: all 0.3s ease !important;
            background: rgba(255,255,255,0.1) !important;
            backdrop-filter: blur(10px) !important;
        }
        .theme-toggle:hover {
            border-color: rgba(37,99,235,0.5) !important;
            background: rgba(37,99,235,0.1) !important;
            transform: scale(1.05);
        }
        [data-theme="dark"] .theme-toggle .sun-icon { display: inline-block !important; color: #fbbf24 !important; }
        [data-theme="dark"] .theme-toggle .moon-icon { display: none !important; }
        [data-theme="light"] .theme-toggle .sun-icon { display: none !important; }
        [data-theme="light"] .theme-toggle .moon-icon { display: inline-block !important; color: #0f172a !important; }

        /* Responsive */
        @media (max-width: 992px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.active { transform: translateX(0); }
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <!-- Sidebar -->
    <aside class="admin-sidebar vh-100" id="adminSidebar">
        <div class="admin-sidebar-header">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check fs-2 me-3 opacity-75"></i>
                <div>
                    <h4 class="mb-0 fw-bold">Jan Suraksha</h4>
                    <small class="opacity-75">Command Center</small>
                </div>
            </div>
        </div>
        <nav class="nav flex-column admin-sidebar-nav mt-4">
            <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a class="nav-link <?= $current_page === 'cases.php' ? 'active' : '' ?>" href="cases.php">
                <i class="bi bi-file-earmark-shield-fill"></i> Complaints
            </a>
            <a class="nav-link <?= $current_page === 'criminals.php' ? 'active' : '' ?>" href="criminals.php">
                <i class="bi bi-person-lines-fill"></i> Criminals
            </a>
            <a class="nav-link <?= $current_page === 'settings.php' ? 'active' : '' ?>" href="settings.php">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow-1">
        <!-- Topbar -->
        <header class="admin-topbar px-4 py-3 d-flex align-items-center">
            <button class="btn btn-outline-light btn-sm d-lg-none me-3" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="ms-auto d-flex align-items-center gap-3">
                <button id="theme-toggle" class="btn theme-toggle" aria-label="Toggle light/dark theme" title="Toggle Theme">
                    <i class="bi bi-sun-fill sun-icon"></i>
                    <i class="bi bi-moon-fill moon-icon"></i>
                </button>
                <div class="input-group input-group-sm flex-nowrap search-quick" style="max-width: 400px;">
                    <span class="input-group-text bg-transparent border-0 px-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control bg-transparent border-0 px-3" 
                           placeholder="Quick search..."
                           style="color: var(--text-primary);">
                </div>
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">Admin</span>
            </div>
        </header>

        <main class="p-5">
            <!-- Header + Stats -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-5">
                <div>
                    <h1 class="display-5 fw-bold mb-2" style="color: var(--text-primary);">Complaints Management</h1>
                    <p class="mb-0 lead" style="color: var(--text-secondary);">
                        Manage, filter, and update complaint cases
                    </p>
                </div>
                <div class="admin-stats-bar">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="text-secondary small">Total Results</div>
                            <div class="h5 fw-bold" style="color: var(--text-primary);"><?= $total_results ?></div>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary small">Filtered</div>
                            <div class="h5 fw-bold" style="color: #2563eb;"><?= $total_results ?></div>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary small">Active Filters</div>
                            <div class="h5 fw-bold" style="color: #10b981;"><?= $active_filters ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Search -->
            <div class="admin-surface mb-5 p-4">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label fw-semibold mb-2" style="color: var(--text-primary);">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-start-0 border-0 pe-0">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                            <input type="text"
                                   class="form-control ps-0"
                                   name="q"
                                   placeholder="FIR ID or name..."
                                   value="<?= htmlspecialchars($q) ?>">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-semibold mb-2" style="color: var(--text-primary);">Crime Type</label>
                        <select class="form-select" name="crime_type">
                            <option value="">All Types</option>
                            <option value="Theft"      <?= $crime_type === 'Theft' ? 'selected' : '' ?>>Theft</option>
                            <option value="Assault"    <?= $crime_type === 'Assault' ? 'selected' : '' ?>>Assault</option>
                            <option value="Harassment" <?= $crime_type === 'Harassment' ? 'selected' : '' ?>>Harassment</option>
                            <option value="Fraud"      <?= $crime_type === 'Fraud' ? 'selected' : '' ?>>Fraud</option>
                            <option value="Cybercrime" <?= $crime_type === 'Cybercrime' ? 'selected' : '' ?>>Cybercrime</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-semibold mb-2" style="color: var(--text-primary);">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="Pending"     <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Resolved"    <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="Closed"      <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-semibold mb-2" style="color: var(--text-primary);">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn admin-btn admin-btn-primary flex-fill px-4">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="cases.php" class="btn admin-btn admin-btn-ghost">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label fw-semibold mb-2" style="color: var(--text-primary);">Sort By</label>
                        <select class="form-select">
                            <option>Date (Newest)</option>
                            <option>Date (Oldest)</option>
                            <option>Priority (High)</option>
                            <option>Status</option>
                        </select>
                        <select class="form-select" name="anonymous_filter">
                            <option value="">All Complaints</option>
                            <option <?= $anonymous_filter == 'anonymous' ? 'selected' : '' ?> value="anonymous">Anonymous Only</option>
                            <option <?= $anonymous_filter == 'regular' ? 'selected' : '' ?> value="regular">Regular Only</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </form>
            </div>

            <div>
                <?php while($r = $res->fetch_assoc()): ?>
                    <div class="complaint-card">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <?php if($r['is_anonymous'] == 1): ?>
                                    <span class="badge bg-warning text-dark me-2">
                                        <i class="bi bi-shield-lock"></i> Anonymous
                                    </span>
                                <?php endif; ?>
                                <h5 class="mb-0">
                                    <?php 
                                    if($r['is_anonymous'] == 1 && !empty($r['anonymous_tracking_id'])) {
                                        echo 'ID: ' . e($r['anonymous_tracking_id']);
                                    } else {
                                        echo 'FIR/Complaint ID: ' . e($r['complaint_code']);
                                    }
                                    ?>
                                </h5>
                            </div>
                            <p class="mb-1 text-secondary">
                                Crime Type: <?= e($r['crime_type']) ?> <br>
                                Complainant: 
                                <?php 
                                if($r['is_anonymous'] == 1) {
                                    echo '<span class="text-muted fst-italic"><i class="bi bi-lock-fill"></i> Protected (Anonymous)</span>';
                                } else {
                                    echo e($r['complainant_name']);
                                }
                                ?>
                            </p>
                            <?php
                                $status_class = 'text-secondary'; // Default
                                $status_key = str_replace(' ', '-', strtolower($r['status']));
                                if ($status_key === 'resolved' || $status_key === 'closed') $status_class = 'status-resolved';
                                else if ($status_key === 'pending') $status_class = 'status-pending';
                                else if ($status_key === 'in-progress') $status_class = 'status-in-progress';
                            ?>
                            <strong class="<?= $status_class ?>">Status: <?= e($r['status']) ?></strong>
                        </div>
                        <div>
                            <!-- THE CRITICAL FIX IS HERE -->
                            <a href="update-case.php?id=<?= (int)$r['id'] ?>" class="btn btn-light btn-sm">Update Case</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php while ($r = $res->fetch_assoc()): ?>
                        <?php
                        $priority     = strtolower($r['priority'] ?? 'low');
                        $statusSlug   = str_replace(' ', '-', strtolower($r['status']));
                        $statusClass  = 'admin-pill-status-' . $statusSlug;
                        $isUrgent     = ($r['is_urgent'] == 1);
                        $cardClass    = $isUrgent ? 'admin-card urgent-complaint-row h-100 p-4' : 'admin-card h-100 p-4';
                        ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="<?= $cardClass ?>">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <?php if ($isUrgent): ?>
                                        <span class="urgent-badge mb-2" role="status" aria-label="Urgent complaint">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            URGENT
                                        </span>
                                        <?php endif; ?>
                                        <h5 class="mb-1 fw-bold" style="color: var(--text-primary);">
                                            <?php if ($r['is_anonymous'] == 1 && !empty($r['anonymous_tracking_id'])): ?>
                                                ID: <?= htmlspecialchars($r['anonymous_tracking_id']) ?>
                                            <?php else: ?>
                                                FIR #<?= htmlspecialchars($r['complaint_code']) ?>
                                            <?php endif; ?>
                                        </h5>
                                        <div class="admin-pill admin-pill-priority-<?= $priority ?>">
                                            <?= strtoupper($r['priority'] ?? 'LOW') ?> PRIORITY
                                        </div>
                                    </div>
                                    <div class="admin-pill <?= $statusClass ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="fw-semibold mb-1" style="color: var(--text-primary);">Crime Type</div>
                                    <span class="badge" style="background: rgba(107,114,128,0.2); color: var(--text-muted); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 500;">
                                        <?= htmlspecialchars($r['crime_type']) ?>
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <div class="fw-semibold mb-1" style="color: var(--text-primary);">Complainant</div>
                                    <span style="color: var(--text-secondary);">
                                        <?= htmlspecialchars($r['complainant_name']) ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-muted);">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= date('M d, Y H:i', strtotime($r['created_at'])) ?>
                                    </small>
                                    <div class="d-flex gap-2">
                                        <a href="view-complaint.php?id=<?= (int)$r['id'] ?>"
                                           class="btn admin-btn admin-btn-ghost btn-sm px-3">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="update-case.php?id=<?= (int)$r['id'] ?>"
                                           class="btn admin-btn admin-btn-primary btn-sm px-3">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_results > 20): ?>
                <div class="text-center mt-5">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" style="color: var(--text-primary); border-color: var(--border-subtle);">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#" style="color: var(--text-primary); background: var(--admin-btn-primary-bg); border-color: var(--admin-btn-primary-bg);">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" style="color: var(--text-primary); border-color: var(--border-subtle);">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" style="color: var(--text-primary); border-color: var(--border-subtle);">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('adminSidebar')?.classList.toggle('active');
});

// Perfect Theme Toggle (Same as Dashboard - No Refresh!)
(function() {
    const html = document.documentElement;
    const toggle = document.getElementById('theme-toggle');
    const isDark = localStorage.getItem('admin-theme') !== 'light';

    function applyTheme(dark) {
        html.setAttribute('data-theme', dark ? 'dark' : 'light');
        toggle.setAttribute('aria-pressed', dark);
        localStorage.setItem('admin-theme', dark ? 'dark' : 'light');
    }

    // Set initial theme
    applyTheme(isDark);

    // Toggle theme
    toggle.addEventListener('click', () => {
        const currentIsDark = html.getAttribute('data-theme') === 'dark';
        applyTheme(!currentIsDark);
    });
})();
</script>
</body>
</html>
