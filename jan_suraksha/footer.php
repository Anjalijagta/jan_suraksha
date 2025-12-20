<?php
// footer.php
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* Space for footer */
body {
    margin: 0;
}

/* Footer base */
footer.footer-main {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 999;

    transform: translateY(100%);
    opacity: 0;
    transition: transform 0.35s ease, opacity 0.35s ease;
    pointer-events: none;
}

/* Visible footer */
footer.footer-main.visible {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}

/* Background */
.footer-bg {
    width: 100%;
    background: radial-gradient(circle at top, #2563eb 0%, #0f172a 55%, #020617 100%);
}

/* Inner container */
.footer-inner {
    max-width: 1700px;
    margin: auto;
    padding: 1.5rem 1rem;
}

/* White card */
.bg-white {
    border-radius: 1.25rem 1.25rem 0 0;
}

/* Mobile */
@media (max-width: 576px) {
    .footer-inner {
        padding: 1rem 0.75rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const footer = document.querySelector('.footer-main');
    if (!footer) return;

    function toggleFooter() {
        const scrollTop = window.scrollY;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;

        // Show footer only near bottom
        if (scrollTop + windowHeight >= docHeight - 60) {
            footer.classList.add('visible');
        } else {
            footer.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', toggleFooter);
    window.addEventListener('resize', toggleFooter);
    toggleFooter(); // run once
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const footer = document.querySelector('.footer-main');
    if (!footer) return;

    function adjustBodyPadding() {
        const footerHeight = footer.offsetHeight;
        document.body.style.paddingBottom = footerHeight + 'px';
    }

    adjustBodyPadding();
    window.addEventListener('resize', adjustBodyPadding);
});
</script>


<footer class="footer-main">
    <div class="footer-bg">
        <div class="footer-inner">

            <div class="bg-white shadow-sm px-4 px-md-5 py-4">
                <div class="row gy-4 text-center text-md-start">

                    <!-- Brand -->
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-3">
                            <img src="logo.png" alt="Jan Suraksha"
                                 style="height:48px;border-radius:50%;border:2px solid #2563eb;" class="me-2">
                            <div>
                                <h5 class="fw-bold text-primary mb-0">Jan Suraksha</h5>
                                <small class="text-muted">Aapki Suraksha, Hamari Zimmedari</small>
                            </div>
                        </div>
                        <p class="text-muted mb-0">
                            A public safety & grievance redressal portal to report incidents
                            and track complaints securely.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-md-2">
                        <h6 class="fw-bold text-primary mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li><a href="file-complaint.php" class="text-muted text-decoration-none"><i class="bi bi-flag-fill me-2 text-primary"></i>File Complaint</a></li>
                            <li><a href="track-status.php" class="text-muted text-decoration-none"><i class="bi bi-search me-2 text-primary"></i>Track Status</a></li>
                            <li><a href="blog.php" class="text-muted text-decoration-none"><i class="bi bi-journal-text me-2 text-primary"></i>Awareness Blog</a></li>
                            <li><a href="about-us.php" class="text-muted text-decoration-none"><i class="bi bi-info-circle me-2 text-primary"></i>About Us</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="col-md-2">
                        <h6 class="fw-bold text-primary mb-3">Legal</h6>
                        <ul class="list-unstyled">
                            <li><a href="privacy.php" class="text-muted text-decoration-none"><i class="bi bi-shield-check me-2 text-primary"></i>Privacy Policy</a></li>
                            <li><a href="terms.php" class="text-muted text-decoration-none"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Terms</a></li>
                            <li><a href="admin/index.php" class="text-muted text-decoration-none"><i class="bi bi-lock-fill me-2 text-primary"></i>Admin Login</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="col-md-4">
                        <h6 class="fw-bold text-primary mb-3">Reach Us</h6>
                        <ul class="list-unstyled text-muted mb-3">
                            <li><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Mumbai, Maharashtra</li>
                            <li><i class="bi bi-envelope-fill me-2 text-primary"></i>contact@jsuraksha.gov.in</li>
                            <li><i class="bi bi-telephone-fill me-2 text-primary"></i>+91 22 2345 6789</li>
                        </ul>

                        <div class="d-flex justify-content-center justify-content-md-start gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center text-white small mt-2 p-2 bg-dark bg-opacity-50">
                &copy; <?= date('Y') ?> Jan Suraksha Portal. All Rights Reserved.
            </div>

        </div>
    </div>
</footer>
