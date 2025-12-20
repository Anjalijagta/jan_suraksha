<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include 'header.php'; ?>

<style>
    .terms-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(15,23,42,0.08);
        background: #ffffff;
    }
    .terms-card .card-header {
        border-bottom: none;
        background: linear-gradient(135deg, #2563eb 0%, #0f172a 100%);
        color: #fff;
        border-radius: 16px 16px 0 0;
    }
    .terms-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: .5rem;
    }
    .terms-section {
        border-left: 3px solid #e5e7eb;
        padding-left: 1rem;
    }
    .terms-section + .terms-section {
        margin-top: 1.5rem;
    }
    @media (max-width: 576px) {
        .terms-section {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            padding-left: 0;
            padding-top: 1rem;
        }
    }
</style>
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


<main id="page-content" class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card terms-card">
                <div class="card-header py-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="terms-icon bg-light bg-opacity-25">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                </div>
                                <h1 class="h3 mb-0 ms-1">Terms of Service</h1>
                            </div>
                            <p class="mb-0 small opacity-75">
                                These Terms explain the rules for using the Jan Suraksha portal. By accessing or using this portal, you agree to these terms.
                            </p>
                        </div>
                        <span class="badge bg-light text-dark">
                            Last updated: <?= date('F j, Y') ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <section class="terms-section">
                        <h2 class="h5 mb-2">1. Purpose and Scope</h2>
                        <p class="mb-0">
                            The Jan Suraksha portal is designed to help citizens submit and track complaints related to public safety and other supported
                            categories. The portal facilitates communication and transparency but does not replace emergency services or formal legal
                            procedures.
                        </p>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">2. User Responsibilities</h2>
                        <p class="mb-1">When using this portal, you agree to:</p>
                        <ul class="mb-2">
                            <li>Provide accurate, complete, and up-to-date information when registering and submitting complaints.</li>
                            <li>Use the portal only for lawful purposes and genuine complaints.</li>
                            <li>Respect the privacy and safety of others and avoid sharing unnecessary sensitive personal data.</li>
                        </ul>
                        <p class="mb-1">You must not:</p>
                        <ul class="mb-0">
                            <li>Submit false, misleading, or malicious complaints.</li>
                            <li>Upload content that is offensive, defamatory, or violates any law or third-party rights.</li>
                            <li>Attempt to interfere with the portal's security, availability, or normal operation.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">3. Complaint Handling</h2>
                        <p class="mb-0">
                            Complaints submitted through the portal are forwarded to the appropriate officials or departments for review. While efforts are
                            made to process complaints promptly, no specific outcome or resolution timeline is guaranteed. You can track the status of your
                            complaint using the provided complaint ID.
                        </p>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">4. Privacy and Data Usage</h2>
                        <p class="mb-0">
                            Information you provide, including personal details and complaint content, is handled in accordance with our
                            <a href="privacy.php">Privacy Policy</a>. Please review the Privacy Policy to understand how your data is collected, used, and
                            protected.
                        </p>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">5. Disclaimers and Limitation of Liability</h2>
                        <ul class="mb-2">
                            <li>The portal is provided on an "as is" and "as available" basis without any warranties of any kind, express or implied.</li>
                            <li>We do not guarantee uninterrupted or error-free operation of the portal.</li>
                        </ul>
                        <p class="mb-1">To the maximum extent permitted by law, the portal operators are not liable for any loss, damage, or consequences arising from:</p>
                        <ul class="mb-0">
                            <li>Your use or inability to use the portal.</li>
                            <li>Delays or failures in complaint processing.</li>
                            <li>Actions taken or not taken by authorities in response to complaints.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">6. Suspension and Termination</h2>
                        <p class="mb-0">
                            We may suspend or terminate your access to the portal, without prior notice, if we believe you have violated these Terms,
                            attempted to misuse the system, or created a security or legal risk for the portal or other users.
                        </p>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">7. Changes to These Terms</h2>
                        <p class="mb-0">
                            These Terms may be updated from time to time to reflect changes in the portal or applicable laws. Continued use of the portal
                            after changes are posted constitutes acceptance of the updated Terms.
                        </p>
                    </section>

                    <section class="terms-section">
                        <h2 class="h5 mb-2">8. Contact and Support</h2>
                        <p class="mb-0">
                            If you have questions about these Terms or need support related to the portal, you can reach us using the contact details listed
                            in the "Reach Us" section of the site footer.
                        </p>
                    </section>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
