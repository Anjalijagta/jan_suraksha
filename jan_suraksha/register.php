<?php
require_once __DIR__ . '/config.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $err = 'Invalid security token. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $mobile = preg_replace('/\D+/', '', $mobile); // Keep only digits
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        // Validation
        if (!$name || !preg_match('/^[0-9]{10}$/', $mobile) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $password !== $confirm) {
            $err = 'Please fill form correctly: 10-digit mobile, valid email, and matching passwords (min 6 chars).';
        } else {
            // Check for duplicates
            $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? OR mobile = ?');
            $stmt->bind_param('ss', $email, $mobile);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $err = 'Email or mobile already registered.';
            } else {
                // Insert new user
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $mysqli->prepare('INSERT INTO users (name, mobile, email, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())');
                $ins->bind_param('ssss', $name, $mobile, $email, $hash);

                if ($ins->execute()) {
                    // Session Fixation Protection - Regenerate session ID
                    session_regenerate_id(true);
                    // Regenerate CSRF token after successful registration
                    unset($_SESSION['csrf_token']);
                    header('Location: register-success.php');
                    exit;
                } else {
                    $err = 'Error creating account. Please try again.';
                }
            }
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register - Jan Suraksha</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  padding: 1rem;
  box-sizing: border-box;
}

  
  .auth-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: slideUp 0.5s ease-out;
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .auth-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
  }
  
  .auth-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
  }
  
  .auth-header p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 0.95rem;
  }
  
  .auth-body {
    padding: 2.5rem 2rem;
  }
  
  .form-label {
    font-weight: 600;
    color: #344054;
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
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }
  
  .form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
  }
  
  .form-control.is-valid {
    border-color: #198754;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
  }
  
  .invalid-feedback {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 0.85rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
  }
  
  .auth-footer {
    text-align: center;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
  }
  
  .auth-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
  }
  
  .auth-footer a:hover {
    text-decoration: underline;
  }
  
  .back-home {
    position: absolute;
    top: 20px;
    left: 20px;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
  }
  
  .back-home:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
  }
  
  .alert {
    border-radius: 10px;
    border: none;
  }

  .input-group-text {
    border: 2px solid #e5e7eb;
    border-left: none;
    background: #f8fafc;
    border-radius: 0 10px 10px 0;
    color: #64748b;
    padding: 0.75rem;
  }

  .input-group-text:hover {
    background: #e2e8f0;
    color: #475569;
  }
</style>
</head>
<body>
<a href="index.php" class="back-home">
  <i class="bi bi-arrow-left"></i> Back to Home
</a>
<div class="container register-container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="auth-card">
        <div class="auth-header">
          <h2><i class="bi bi-shield-check"></i> Create Account</h2>
          <p>Join Jan Suraksha today</p>
        </div>
        <div class="auth-body">
          <?php if($err): ?><div class="alert alert-danger"><?=e($err)?></div><?php endif; ?>
          <form method="post" id="registerForm" novalidate>
            <?php echo csrf_token_field(); ?>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input class="form-control" name="name" type="text" autocomplete="name" placeholder="Enter your full name" required>
              <div class="invalid-feedback">Please enter your full name.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Mobile Number</label>
              <input class="form-control" name="mobile" type="tel" autocomplete="tel" maxlength="10" placeholder="10 digit mobile number" required>
              <div class="invalid-feedback">Enter a valid 10 digit mobile number.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input class="form-control" name="email" type="email" autocomplete="email" placeholder="your.email@example.com" required>
              <div class="invalid-feedback">Enter a valid email address.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <div class="input-group">
                <input class="form-control" id="passwordField" name="password" type="password" autocomplete="new-password" placeholder="Create a strong password" minlength="6" required>
                <button class="btn input-group-text" type="button" id="togglePassword">
                  <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
              </div>
              <div class="invalid-feedback">Password must be at least 6 characters.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <div class="input-group">
                <input class="form-control" id="confirmField" name="confirm" type="password" autocomplete="new-password" placeholder="Re-enter your password" required>
                <button class="btn input-group-text" type="button" id="toggleConfirm">
                  <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                </button>
              </div>
              <div class="invalid-feedback">Passwords must match.</div>
            </div>

            <button class="btn btn-primary w-100" type="submit">
              <i class="bi bi-person-plus-fill me-2"></i>Create Account
            </button>
          </form>
          <div class="auth-footer">
            <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registerForm');
  const nameInput = form.querySelector('input[name="name"]');
  const mobileInput = form.querySelector('input[name="mobile"]');
  const emailInput = form.querySelector('input[name="email"]');
  const passwordInput = document.getElementById('passwordField');
  const confirmInput = document.getElementById('confirmField');

  const togglePasswordBtn = document.getElementById('togglePassword');
  const toggleConfirmBtn = document.getElementById('toggleConfirm');

  // Show / hide password
  togglePasswordBtn.addEventListener('click', function () {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    document.getElementById('toggleIcon').className =
      isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
  });

  toggleConfirmBtn.addEventListener('click', function () {
    const isPassword = confirmInput.type === 'password';
    confirmInput.type = isPassword ? 'text' : 'password';
    document.getElementById('toggleConfirmIcon').className =
      isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
  });

  form.addEventListener('submit', function (e) {
    let hasError = false;

    [nameInput, mobileInput, emailInput, passwordInput, confirmInput].forEach(el => {
      el.classList.remove('is-invalid', 'is-valid');
    });

    // Name required
    if (!nameInput.value.trim()) {
      nameInput.classList.add('is-invalid');
      hasError = true;
    } else {
      nameInput.classList.add('is-valid');
    }

    // Mobile: 10 digits
    const mobileDigits = mobileInput.value.replace(/\D+/g, '');
    if (!/^\d{10}$/.test(mobileDigits)) {
      mobileInput.classList.add('is-invalid');
      hasError = true;
    } else {
      mobileInput.classList.add('is-valid');
    }

    // Email
    if (!emailInput.value.trim() || !emailInput.checkValidity()) {
      emailInput.classList.add('is-invalid');
      hasError = true;
    } else {
      emailInput.classList.add('is-valid');
    }

    // Password length
    if (!passwordInput.value.trim() || passwordInput.value.length < 6) {
      passwordInput.classList.add('is-invalid');
      hasError = true;
    } else {
      passwordInput.classList.add('is-valid');
    }

    // Confirm matches
    if (!confirmInput.value.trim() || confirmInput.value !== passwordInput.value) {
      confirmInput.classList.add('is-invalid');
      hasError = true;
    } else {
      confirmInput.classList.add('is-valid');
    }

    if (hasError) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
