<?php
// index.php

// Header already does config.php + session_start()
include 'header.php';
?>

<style>
    /* For pages with custom backgrounds, override body background */
body {
    background-color: var(--color-bg) !important;
    background-image: var(--custom-bg, none) !important;
}

/* Update hardcoded colors to use CSS vars */
.text-primary { color: var(--color-primary) !important; }
.btn-primary { 
    background-color: var(--color-primary); 
    border-color: var(--color-primary); 
}
.btn-primary:hover {
    background-color: color-mix(in srgb, var(--color-primary) 90%, black);
    border-color: color-mix(in srgb, var(--color-primary) 80%, black);
}

</style>


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
                        <p class="text-muted">
                            Fill out the detailed complaint form with all necessary information and evidence.
                            It's quick, secure, and available 24/7.
                        </p>
                    </div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-icon">2</div>
                    <div>
                        <h5 class="fw-semibold">Received by Officials</h5>
                        <p class="text-muted">
                            Your complaint is securely transmitted to the concerned department where officials
                            will review and verify the details provided.
                        </p>
                    </div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-icon">3</div>
                    <div>
                        <h5 class="fw-semibold">Track The Progress in Real Time</h5>
                        <p class="text-muted">
                            Use your unique Case ID to track the status of your complaint at any time,
                            from anywhere, ensuring complete transparency.
                        </p>
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
                        <p class="card-text">
                            Learn how to protect yourself from online frauds, phishing scams,
                            and secure your digital identity.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm awareness-card">
                    <div class="icon-placeholder"><i class="bi bi-person-standing-dress"></i></div>
                    <div class="card-body">
                        <p class="text-primary fw-bold small text-uppercase">Women's Safety</p>
                        <h5 class="card-title">Women's Safety Guidelines</h5>
                        <p class="card-text">
                            Essential guidelines and resources to enhance personal safety
                            for women in public and private spaces.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm awareness-card">
                    <div class="icon-placeholder"><i class="bi bi-journal-text"></i></div>
                    <div class="card-body">
                        <p class="text-primary fw-bold small text-uppercase">Guideline</p>
                        <h5 class="card-title">Codes and Regulations</h5>
                        <p class="card-text">
                            Understand the basic laws and your rights as a citizen.
                            Knowledge is the first step towards protection.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// closes </main>, outputs footer + scripts
include 'footer.php';
?>
