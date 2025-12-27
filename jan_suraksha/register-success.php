<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registration Successful - Jan Suraksha</title>
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

    .success-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      animation: slideUp 0.5s ease-out;
      max-width: 500px;
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

    .success-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 3rem 2rem;
      text-align: center;
    }

    .success-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
      animation: bounce 0.6s ease-out;
    }

    @keyframes bounce {
      0% {
        transform: scale(0.5);
        opacity: 0;
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    .success-header h2 {
      margin: 0;
      font-weight: 700;
      font-size: 1.8rem;
    }

    .success-header p {
      margin: 0.5rem 0 0 0;
      opacity: 0.9;
      font-size: 0.95rem;
    }

    .success-body {
      padding: 2.5rem 2rem;
      text-align: center;
    }

    .success-body p {
      color: #6b7280;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 10px;
      padding: 0.85rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
      color: white;
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
  </style>
</head>
<body>
<a href="index.php" class="back-home">
  <i class="bi bi-arrow-left"></i> Back to Home
</a>
<div class="success-card">
  <div class="success-header">
    <div class="success-icon">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <h2>Account Created!</h2>
    <p>Your registration is complete</p>
  </div>
  <div class="success-body">
    <p><strong>Welcome to Jan Suraksha!</strong></p>
    <p>Your account has been successfully created. You can now login with your credentials to access your profile and file complaints.</p>
    <a href="login.php" class="btn btn-primary">
      <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
    </a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
