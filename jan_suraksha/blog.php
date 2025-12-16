<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$res = $mysqli->query('SELECT id,title,excerpt,image,created_at FROM articles ORDER BY created_at DESC');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Awareness Blog - Jan Suraksha Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f0f2f5;
            padding-top: 70px;
        }
        .blog-card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .blog-card-img {
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }
        .blog-card .card-body {
            padding: 1.5rem;
        }
        .blog-card .card-title {
            font-weight: 600;
            font-size: 1.25rem;
        }
        .blog-card .read-more-link {
            text-decoration: none;
            font-weight: 500;
        }
        .footer-section {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
        }
        .footer-section h5 { color: #0d6efd; }
        .footer-section .list-unstyled a { text-decoration: none; color: #6c757d; }
        .footer-section .list-unstyled a:hover { color: #0d6efd; }
        .footer-bottom { background-color: #f8f9fa; }
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
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="file-complaint.php">File a Complaint</a></li>
                <li class="nav-item"><a class="nav-link" href="track-status.php">Track Status</a></li>
                <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php">FAQ / Help</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (empty($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="btn btn-outline-primary me-2" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="register.php">Sign Up</a></li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Namaste, <?= e($_SESSION['user_name']) ?>
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

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-5">
                <h1 class="fw-bold">Awareness Blog</h1>
                <p class="lead text-muted">Stay informed with our latest articles and safety guides.</p>
            </div>

            <!-- Static starter posts -->
            <div class="card blog-card mb-4">
                <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Using+the+Portal" class="card-img-top blog-card-img" alt="How to file a complaint">
                <div class="card-body">
                    <h5 class="card-title">How to file a complaint on Jan Suraksha</h5>
                    <p class="card-text text-secondary">
                        When to use this portal, what details to include, and how to track your complaint using your Case ID so authorities can act faster.
                    </p>
                    <a class="read-more-link" href="#">
                        Read the step-by-step guide <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="card blog-card mb-4">
                <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Cyber+Safety" class="card-img-top blog-card-img" alt="Cyber safety basics">
                <div class="card-body">
                    <h5 class="card-title">Cyber safety and online fraud basics</h5>
                    <p class="card-text text-secondary">
                        Simple tips to recognise suspicious messages, protect your OTPs and passwords, and know when to report cybercrime.
                    </p>
                    <a class="read-more-link" href="#">
                        Learn how to stay safe online <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="card blog-card mb-4">
                <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Road+Safety" class="card-img-top blog-card-img" alt="Road safety basics">
                <div class="card-body">
                    <h5 class="card-title">Road safety basics for everyday commuters</h5>
                    <p class="card-text text-secondary">
                        Key reminders for pedestrians, riders, and drivers to reduce accidents and report dangerous spots in your area.
                    </p>
                    <a class="read-more-link" href="#">
                        See road safety do’s and don’ts <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="card blog-card mb-4">
                <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Community+Safety" class="card-img-top blog-card-img" alt="Neighborhood safety">
                <div class="card-body">
                    <h5 class="card-title">Neighborhood safety and “see something, say something”</h5>
                    <p class="card-text text-secondary">
                        Practical ideas to stay alert in your area, work with neighbours, and raise issues through Jan Suraksha before they escalate.
                    </p>
                    <a class="read-more-link" href="#">
                        Explore neighborhood safety tips <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Dynamic DB posts (if any) -->
            <?php if ($res && $res->num_rows > 0): ?>
                <?php while($a = $res->fetch_assoc()): ?>
                    <div class="card blog-card mb-4">
                        <?php if ($a['image']): ?>
                            <img src="/uploads/<?= e($a['image']) ?>" class="card-img-top blog-card-img" alt="<?= e($a['title']) ?>">
                        <?php else: ?>
                            <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Article" class="card-img-top blog-card-img" alt="Article">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= e($a['title']) ?></h5>
                            <p class="card-text text-secondary"><?= e($a['excerpt']) ?></p>
                            <a class="read-more-link" href="article.php?id=<?= e($a['id']) ?>">
                                Read More <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</main>

<footer class="footer-section pt-5 pb-4 mt-auto">
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
                    <li class="mb-2"><a href="privacy.php">Privacy Policy</a></li>
                    <li class="mb-2"><a href="terms.php">Terms of Service</a></li>
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
</body>
</html>
