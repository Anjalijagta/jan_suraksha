<?php
require_once __DIR__ . '/config.php';
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

/* FAQ Hero Section */
.faq-hero {
    background: linear-gradient(135deg, var(--color-primary, #0d6efd) 0%, var(--color-primary-light, #0dcaf0) 100%);
    color: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(13, 110, 253, 0.3);
    padding: 3rem 2rem;
    margin-bottom: 2.5rem;
    text-align: center;
}

.faq-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.faq-hero p {
    font-size: 1.125rem;
    opacity: 0.95;
    margin-bottom: 0;
}

/* FAQ Card Container */
.faq-card {
    background-color: var(--color-surface, #ffffff);
    border: 1px solid var(--color-border, rgba(0,0,0,0.08));
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    padding: 2rem;
}

/* Accordion Styling */
.accordion-item {
    border: 1px solid var(--color-border, rgba(0,0,0,0.08));
    border-radius: 12px !important;
    margin-bottom: 1rem;
    overflow: hidden;
}

.accordion-item:last-child {
    margin-bottom: 0;
}

.accordion-button {
    font-size: 1.125rem;
    font-weight: 600;
    padding: 1.25rem 1.5rem;
    background-color: var(--color-surface-subtle, rgba(0,0,0,0.02));
    border: none;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 202, 240, 0.1) 100%);
    color: var(--color-primary, #0d6efd);
    font-weight: 700;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    border-color: var(--color-primary, #0d6efd);
}

.accordion-body {
    padding: 1.5rem;
    font-size: 1rem;
    line-height: 1.75;
    color: var(--color-text, #495057);
}

/* Icon styling */
.accordion-button i {
    margin-right: 0.75rem;
    color: var(--color-primary, #0d6efd);
}

</style>


<main id="page-content" class="container py-5">
    <!-- Hero Section -->
    <div class="faq-hero">
        <h1><i class="bi bi-question-circle me-2"></i>FAQ & Help</h1>
        <p>
            Answers to common questions about using the Jan Suraksha complaint portal.
        </p>
    </div>

    <!-- FAQ Card Container -->
    <div class="faq-card">
        <div class="accordion" id="faqAccordion">
        <!-- Q1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq1-heading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                    <i class="bi bi-people-fill"></i>Who can use the Jan Suraksha portal?
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
                    <i class="bi bi-file-earmark-plus"></i>How do I file a new complaint?
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
                    <i class="bi bi-card-checklist"></i>What details should I include in my complaint?
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
                    <i class="bi bi-search"></i>How can I track the status of my complaint?
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
                    <i class="bi bi-clock-history"></i>How long does it take to resolve a complaint?
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
                    <i class="bi bi-person-check"></i>Do I need an account to submit a complaint?
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
                    <i class="bi bi-pencil-square"></i>Can I edit or withdraw a complaint after submitting it?
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
                    <i class="bi bi-headset"></i>Who can I contact for technical support?
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
    </div>
</main>

<?php include 'footer.php'; ?>
