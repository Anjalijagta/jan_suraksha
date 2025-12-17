<?php
require_once __DIR__ . '/../config.php';

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $aid = $_POST['admin_id'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare('SELECT id,admin_name,password_hash FROM admins WHERE admin_id=?');
    $stmt->bind_param('s',$aid); $stmt->execute(); $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        if(password_verify($pass,$row['password_hash'])){
            $_SESSION['admin_id'] = $row['id']; header('Location: dashboard.php'); exit;
        }
    }
    $err = 'Invalid admin credentials.';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login - Jan Suraksha</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #1e293b 0%, #020617 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 1rem;
      box-sizing: border-box;
    }

    .admin-container {
      width: 100%;
      max-width: 420px;
    }

    .auth-card {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
      overflow: hidden;
      animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .auth-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: white;
      padding: 2.25rem 2rem;
      text-align: center;
    }

    .auth-header h2 {
      margin: 0;
      font-weight: 700;
      font-size: 1.7rem;
    }

    .auth-header p {
      margin: 0.5rem 0 0;
      opacity: 0.9;
      font-size: 0.95rem;
    }

    .auth-body {
      padding: 2.25rem 2rem;
    }

    .form-label {
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0f172a;
      box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.18);
    }

    .form-control.is-invalid {
      border-color: #dc3545;
    }

    .invalid-feedback {
      display: block;
      margin-top: 0.25rem;
      font-size: 0.875em;
      color: #dc3545;
    }

    .btn-primary {
      background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
      border: none;
      border-radius: 10px;
      padding: 0.85rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(30, 64, 175, 0.45);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(30, 64, 175, 0.65);
    }

    .alert {
      border-radius: 10px;
      border: none;
    }

    .input-group-text {
      border: 2px solid #e5e7eb;
      border-left: none;
      background: #f9fafb;
      border-radius: 0 10px 10px 0;
      color: #64748b;
      padding: 0.75rem;
    }

    .input-group-text:hover {
      background: #e5e7eb;
      color: #111827;
    }

    @media (max-width: 576px) {
      .auth-header { padding: 2rem 1.5rem; }
      .auth-body   { padding: 2rem 1.5rem; }
    }
  </style>
</head>
<body>
<div class="admin-container">
  <div class="auth-card">
    <div class="auth-header">
      <h2><i class="bi bi-shield-lock-fill me-2"></i>Admin Login</h2>
      <p>Secure access to the admin dashboard</p>
    </div>
    <div class="auth-body">
      <?php if($err): ?>
        <div class="alert alert-danger"><?= e($err) ?></div>
      <?php endif; ?>

      <form method="post" id="adminLoginForm" novalidate>
        <div class="mb-3">
          <label class="form-label">Admin ID</label>
          <input class="form-control" name="admin_id" type="text" placeholder="Enter admin ID">
          <div class="invalid-feedback">Please enter your admin ID.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input class="form-control" id="adminPassword" name="password" type="password"
                   autocomplete="current-password" placeholder="Enter your password">
            <button class="btn input-group-text" type="button" id="toggleAdminPassword">
              <i class="bi bi-eye" id="toggleAdminIcon"></i>
            </button>
          </div>
          <div class="invalid-feedback">Password is required.</div>
        </div>

        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('adminLoginForm');
  const idInput = form.querySelector('input[name="admin_id"]');
  const passwordInput = document.getElementById('adminPassword');
  const toggleBtn = document.getElementById('toggleAdminPassword');
  const toggleIcon = document.getElementById('toggleAdminIcon');

  // show / hide password
  toggleBtn.addEventListener('click', function () {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
  });

  // simple inline validation
  form.addEventListener('submit', function (e) {
    let hasError = false;

    [idInput, passwordInput].forEach(el => {
      el.classList.remove('is-invalid');
    });

    if (!idInput.value.trim()) {
      idInput.classList.add('is-invalid');
      hasError = true;
    }

    if (!passwordInput.value.trim()) {
      passwordInput.classList.add('is-invalid');
      hasError = true;
    }

    if (hasError) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
