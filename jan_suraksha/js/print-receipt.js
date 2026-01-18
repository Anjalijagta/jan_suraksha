/**
 * Print Receipt JavaScript for Jan Suraksha
 * Handles receipt generation, QR code creation, and print functionality
 * Supports both regular and anonymous complaints
 * 
 * Dependencies: QRCode.js library (loaded via CDN)
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Global configuration
    function getTrackingBaseUrl() {
        try {
            // Prefer an explicit configuration via meta tag
            var meta = document.querySelector('meta[name="jan-suraksha-tracking-base-url"]');
            if (meta && meta.content) {
                var metaBase = String(meta.content).replace(/\/+$/, '');
                return metaBase + '/jan_suraksha/track-status.php';
            }

            // Fallback to global configuration variable if provided
            if (window.JAN_SURAKSHA_TRACKING_BASE_URL) {
                var globalBase = String(window.JAN_SURAKSHA_TRACKING_BASE_URL).replace(/\/+$/, '');
                return globalBase + '/jan_suraksha/track-status.php';
            }

            // Final fallback: use current origin (original behavior)
            var originBase = String(window.location.origin || '').replace(/\/+$/, '');
            return originBase + '/jan_suraksha/track-status.php';
        } catch (e) {
            // In case of any unexpected error, fall back to the original simple behavior
            return window.location.origin + '/jan_suraksha/track-status.php';
        }
    }

    const CONFIG = {
        qrCodeSize: 150,
        qrCodeErrorCorrectionLevel: 'H',
        trackingBaseUrl: getTrackingBaseUrl(),
        dateFormat: {
            locale: 'en-IN',
            options: {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }
        }
    };

    /**
     * Extract complaint data from the page
     * Looks for data in multiple sources: data attributes, hidden fields, DOM elements
     * @returns {Object|null} Complaint data object or null if not found
     */
    function getComplaintData() {
        try {
            // Try to get data from data attributes first
            const receiptContainer = document.getElementById('receiptData');
            if (receiptContainer) {
                return {
                    tracking_id: receiptContainer.dataset.trackingId || '',
                    complaint_type: receiptContainer.dataset.complaintType || 'Not specified',
                    location: receiptContainer.dataset.location || 'Not specified',
                    status: receiptContainer.dataset.status || 'Submitted',
                    submission_date: receiptContainer.dataset.submissionDate || new Date().toISOString(),
                    complaint_id: receiptContainer.dataset.complaintId || '',
                    incident_date: receiptContainer.dataset.incidentDate || '',
                    description: receiptContainer.dataset.description || '',
                    is_anonymous: receiptContainer.dataset.isAnonymous === 'true'
                };
            }

            // Fallback: try to extract from DOM elements
            const trackingIdElement = document.getElementById('tracking-id-text') || 
                                     document.querySelector('.tracking-id-display');
            
            if (trackingIdElement) {
                const trackingId = trackingIdElement.textContent.trim();
                const isAnonymous = trackingId.startsWith('ANON-');
                
                return {
                    tracking_id: trackingId,
                    complaint_type: extractFromPage('complaint_type', 'Crime Type', 'Not specified'),
                    location: extractFromPage('location', 'Location', 'Not specified'),
                    status: 'Submitted',
                    submission_date: new Date().toISOString(),
                    complaint_id: trackingId,
                    incident_date: extractFromPage('incident_date', 'Incident Date', ''),
                    description: extractFromPage('description', 'Description', ''),
                    is_anonymous: isAnonymous
                };
            }

            console.warn('No complaint data found on page');
            return null;

        } catch (error) {
            console.error('Error extracting complaint data:', error);
            return null;
        }
    }

    /**
     * Helper function to extract data from page elements
     * @param {string} id - Element ID
     * @param {string} labelText - Label text to search for
     * @param {string} defaultValue - Default value if not found
     * @returns {string} Extracted value
     */
    function extractFromPage(id, labelText, defaultValue) {
        // Try by ID first
        const element = document.getElementById(id);
        if (element) {
            return element.value || element.textContent.trim();
        }

        // Try by label text
        const labels = document.querySelectorAll('label, .label, strong');
        for (let label of labels) {
            if (label.textContent.includes(labelText)) {
                const nextElement = label.nextElementSibling || label.parentElement.querySelector('.value');
                if (nextElement) {
                    return nextElement.textContent.trim() || defaultValue;
                }
            }
        }

        return defaultValue;
    }

    /**
     * Extract user data from the page
     * @returns {Object} User data object
     */
    function getUserData() {
        const receiptContainer = document.getElementById('receiptData');
        
        // Check if anonymous complaint
        if (receiptContainer && receiptContainer.dataset.isAnonymous === 'true') {
            return {
                full_name: 'Anonymous User',
                mobile_number: 'Hidden',
                email: 'Hidden',
                is_anonymous: true
            };
        }

        // Extract regular user data
        return {
            full_name: receiptContainer?.dataset.userName || 
                      extractFromPage('user_name', 'Name', 'Not provided'),
            mobile_number: receiptContainer?.dataset.userMobile || 
                          extractFromPage('user_mobile', 'Mobile', 'Not provided'),
            email: receiptContainer?.dataset.userEmail || 
                   extractFromPage('user_email', 'Email', ''),
            is_anonymous: false
        };
    }

    /**
     * Format date to human-readable format
     * @param {string|Date} dateString - Date to format
     * @returns {string} Formatted date string
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            
            return date.toLocaleString(
                CONFIG.dateFormat.locale, 
                CONFIG.dateFormat.options
            );
        } catch (error) {
            console.error('Error formatting date:', error);
            return dateString;
        }
    }

    /**
     * Capitalize first letter of each word
     * @param {string} str - String to capitalize
     * @returns {string} Capitalized string
     */
    function capitalizeWords(str) {
        if (!str) return '';
        return str.replace(/\b\w/g, l => l.toUpperCase());
    }

    /**
     * Format complaint data for display
     * @param {Object} data - Raw complaint data
     * @returns {Object} Formatted complaint data
     */
    function formatReceiptData(data) {
        if (!data) return null;

        return {
            tracking_id: data.tracking_id || 'N/A',
            complaint_type: capitalizeWords(data.complaint_type),
            location: capitalizeWords(data.location),
            status: capitalizeWords(data.status),
            submission_date: formatDate(data.submission_date),
            incident_date: data.incident_date ? formatDate(data.incident_date) : 'Not specified',
            complaint_id: data.complaint_id || data.tracking_id,
            description: data.description || 'Details provided in complaint form',
            is_anonymous: data.is_anonymous || false
        };
    }

    /**
     * Validate receipt data
     * @param {Object} data - Complaint data to validate
     * @returns {boolean} True if data is valid
     */
    function validateReceiptData(data) {
        if (!data) {
            alert('Error: No complaint data available to print. Please try again.');
            return false;
        }

        if (!data.tracking_id) {
            alert('Error: Tracking ID is missing. Cannot generate receipt.');
            return false;
        }

        return true;
    }

    /**
     * Generate QR code for tracking URL
     * @param {string} trackingId - Complaint tracking ID
     * @returns {boolean} True if successful, false otherwise
     */
    function generateQRCode(trackingId) {
        const qrContainer = document.getElementById('qrcodeContainer');
        
        if (!qrContainer) {
            console.error('QR code container not found');
            return false;
        }

        // Check if QRCode library is loaded
        if (typeof QRCode === 'undefined') {
            console.error('QRCode.js library not loaded');
            qrContainer.innerHTML = '<p style="color: #dc3545;">QR Code unavailable (library not loaded)</p>';
            return false;
        }

        try {
            // Clear previous QR code if exists
            qrContainer.innerHTML = '';

            // Generate tracking URL
            const trackingUrl = `${CONFIG.trackingBaseUrl}?id=${encodeURIComponent(trackingId)}`;

            // Create QR code
            new QRCode(qrContainer, {
                text: trackingUrl,
                width: CONFIG.qrCodeSize,
                height: CONFIG.qrCodeSize,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel[CONFIG.qrCodeErrorCorrectionLevel]
            });

            // Add descriptive text below QR code
            const description = document.createElement('p');
            description.style.marginTop = '0.5rem';
            description.style.fontSize = '0.85rem';
            description.style.color = '#6c757d';
            description.textContent = 'Scan to track complaint status';
            qrContainer.appendChild(description);

            console.log('QR code generated successfully');
            return true;

        } catch (error) {
            console.error('Error generating QR code:', error);
            qrContainer.innerHTML = '<p style="color: #dc3545;">Failed to generate QR code</p>';
            return false;
        }
    }

    /**
     * Generate complete receipt HTML structure
     * @param {Object} complaintData - Formatted complaint data
     * @param {Object} userData - User data
     * @returns {string} HTML string for receipt
     */
    function generateReceiptHTML(complaintData, userData) {
        const currentDateTime = formatDate(new Date());
        const isAnonymous = complaintData.is_anonymous;

        return `
            <div class="receipt-header">
                <img src="logo.png" alt="Jan Suraksha Logo" class="receipt-logo" onerror="this.style.display='none'">
                <h1>JAN SURAKSHA</h1>
                <p class="tagline">Aapki Suraksha, Hamari Zimmedari</p>
                <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #495057;">
                    <strong>Crime Reporting Portal</strong><br>
                    Official Complaint Receipt
                </p>
            </div>

            <div class="tracking-id-box">
                <h3>Complaint Tracking ID</h3>
                <div class="tracking-id-display">${complaintData.tracking_id}</div>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">Save this ID to track your complaint</p>
            </div>

            <div class="receipt-section">
                <h2><i class="bi bi-file-text"></i> Complaint Information</h2>
                <div class="receipt-details">
                    <div class="detail-item">
                        <span class="label">Complaint Type</span>
                        <span class="value">${complaintData.complaint_type}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Status</span>
                        <span class="value">${complaintData.status}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Location</span>
                        <span class="value">${complaintData.location}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Incident Date</span>
                        <span class="value">${complaintData.incident_date}</span>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <span class="label">Submission Date & Time</span>
                        <span class="value">${complaintData.submission_date}</span>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <h2><i class="bi bi-person"></i> Submitted By</h2>
                <div class="receipt-details">
                    ${isAnonymous ? `
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="label">Identity</span>
                            <span class="value">
                                <strong>Anonymous Complaint</strong>
                                <br><small style="color: #6c757d;">Your identity is protected and not recorded</small>
                            </span>
                        </div>
                    ` : `
                        <div class="detail-item">
                            <span class="label">Name</span>
                            <span class="value">${userData.full_name}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Mobile Number</span>
                            <span class="value">${userData.mobile_number}</span>
                        </div>
                        ${userData.email ? `
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="label">Email</span>
                            <span class="value">${userData.email}</span>
                        </div>
                        ` : ''}
                    `}
                </div>
            </div>

            <div class="qr-code-container">
                <h3 style="font-size: 1rem; margin-bottom: 1rem; color: #495057;">Track Your Complaint</h3>
                <div id="qrcodeContainer"></div>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: #6c757d;">
                    Visit: <strong>${CONFIG.trackingBaseUrl}</strong>
                </p>
            </div>

            <div class="receipt-footer">
                <div class="important-notes">
                    <h4><i class="bi bi-info-circle"></i> Important Instructions</h4>
                    <ul>
                        <li>Keep this receipt safe for your records</li>
                        <li>Use the Tracking ID to monitor complaint status online</li>
                        <li>Do not share your tracking ID with unauthorized persons</li>
                        ${isAnonymous ? '<li>This is an anonymous complaint - your identity remains confidential</li>' : ''}
                        <li>For urgent matters, contact your local police station directly</li>
                        <li>Updates will be reflected in the tracking system</li>
                    </ul>
                </div>
                
                <div class="disclaimer">
                    <p>
                        This is an electronically generated receipt from Jan Suraksha Crime Reporting Portal.
                        No signature is required. This receipt serves as proof of complaint submission.
                    </p>
                </div>
                
                <div class="print-timestamp">
                    Printed on: ${currentDateTime}
                </div>
            </div>
        `;
    }

    /**
     * Trigger browser print dialog
     * Applies print-specific styles before printing
     */
    function printReceipt() {
        try {
            // Ensure receipt is visible
            const receiptContainer = document.querySelector('.receipt-container');
            if (receiptContainer) {
                receiptContainer.style.display = 'block';
                receiptContainer.style.visibility = 'visible';
            }

            // Small delay to ensure QR code is rendered
            setTimeout(() => {
                window.print();
            }, 250);

            console.log('Print dialog opened');

        } catch (error) {
            console.error('Error opening print dialog:', error);
            alert('Failed to open print dialog. Please try again or use your browser\'s print function (Ctrl+P).');
        }
    }

    /**
     * Initialize print receipt functionality
     * Main function that orchestrates the entire process
     */
    function initializePrintReceipt() {
        console.log('Initializing print receipt functionality...');

        // Get the print button
        const printBtn = document.getElementById('printReceiptBtn');
        if (!printBtn) {
            console.warn('Print receipt button not found');
            return;
        }

        // Get receipt container
        const receiptContainer = document.querySelector('.receipt-container');
        if (!receiptContainer) {
            console.warn('Receipt container not found');
            return;
        }

        // Extract and validate data
        const complaintData = getComplaintData();
        if (!validateReceiptData(complaintData)) {
            printBtn.disabled = true;
            printBtn.title = 'Receipt data unavailable';
            return;
        }

        const userData = getUserData();
        const formattedData = formatReceiptData(complaintData);

        // Generate receipt HTML
        receiptContainer.innerHTML = generateReceiptHTML(formattedData, userData);

        // Generate QR code synchronously after receipt HTML is in place
        generateQRCode(formattedData.tracking_id);

        // Attach print handler
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            printReceipt();
        });

        console.log('Print receipt initialized successfully');
    }

    /**
     * Auto-initialize when QRCode library is ready
     */
    function checkQRCodeLibrary() {
        if (typeof QRCode !== 'undefined') {
            initializePrintReceipt();
        } else {
            console.warn('QRCode.js not loaded yet, retrying...');
            setTimeout(checkQRCodeLibrary, 100);
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkQRCodeLibrary);
    } else {
        // DOM already loaded
        checkQRCodeLibrary();
    }

})();
