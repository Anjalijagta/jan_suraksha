<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FAQ &amp; Help - Jan Suraksha</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
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
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="file-complaint.php">File a Complaint</a></li>
        <li class="nav-item"><a class="nav-link" href="track-status.php">Track Status</a></li>
        <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link active" href="faq.php">FAQ / Help</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="btn btn-outline-primary me-2" href="login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-primary" href="register.php">Sign Up</a></li>
        <?php else: ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              Namaste, <?= htmlspecialchars($_SESSION['user_name']) ?>
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

<main class="container py-5" style="margin-top: 5rem;">
  <h1 class="mb-4 text-center">FAQ &amp; Help</h1>
  <p class="text-muted text-center mb-5">
    Answers to common questions about using the Jan Suraksha complaint portal.
  </p>

  <div class="accordion" id="faqAccordion">
    <!-- Q1 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq1-heading">
        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
          Who can use the Jan Suraksha portal?
        </button>
      </h2>
      <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="faq1-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Any citizen can register and use this portal to submit complaints related to safety,
          public issues, or other supported categories in their area.
        </div>
      </div>
    </div>

    <!-- Q2 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq2-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
          How do I file a new complaint?
        </button>
      </h2>
      <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          After logging in, go to the “New Complaint” page, choose a category, describe the issue
          clearly, add location details, and submit the form to generate a complaint ID.
        </div>
      </div>
    </div>

    <!-- Q3 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq3-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
          What details should I include in my complaint?
        </button>
      </h2>
      <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Include a clear title, a detailed description of the incident, date and time,
          location/address, and any supporting information that helps authorities understand the issue.
        </div>
      </div>
    </div>

    <!-- Q4 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq4-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
          How can I track the status of my complaint?
        </button>
      </h2>
      <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faq4-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Use your complaint ID in the “My Complaints” or “Track Complaint” section to view
          the current status, updates, and any actions taken.
        </div>
      </div>
    </div>

    <!-- Q5 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq5-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
          How long does it take to resolve a complaint?
        </button>
      </h2>
      <div id="faq5" class="accordion-collapse collapse" aria-labelledby="faq5-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Resolution time depends on the nature and severity of the complaint.
          You can check the latest status and updates from the responsible authority in your dashboard.
        </div>
      </div>
    </div>

    <!-- Q6 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq6-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
          Do I need an account to submit a complaint?
        </button>
      </h2>
      <div id="faq6" class="accordion-collapse collapse" aria-labelledby="faq6-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Yes, you need to register and log in so that your complaints are linked to your account
          and you can receive updates and track their status.
        </div>
      </div>
    </div>

    <!-- Q7 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq7-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
          Can I edit or withdraw a complaint after submitting it?
        </button>
      </h2>
      <div id="faq7" class="accordion-collapse collapse" aria-labelledby="faq7-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          You can usually add additional information or comments to an existing complaint;
          editing or withdrawing may depend on its current processing stage.
        </div>
      </div>
    </div>

    <!-- Q8 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq8-heading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq8" aria-expanded="false" aria-controls="faq8">
          Who can I contact for technical support?
        </button>
      </h2>
      <div id="faq8" class="accordion-collapse collapse" aria-labelledby="faq8-heading"
           data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          If you face login, registration, or portal issues, use the contact details provided
          on the Contact or Support section, or raise a support issue through the portal.
        </div>
      </div>
    </div>
  </div>
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
</body>
</html>
