/**
 * Anonymous Complaint Handler
 * Handles the behavior of anonymous complaint checkbox in file-complaint.php
 * Issue: #131 - Add anonymous crime reporting option
 */

document.addEventListener('DOMContentLoaded', function() {
    const anonymousCheckbox = document.getElementById('anonymous-checkbox');
    const personalInfoSection = document.getElementById('personal-info-section');
    const anonymousDisclaimer = document.getElementById('anonymous-disclaimer');
    
    // Personal info fields
    const nameField = document.getElementById('name');
    const mobileField = document.getElementById('mobile');
    const addressFields = document.querySelectorAll('input[name="house"], input[name="city"], select[name="state"], input[name="pincode"]');

    if (!anonymousCheckbox || !personalInfoSection || !anonymousDisclaimer) {
        console.error('Anonymous complaint elements not found');
        return;
    }

    // Handle checkbox change event
    anonymousCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // ANONYMOUS MODE - Hide personal info
            toggleAnonymousMode(true);
        } else {
            // REGULAR MODE - Show personal info
            toggleAnonymousMode(false);
        }
    });

    /**
     * Toggle anonymous mode on/off
     * @param {boolean} isAnonymous - true for anonymous mode, false for regular mode
     */
    function toggleAnonymousMode(isAnonymous) {
        if (isAnonymous) {
            // Hide personal info section with smooth transition
            personalInfoSection.style.transition = 'opacity 0.3s ease, max-height 0.3s ease';
            personalInfoSection.style.opacity = '0';
            personalInfoSection.style.maxHeight = '0';
            personalInfoSection.style.overflow = 'hidden';
            personalInfoSection.style.marginBottom = '0';
            
            setTimeout(() => {
                personalInfoSection.style.display = 'none';
            }, 300);

            // Remove required attributes
            if (nameField) nameField.removeAttribute('required');
            if (mobileField) mobileField.removeAttribute('required');

            // Clear values from personal info fields
            if (nameField) nameField.value = '';
            if (mobileField) mobileField.value = '';
            addressFields.forEach(field => {
                if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
            });

            // Show disclaimer with fade in
            anonymousDisclaimer.style.display = 'block';
            anonymousDisclaimer.style.opacity = '0';
            anonymousDisclaimer.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                anonymousDisclaimer.style.opacity = '1';
            }, 50);

        } else {
            // Show personal info section with smooth transition
            personalInfoSection.style.display = 'block';
            personalInfoSection.style.opacity = '0';
            personalInfoSection.style.maxHeight = 'none';
            personalInfoSection.style.overflow = 'visible';
            personalInfoSection.style.marginBottom = '1.5rem';
            
            setTimeout(() => {
                personalInfoSection.style.transition = 'opacity 0.3s ease';
                personalInfoSection.style.opacity = '1';
            }, 50);

            // Re-add required attributes
            if (nameField) nameField.setAttribute('required', 'required');
            if (mobileField) mobileField.setAttribute('required', 'required');

            // Hide disclaimer with fade out
            anonymousDisclaimer.style.transition = 'opacity 0.3s ease';
            anonymousDisclaimer.style.opacity = '0';
            
            setTimeout(() => {
                anonymousDisclaimer.style.display = 'none';
            }, 300);
        }
    }

    // Form validation enhancement
    const form = document.getElementById('complaintForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Additional validation for anonymous complaints
            if (anonymousCheckbox.checked) {
                // Ensure required fields for anonymous complaints are filled
                const crimeType = document.getElementById('crime_type');
                const description = document.getElementById('description');

                // Clear any previous custom validation messages
                if (crimeType) {
                    crimeType.setCustomValidity('');
                }
                if (description) {
                    description.setCustomValidity('');
                }
                
                if (!crimeType || !crimeType.value) {
                    e.preventDefault();
                    if (crimeType) {
                        crimeType.setCustomValidity('Please select a crime type');
                        crimeType.reportValidity();
                        crimeType.focus();
                    }
                    return;
                }
                
                if (!description || !description.value.trim()) {
                    e.preventDefault();
                    if (description) {
                        description.setCustomValidity('Please provide a detailed description');
                        description.reportValidity();
                        description.focus();
                    }
                    return;
                }
            }
        });
    }
});
