<?php
require_once __DIR__ . '/config.php';
$code = isset($_GET['code']) ? $_GET['code'] : '';
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
    <h2 class="mb-4">Complaint Submitted</h2>
    <?php if($code): ?>
        <div class="alert alert-success">
            Your complaint has been submitted successfully.<br>
            <strong>Complaint ID: <?=e($code)?></strong><br>
            Please save this ID to track your complaint status.
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No complaint ID provided.</div>
    <?php endif; ?>
    <a class="btn btn-primary" href="index.php">Return to Home</a>
</main>

<?php include 'footer.php'; ?>
