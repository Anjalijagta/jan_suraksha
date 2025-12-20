<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$err=''; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if(!$subject || !$message) $err = 'Please provide subject and message.';
    else {
        $stmt = $mysqli->prepare('INSERT INTO feedback (name,email,subject,message,created_at) VALUES (?,?,?,?,NOW())');
        $stmt->bind_param('ssss',$name,$email,$subject,$message);
        $stmt->execute();
        $success = 'Thank you! Your feedback has been successfully submitted.';
    }
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


<main id="page-content" class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="content-container p-4 p-md-5">
                <h1 class="h3 mb-4">Send Us Your Feedback</h1>
                <p class="text-muted">We value your opinion. Let us know how we can improve.</p>
                
                <?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
                
                <?php if(!$success): // Hide form on success ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name (optional)</label>
                        <input class="form-control" id="name" name="name" value="<?= isset($_SESSION['user_name']) ? e($_SESSION['user_name']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (optional)</label>
                        <input class="form-control" id="email" name="email" type="email">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select class="form-select" id="subject" name="subject">
                            <option>General Inquiry</option>
                            <option>Bug Report</option>
                            <option>Feature Suggestion</option>
                            <option>Praise</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Feedback</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
