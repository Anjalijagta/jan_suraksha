<?php
require_once __DIR__ . '/../config.php';

// --- PHP LOGIC ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if(empty($_SESSION['admin_id'])){ header('Location: index.php'); exit; }

// Handle filters/search
$q = trim($_GET['q'] ?? '');
$crime_type = trim($_GET['crime_type'] ?? '');
$status = trim($_GET['status'] ?? '');
$anonymous_filter = trim($_GET['anonymous_filter'] ?? '');

$where = [];
$types = '';
$params = [];

// This query correctly selects the complaint ID as 'id' and includes anonymous fields
$sql = 'SELECT c.id, c.complaint_code, c.complainant_name, c.crime_type, c.status, c.is_anonymous, c.anonymous_tracking_id FROM complaints c';

if($q){ 
    $where[] = '(c.complaint_code LIKE ? OR c.complainant_name LIKE ? OR c.anonymous_tracking_id LIKE ?)'; 
    $params[] = "%$q%"; 
    $params[] = "%$q%"; 
    $params[] = "%$q%"; 
    $types .= 'sss'; 
}
if($crime_type){ 
    $where[] = 'c.crime_type = ?'; 
    $params[] = $crime_type; 
    $types .= 's'; 
}
if($status){ 
    $where[] = 'c.status = ?'; 
    $params[] = $status; 
    $types .= 's'; 
}
if($anonymous_filter === 'anonymous'){ 
    $where[] = 'c.is_anonymous = 1'; 
}
if($anonymous_filter === 'regular'){ 
    $where[] = 'c.is_anonymous = 0'; 
}

if($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY c.created_at DESC LIMIT 200';

$stmt = $mysqli->prepare($sql);
if($params){ 
    $stmt->bind_param($types, ...$params); 
}
$stmt->execute(); 
$res = $stmt->get_result();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complaints - Admin Command Center</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bs-body-bg: #0d1117; --bs-body-color: #c9d1d9; --primary-dark: #161b22;
            --secondary-dark: #21262d; --border-color: #30363d;
        }
        /* Light theme overrides */
        html.light-theme {
            --bs-body-bg: #ffffff;
            --bs-body-color: #212529;
            --primary-dark: #ffffff;
            --secondary-dark: #f4f5f6ff;
            --border-color: #cdd3daff;
        }
        html.disable-transitions *, html.disable-transitions *::before, html.disable-transitions *::after { transition: none !important; -webkit-transition: none !important; }
        /* Ensure text-light becomes dark in light theme */
        html.light-theme .text-light { color: #212529 !important; }
        html:not(.light-theme) .text-light { color: #ffffff !important; }
        /* Theme toggle style */
        .theme-toggle { cursor: pointer; }
        .theme-toggle .bi { font-size: 1.05rem; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans", sans-serif; }
        .main-content { margin-left: 0; }
        @media (min-width: 992px) { .main-content { margin-left: 260px; } }
        /* Sidebar styles are now self-contained for simplicity */
        .sidebar {
            width: 260px; background-color: var(--primary-dark); border-right: 1px solid var(--border-color);
            position: fixed; top: 0; bottom: 0; left: 0;
            transform: translateX(-100%); transition: transform 0.3s ease-in-out; z-index: 1040;
        }
        @media (min-width: 992px) { .sidebar { transform: translateX(0); } }
        .sidebar.active { transform: translateX(0); }

        .sidebar-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .sidebar-nav .nav-link {
            color: #8b949e; padding: 0.75rem 1.5rem; display: flex; align-items: center; font-size: 1rem;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            background-color: var(--secondary-dark); color: #c9d1d9; border-radius: 6px;
        }
        .sidebar-nav .nav-link i { margin-right: 1rem; font-size: 1.2rem; }
        .topbar { background-color: var(--primary-dark); border-bottom: 1px solid var(--border-color); }
        .card { background-color: var(--primary-dark); border: 1px solid var(--border-color); }
        
        /* Page specific styles */
        .complaint-card {
            background-color: var(--primary-dark); border: 1px solid var(--border-color);
            border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .status-resolved, .status-closed { color: #28a745; }
        .status-pending { color: #ffc107; }
        .status-in-progress { color: #0dcaf0; }

        .action-icon { font-size: 1.5rem; color: #28a745; }
        .search-form .form-control, .search-form .form-select, .search-form .btn {
             background-color: var(--secondary-dark); border-color: var(--border-color); color: var(--bs-body-color);
        }
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
                <a class="nav-link active" href="view_complaints.php"><i class="bi bi-shield-check"></i> View Complaints</a>
                <a class="nav-link" href="criminals.php"><i class="bi bi-person-badge"></i> Criminals</a>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <header class="topbar p-3 sticky-top">
             <button class="btn btn-dark d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
             <div class="ms-auto d-flex align-items-center">
                 <div id="themeToggleWrapper" class="form-check form-switch theme-toggle text-light me-2" title="Toggle light / dark theme">
                     <input class="form-check-input" type="checkbox" id="themeToggle">
                     <label class="form-check-label mb-0 d-flex align-items-center" for="themeToggle"><i id="themeIcon" class="bi bi-moon-stars-fill"></i></label>
                 </div>
             </div>
        </header>

        <main class="p-4">
            <h3 class="mb-4">Complaints</h3>
            
            <div class="card p-3 mb-4">
                <form class="search-form" method="get">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="q" placeholder="Search by FIR ID or Complainant Name" value="<?= e($q) ?>">
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <select class="form-select" name="crime_type">
                            <option value="">All Crime Types</option>
                            <option <?= $crime_type == 'Theft' ? 'selected' : '' ?>>Theft</option>
                            <option <?= $crime_type == 'Assault' ? 'selected' : '' ?>>Assault</option>
                            <option <?= $crime_type == 'Cybercrime' ? 'selected' : '' ?>>Cybercrime</option>
                            <option <?= $crime_type == 'Fraud' ? 'selected' : '' ?>>Fraud</option>
                            <option <?= $crime_type == 'Harassment' ? 'selected' : '' ?>>Harassment</option>
                        </select>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option <?= $status == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option <?= $status == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                             <option <?= $status == 'Closed' ? 'selected' : '' ?>>Closed</option>
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
                <?php endwhile; ?>
                
                <?php if($res->num_rows === 0): ?>
                    <div class="text-center p-5 card"><p class="text-secondary">No complaints found.</p></div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('adminSidebar').classList.toggle('active');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>

