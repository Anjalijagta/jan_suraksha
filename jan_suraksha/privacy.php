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

<main id="page-content" class="container py-5">
    <h1 class="mb-3 text-center">Privacy Policy</h1>
    <p class="text-muted text-center mb-4">
        This Privacy Policy explains how the Jan Suraksha portal collects, uses, and protects your information when you use this service.
    </p>

    <div class="row g-4">
        <!-- Info we collect -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">1. Information We Collect</h2>
                    <p class="card-text mb-1">We collect the following types of information when you use this portal:</p>
                    <ul class="mb-0">
                        <li>Account details such as your name, email address, and mobile number when you register.</li>
                        <li>Complaint information including titles, descriptions, categories, locations, and any attachments you submit.</li>
                        <li>Technical data such as IP address, browser type, and access logs used for security and performance monitoring.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- How we use it -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">2. How We Use Your Information</h2>
                    <ul class="mb-0">
                        <li>To create and manage your user account and authenticate your access to the portal.</li>
                        <li>To register, process, and track your complaints and provide status updates.</li>
                        <li>To improve the portal's functionality, security, and user experience through analytics and feedback.</li>
                        <li>To communicate important notifications related to your account or complaints.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sharing -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">3. Information Sharing</h2>
                    <p class="card-text mb-1">We do not sell your personal information. We may share your information:</p>
                    <ul class="mb-0">
                        <li>With authorized officials or departments responsible for handling and resolving complaints.</li>
                        <li>With service providers who support hosting, security, or communication, under appropriate safeguards.</li>
                        <li>When required by law, court order, or to protect the safety and rights of users or the public.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Storage & security -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">4. Data Storage & Security</h2>
                    <ul class="mb-0">
                        <li>Your information is stored in secure databases with access restricted to authorized personnel.</li>
                        <li>We use reasonable technical and organizational measures to protect data against loss, misuse, or unauthorized access.</li>
                        <li>No system is completely secure, so you should also protect your account credentials and log out after use.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rights -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">5. Your Rights & Choices</h2>
                    <ul class="mb-0">
                        <li>You can view and update your basic account details from your profile page.</li>
                        <li>You may request correction of inaccurate information associated with your account or complaints, where applicable.</li>
                        <li>If you wish to deactivate your account or have questions about your data, you can contact us using the details below.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cookies -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">6. Cookies & Tracking</h2>
                    <p class="card-text mb-0">
                        The portal may use cookies or similar technologies to maintain sessions, remember preferences, and gather basic usage statistics.
                        You can control cookies through your browser settings, but disabling them may affect certain features of the portal.
                    </p>
                </div>
            </div>
        </div>

        <!-- Changes -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">7. Changes to This Policy</h2>
                    <p class="card-text mb-0">
                        We may update this Privacy Policy from time to time to reflect changes in the portal or legal requirements.
                        The "Last updated" date will be revised accordingly, and continued use of the portal after changes means you accept the updated policy.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 card-title">8. Contact Us</h2>
                    <p class="card-text mb-0">
                        If you have questions or requests regarding this Privacy Policy, you can reach us using the contact details provided
                        in the "Reach Us" section of the site footer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted small mt-4">Last updated: <?= date('F j, Y') ?></p>
</main>

<?php include 'footer.php'; ?>
