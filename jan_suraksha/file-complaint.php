<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$err = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $user_id = $_SESSION['user_id'] ?? null;
    // If user not logged in, block server-side processing and show an error (client-side modal will prompt login)
    if (empty($user_id)) {
        $err = 'Please login or sign up before filing a complaint.';
    }
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

    // validation
    if(!$name || !preg_match('/^[0-9]{10}$/', $mobile) || !$crime){
        $err = 'Please fill required fields correctly (name, 10-digit mobile, crime type).';
    } elseif($pincode && !preg_match('/^[0-9]{6}$/',$pincode)){
        $err = 'Pincode must be 6 digits.';
    } else {
        // handle file upload
        $uploadedFile = null;
        if(!empty($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK){
            $u = $_FILES['evidence'];
            $allowed = ['image/jpeg','image/png','application/pdf','video/mp4'];
            if(!in_array($u['type'],$allowed)){
                $err = 'Unsupported file type. Allowed: JPG, PNG, PDF, MP4';
            } elseif($u['size'] > 20 * 1024 * 1024){ // 20 MB limit
                $err = 'File too large. Maximum 20MB.';
            } else {
                $ext = pathinfo($u['name'], PATHINFO_EXTENSION);
                $safe = bin2hex(random_bytes(16)) . '.' . $ext;
                $destDir = __DIR__ . '/uploads';
                if(!is_dir($destDir)) mkdir($destDir,0755,true);
                $dest = $destDir . '/' . $safe;
                if(move_uploaded_file($u['tmp_name'],$dest)){
                    $uploadedFile = $safe;
                } else {
                    $err = 'Failed to move uploaded file.';
                }
            }
        }

        if(!$err){
            // generate complaint code
            $prefix = 'IN/'.date('Y').'/';
            $code = $prefix . str_pad(rand(1,99999),5,'0',STR_PAD_LEFT);
            
            // *** DEFINITIVE FIX APPLIED HERE ***
            // 1. Combine address fields into one string to avoid losing data.
            $complainantAddress = trim("$house, $city, $state - $pincode");
            if ($complainantAddress === ',  -') { // Check for empty address
                $complainantAddress = '';
            }
            
            // 2. Prepend the address to the main description.
            $finalDescription = $desc;
            if (!empty($complainantAddress)) {
                $finalDescription = "Complainant Address: " . $complainantAddress . "\n\n---\n\n" . $desc;
            }

            // 3. Use the correct column names from your original schema.
            // Include `evidence` column so column count matches bound values/placeholders.
            $stmt = $mysqli->prepare('INSERT INTO complaints (user_id, complaint_code, complainant_name, mobile, crime_type, date_filed, location, description, evidence, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            
            // Ensure uid is an integer (user must be logged in due to earlier check)
            $uid = $user_id ? (int)$user_id : 0;
            $status = 'Pending';

            // Normalize evidence to an empty string if nothing uploaded to avoid null-binding issues
            $uploadedFile = $uploadedFile ?? '';

            // Check prepare() success before binding
            if ($stmt === false) {
                $err = 'Database prepare error: ' . $mysqli->error;
            } else {
                // 4. Bind the correct variables to the corrected query.
                $stmt->bind_param('isssssssss', $uid, $code, $name, $mobile, $crime, $date, $location, $finalDescription, $uploadedFile, $status);

                if($stmt->execute()){
                    header('Location: complain-success.php?code='.urlencode($code)); 
                    exit;
                } else {
                    // Provide a more detailed error for debugging
                    $err = 'Database execution error: ' . $stmt->error;
                }
            }
        }
    }
}
?>
<?php include 'header.php'; ?>

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
                    
                    <section class="mb-4">
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
});
</script>

<?php include 'footer.php'; ?>
