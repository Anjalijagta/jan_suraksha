<?php
require_once __DIR__ . '/config.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    // CSRF Protection
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $err = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $user_id = $_SESSION['user_id'] ?? null;

        if (empty($user_id)) {
            $err = 'Please login before filing a complaint.';
        }

    // Check if this is an anonymous complaint
    $isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] == '1';

    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $house = trim($_POST['house'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $crime = trim($_POST['crime_type'] ?? '');
    $date = trim($_POST['incident_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    // Urgent flag processing
    $isUrgent = isset($_POST['is_urgent']) && $_POST['is_urgent'] == '1';
    $urgencyJustification = trim($_POST['urgency_justification'] ?? '');

    // Validation
    if (!$name || !preg_match('/^[0-9]{10}$/', $mobile) || !$crime) {
        $err = 'Fill required fields: name, 10-digit mobile, crime type.';
    } elseif ($pincode && !preg_match('/^[0-9]{6}$/', $pincode)) {
        $err = 'Pincode must be 6 digits.';
    } elseif ($isUrgent && empty($urgencyJustification)) {
        $err = 'Justification is required when marking complaint as urgent.';
    } elseif ($isUrgent && mb_strlen($urgencyJustification) < 10) {
        $err = 'Urgency justification must be at least 10 characters.';
    } elseif ($isUrgent && mb_strlen($urgencyJustification) > 500) {
        $err = 'Urgency justification cannot exceed 500 characters.';
    } else {
        // Handle file upload
        $uploadedFile = null;
        if (!empty($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
            $u = $_FILES['evidence'];
            $allowed = ['image/jpeg', 'image/png', 'application/pdf', 'video/mp4'];

            if (!empty($_FILES['evidence']) && $_FILES['evidence']['error'] !== UPLOAD_ERR_NO_FILE) {
            $evidenceFile = $_FILES['evidence'];

            // Strict allow-list: images + selected document/media types
            $allowedEvidenceTypes = [
                'jpg'  => ['image/jpeg', 'image/pjpeg'],
                'jpeg' => ['image/jpeg', 'image/pjpeg'],
                'png'  => ['image/png'],
                'pdf'  => ['application/pdf'],
                'mp4'  => ['video/mp4', 'video/x-m4v'],
            ];

            $maxEvidenceSize = 20 * 1024 * 1024; // 20MB
            $uploadError = null;
            $destDir = __DIR__ . '/uploads';

            $storedName = js_secure_upload($evidenceFile, $allowedEvidenceTypes, $destDir, $maxEvidenceSize, $uploadError, 'evidence');

            if ($uploadError !== null) {
                $err = $uploadError . ' Allowed types: JPG, JPEG, PNG, PDF, MP4.';
            } else {
                $uploadedFile = $storedName;
            }
        }

        if (!$err) {
            // Generate complaint code
            $prefix = 'IN/' . date('Y') . '/';
            $code = $prefix . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Generate anonymous tracking ID if this is an anonymous complaint
            $anonymousTrackingId = null;
            if ($isAnonymous) {
                // Format: ANON-YYYY-XXXXXX (6 random hex characters)
                $anonymousTrackingId = 'ANON-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            }

            // Combine address fields (only for regular complaints)
            $complainantAddress = '';
            if (!$isAnonymous) {
                $complainantAddress = trim("$house, $city, $state - $pincode");
                if ($complainantAddress === ',  -') {
                    $complainantAddress = '';
                }
            }

            // Prepend address to description
            $finalDescription = $desc;
            if (!empty($complainantAddress)) {
                $finalDescription = "Complainant Address: " . $complainantAddress . "\n\n---\n\n" . $desc;
            }

            // Prepare INSERT statement with urgent flag support
            $stmt = $mysqli->prepare('INSERT INTO complaints (user_id, complaint_code, complainant_name, mobile, crime_type, date_filed, location, description, evidence, status, is_urgent, urgency_justification, urgent_marked_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

            if ($stmt === false) {
                $err = 'Database error: ' . $mysqli->error;
            } else {
                $uid = (int)$user_id;
                $uploadedFile = $uploadedFile ?? '';
                $status = 'Pending';
                
                // Sanitize urgency justification (XSS protection)
                $sanitizedJustification = $isUrgent ? htmlspecialchars($urgencyJustification, ENT_QUOTES, 'UTF-8') : null;
                $urgentFlag = $isUrgent ? 1 : 0;
                // Set timestamp only for urgent complaints, NULL otherwise
                $urgentTimestamp = $isUrgent ? date('Y-m-d H:i:s') : null;

                $stmt->bind_param('issssssssiiss', $uid, $code, $name, $mobile, $crime, $date, $location, $finalDescription, $uploadedFile, $status, $urgentFlag, $sanitizedJustification, $urgentTimestamp);

                if ($stmt->execute()) {
                    // Get the inserted complaint ID
                    $complaintId = $mysqli->insert_id;
                    
                    // Phase 3: Send urgent complaint email notification
                    if ($isUrgent) {
                        try {
                            // Include email functions
                            require_once __DIR__ . '/includes/email-functions.php';
                            
                            // Prepare complaint data for email
                            $emailComplaintData = [
                                'complaint_id' => $complaintId,
                                'complaint_code' => $isAnonymous ? $anonymousTrackingId : $code,
                                'crime_type' => $crime,
                                'location' => $location,
                                'date_filed' => date('Y-m-d H:i:s'),
                                'urgency_justification' => $urgencyJustification,
                                'is_anonymous' => $isAnonymous ? 1 : 0
                            ];
                            
                            // Send email notification (non-blocking - don't fail if email fails)
                            $emailResult = sendUrgentComplaintEmail($emailComplaintData);
                            
                            // Log the result (optional - silently fails if logging not configured)
                            if (function_exists('logEmailAttempt')) {
                                logEmailAttempt($emailComplaintData['complaint_code'], $emailResult);
                            }
                            
                            // Note: We don't show email errors to the user
                            // The complaint is successfully filed regardless of email status
                            
                        } catch (Exception $e) {
                            // Silently log error - don't show to user, don't fail submission
                            error_log('Phase 3 - Urgent email notification failed: ' . $e->getMessage());
                        }
                    }
                    
                    // Redirect to appropriate success page
                    if ($isAnonymous) {
                        header('Location: anonymous-success.php?tracking_id=' . urlencode($anonymousTrackingId));
                    } else {
                        header('Location: complain-success.php?code=' . urlencode($code));
                    }
                    exit;
                } else {
                    $err = 'Error filing complaint: ' . $stmt->error;
                }
            }
        }
    }
}
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/anonymous.css">

<style>
    .form-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        background-color: #f1f3f5;
        border: none;
        border-radius: 8px;
        padding: 0.8rem 1rem;
        transition: box-shadow 0.2s;
    }
    .form-control:focus, .form-select:focus {
        background-color: #f1f3f5;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
    }
    .form-section-heading {
        font-size: 1.25rem;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .upload-area {
        border: 2px dashed #ced4da;
        border-radius: 8px;
        padding: 2.5rem;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: background-color 0.2s, border-color 0.2s;
    }
    .upload-area:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    .upload-area .upload-icon {
        font-size: 3rem;
        color: #0d6efd;
    }
    #evidence-file-input {
        display: none;
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


<main id="page-content" class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="form-container p-4 p-md-5">
                
                <div class="d-flex align-items-center mb-4">
                    <h1 class="h3 mb-0">File a Complaint</h1>
                </div>

                <?php if($err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" id="complaintForm">
                    <?php echo csrf_token_field(); ?>
                    
                    <!-- Anonymous Reporting Option -->
                    <section class="mb-4 p-3" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="anonymous-checkbox" name="is_anonymous" value="1">
                            <label class="form-check-label" for="anonymous-checkbox">
                                <strong>🔒 Report Anonymously</strong>
                                <p class="text-muted small mb-0">Your identity will be protected. Personal information fields will be hidden.</p>
                            </label>
                        </div>
                        
                        <!-- Privacy Disclaimer (Hidden by default, shown when anonymous is checked) -->
                        <div id="anonymous-disclaimer" class="alert alert-info mt-3" style="display: none; border-left: 4px solid #0dcaf0;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill me-2 mt-1" style="font-size: 1.25rem;"></i>
                                <div>
                                    <h6 class="mb-2"><strong>⚠️ Important: Anonymous Reporting</strong></h6>
                                    <ul class="mb-0 small">
                                        <li>Your identity will be completely protected</li>
                                        <li><strong>Save your tracking ID securely</strong> - you cannot recover it later</li>
                                        <li>Anonymous complaints may take longer to investigate</li>
                                        <li>No personal information will be stored in our system</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <section class="mb-4" id="personal-info-section">
                        <h2 class="form-section-heading">Complainant Details</h2>
                        <div class="mb-3">
                            <label for="name" class="form-label">Complainant's Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= isset($_SESSION['user_name']) ? e($_SESSION['user_name']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="+91 9876543210" pattern="[0-9]{10}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control mb-2" name="house" placeholder="House No. / Street Name">
                            <input type="text" class="form-control" name="city" placeholder="City / Town / Village">
                            <div class="row g-2 mt-0">
                                <div class="col-sm-8">
                                    <select class="form-select mt-2" name="state">
                                        <option value="">Select State</option>
                                        <option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option><option>Bihar</option><option>Chhattisgarh</option><option>Goa</option><option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option><option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option><option>Meghalaya</option><option>Mizoram</option><option>Nagaland</option><option>Odisha</option><option>Punjab</option><option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option><option>Telangana</option><option>Tripura</option><option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option><option>Delhi</option><option>Puducherry</option><option>Andaman and Nicobar Islands</option><option>Lakshadweep</option><option>Jammu & Kashmir</option><option>Ladakh</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control mt-2" name="pincode" placeholder="Pincode" pattern="[0-9]{6}">
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <section class="mb-4">
                        <h2 class="form-section-heading">Incident Details</h2>
                        <div class="mb-3">
                            <label for="crime_type" class="form-label">Type of Crime</label>
                            <select class="form-select" id="crime_type" name="crime_type" required>
                                <option value="">Select Crime Type</option>
                                <option>Theft</option><option>Assault</option><option>Cybercrime</option><option>Harassment</option><option>Missing Person</option><option>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="incident_date" class="form-label">Date and Time of Incident</label>
                            <input type="datetime-local" class="form-control" id="incident_date" name="incident_date">
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location of Incident</label>
                            <textarea class="form-control" id="location" name="location" rows="2" placeholder="Enter Location Details"></textarea>
                        </div>
                    </section>

                    <section class="mb-4">
                        <label for="description" class="form-label">Detailed Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide a detailed description of the incident" required></textarea>
                    </section>

                    <!-- Urgent Complaint Flag Section -->
                    <section class="mb-4 urgent-flag-section">
                        <div class="urgent-checkbox-wrapper">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="urgent-checkbox" name="is_urgent" value="1">
                                <label class="form-check-label" for="urgent-checkbox">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                    <strong>Mark as Urgent</strong>
                                </label>
                            </div>
                            <p class="urgent-help-text text-muted small mb-0 mt-1">
                                Check this box for time-sensitive emergencies (e.g., assault in progress, kidnapping, immediate danger)
                            </p>
                        </div>
                        
                        <!-- Justification Field (Hidden by Default) -->
                        <div id="urgency-justification-container" class="urgency-justification-container mt-3" style="display: none;">
                            <label for="urgency_justification" class="form-label">
                                <i class="bi bi-chat-left-text text-danger me-1"></i>
                                Why is this urgent? <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="urgency_justification" 
                                name="urgency_justification" 
                                rows="3" 
                                maxlength="500"
                                placeholder="Example: Suspect is still at the location, victim needs immediate medical attention, ongoing criminal activity, etc."
                            ></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Minimum 10 characters required</small>
                                <small class="text-muted" id="char-counter">0 / 500</small>
                            </div>
                        </div>
                    </section>

                    <section class="mb-4">
                        <h2 class="form-section-heading">Evidence Upload</h2>
                        <div id="upload-area" class="upload-area">
                            <input type="file" name="evidence" id="evidence-file-input">
                            <div class="upload-content">
                                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                <h5 class="mt-2 mb-1">Upload Files</h5>
                                <p class="text-muted small mb-2">Photos, videos, or documents</p>
                                <button type="button" class="btn btn-light border" id="browse-btn">Browse Files</button>
                                <div id="file-name-display" class="text-muted small mt-2"></div>
                            </div>
                        </div>
                    </section>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Complaint</button>
                    </div>
                </form>
                
                <!-- Login required modal shown when anonymous users try to submit -->
                <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="loginRequiredModalLabel">Please sign in</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                You need to be logged in to file a complaint. Please login or sign up to continue.
                            </div>
                            <div class="modal-footer">
                                <a href="login.php?next=file-complaint.php" class="btn btn-primary">Login</a>
                                <a href="register.php?next=file-complaint.php" class="btn btn-outline-secondary">Sign Up</a>
                                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('evidence-file-input');
    const browseBtn = document.getElementById('browse-btn');
    const fileNameDisplay = document.getElementById('file-name-display');

    uploadArea.addEventListener('click', () => fileInput.click());
    browseBtn.addEventListener('click', (e) => {
        e.stopPropagation(); 
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = 'Selected: ' + fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = '';
        }
    });

    // ===== URGENT FLAG FUNCTIONALITY =====
    const urgentCheckbox = document.getElementById('urgent-checkbox');
    const justificationContainer = document.getElementById('urgency-justification-container');
    const justificationTextarea = document.getElementById('urgency_justification');
    const charCounter = document.getElementById('char-counter');
    const complaintForm = document.getElementById('complaintForm');

    // Toggle justification field when checkbox is clicked
    urgentCheckbox.addEventListener('change', function() {
        if (this.checked) {
            justificationContainer.style.display = 'block';
            justificationTextarea.setAttribute('required', 'required');
        } else {
            justificationContainer.style.display = 'none';
            justificationTextarea.removeAttribute('required');
            justificationTextarea.value = ''; // Clear field when unchecked
            charCounter.textContent = '0 / 500';
        }
    });

    // Character counter for justification field
    justificationTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCounter.textContent = length + ' / 500';
        
        // Change color if minimum not met
        if (urgentCheckbox.checked && length < 10) {
            charCounter.classList.add('text-danger');
            charCounter.classList.remove('text-muted');
        } else {
            charCounter.classList.remove('text-danger');
            charCounter.classList.add('text-muted');
        }
    });

    // Client-side validation on form submit
    complaintForm.addEventListener('submit', function(e) {
        if (urgentCheckbox.checked) {
            const justification = justificationTextarea.value.trim();
            
            if (justification.length === 0) {
                e.preventDefault();
                alert('Please provide a justification for marking this complaint as urgent.');
                justificationTextarea.focus();
                return false;
            }
            
            if (justification.length < 10) {
                e.preventDefault();
                alert('Urgency justification must be at least 10 characters long.');
                justificationTextarea.focus();
                return false;
            }
        }
    });
});
</script>

<script src="js/anonymous-handler.js"></script>

<?php include 'footer.php'; ?>
