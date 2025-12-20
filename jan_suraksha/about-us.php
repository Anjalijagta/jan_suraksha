<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include 'header.php'; ?>
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

<main id="page-content" style="padding-top:72px;">
    <div class="container-sm-card">
        <section>
            <h2 class="section-title">Our Mission</h2>
            <p class="lead-muted">To empower every citizen of India with a secure and efficient platform for voicing concerns and ensuring accountability from public service providers. We are committed to transparency, responsiveness, and upholding the highest standards of integrity in addressing grievances.</p>
        </section>

        <section class="mt-4">
            <h2 class="section-title">How It Works</h2>
            <div class="stepper mt-3">
                <div class="step">
                    <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div>
                        <h6>File a Complaint</h6>
                        <p>Submit your complaint with detailed information.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="icon" style="background:#0284c7;"><i class="bi bi-search"></i></div>
                    <div>
                        <h6>Track Your Status</h6>
                        <p>Monitor the progress of your complaint in real-time.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="icon" style="background:#059669;"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <h6>Get Resolution</h6>
                        <p>Receive updates and resolution details directly.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="privacy">
            <h2 class="section-title">Our Commitment to Privacy</h2>
            <p class="lead-muted">We adhere to the highest standards of data privacy, complying with applicable Indian laws and regulations. Your personal information is protected with robust security measures, ensuring confidentiality and integrity throughout the complaint resolution process.</p>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>
