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
    <h1 class="mb-4 text-center">FAQ & Help</h1>
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
                    After logging in, go to the "New Complaint" page, choose a category, describe the issue
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
                    Use your complaint ID in the "My Complaints" or "Track Complaint" section to view
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

<?php include 'footer.php'; ?>
