<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$res = $mysqli->query('SELECT id,title,excerpt,image,created_at FROM articles ORDER BY created_at DESC');
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


<main id="page-content" class="container my-5">
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
                        See road safety do's and don'ts <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="card blog-card mb-4">
                <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Community+Safety" class="card-img-top blog-card-img" alt="Neighborhood safety">
                <div class="card-body">
                    <h5 class="card-title">Neighborhood safety and "see something, say something"</h5>
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

<?php include 'footer.php'; ?>
