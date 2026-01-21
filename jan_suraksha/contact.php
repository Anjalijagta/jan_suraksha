<?php require_once 'header.php'; ?>

<div id="page-content" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-telephone-fill text-primary me-3"></i>
                Contact Us
            </h1>
            <p class="lead text-muted">
                We're here to help. Reach out to us through any of the channels below
            </p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Contact Information Cards -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-telephone-fill display-4 text-primary"></i>
                        </div>
                        <h5 class="card-title">Phone</h5>
                        <p class="card-text text-muted">
                            <a href="tel:+919372693389" class="text-decoration-none">+91-9372693389</a><br>
                            <a href="tel:+917972409656" class="text-decoration-none">+91-7972409656</a>
                        </p>
                        <small class="text-muted">Mon-Sat, 9 AM - 6 PM</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-whatsapp display-4 text-success"></i>
                        </div>
                        <h5 class="card-title">WhatsApp</h5>
                        <p class="card-text text-muted">
                            <a href="https://wa.me/919372693389" class="text-decoration-none" target="_blank">+91-9372693389</a><br>
                            <a href="https://wa.me/917972409656" class="text-decoration-none" target="_blank">+91-7972409656</a>
                        </p>
                        <small class="text-muted">Quick Response</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-envelope-fill display-4 text-danger"></i>
                        </div>
                        <h5 class="card-title">Email</h5>
                        <p class="card-text text-muted">
                            <a href="mailto:support@jansuraksha.in" class="text-decoration-none">support@jansuraksha.in</a>
                        </p>
                        <small class="text-muted">24/7 Email Support</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-geo-alt-fill display-4 text-warning"></i>
                        </div>
                        <h5 class="card-title">Address</h5>
                        <p class="card-text text-muted small">
                            Jan Suraksha Portal<br>
                            India
                        </p>
                        <small class="text-muted">Main Office</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4 text-center">
                            <i class="bi bi-chat-left-text-fill text-primary me-2"></i>
                            Send us a Message
                        </h3>
                        
                        <form id="contactForm" method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" pattern="[6-9][0-9]{9}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-chat-square-text-fill"></i></span>
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-primary text-white shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <h4 class="mb-3">Need Immediate Assistance?</h4>
                        <p class="mb-4">For urgent matters, please contact emergency services or file a complaint directly.</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="file-complaint.php" class="btn btn-light btn-lg">
                                <i class="bi bi-flag-fill me-2"></i>
                                File a Complaint
                            </a>
                            <a href="tel:100" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-telephone-fill me-2"></i>
                                Emergency: 100
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    contactForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const formData = new FormData(this);
        let isValid = true;
        
        // Validate phone number
        const phone = formData.get('phone');
        if (phone && !/^[6-9]\d{9}$/.test(phone)) {
            alert('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.');
            isValid = false;
            return;
        }
        
        if (isValid) {
            // Here you would typically send the data to a server
            alert('Thank you for contacting us! We will get back to you soon.');
            this.reset();
        }
    });
});
</script>

<?php require_once 'footer.php'; ?>
