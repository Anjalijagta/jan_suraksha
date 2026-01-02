<?php
// Footer Component - Jan Suraksha Portal
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    margin: 0;
}

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

footer.footer-main.visible {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}

.footer-bg {
    width: 100%;
    background: radial-gradient(circle at top, #2563eb 0%, #0f172a 55%, #020617 100%);
}

.footer-inner {
    max-width: 1700px;
    margin: auto;
    padding: 1.5rem 1rem;
    position: relative; /* Added for scroll button positioning */
}

.bg-white {
    border-radius: 1.25rem 1.25rem 0 0;
}

@media (max-width: 576px) {
    .footer-inner {
        padding: 1rem 0.75rem;
    }
}

/* Scroll Button Styles */
.scroll-btn-container {
    pointer-events: auto;
    position: absolute;
    top: -10px; /* Slightly above footer */
    right: 20px;
}

#scrollBtn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #2563eb !important;
    color: white !important;
    border: none !important;
    font-size: 20px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    cursor: pointer;
    z-index: 1001;
}

#scrollBtn.show {
    opacity: 1;
    visibility: visible;
}

#scrollBtn:hover {
    transform: scale(1.1);
    background: #1e40af !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.6);
}

@media (max-width: 768px) {
    .scroll-btn-container {
        right: 15px;
    }
    #scrollBtn {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}
</style>

<footer class="footer-main">
    <div class="footer-bg">
        <div class="footer-inner">
            <div class="bg-white shadow-sm px-4 px-md-5 py-4">
                <div class="row gy-4 text-center text-md-start">
                    <!-- Brand -->
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-3">
                            <img src="logo.png" alt="Jan Suraksha" style="height:48px;border-radius:50%;border:2px solid #2563eb;" class="me-2">
                            <div>
                                <h5 class="fw-bold text-primary mb-0">Jan Suraksha</h5>
                                <small class="text-muted">Aapki Suraksha, Hamari Zimmedari</small>
                            </div>
                        </div>
                        <p class="text-muted mb-0">A public safety & grievance redressal portal to report incidents and track complaints securely.</p>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-md-2">
                        <h6 class="fw-bold text-primary mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li><a href="file-complaint.php" class="text-muted text-decoration-none"><i class="bi bi-flag-fill me-2 text-primary"></i>File Complaint</a></li>
                            <li><a href="track-status.php" class="text-muted text-decoration-none"><i class="bi bi-search me-2 text-primary"></i>Track Status</a></li>
                            <li><a href="blog.php" class="text-muted text-decoration-none"><i class="bi bi-journal-text me-2 text-primary"></i>Blog</a></li>
                            <li><a href="about-us.php" class="text-muted text-decoration-none"><i class="bi bi-info-circle me-2 text-primary"></i>About Us</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="col-md-2">
                        <h6 class="fw-bold text-primary mb-3">Legal</h6>
                        <ul class="list-unstyled">
                            <li><a href="privacy.php" class="text-muted text-decoration-none"><i class="bi bi-shield-check me-2 text-primary"></i>Privacy</a></li>
                            <li><a href="terms.php" class="text-muted text-decoration-none"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Terms</a></li>
                            <li><a href="admin/index.php" class="text-muted text-decoration-none"><i class="bi bi-lock-fill me-2 text-primary"></i>Admin</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="col-md-4">
                        <h6 class="fw-bold text-primary mb-3">Contact Us</h6>
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

            <!-- Scroll Button -->
            <div class="scroll-btn-container">
                <button id="scrollBtn" aria-label="Scroll navigation" title="Scroll to top/bottom">
                    <span class="scroll-icon" aria-hidden="true">⬆</span>
                </button>
            </div>

            <!-- Copyright -->
            <div class="text-center text-white small mt-2 p-2 bg-dark bg-opacity-50">
                © <?= date('Y') ?> Jan Suraksha Portal. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const footer = document.querySelector('.footer-main');
    const scrollBtn = document.getElementById('scrollBtn');
    if (!footer || !scrollBtn) return;

    let isAtTop = true;

    function toggleFooter() {
        const scrollTop = window.scrollY;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= docHeight - 60) {
            footer.classList.add('visible');
        } else {
            footer.classList.remove('visible');
        }
    }

    function adjustBodyPadding() {
        const footerHeight = footer.offsetHeight;
        document.body.style.paddingBottom = footerHeight + 'px';
    }

    function handleScroll() {
        toggleFooter();
        
        // Scroll button logic
        if (window.scrollY > 300) {
            scrollBtn.classList.add('show');
        } else {
            scrollBtn.classList.remove('show');
        }
        
        isAtTop = window.scrollY === 0;
        scrollBtn.querySelector('.scroll-icon').textContent = isAtTop ? '⬇' : '⬆';
        scrollBtn.title = isAtTop ? 'Scroll to bottom' : 'Scroll to top';
    }

    function handleScrollClick() {
        window.scrollTo({
            top: isAtTop ? document.body.scrollHeight : 0,
            behavior: 'smooth'
        });
    }

    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', adjustBodyPadding);
    scrollBtn.addEventListener('click', handleScrollClick);
    
    // Initial calls
    handleScroll();
    adjustBodyPadding();
});
</script>

</main>
</body>
</html>
