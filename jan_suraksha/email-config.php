<?php
/**
 * Email Configuration for Jan Suraksha - Urgent Complaint Notifications
 * 
 * Phase 3: Email Notifications System
 * Issue #137 - Add "Urgent Complaint" Flag
 * 
 * SETUP INSTRUCTIONS:
 * 1. Set ADMIN_EMAIL to a real email address where urgent notifications should be sent
 * 2. Configure SMTP settings if using Gmail/Outlook (recommended for production)
 * 3. Set URGENT_EMAIL_ENABLED to true to activate email notifications
 * 4. For testing locally, you can use native mail() or configure a test SMTP service
 * 
 * SECURITY NOTE:
 * - Never commit SMTP passwords to Git
 * - Use environment variables or .env file for sensitive data in production
 * - This file should be added to .gitignore if it contains real credentials
 */

// ============================================
// ADMIN EMAIL RECIPIENTS
// ============================================

// Primary admin email - urgent complaint notifications will be sent here
define('ADMIN_EMAIL', 'admin@jansuraksha.com');

// Optional: Additional CC recipients (comma-separated)
// Example: 'supervisor@jansuraksha.com,manager@jansuraksha.com'
define('ADMIN_EMAIL_CC', '');

// ============================================
// EMAIL SENDER CONFIGURATION
// ============================================

// Email address that appears in the "From" field
define('EMAIL_FROM_ADDRESS', 'noreply@jansuraksha.com');

// Display name for the sender
define('EMAIL_FROM_NAME', 'Jan Suraksha - Complaint Management System');

// ============================================
// SMTP SETTINGS (Optional - For Production)
// ============================================

// Enable SMTP for reliable email delivery (recommended for production)
// Set to false to use PHP's native mail() function (works for testing)
define('SMTP_ENABLED', false);

// SMTP Server Configuration (only used if SMTP_ENABLED = true)
define('SMTP_HOST', 'smtp.gmail.com');        // Gmail SMTP server
define('SMTP_PORT', 587);                      // TLS port (587) or SSL port (465)
define('SMTP_ENCRYPTION', 'tls');              // 'tls' or 'ssl'

// SMTP Authentication
// NOTE: For Gmail, use an "App Password" not your regular password
// Create App Password: https://myaccount.google.com/apppasswords
define('SMTP_USERNAME', '');                   // Your Gmail address
define('SMTP_PASSWORD', '');                   // App Password (not regular password!)

// ============================================
// EMAIL NOTIFICATION SETTINGS
// ============================================

// Master switch for urgent complaint email notifications
// Set to false to disable all urgent email notifications
define('URGENT_EMAIL_ENABLED', true);

// Email debugging mode
// Set to true to enable detailed email logging in logs/email-log.txt
// Set to false in production to avoid large log files
define('EMAIL_DEBUG', true);

// ============================================
// ADMIN PANEL CONFIGURATION
// ============================================

// Base URL for the admin panel
// Used to generate "View Complaint" links in emails
// IMPORTANT: Update this with your actual admin panel URL
define('ADMIN_PANEL_BASE_URL', 'http://localhost/jan_suraksha/jan_suraksha/admin');

// Full URL pattern for viewing complaints
// {ID} will be replaced with the actual complaint ID
define('ADMIN_PANEL_URL', ADMIN_PANEL_BASE_URL . '/update-case.php?id={ID}');

// ============================================
// EMAIL TEMPLATE SETTINGS
// ============================================

// System branding for email footer
define('SYSTEM_NAME', 'Jan Suraksha');
define('SYSTEM_TAGLINE', 'AAPKI SURAKSHA, HAMARI ZIMMEDARI');

// Current year for copyright notice (auto-generated)
define('COPYRIGHT_YEAR', date('Y'));

// ============================================
// RATE LIMITING (Future Enhancement)
// ============================================

// Maximum emails to send per hour (to prevent spam)
// Not implemented yet, but reserved for future use
define('EMAIL_RATE_LIMIT', 50);

// ============================================
// LOGGING CONFIGURATION
// ============================================

// Log file path
define('EMAIL_LOG_FILE', __DIR__ . '/logs/email-log.txt');

// Maximum log file size (in bytes) before rotation
// 5MB = 5 * 1024 * 1024 bytes
define('EMAIL_LOG_MAX_SIZE', 5242880);

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Get the admin panel URL for a specific complaint
 * 
 * @param int $complaintId The complaint ID
 * @return string Full URL to view complaint in admin panel
 */
function getAdminPanelUrl($complaintId) {
    return str_replace('{ID}', $complaintId, ADMIN_PANEL_URL);
}

/**
 * Check if email notifications are properly configured
 * 
 * @return array ['configured' => bool, 'message' => string]
 */
function checkEmailConfiguration() {
    $issues = [];
    
    // Check if emails are enabled
    if (!URGENT_EMAIL_ENABLED) {
        $issues[] = 'Email notifications are disabled (URGENT_EMAIL_ENABLED = false)';
    }
    
    // Check if admin email is set
    if (empty(ADMIN_EMAIL) || ADMIN_EMAIL === 'admin@jansuraksha.com') {
        $issues[] = 'Admin email is not configured. Please set ADMIN_EMAIL in email-config.php';
    }
    
    // Check if SMTP is configured (if enabled)
    if (SMTP_ENABLED) {
        if (empty(SMTP_USERNAME) || empty(SMTP_PASSWORD)) {
            $issues[] = 'SMTP is enabled but credentials are not set';
        }
    }
    
    if (empty($issues)) {
        return [
            'configured' => true,
            'message' => 'Email configuration is valid'
        ];
    } else {
        return [
            'configured' => false,
            'message' => implode('; ', $issues)
        ];
    }
}

// ============================================
// PRODUCTION DEPLOYMENT NOTES
// ============================================

/**
 * BEFORE DEPLOYING TO PRODUCTION:
 * 
 * 1. Email Configuration:
 *    - Set ADMIN_EMAIL to a real monitored email address
 *    - Configure SMTP with a reliable email service (Gmail, SendGrid, AWS SES)
 *    - Test email delivery from production server
 * 
 * 2. Security:
 *    - Use environment variables for SMTP_USERNAME and SMTP_PASSWORD
 *    - Add email-config.php to .gitignore if it contains real credentials
 *    - Enable HTTPS for secure email transmission
 * 
 * 3. Deliverability:
 *    - Configure SPF and DKIM records for your domain
 *    - Verify sender domain with email service provider
 *    - Monitor email bounce rates and spam reports
 * 
 * 4. Performance:
 *    - Consider using a queue system for email sending (Redis, RabbitMQ)
 *    - Implement rate limiting to prevent abuse
 *    - Set up email monitoring/alerting (Sentry, New Relic)
 * 
 * 5. Testing:
 *    - Test with real email addresses before launch
 *    - Verify HTML rendering in multiple email clients (Gmail, Outlook, etc.)
 *    - Check spam score using tools like Mail Tester
 *    - Test failure scenarios (email server down, invalid recipient)
 */
?>
