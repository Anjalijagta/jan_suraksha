<?php
require_once __DIR__ . '/../config.php';

// --- PHP Data Fetching ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(empty($_SESSION['admin_id'])){ header('Location: index.php'); exit; }

// safe query helper: returns mysqli_result or false and logs DB errors
function run_query($sql){
    global $mysqli;
    $res = $mysqli->query($sql);
    if($res === false){
        error_log("DB query failed: " . $mysqli->error . " -- SQL: " . $sql);
        return false;
    }
    return $res;
}

// Determine the active page to highlight the link in the sidebar
$current_page = basename($_SERVER['PHP_SELF']);

// 1. Top-Level Metrics
$res = run_query("SELECT COUNT(*) AS c FROM complaints");
$total = ($res) ? ($res->fetch_assoc()['c'] ?? 0) : 0;
$res = run_query("SELECT COUNT(*) AS c FROM complaints WHERE status='Pending'");
$pending = ($res) ? ($res->fetch_assoc()['c'] ?? 0) : 0;
$res = run_query("SELECT COUNT(*) AS c FROM complaints WHERE status='In Progress'");
$investigating = ($res) ? ($res->fetch_assoc()['c'] ?? 0) : 0;
$res = run_query("SELECT COUNT(*) AS c FROM complaints WHERE status='Resolved'");
$resolved = ($res) ? ($res->fetch_assoc()['c'] ?? 0) : 0;

// 2. State-wise Crime Hotspots
$state_hotspots_query = run_query("SELECT state, COUNT(*) as count FROM complaints WHERE state IS NOT NULL AND state != '' GROUP BY state ORDER BY count DESC LIMIT 4");
$state_hotspots = [];
$res = run_query("SELECT COUNT(*) as c FROM complaints WHERE state IS NOT NULL AND state != ''");
$total_state_complaints = ($res) ? ($res->fetch_assoc()['c'] ?? 0) : 0;
$state_hotspots_query = run_query("SELECT state, COUNT(*) as count FROM complaints WHERE state IS NOT NULL AND state != '' GROUP BY state ORDER BY count DESC LIMIT 4");

// 3. Daily Reports (Last 30 days)
$daily_reports_query = run_query("SELECT DATE(created_at) as report_date, COUNT(*) as count FROM complaints WHERE created_at >= CURDATE() - INTERVAL 30 DAY GROUP BY report_date ORDER BY report_date ASC");
$daily_labels = []; $daily_data = [];
if($daily_reports_query){
    while($row = $daily_reports_query->fetch_assoc()){
        $daily_labels[] = date('M d', strtotime($row['report_date']));
        $daily_data[] = $row['count'];
    }
}

// 4. Crime Category Breakdown
$category_query = run_query("SELECT crime_type, COUNT(*) as count FROM complaints GROUP BY crime_type ORDER BY count DESC LIMIT 5");
$category_labels = []; $category_data = [];
if($category_query){
    while($row = $category_query->fetch_assoc()){
        $category_labels[] = $row['crime_type'];
        $category_data[] = $row['count'];
    }
}

// 5. Recent Activity Feed
$recent_activity = run_query("SELECT complaint_code, crime_type, created_at FROM complaints ORDER BY created_at DESC LIMIT 5");
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Jan Suraksha</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bs-body-bg: #0d1117;
            --bs-body-color: #c9d1d9;
            --primary-dark: #161b22;
            --secondary-dark: #21262d;
            --border-color: #30363d;
        }
        /* Light theme override when class 'light-theme' is applied to <html> */
        html.light-theme {
            --bs-body-bg: #ffffff;
            --bs-body-color: #212529;
            --primary-dark: #ffffff;
            --secondary-dark: #f4f5f6ff;
            --border-color: #d4dbe2ff;
        }
        /* Temporarily disable transitions when switching theme to avoid faded effect */
        html.disable-transitions *, html.disable-transitions *::before, html.disable-transitions *::after {
            transition: none !important;
            -webkit-transition: none !important;
        }
        /* Theme toggle styles */
        .theme-toggle { cursor: pointer; }
        .theme-toggle .bi { font-size: 1.05rem; }

        /* Light-theme detailed overrides to improve contrast and readability */
        html.light-theme body { background-color: var(--bs-body-bg); color: var(--bs-body-color); }
        html.light-theme .sidebar { background-color: var(--primary-dark); border-right-color: var(--border-color); }
        html.light-theme .topbar { background-color: var(--primary-dark); border-bottom-color: var(--border-color); }
        html.light-theme .card { background-color: var(--secondary-dark); color: var(--bs-body-color); border-color: var(--border-color); }
        html.light-theme .sidebar-nav .nav-link { color: #495057; }
        html.light-theme .sidebar-nav .nav-link:hover, html.light-theme .sidebar-nav .nav-link.active { background-color: #e9ecef; color: #212529; }
        html.light-theme .list-group-item { color: #212529; background: transparent; border-color: #e9ecef; }
        /* Ensure text-light switches to dark text in light-theme, and stays white in dark-theme */
        html.light-theme .text-light { color: #212529 !important; }
        html:not(.light-theme) .text-light { color: #ffffff !important; }
        /* Secondary text color adjustments */
        html.light-theme .text-secondary { color: #6c757d !important; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans", sans-serif; }
        .sidebar {
            width: 260px; background-color: var(--primary-dark); border-right: 1px solid var(--border-color);
            position: fixed; top: 0; bottom: 0; left: 0;
            transition: transform 0.3s ease-in-out; z-index: 1040;
        }
        .main-content { margin-left: 260px; transition: margin-left 0.3s ease-in-out; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .sidebar.active { transform: translateX(0); }
        }
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
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar vh-100" id="adminSidebar">
        <div class="sidebar-header"><h5 class="mb-0">Command Center</h5></div>
        <div class="p-3">
             <nav class="nav flex-column sidebar-nav">
                <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
                <a class="nav-link" href="cases.php"><i class="bi bi-shield-check"></i> View Complaints</a>
                <a class="nav-link" href="criminals.php"><i class="bi bi-people-fill"></i> criminals</a>
                <a class="nav-link" href="#"><i class="bi bi-gear-fill"></i> Settings</a>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </div>
      </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <header class="topbar p-3 sticky-top d-flex align-items-center">
             <button class="btn btn-dark d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
             <!-- Theme toggle on the right -->
             <div class="ms-auto d-flex align-items-center">
                 <div id="themeToggleWrapper" class="form-check form-switch theme-toggle text-light me-2" title="Toggle light / dark theme">
                     <input class="form-check-input" type="checkbox" id="themeToggle">
                     <label class="form-check-label mb-0 d-flex align-items-center" for="themeToggle"><i id="themeIcon" class="bi bi-moon-stars-fill"></i></label>
                 </div>
             </div>
        </header>

        <main class="p-4">
            <!-- Page specific content starts here -->
            <h3 class="mb-4">Top-Level Metrics</h3>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3"><div class="card p-3"><div class="text-secondary">Total Complaints</div><h3 id="totalComplaints" class="fw-bold mt-1"><?= number_format($total) ?></h3></div></div>
                <div class="col-md-6 col-lg-3"><div class="card p-3"><div class="text-secondary">Pending</div><h3 id="pendingComplaints" class="fw-bold mt-1"><?= number_format($pending) ?></h3></div></div>
                <div class="col-md-6 col-lg-3"><div class="card p-3"><div class="text-secondary">Resolved</div><h3 id="resolvedComplaints" class="fw-bold mt-1"><?= number_format($resolved) ?></h3></div></div>
                <div class="col-md-6 col-lg-3"><div class="card p-3"><div class="text-secondary">Total Criminals</div><h3 id="totalCriminals" class="fw-bold mt-1">Loading...</h3></div></div>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-12">
                    <div class="card p-3"><h5 class="mb-3">Crime Category Breakdown</h5><canvas id="categoryBreakdownChart"></canvas></div>
                </div>
            </div>

            <h3 class="mt-5 mb-4">Overview</h3>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card p-3">
                        <h5 class="mb-3">Case Status Distribution</h5>
                        <div class="d-flex align-items-center">
                            <canvas id="statusDoughnut" style="max-width:220px"></canvas>
                            <div class="ms-3">
                                <div class="small text-secondary">Pending</div>
                                <div class="small text-secondary">In Progress</div>
                                <div class="small text-secondary">Resolved</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                            <div class="card p-3"><h5 class="mb-3">Complaints This Month</h5><div class="text-secondary small">Summary statistics and trends are available in the reports section.</div></div>
                        </div>
            </div>

            <div class="card mt-4 p-3">
                <h5 class="mb-3">Recent Activity Feed</h5>
                <ul class="list-group list-group-flush">
                    <?php while($activity = $recent_activity->fetch_assoc()): ?>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center text-light border-secondary px-0">
                        <div><strong>Complaint #<?= e($activity['complaint_code']) ?></strong><div class="text-secondary small"><?= e($activity['crime_type']) ?></div></div>
                        <span class="text-secondary small"><?= date('M d, H:i', strtotime($activity['created_at'])) ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </main>
    </div>
</div>

<script>
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('adminSidebar').classList.toggle('active');
});

document.addEventListener('DOMContentLoaded', function () {
    const chartConfig = { plugins: { legend: { display: false } }, scales: { x: { ticks: { color: '#8b949e' }, grid: { color: 'rgba(139, 148, 158, 0.2)' } }, y: { ticks: { color: '#8b949e' }, grid: { color: 'rgba(139, 148, 158, 0.2)' } } } };

    // Fetch dashboard JSON and render charts
        // use explicit relative path to avoid base href issues
        fetch('./api/dashboard-data.php', { credentials: 'same-origin' }).then(r => {
        if (!r.ok) throw new Error('Failed to fetch dashboard data');
        return r.json();
    }).then(data => {
            // debug: show returned object in console to help troubleshoot missing charts
            console.debug('dashboard-data:', data);

            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded (window.Chart is undefined). Check your script includes.');
                return;
            }
            // Category Breakdown (defensive)
            try {
                const catEl = document.getElementById('categoryBreakdownChart');
                if (catEl && data.category) {
                    const catLabels = data.category.labels || [];
                    const catData = data.category.data || [];
                    new Chart(catEl, { type: 'bar', data: { labels: catLabels, datasets: [{ data: catData, backgroundColor: 'rgba(88, 166, 255, 0.5)' }] }, options: { ...chartConfig, indexAxis: 'y' } });
                }
            } catch (err) {
                console.warn('Category chart failed to render:', err);
            }

        // state hotspots are not shown on this dashboard; skip updating them

            // Status doughnut (defensive)
            try {
                const statusEl = document.getElementById('statusDoughnut');
                if (statusEl) {
                    const statusCounts = data.status_counts || {};
                    const statusLabels = ['Pending', 'In Progress', 'Resolved'];
                    const statusValues = [statusCounts['Pending'] || 0, statusCounts['In Progress'] || 0, statusCounts['Resolved'] || 0];
                    const ctxStatus = statusEl.getContext('2d');
                    new Chart(ctxStatus, { type: 'doughnut', data: { labels: statusLabels, datasets: [{ data: statusValues, backgroundColor: ['#ffb86b', '#58a6ff', '#6ee7b7'] }] }, options: { plugins: { legend: { display: false } }, cutout: '70%' } });
                }
            } catch (err) {
                console.warn('Status doughnut failed to render:', err);
            }

    // Update recent activity feed
        if (Array.isArray(data.recent_activity)){
            const list = document.querySelector('.list-group');
            if (list){
                list.innerHTML = '';
                data.recent_activity.forEach(act => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item bg-transparent d-flex justify-content-between align-items-center text-light border-secondary px-0';
                    const left = document.createElement('div');
                    left.innerHTML = `<strong>Complaint #${escapeHtml(act.complaint_code)}</strong><div class="text-secondary small">${escapeHtml(act.crime_type)}</div>`;
                    const right = document.createElement('span');
                    right.className = 'text-secondary small';
                    right.textContent = (new Date(act.created_at)).toLocaleString(undefined, { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });
                    li.appendChild(left); li.appendChild(right); list.appendChild(li);
                });
            }
        }

        // Update top-level numeric metrics
        if (data.totals){
            const t = data.totals;
            if (document.getElementById('totalComplaints')) document.getElementById('totalComplaints').textContent = Number(t.total||0).toLocaleString();
            if (document.getElementById('pendingComplaints')) document.getElementById('pendingComplaints').textContent = Number(t.pending||0).toLocaleString();
            if (document.getElementById('resolvedComplaints')) document.getElementById('resolvedComplaints').textContent = Number(t.resolved||0).toLocaleString();
            if (document.getElementById('totalCriminals')) document.getElementById('totalCriminals').textContent = Number(t.criminals||0).toLocaleString();
        }
    }).catch(err => { console.error(err); });

    // small helper to avoid XSS when inserting server data
    function escapeHtml(s){
        if (!s) return '';
        return s.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }
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
    const body = document.body;
    const wrapper = document.getElementById('themeToggleWrapper');

    function applyTheme(isLight){
        // Temporarily disable CSS transitions for instant theme flip
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
            // Force reflow then remove transition-disable after a short delay
            void htmlEl.offsetWidth;
            setTimeout(() => htmlEl.classList.remove('disable-transitions'), 50);
        });
    }

    // Initialize from localStorage or prefer dark (default as in original CSS)
    const stored = localStorage.getItem('js_theme');
    const useLight = stored ? (stored === 'light') : false;
    applyTheme(useLight);

    // Bind change
    if(toggle){
        // Ensure wrapper text color matches initial theme
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

