<?php
/**
 * Email Template: Urgent Complaint Notification
 * 
 * Phase 3: Email Notifications
 * Issue #137 - Add "Urgent Complaint" Flag
 * 
 * This file contains both HTML and plain text email templates
 * for urgent complaint notifications sent to administrators.
 * 
 * Available Variables:
 * - $complaintCode: Tracking ID (e.g., IN/2026/00001 or ANON-2026-ABC123)
 * - $crimeType: Type of crime reported
 * - $location: Location where incident occurred
 * - $dateFiled: Date and time complaint was filed (formatted)
 * - $urgencyJustification: Reason why complaint is marked urgent
 * - $adminPanelLink: Direct link to view complaint in admin panel
 * - $isAnonymous: Whether the complaint is anonymous (optional)
 */

/**
 * Generate HTML email body for urgent complaint notification
 * 
 * @param array $data Associative array with email template variables
 * @return string HTML email body
 */
function getUrgentComplaintEmailHTML($data) {
    // Sanitize all data to prevent XSS
    $complaintCode = htmlspecialchars($data['complaintCode'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $crimeType = htmlspecialchars($data['crimeType'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
    $location = htmlspecialchars($data['location'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
    $dateFiled = htmlspecialchars($data['dateFiled'] ?? date('F j, Y \a\t g:i A'), ENT_QUOTES, 'UTF-8');
    $urgencyJustification = htmlspecialchars($data['urgencyJustification'] ?? 'No justification provided.', ENT_QUOTES, 'UTF-8');
    $adminPanelLink = htmlspecialchars($data['adminPanelLink'] ?? '#', ENT_QUOTES, 'UTF-8');
    $isAnonymous = isset($data['isAnonymous']) && $data['isAnonymous'] == 1;
    
    // Get system branding from config
    $systemName = defined('SYSTEM_NAME') ? SYSTEM_NAME : 'Jan Suraksha';
    $systemTagline = defined('SYSTEM_TAGLINE') ? SYSTEM_TAGLINE : 'AAPKI SURAKSHA, HAMARI ZIMMEDARI';
    $copyrightYear = defined('COPYRIGHT_YEAR') ? COPYRIGHT_YEAR : date('Y');
    
    $html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urgent Complaint Notification</title>
    <style>
        /* Reset styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        
        /* Main container */
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Header section */
        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .urgent-badge {
            background: #ffffff;
            color: #dc2626;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            display: inline-block;
            margin-top: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        ' . ($isAnonymous ? '
        .anonymous-indicator {
            background: #fbbf24;
            color: #78350f;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            display: inline-block;
            margin-top: 10px;
        }
        ' : '') . '
        
        /* Body section */
        .email-body {
            padding: 40px 30px;
        }
        
        .email-body p {
            margin: 0 0 20px 0;
            color: #374151;
            font-size: 15px;
        }
        
        /* Complaint details card */
        .complaint-details {
            background: #f9fafb;
            border-left: 5px solid #dc2626;
            padding: 25px;
            margin: 25px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .complaint-details h2 {
            margin: 0 0 20px 0;
            color: #111827;
            font-size: 20px;
            font-weight: 700;
        }
        
        .detail-row {
            margin: 14px 0;
            display: flex;
            align-items: baseline;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 120px;
            font-size: 14px;
        }
        
        .detail-value {
            color: #111827;
            flex: 1;
            font-size: 15px;
        }
        
        /* Urgency justification box */
        .urgency-box {
            background: linear-gradient(145deg, #fef2f2, #fee2e2);
            border: 2px solid #dc2626;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }
        
        .urgency-box h3 {
            color: #dc2626;
            margin: 0 0 12px 0;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .urgency-box p {
            margin: 0;
            color: #374151;
            font-size: 15px;
            line-height: 1.7;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        /* Call-to-action button */
        .cta-container {
            text-align: center;
            margin: 35px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white !important;
            padding: 16px 36px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
        }
        
        /* Footer section */
        .email-footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .email-footer p {
            margin: 8px 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .email-footer .brand {
            font-weight: 700;
            color: #111827;
            font-size: 14px;
        }
        
        /* Disclaimer text */
        .disclaimer {
            color: #9ca3af;
            font-size: 13px;
            margin-top: 25px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 6px;
            line-height: 1.6;
        }
        
        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 22px;
            }
            
            .email-body {
                padding: 25px 20px;
            }
            
            .complaint-details {
                padding: 20px 15px;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 4px;
            }
            
            .detail-label {
                min-width: auto;
            }
            
            .cta-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🚨 URGENT COMPLAINT RECEIVED</h1>
            <div class="urgent-badge">⚠️ IMMEDIATE ATTENTION REQUIRED</div>
            ' . ($isAnonymous ? '<div class="anonymous-indicator">🔒 ANONYMOUS COMPLAINT</div>' : '') . '
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <p><strong>Dear Admin,</strong></p>
            <p>A new <strong style="color: #dc2626;">urgent complaint</strong> has been submitted to the ' . $systemName . ' system and requires your immediate attention.</p>
            
            <!-- Complaint Details -->
            <div class="complaint-details">
                <h2>📋 Complaint Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Tracking ID:</span>
                    <span class="detail-value"><strong>' . $complaintCode . '</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Crime Type:</span>
                    <span class="detail-value">' . $crimeType . '</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">' . $location . '</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date Filed:</span>
                    <span class="detail-value">' . $dateFiled . '</span>
                </div>
                
                ' . ($isAnonymous ? '
                <div class="detail-row">
                    <span class="detail-label">Complainant:</span>
                    <span class="detail-value"><em style="color: #9ca3af;">Protected (Anonymous)</em></span>
                </div>
                ' : '') . '
            </div>
            
            <!-- Urgency Justification -->
            <div class="urgency-box">
                <h3>⚠️ Why This is Urgent:</h3>
                <p>' . nl2br($urgencyJustification) . '</p>
            </div>
            
            <!-- Call to Action -->
            <div class="cta-container">
                <a href="' . $adminPanelLink . '" class="cta-button">
                    View Complaint in Admin Panel →
                </a>
            </div>
            
            <div class="disclaimer">
                <strong>⏰ Action Required:</strong> Please review this complaint as soon as possible. Time-sensitive complaints require immediate attention to ensure public safety.
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p class="brand">' . $systemName . '</p>
            <p>' . $systemTagline . '</p>
            <p style="margin-top: 15px;">This is an automated notification from the ' . $systemName . ' Complaint Management System.</p>
            <p>You received this email because an urgent complaint was filed in your jurisdiction.</p>
            <p style="margin-top: 15px;">© ' . $copyrightYear . ' ' . $systemName . '. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
    ';
    
    return $html;
}

/**
 * Generate plain text email body for urgent complaint notification
 * Used as fallback for email clients that don't support HTML
 * 
 * @param array $data Associative array with email template variables
 * @return string Plain text email body
 */
function getUrgentComplaintEmailText($data) {
    // Sanitize data
    $complaintCode = $data['complaintCode'] ?? 'N/A';
    $crimeType = $data['crimeType'] ?? 'Not specified';
    $location = $data['location'] ?? 'Not specified';
    $dateFiled = $data['dateFiled'] ?? date('F j, Y \a\t g:i A');
    $urgencyJustification = $data['urgencyJustification'] ?? 'No justification provided.';
    $adminPanelLink = $data['adminPanelLink'] ?? '#';
    $isAnonymous = isset($data['isAnonymous']) && $data['isAnonymous'] == 1;
    
    // Get system branding
    $systemName = defined('SYSTEM_NAME') ? SYSTEM_NAME : 'Jan Suraksha';
    $systemTagline = defined('SYSTEM_TAGLINE') ? SYSTEM_TAGLINE : 'AAPKI SURAKSHA, HAMARI ZIMMEDARI';
    $copyrightYear = defined('COPYRIGHT_YEAR') ? COPYRIGHT_YEAR : date('Y');
    
    $text = "==============================================================\n";
    $text .= "🚨 URGENT COMPLAINT RECEIVED\n";
    $text .= "==============================================================\n\n";
    
    if ($isAnonymous) {
        $text .= "🔒 ANONYMOUS COMPLAINT\n\n";
    }
    
    $text .= "Dear Admin,\n\n";
    $text .= "A new urgent complaint has been submitted to the {$systemName} system\n";
    $text .= "and requires immediate attention.\n\n";
    
    $text .= "COMPLAINT DETAILS:\n";
    $text .= "--------------------------------------------------------------\n";
    $text .= "Tracking ID:       {$complaintCode}\n";
    $text .= "Crime Type:        {$crimeType}\n";
    $text .= "Location:          {$location}\n";
    $text .= "Date Filed:        {$dateFiled}\n";
    
    if ($isAnonymous) {
        $text .= "Complainant:       Protected (Anonymous)\n";
    }
    
    $text .= "\n";
    $text .= "WHY THIS IS URGENT:\n";
    $text .= "--------------------------------------------------------------\n";
    $text .= "{$urgencyJustification}\n\n";
    
    $text .= "ACTION REQUIRED:\n";
    $text .= "--------------------------------------------------------------\n";
    $text .= "Please review this complaint immediately to ensure public safety.\n\n";
    
    $text .= "View in Admin Panel:\n";
    $text .= "{$adminPanelLink}\n\n";
    
    $text .= "==============================================================\n";
    $text .= "{$systemName} - {$systemTagline}\n";
    $text .= "© {$copyrightYear} {$systemName}. All rights reserved.\n";
    $text .= "==============================================================\n";
    
    return $text;
}

/**
 * Validate email template data
 * Ensures all required fields are present
 * 
 * @param array $data Email template data
 * @return array ['valid' => bool, 'errors' => array]
 */
function validateEmailTemplateData($data) {
    $errors = [];
    
    // Required fields
    $required = ['complaintCode', 'crimeType', 'location', 'dateFiled', 'urgencyJustification', 'adminPanelLink'];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = "Missing required field: {$field}";
        }
    }
    
    // Validate URL format
    if (isset($data['adminPanelLink']) && !filter_var($data['adminPanelLink'], FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid admin panel link URL";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}
?>
