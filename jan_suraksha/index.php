<?php
require_once __DIR__ . '/config.php';
// Ensure we don't call session_start() if a session is already active (config.php handles session start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jan Suraksha - Aapki Suraksha, Hamari Zimmedari</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Hero Section Styling */
        .hero-section {
            /* IMPORTANT: Replace with your own high-quality background image */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('uploads/main.jpg') no-repeat center center;
            background-size: cover;
            padding: 120px 0;
            color: white;
            text-align: center;
        }

        .hero-section .btn-light {
            color: #0d6efd;
            border-color: white;
        }
        .hero-section .btn-light:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }

        /* How It Works - Stepper Styling */
        .how-it-works {
            position: relative;
            padding: 40px 0;
        }
        .stepper-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }
        .stepper-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            width: 2px;
            height: calc(100% - 20px);
            background-color: #0d6efd;
        }
        .stepper-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin-right: 20px;
            z-index: 1;
        }
        
        /* Public Awareness Card Styling */
        .awareness-section {
            background-color: #ffffff;
        }
        .awareness-card .icon-placeholder {
            width: 100%;
            height: 120px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #adb5bd;
            border-top-left-radius: var(--bs-card-inner-border-radius);
            border-top-right-radius: var(--bs-card-inner-border-radius);
        }
        .awareness-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .awareness-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        /* Footer Styling */
        .footer-section {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
        }
        .footer-section h5 {
            color: #0d6efd;
        }
        .footer-section .list-unstyled a {
            text-decoration: none;
            color: #6c757d;
            transition: color 0.2s;
        }
        .footer-section .list-unstyled a:hover {
            color: #0d6efd;
        }
        .footer-bottom {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">Jan Suraksha</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navs">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navs">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="file-complaint.php">File a Complaint</a></li>
                <li class="nav-item"><a class="nav-link" href="track-status.php">Track Status</a></li>
                <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php">FAQ / Help</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if(empty($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="btn btn-outline-primary me-2" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="register.php">Sign Up</a></li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Namaste, <?=htmlspecialchars($_SESSION['user_name'])?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
    <header class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Aapki Suraksha, Hamari Zimmedari</h1>
            <p class="lead col-lg-8 mx-auto">
                Lodge your complaints online, get your case updates and track the status, anytime.
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                <a href="file-complaint.php" class="btn btn-primary btn-lg px-4 gap-3">File a Complaint</a>
                <a href="track-status.php" class="btn btn-light btn-lg px-4">Check Complaint Status</a>
            </div>
        </div>
    </header>

    <section class="how-it-works py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How It Works</h2>
                <p class="lead text-muted">A simple, fast, and transparent process.</p>
            </div>
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="stepper-item">
                        <div class="stepper-icon">1</div>
                        <div>
                            <h5 class="fw-semibold">Submit Your Complaint Online</h5>
                            <p class="text-muted">Fill out the detailed complaint form with all necessary information and evidence. It's quick, secure, and available 24/7.</p>
                        </div>
                    </div>
                    <div class="stepper-item">
                        <div class="stepper-icon">2</div>
                        <div>
                            <h5 class="fw-semibold">Received by Officials</h5>
                            <p class="text-muted">Your complaint is securely transmitted to the concerned department where officials will review and verify the details provided.</p>
                        </div>
                    </div>
                    <div class="stepper-item">
                        <div class="stepper-icon">3</div>
                        <div>
                            <h5 class="fw-semibold">Track The Progress in Real Time</h5>
                            <p class="text-muted">Use your unique Case ID to track the status of your complaint at any time, from anywhere, ensuring complete transparency.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="awareness-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Public Awareness</h2>
                <p class="lead text-muted">Stay informed with our latest articles and safety guides.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm awareness-card">
                        <div class="icon-placeholder"><i class="bi bi-shield-lock"></i></div>
                        <div class="card-body">
                            <p class="text-primary fw-bold small text-uppercase">Cyber Crime</p>
                            <h5 class="card-title">Cyber Safety Tips</h5>
                            <p class="card-text">Learn how to protect yourself from online frauds, phishing scams, and secure your digital identity.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm awareness-card">
                        <div class="icon-placeholder"><i class="bi bi-person-standing-dress"></i></div>
                        <div class="card-body">
                            <p class="text-primary fw-bold small text-uppercase">Women's Safety</p>
                            <h5 class="card-title">Women's Safety Guidelines</h5>
                            <p class="card-text">Essential guidelines and resources to enhance personal safety for women in public and private spaces.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm awareness-card">
                        <div class="icon-placeholder"><i class="bi bi-journal-text"></i></div>
                        <div class="card-body">
                            <p class="text-primary fw-bold small text-uppercase">Guideline</p>
                            <h5 class="card-title">Codes and Regulations</h5>
                            <p class="card-text">Understand the basic laws and your rights as a citizen. Knowledge is the first step towards protection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer-section pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row">
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <h5 class="text-uppercase fw-bold mb-4">Jan Suraksha</h5>
                <p>A dedicated portal for public safety and grievance redressal. Report incidents, track progress, and stay informed.</p>
            </div>
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h5 class="text-uppercase fw-bold mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="file-complaint.php">File a Complaint</a></li>
                    <li class="mb-2"><a href="track-status.php">Track a Complaint</a></li>
                    <li class="mb-2"><a href="blog.php">Awareness Blog</a></li>
                    <li class="mb-2"><a href="about-us.php">About Us</a></li>
                    <li class="mb-2"><a href="faq.php">FAQ / Help</a></li>
                </ul>
            </div>
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                <h5 class="text-uppercase fw-bold mb-4">Legal</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#">Terms of Service</a></li>
                    <li class="mb-2"><a href="admin/index.php">Administrator Login</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h5 class="text-uppercase fw-bold mb-4">Reach Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-geo-alt-fill me-2"></i>Police HQ, Mumbai, MH</li>
                    <li class="mb-2"><i class="bi bi-envelope-fill me-2"></i>contact@jsuraksha.gov.in</li>
                    <li class="mb-2"><i class="bi bi-telephone-fill me-2"></i>+91 22 2345 6789</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<div class="footer-bottom text-center p-3">
    &copy; <?= date('Y') ?> Jan Suraksha Portal. All Rights Reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
