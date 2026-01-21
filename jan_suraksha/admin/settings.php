<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$current_page = 'settings.php';

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = $error = '';

// Helper functions for settings
function getSettings($mysqli) {
    $defaults = [
        'portal_name' => 'Jan Suraksha',
        'department' => 'Police Department', 
        'logo_url' => '',
        'timezone' => 'Asia/Kolkata',
        'date_format' => 'd-m-Y',
        'sla_hours' => 24,
        'priority_levels' => json_encode(['Low', 'Medium', 'High', 'Critical']),
        'crime_categories' => json_encode(['Theft', 'Assault', 'Fraud', 'Cybercrime']),
        'email_notifications' => 1,
        'sms_notifications' => 1,
        'session_timeout' => 3600,
        'min_password_length' => 8
    ];
    
    $stmt = $mysqli->prepare("SELECT settings_data FROM settings WHERE id = 1");
    if ($stmt === false) {
        // If settings table doesn't exist or query fails, return defaults
        return $defaults;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $decoded = json_decode($row['settings_data'], true) ?: [];
        return array_merge($defaults, $decoded);
    }
    return $defaults;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $settings = [
        'portal_name' => trim($_POST['portal_name'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'logo_url' => trim($_POST['logo_url'] ?? ''),
        'timezone' => $_POST['timezone'] ?? 'Asia/Kolkata',
        'date_format' => $_POST['date_format'] ?? 'd-m-Y',
        'sla_hours' => (int)($_POST['sla_hours'] ?? 24),
        'priority_levels' => isset($_POST['priority_levels']) ? $_POST['priority_levels'] : [],
        'crime_categories' => isset($_POST['crime_categories']) ? $_POST['crime_categories'] : [],
        'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
        'sms_notifications' => isset($_POST['sms_notifications']) ? 1 : 0,
        'session_timeout' => (int)($_POST['session_timeout'] ?? 3600),
        'min_password_length' => (int)($_POST['min_password_length'] ?? 8),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $settings_data = json_encode($settings);
    $stmt = $mysqli->prepare("INSERT INTO settings (id, settings_data) VALUES (1, ?) ON DUPLICATE KEY UPDATE settings_data = ?");
    if ($stmt === false) {
        $error = "Failed to prepare statement: " . $mysqli->error;
    } else {
        $stmt->bind_param("ss", $settings_data, $settings_data);
        
        if ($stmt->execute()) {
            $success = "All settings updated successfully!";
        } else {
            $error = "Failed to save settings: " . $mysqli->error;
        }
        $stmt->close();
    }
}

$current_settings = getSettings($mysqli);
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings - <?= htmlspecialchars($current_settings['portal_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-bg: #0f0f23;
            --admin-card: #111827;
            --admin-primary: #2563eb;
            --admin-primary-hover: #1d4ed8;
            --admin-text: #e5e7eb;
            --admin-border: #374151;
            --sidebar-width: 250px;
        }
        
        * { box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, var(--admin-bg) 0%, #1a1f2e 100%); 
            min-height: 100vh; 
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Fixed Sidebar - NO COLLAPSE */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(17, 24, 39, 0.98);
            backdrop-filter: blur(20px);
            z-index: 1040;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
        }
        
        .sidebar .nav-link {
            color: var(--admin-text);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover { background: rgba(37, 99, 235, 0.1); }
        .sidebar .nav-link.active { 
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-hover));
            color: white !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        
        /* Main Content - Fixed margin */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Header */
        .admin-header {
            background: rgba(17, 24, 39, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--admin-border) !important;
        }
        
        /* Cards */
        .settings-card {
            background: linear-gradient(135deg, var(--admin-card) 0%, #1f2937 100%);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .settings-card:hover { transform: translateY(-2px); box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2); }
        
        /* Forms */
        .form-control, .form-select {
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid var(--admin-border);
            color: var(--admin-text);
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(31, 41, 55, 0.95);
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.2);
            color: var(--admin-text);
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-hover));
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }
        
        /* Tabs */
        .nav-tabs .nav-link {
            color: var(--admin-text);
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: var(--admin-primary);
            border-bottom-color: var(--admin-primary);
            background: transparent;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0 !important; }
            .sidebar.active { transform: translateX(0); }
        }
        
        /* Status badges */
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8em; font-weight: 500; }
    </style>
</head>
<body>
    <!-- FIXED Sidebar -->
    <aside id="adminSidebar" class="sidebar p-4 d-flex flex-column gap-3">
        <div class="sidebar-brand d-flex align-items-center gap-3 pb-4 border-bottom">
            <i class="fas fa-shield-alt text-primary fs-2"></i>
            <div>
                <div class="fw-bold fs-5"><?= htmlspecialchars($current_settings['portal_name']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($current_settings['department']) ?></small>
            </div>
        </div>
        
        <nav class="nav flex-column flex-grow-1">
            <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a class="nav-link <?= $current_page === 'view_complaints.php' ? 'active' : '' ?>" href="view_complaints.php">
                <i class="fas fa-file-alt me-2"></i> Complaints
            </a>
            <a class="nav-link <?= $current_page === 'criminals.php' ? 'active' : '' ?>" href="criminals.php">
                <i class="fas fa-users me-2"></i> Criminals
            </a>
            <a class="nav-link active" href="settings.php">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </nav>
        
        <div class="dropdown mt-auto">
            <a class="nav-link text-danger text-decoration-none" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="admin-header navbar navbar-expand px-4 py-3">
            <button class="btn btn-link text-white p-0 d-md-none me-3" id="sidebarToggleMobile">
                <i class="fas fa-bars fs-4"></i>
            </button>
            <div class="navbar-nav ms-auto">
                <span class="badge bg-primary me-2">Administrator</span>
                <i class="fas fa-user-circle text-white fs-4"></i>
            </div>
        </header>

        <!-- Settings Content -->
        <div class="container-fluid px-4 py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="settings-card p-5 mb-5">
                        <div class="d-flex align-items-center mb-5">
                            <i class="fas fa-sliders-h fs-2 text-primary me-3"></i>
                            <div>
                                <h1 class="mb-1">System Settings</h1>
                                <p class="text-muted mb-0">Configure portal branding, workflows, notifications & security</p>
                            </div>
                        </div>

                        <form method="POST" id="settingsForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            
                            <ul class="nav nav-tabs mb-5" id="settingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">General</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="complaints-tab" data-bs-toggle="tab" data-bs-target="#complaints" type="button">Complaints</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button">Notifications</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Security</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="settingsTabContent">
                                <!-- General Settings -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Portal Name</label>
                                            <input type="text" class="form-control form-control-lg" name="portal_name" value="<?= htmlspecialchars($current_settings['portal_name']) ?>" required>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Department Name</label>
                                            <input type="text" class="form-control form-control-lg" name="department" value="<?= htmlspecialchars($current_settings['department']) ?>" required>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Logo URL</label>
                                            <input type="url" class="form-control" name="logo_url" value="<?= htmlspecialchars($current_settings['logo_url']) ?>" placeholder="https://example.com/logo.png">
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Default Timezone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="Asia/Kolkata" <?= $current_settings['timezone'] === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST)</option>
                                                <option value="Asia/Mumbai">Asia/Mumbai</option>
                                                <option value="UTC">UTC</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Date Format</label>
                                            <select class="form-select" name="date_format">
                                                <option value="d-m-Y" <?= $current_settings['date_format'] === 'd-m-Y' ? 'selected' : '' ?>>DD-MM-YYYY</option>
                                                <option value="Y-m-d">YYYY-MM-DD</option>
                                                <option value="m/d/Y">MM/DD/YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Complaints Settings -->
                                <div class="tab-pane fade" id="complaints" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">SLA Overdue Threshold (hours)</label>
                                            <input type="number" class="form-control" name="sla_hours" value="<?= $current_settings['sla_hours'] ?>" min="1" max="720">
                                            <div class="form-text">Complaints older than this are marked "Overdue"</div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Priority Levels</label>
                                            <select class="form-select" name="priority_levels[]" multiple size="4">
                                                <?php 
                                                $priorities = json_decode($current_settings['priority_levels'], true) ?: [];
                                                $options = ['Low', 'Medium', 'High', 'Critical', 'Emergency'];
                                                foreach($options as $opt): ?>
                                                    <option value="<?= $opt ?>" <?= in_array($opt, $priorities) ? 'selected' : '' ?>><?= $opt ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Crime Categories</label>
                                            <select class="form-select" name="crime_categories[]" multiple size="6">
                                                <?php 
                                                $categories = json_decode($current_settings['crime_categories'], true) ?: [];
                                                $all_categories = ['Theft', 'Assault', 'Fraud', 'Cybercrime', 'Harassment', 'Traffic', 'Domestic Violence'];
                                                foreach($all_categories as $cat): ?>
                                                    <option value="<?= $cat ?>" <?= in_array($cat, $categories) ? 'selected' : '' ?>><?= $cat ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notifications -->
                                <div class="tab-pane fade" id="notifications" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" <?= $current_settings['email_notifications'] ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-semibold" for="emailNotifications">
                                                    Email Notifications
                                                    <div class="form-text">Acknowledgement, status updates, SLA alerts</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="sms_notifications" id="smsNotifications" <?= $current_settings['sms_notifications'] ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-semibold" for="smsNotifications">
                                                    SMS Notifications
                                                    <div class="form-text">Critical alerts and status changes</div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security -->
                                <div class="tab-pane fade" id="security" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Session Timeout (seconds)</label>
                                            <input type="number" class="form-control" name="session_timeout" value="<?= $current_settings['session_timeout'] ?>" min="300" max="7200">
                                            <div class="form-text">Auto-logout after inactivity (5min - 2hrs)</div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">Minimum Password Length</label>
                                            <input type="number" class="form-control" name="min_password_length" value="<?= $current_settings['min_password_length'] ?>" min="6" max="20">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-5 border-top d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save me-2"></i>Save All Changes
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg px-5">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle ONLY (desktop stays fixed)
        document.getElementById('sidebarToggleMobile')?.addEventListener('click', () => {
            document.getElementById('adminSidebar').classList.toggle('active');
        });

        // Live preview for portal name
        document.querySelector('[name="portal_name"]')?.addEventListener('input', function() {
            const sidebarBrand = document.querySelector('.sidebar-brand .fw-bold');
            if (sidebarBrand) sidebarBrand.textContent = this.value || 'Jan Suraksha';
        });

        // Form validation
        document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
            const portalName = document.querySelector('[name="portal_name"]').value.trim();
            if (!portalName) {
                e.preventDefault();
                alert('Portal name is required');
            }
        });

        // Initialize live preview
        document.querySelector('[name="portal_name"]')?.dispatchEvent(new Event('input'));
    </script>
</body>
</html>
