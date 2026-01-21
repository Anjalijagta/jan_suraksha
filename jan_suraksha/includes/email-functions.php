<?php
/**
 * Email Functions for Jan Suraksha
 * 
 * Phase 3: Email Notifications System
 * Issue #137 - Add "Urgent Complaint" Flag
 * 
 * This file handles sending urgent complaint notifications to administrators.
 * It supports both PHPMailer (SMTP) and native PHP mail() function.
 * 
 * CRITICAL: Email sending MUST NOT block complaint submission.
 * All email operations are wrapped in try-catch to ensure graceful failure.
 */

require_once __DIR__ . '/../email-config.php';
require_once __DIR__ . '/../email-templates/urgent-complaint.php';

/**
 * Send urgent complaint notification to admin
 * 
 * This is the main entry point for sending urgent complaint emails.
 * It handles all error cases gracefully and never throws exceptions
 * that could break the complaint submission flow.
 * 
 * @param array $complaintData Complaint data with keys:
 *   - complaint_id: int (database ID)
 *   - complaint_code: string (tracking ID like IN/2026/00001)
 *   - crime_type: string
 *   - location: string
 *   - date_filed: string (datetime)
 *   - urgency_justification: string
 *   - is_anonymous: int (0 or 1, optional)
 * 
 * @return array Result with keys:
 *   - success: bool (whether email was sent)
 *   - message: string (status message)
 *   - error: string|null (error details if failed)
 */
function sendUrgentComplaintEmail($complaintData) {
    // Check if email notifications are enabled
    if (!defined('URGENT_EMAIL_ENABLED') || !URGENT_EMAIL_ENABLED) {
        return [
            'success' => true,
            'message' => 'Email notifications are disabled in configuration',
            'error' => null
        ];
    }
    
    // Validate admin email configuration
    if (!defined('ADMIN_EMAIL') || empty(ADMIN_EMAIL)) {
        $error = 'Admin email not configured. Please set ADMIN_EMAIL in email-config.php';
        logEmailAttempt($complaintData['complaint_code'] ?? 'UNKNOWN', [
            'success' => false,
            'message' => $error
        ]);
        
        return [
            'success' => false,
            'message' => 'Email configuration error',
            'error' => $error
        ];
    }
    
    // Validate complaint data
    $validation = validateComplaintData($complaintData);
    if (!$validation['valid']) {
        $error = 'Invalid complaint data: ' . implode(', ', $validation['errors']);
        logEmailAttempt($complaintData['complaint_code'] ?? 'UNKNOWN', [
            'success' => false,
            'message' => $error
        ]);
        
        return [
            'success' => false,
            'message' => 'Data validation failed',
            'error' => $error
        ];
    }
    
    try {
        // Prepare email data for template
        $emailData = prepareEmailData($complaintData);
        
        // Generate email content from templates
        $htmlBody = getUrgentComplaintEmailHTML($emailData);
        $textBody = getUrgentComplaintEmailText($emailData);
        
        // Create email subject
        $subject = '🚨 URGENT Complaint Received - ' . $emailData['complaintCode'];
        
        // Attempt to send email
        $result = sendEmail(ADMIN_EMAIL, $subject, $htmlBody, $textBody);
        
        // Log the attempt
        logEmailAttempt($complaintData['complaint_code'], $result);
        
        return $result;
        
    } catch (Exception $e) {
        // Catch any unexpected errors
        $error = 'Unexpected error: ' . $e->getMessage();
        
        logEmailAttempt($complaintData['complaint_code'] ?? 'UNKNOWN', [
            'success' => false,
            'message' => $error
        ]);
        
        return [
            'success' => false,
            'message' => 'Email sending failed',
            'error' => $error
        ];
    }
}

/**
 * Prepare complaint data for email template
 * Converts database data into template-friendly format
 * 
 * @param array $complaintData Raw complaint data
 * @return array Formatted data for email template
 */
function prepareEmailData($complaintData) {
    // Format date
    $dateFiled = isset($complaintData['date_filed']) 
        ? date('F j, Y \a\t g:i A', strtotime($complaintData['date_filed']))
        : date('F j, Y \a\t g:i A');
    
    // Generate admin panel link
    $adminPanelLink = getAdminPanelUrl($complaintData['complaint_id']);
    
    // Check if anonymous
    $isAnonymous = isset($complaintData['is_anonymous']) && $complaintData['is_anonymous'] == 1;
    
    return [
        'complaintCode' => $complaintData['complaint_code'],
        'crimeType' => $complaintData['crime_type'],
        'location' => $complaintData['location'],
        'dateFiled' => $dateFiled,
        'urgencyJustification' => $complaintData['urgency_justification'],
        'adminPanelLink' => $adminPanelLink,
        'isAnonymous' => $isAnonymous
    ];
}

/**
 * Validate complaint data before sending email
 * 
 * @param array $data Complaint data
 * @return array ['valid' => bool, 'errors' => array]
 */
function validateComplaintData($data) {
    $errors = [];
    
    // Required fields for email
    $required = [
        'complaint_id' => 'Complaint ID',
        'complaint_code' => 'Complaint code',
        'crime_type' => 'Crime type',
        'location' => 'Location',
        'urgency_justification' => 'Urgency justification'
    ];
    
    foreach ($required as $field => $label) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = "{$label} is missing";
        }
    }
    
    // Validate complaint ID is numeric
    if (isset($data['complaint_id']) && !is_numeric($data['complaint_id'])) {
        $errors[] = "Complaint ID must be numeric";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Send email using available method (PHPMailer or native mail)
 * Automatically detects which method to use
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $textBody Plain text email body
 * @return array Result with success status and message
 */
function sendEmail($to, $subject, $htmlBody, $textBody) {
    // Check if PHPMailer is available and SMTP is enabled
    if (defined('SMTP_ENABLED') && SMTP_ENABLED && class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return sendEmailViaPHPMailer($to, $subject, $htmlBody, $textBody);
    } else {
        // Fallback to native PHP mail() function
        return sendEmailViaNativeMail($to, $subject, $htmlBody, $textBody);
    }
}

/**
 * Send email using PHPMailer with SMTP
 * This is the preferred method for production environments
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $htmlBody HTML body
 * @param string $textBody Plain text body
 * @return array Result
 */
function sendEmailViaPHPMailer($to, $subject, $htmlBody, $textBody) {
    // PHPMailer implementation would go here
    // For now, this is a placeholder that falls back to native mail
    
    // Check if PHPMailer class exists
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return [
            'success' => false,
            'message' => 'PHPMailer not installed',
            'error' => 'PHPMailer class not found. Please install via Composer or use native mail()'
        ];
    }
    
    try {
        // This would be the full PHPMailer implementation:
        /*
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;
        
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email sent successfully via SMTP to ' . $to,
            'error' => null
        ];
        */
        
        // For now, fallback to native mail
        return sendEmailViaNativeMail($to, $subject, $htmlBody, $textBody);
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'PHPMailer error',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Send email using PHP's native mail() function
 * This is the fallback method - works on most servers but less reliable
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $htmlBody HTML body
 * @param string $textBody Plain text body (not used with native mail HTML)
 * @return array Result
 */
function sendEmailViaNativeMail($to, $subject, $htmlBody, $textBody) {
    try {
        // Validate email address
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid recipient email address',
                'error' => "Email address '{$to}' is not valid"
            ];
        }
        
        // Prepare headers for HTML email
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        // From header
        $fromName = defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'Jan Suraksha';
        $fromAddress = defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : 'noreply@jansuraksha.com';
        $headers[] = 'From: ' . $fromName . ' <' . $fromAddress . '>';
        $headers[] = 'Reply-To: ' . $fromAddress;
        
        // Additional headers
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'X-Priority: 1 (Highest)';  // Mark as high priority
        $headers[] = 'Importance: High';
        
        // Combine headers
        $headerString = implode("\r\n", $headers);
        
        // Encode subject to handle special characters
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        
        // Send email
        $sent = @mail($to, $encodedSubject, $htmlBody, $headerString);
        
        if ($sent) {
            return [
                'success' => true,
                'message' => 'Email sent successfully to ' . $to,
                'error' => null
            ];
        } else {
            // Get error info if available
            $lastError = error_get_last();
            $errorMessage = $lastError ? $lastError['message'] : 'Unknown mail() error';
            
            return [
                'success' => false,
                'message' => 'Failed to send email via mail() function',
                'error' => 'mail() returned false. Server may not be configured for email sending. Error: ' . $errorMessage
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Exception while sending email',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Log email sending attempts
 * Creates a log file for debugging email issues
 * 
 * @param string $complaintCode Complaint tracking code
 * @param array $result Result from email sending attempt
 * @return void
 */
function logEmailAttempt($complaintCode, $result) {
    // Check if debug logging is enabled
    if (!defined('EMAIL_DEBUG') || !EMAIL_DEBUG) {
        return;
    }
    
    try {
        // Get log file path
        $logFile = defined('EMAIL_LOG_FILE') 
            ? EMAIL_LOG_FILE 
            : __DIR__ . '/../logs/email-log.txt';
        
        $logDir = dirname($logFile);
        
        // Create logs directory if it doesn't exist
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Check log file size and rotate if needed
        if (file_exists($logFile)) {
            $fileSize = filesize($logFile);
            $maxSize = defined('EMAIL_LOG_MAX_SIZE') ? EMAIL_LOG_MAX_SIZE : 5242880; // 5MB default
            
            if ($fileSize > $maxSize) {
                // Rotate log file
                $backupFile = $logFile . '.' . date('Y-m-d-His') . '.bak';
                rename($logFile, $backupFile);
            }
        }
        
        // Prepare log entry
        $timestamp = date('Y-m-d H:i:s');
        $status = $result['success'] ? 'SUCCESS' : 'FAILED';
        $message = $result['message'] ?? 'No message';
        $error = isset($result['error']) && $result['error'] ? ' | Error: ' . $result['error'] : '';
        
        $logEntry = "[{$timestamp}] {$status} - Complaint: {$complaintCode} - {$message}{$error}\n";
        
        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
    } catch (Exception $e) {
        // Silently fail - don't break anything if logging fails
        error_log('Email log write failed: ' . $e->getMessage());
    }
}

/**
 * Test email configuration
 * Sends a test email to verify configuration is working
 * 
 * @param string $testEmail Email address to send test to
 * @return array Result with success status and message
 */
function sendTestEmail($testEmail = null) {
    // Use admin email if no test email provided
    if (!$testEmail) {
        $testEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : null;
    }
    
    if (!$testEmail) {
        return [
            'success' => false,
            'message' => 'No test email address provided',
            'error' => 'Please provide an email address or configure ADMIN_EMAIL'
        ];
    }
    
    // Prepare test email data
    $testData = [
        'complaintCode' => 'TEST-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3))),
        'crimeType' => 'Test Crime Type',
        'location' => 'Test Location',
        'dateFiled' => date('F j, Y \a\t g:i A'),
        'urgencyJustification' => 'This is a test email to verify the urgent complaint notification system is working correctly.',
        'adminPanelLink' => defined('ADMIN_PANEL_BASE_URL') ? ADMIN_PANEL_BASE_URL : 'http://localhost/admin',
        'isAnonymous' => false
    ];
    
    // Generate email content
    $htmlBody = getUrgentComplaintEmailHTML($testData);
    $textBody = getUrgentComplaintEmailText($testData);
    $subject = '🧪 TEST - Urgent Complaint Email System';
    
    // Send test email
    $result = sendEmail($testEmail, $subject, $htmlBody, $textBody);
    
    // Log the test
    logEmailAttempt($testData['complaintCode'], $result);
    
    return $result;
}

/**
 * Get email system status
 * Returns information about email configuration and readiness
 * 
 * @return array Status information
 */
function getEmailSystemStatus() {
    $status = [
        'enabled' => defined('URGENT_EMAIL_ENABLED') && URGENT_EMAIL_ENABLED,
        'admin_email_configured' => defined('ADMIN_EMAIL') && !empty(ADMIN_EMAIL) && ADMIN_EMAIL !== 'admin@jansuraksha.com',
        'smtp_enabled' => defined('SMTP_ENABLED') && SMTP_ENABLED,
        'smtp_configured' => false,
        'phpmailer_available' => class_exists('PHPMailer\\PHPMailer\\PHPMailer'),
        'method' => 'native mail()',
        'debug_enabled' => defined('EMAIL_DEBUG') && EMAIL_DEBUG,
        'log_file' => defined('EMAIL_LOG_FILE') ? EMAIL_LOG_FILE : __DIR__ . '/../logs/email-log.txt'
    ];
    
    // Check SMTP configuration
    if ($status['smtp_enabled']) {
        $status['smtp_configured'] = !empty(SMTP_USERNAME) && !empty(SMTP_PASSWORD) && !empty(SMTP_HOST);
        if ($status['smtp_configured'] && $status['phpmailer_available']) {
            $status['method'] = 'PHPMailer + SMTP';
        }
    }
    
    // Overall ready status
    $status['ready'] = $status['enabled'] && $status['admin_email_configured'];
    
    return $status;
}
?>
