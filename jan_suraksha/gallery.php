<?php require_once 'header.php'; ?>

<div id="page-content" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-images text-primary me-3"></i>
                Gallery
            </h1>
            <p class="lead text-muted">
                A visual journey through our initiatives, events, and community impact
            </p>
        </div>

        <!-- Gallery Filters -->
        <div class="text-center mb-4">
            <div class="btn-group" role="group" aria-label="Gallery filters">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-primary" data-filter="events">Events</button>
                <button type="button" class="btn btn-outline-primary" data-filter="awareness">Awareness Campaigns</button>
                <button type="button" class="btn btn-outline-primary" data-filter="team">Our Team</button>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row g-4" id="galleryGrid">
            <!-- Event Images -->
            <div class="col-md-6 col-lg-4 gallery-item" data-category="events">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-calendar-event display-1 text-primary"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Community Safety Workshop</h5>
                        <p class="card-text text-muted">Annual workshop on public safety and awareness</p>
                        <small class="text-muted">January 2026</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 gallery-item" data-category="events">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-people-fill display-1 text-success"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Women Safety Seminar</h5>
                        <p class="card-text text-muted">Empowerment through awareness and education</p>
                        <small class="text-muted">December 2025</small>
                    </div>
                </div>
            </div>

            <!-- Awareness Campaign Images -->
            <div class="col-md-6 col-lg-4 gallery-item" data-category="awareness">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-megaphone-fill display-1 text-warning"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Cyber Safety Campaign</h5>
                        <p class="card-text text-muted">Digital awareness for all age groups</p>
                        <small class="text-muted">November 2025</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 gallery-item" data-category="awareness">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-shield-check display-1 text-info"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Road Safety Drive</h5>
                        <p class="card-text text-muted">Promoting safe driving practices</p>
                        <small class="text-muted">October 2025</small>
                    </div>
                </div>
            </div>

            <!-- Team Images -->
            <div class="col-md-6 col-lg-4 gallery-item" data-category="team">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-person-badge display-1 text-danger"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Our Dedicated Team</h5>
                        <p class="card-text text-muted">Working together for your safety</p>
                        <small class="text-muted">Team Photo</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 gallery-item" data-category="team">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-award-fill display-1 text-secondary"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Recognition Ceremony</h5>
                        <p class="card-text text-muted">Honoring dedicated service members</p>
                        <small class="text-muted">September 2025</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Notice -->
        <div class="alert alert-info mt-5" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Note:</strong> More photos will be added soon. We regularly update our gallery with new events and activities.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Filter gallery items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });

    // Add transition styles
    galleryItems.forEach(item => {
        item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    });
});
</script>

<?php require_once 'footer.php'; ?>
