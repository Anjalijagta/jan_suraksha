// Jan Suraksha Portal - Form Validation
console.log('Jan Suraksha Portal JS loaded');

// Validation utility functions
const Validators = {
    // Username validation: must contain at least one letter, 3-50 characters
    validateName: (name) => {
        if (!name || name.trim().length === 0) {
            return { valid: false, message: 'Name is required' };
        }
        if (name.trim().length < 3) {
            return { valid: false, message: 'Name must be at least 3 characters long' };
        }
        if (name.trim().length > 50) {
            return { valid: false, message: 'Name must not exceed 50 characters' };
        }
        if (!/[a-zA-Z]/.test(name)) {
            return { valid: false, message: 'Name must contain at least one letter' };
        }
        if (/^\d+$/.test(name.trim())) {
            return { valid: false, message: 'Name cannot contain only numbers' };
        }
        return { valid: true, message: '' };
    },

    // Mobile validation: must be exactly 10 digits
    validateMobile: (mobile) => {
        if (!mobile || mobile.trim().length === 0) {
            return { valid: false, message: 'Mobile number is required' };
        }
        // Remove all non-digit characters
        const digitsOnly = mobile.replace(/\D/g, '');
        
        if (digitsOnly.length !== 10) {
            return { valid: false, message: 'Mobile number must be exactly 10 digits' };
        }
        if (!/^[6-9]/.test(digitsOnly)) {
            return { valid: false, message: 'Mobile number must start with 6, 7, 8, or 9' };
        }
        return { valid: true, message: '' };
    },

    // Email validation: proper email format
    validateEmail: (email) => {
        if (!email || email.trim().length === 0) {
            return { valid: false, message: 'Email is required' };
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return { valid: false, message: 'Please enter a valid email address' };
        }
        return { valid: true, message: '' };
    },

    // Password validation: minimum 6 characters with at least one letter and one number
    validatePassword: (password) => {
        if (!password || password.length === 0) {
            return { valid: false, message: 'Password is required' };
        }
        if (password.length < 6) {
            return { valid: false, message: 'Password must be at least 6 characters long' };
        }
        if (password.length > 50) {
            return { valid: false, message: 'Password must not exceed 50 characters' };
        }
        if (!/[a-zA-Z]/.test(password)) {
            return { valid: false, message: 'Password must contain at least one letter' };
        }
        if (!/[0-9]/.test(password)) {
            return { valid: false, message: 'Password must contain at least one number' };
        }
        return { valid: true, message: '' };
    },

    // Confirm password validation
    validateConfirmPassword: (password, confirmPassword) => {
        if (!confirmPassword || confirmPassword.length === 0) {
            return { valid: false, message: 'Please confirm your password' };
        }
        if (password !== confirmPassword) {
            return { valid: false, message: 'Passwords do not match' };
        }
        return { valid: true, message: '' };
    },

    // Login ID validation (email or mobile)
    validateLoginId: (id) => {
        if (!id || id.trim().length === 0) {
            return { valid: false, message: 'Email or Mobile number is required' };
        }
        // Check if it's a mobile number (contains only digits)
        const digitsOnly = id.replace(/\D/g, '');
        if (digitsOnly.length === 10 && /^[6-9]/.test(digitsOnly)) {
            return { valid: true, message: '' };
        }
        // Otherwise, validate as email
        return Validators.validateEmail(id);
    }
};

// Helper function to show error message
function showError(inputElement, message) {
    const formGroup = inputElement.closest('.mb-3');
    let errorDiv = formGroup.querySelector('.invalid-feedback');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        formGroup.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    inputElement.classList.add('is-invalid');
    inputElement.classList.remove('is-valid');
}

// Helper function to show success
function showSuccess(inputElement) {
    const formGroup = inputElement.closest('.mb-3');
    const errorDiv = formGroup.querySelector('.invalid-feedback');
    
    if (errorDiv) {
        errorDiv.textContent = '';
    }
    
    inputElement.classList.remove('is-invalid');
    inputElement.classList.add('is-valid');
}

// Helper function to clear validation
function clearValidation(inputElement) {
    const formGroup = inputElement.closest('.mb-3');
    const errorDiv = formGroup.querySelector('.invalid-feedback');
    
    if (errorDiv) {
        errorDiv.textContent = '';
    }
    
    inputElement.classList.remove('is-invalid');
    inputElement.classList.remove('is-valid');
}

// Real-time validation for registration form
function initRegisterValidation() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const nameInput = form.querySelector('[name="name"]');
    const mobileInput = form.querySelector('[name="mobile"]');
    const emailInput = form.querySelector('[name="email"]');
    const passwordInput = form.querySelector('[name="password"]');
    const confirmInput = form.querySelector('[name="confirm"]');

    // Real-time validation on blur
    if (nameInput) {
        nameInput.addEventListener('blur', function() {
            const result = Validators.validateName(this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
        });
        nameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const result = Validators.validateName(this.value);
                if (result.valid) {
                    showSuccess(this);
                }
            }
        });
    }

    if (mobileInput) {
        mobileInput.addEventListener('blur', function() {
            const result = Validators.validateMobile(this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
        });
        mobileInput.addEventListener('input', function() {
            // Auto-format: remove non-digits and limit to 10 digits
            this.value = this.value.replace(/\D/g, '').substring(0, 10);
            if (this.classList.contains('is-invalid') && this.value.length === 10) {
                const result = Validators.validateMobile(this.value);
                if (result.valid) {
                    showSuccess(this);
                }
            }
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const result = Validators.validateEmail(this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
        });
        emailInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const result = Validators.validateEmail(this.value);
                if (result.valid) {
                    showSuccess(this);
                }
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', function() {
            const result = Validators.validatePassword(this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
            // Also re-validate confirm password if it has a value
            if (confirmInput && confirmInput.value) {
                const confirmResult = Validators.validateConfirmPassword(this.value, confirmInput.value);
                if (!confirmResult.valid) {
                    showError(confirmInput, confirmResult.message);
                } else {
                    showSuccess(confirmInput);
                }
            }
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('blur', function() {
            const result = Validators.validateConfirmPassword(passwordInput.value, this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
        });
        confirmInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const result = Validators.validateConfirmPassword(passwordInput.value, this.value);
                if (result.valid) {
                    showSuccess(this);
                }
            }
        });
    }

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Validate all fields
        const nameResult = Validators.validateName(nameInput.value);
        if (!nameResult.valid) {
            showError(nameInput, nameResult.message);
            isValid = false;
        } else {
            showSuccess(nameInput);
        }

        const mobileResult = Validators.validateMobile(mobileInput.value);
        if (!mobileResult.valid) {
            showError(mobileInput, mobileResult.message);
            isValid = false;
        } else {
            showSuccess(mobileInput);
        }

        const emailResult = Validators.validateEmail(emailInput.value);
        if (!emailResult.valid) {
            showError(emailInput, emailResult.message);
            isValid = false;
        } else {
            showSuccess(emailInput);
        }

        const passwordResult = Validators.validatePassword(passwordInput.value);
        if (!passwordResult.valid) {
            showError(passwordInput, passwordResult.message);
            isValid = false;
        } else {
            showSuccess(passwordInput);
        }

        const confirmResult = Validators.validateConfirmPassword(passwordInput.value, confirmInput.value);
        if (!confirmResult.valid) {
            showError(confirmInput, confirmResult.message);
            isValid = false;
        } else {
            showSuccess(confirmInput);
        }

        // If all valid, submit the form
        if (isValid) {
            this.submit();
        } else {
            // Scroll to first error
            const firstError = this.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
}

// Real-time validation for login form
function initLoginValidation() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    const idInput = form.querySelector('[name="id"]');
    const passwordInput = form.querySelector('[name="password"]');

    if (idInput) {
        idInput.addEventListener('blur', function() {
            const result = Validators.validateLoginId(this.value);
            if (!result.valid) {
                showError(this, result.message);
            } else {
                showSuccess(this);
            }
        });
        idInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const result = Validators.validateLoginId(this.value);
                if (result.valid) {
                    showSuccess(this);
                }
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', function() {
            if (!this.value || this.value.length === 0) {
                showError(this, 'Password is required');
            } else {
                showSuccess(this);
            }
        });
    }

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        const idResult = Validators.validateLoginId(idInput.value);
        if (!idResult.valid) {
            showError(idInput, idResult.message);
            isValid = false;
        } else {
            showSuccess(idInput);
        }

        if (!passwordInput.value || passwordInput.value.length === 0) {
            showError(passwordInput, 'Password is required');
            isValid = false;
        } else {
            showSuccess(passwordInput);
        }

        if (isValid) {
            this.submit();
        } else {
            const firstError = this.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
            }
        }
    });
}

// ===== LOADING ANIMATIONS =====

// Loading Overlay Functions
const LoadingOverlay = {
    show: function() {
        let overlay = document.getElementById('loadingOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'loading-overlay';
            overlay.setAttribute('role', 'status');
            overlay.setAttribute('aria-live', 'polite');
            overlay.setAttribute('aria-label', 'Loading');
            overlay.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(overlay);
        }
        setTimeout(() => overlay.classList.add('active'), 10);
    },
    
    hide: function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 300);
        }
    }
};

// Form Loading State
function addFormLoading(form) {
    if (form) {
        form.classList.add('form-loading');
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    }
}

function removeFormLoading(form) {
    if (form) {
        form.classList.remove('form-loading');
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }
    }
}

// Show loading on form submissions
function initFormLoadingHandlers() {
    // Login Form
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="id"]');
            const password = this.querySelector('input[name="password"]');
            
            if (email && password && email.value && password.value) {
                addFormLoading(this);
                LoadingOverlay.show();
            }
        });
    }
    
    // Complaint Form
    const complaintForm = document.querySelector('form[action*="file-complaint"]');
    if (complaintForm) {
        complaintForm.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let allFilled = true;
            
            requiredFields.forEach(field => {
                if (!field.value) allFilled = false;
            });
            
            if (allFilled) {
                addFormLoading(this);
                LoadingOverlay.show();
            }
        });
    }
    
    // Feedback Form
    const feedbackForm = document.querySelector('form[action*="feedback"]');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            const subject = this.querySelector('[name="subject"]');
            const message = this.querySelector('[name="message"]');
            
            if (subject && message && subject.value && message.value) {
                addFormLoading(this);
                LoadingOverlay.show();
            }
        });
    }
    
    // Register Form
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let allFilled = true;
            
            requiredFields.forEach(field => {
                if (!field.value) allFilled = false;
            });
            
            if (allFilled) {
                addFormLoading(this);
                LoadingOverlay.show();
            }
        });
    }
}

// Skeleton Loader Functions
function showSkeletonLoader(container, type = 'card') {
    if (!container) return;
    
    container.classList.add('skeleton-container');
    container.setAttribute('aria-label', 'Loading content');
    container.setAttribute('aria-busy', 'true');
    
    if (type === 'card') {
        container.innerHTML = `
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
        `;
    } else if (type === 'table') {
        container.innerHTML = `
            <div class="skeleton skeleton-table-row"></div>
            <div class="skeleton skeleton-table-row"></div>
            <div class="skeleton skeleton-table-row"></div>
            <div class="skeleton skeleton-table-row"></div>
            <div class="skeleton skeleton-table-row"></div>
        `;
    } else if (type === 'gallery') {
        container.innerHTML = `
            <div class="skeleton-gallery-grid">
                <div class="skeleton skeleton-gallery-item"></div>
                <div class="skeleton skeleton-gallery-item"></div>
                <div class="skeleton skeleton-gallery-item"></div>
                <div class="skeleton skeleton-gallery-item"></div>
                <div class="skeleton skeleton-gallery-item"></div>
                <div class="skeleton skeleton-gallery-item"></div>
            </div>
        `;
    } else if (type === 'text') {
        container.innerHTML = `
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text"></div>
        `;
    }
}

function hideSkeletonLoader(container, content) {
    if (!container) return;
    
    container.classList.remove('skeleton-container');
    if (content) {
        container.innerHTML = content;
    }
    container.classList.add('fade-in');
}

// Progress Bar
function showProgressBar() {
    let progressContainer = document.getElementById('progressBarContainer');
    if (!progressContainer) {
        progressContainer = document.createElement('div');
        progressContainer.id = 'progressBarContainer';
        progressContainer.className = 'progress-bar-container';
        progressContainer.innerHTML = '<div class="progress-bar"></div>';
        document.body.appendChild(progressContainer);
    }
    progressContainer.classList.add('active');
    
    setTimeout(() => {
        progressContainer.classList.remove('active');
        setTimeout(() => progressContainer.remove(), 300);
    }, 2000);
}

// Page transition loading
function initPageTransitions() {
    // Show progress bar on navigation using event delegation
    const handlerTarget = document.body || document;

    handlerTarget.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) {
            return;
        }

        const href = link.getAttribute('href');

        // Ignore in-page anchors and javascript: links
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
            return;
        }

        // Ignore downloads and links that open in a new tab/window or frame
        if (link.hasAttribute('download') || link.hasAttribute('target')) {
            return;
        }

        showProgressBar();
    });
}

// AJAX Loading Helper
function toggleElementLoading(element, show = true) {
    if (!element) return;
    
    if (show) {
        element.classList.add('loading');
    } else {
        element.classList.remove('loading');
    }
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initRegisterValidation();
    initLoginValidation();
    initFormLoadingHandlers();
    initPageTransitions();
    
    // Hide loading overlay once the full page has finished loading
    window.addEventListener('load', function () {
        if (window.LoadingOverlay && typeof window.LoadingOverlay.hide === 'function') {
            window.LoadingOverlay.hide();
        }
    });
});

// Export functions for use in other scripts
window.LoadingOverlay = LoadingOverlay;
window.showSkeletonLoader = showSkeletonLoader;
window.hideSkeletonLoader = hideSkeletonLoader;
window.toggleElementLoading = toggleElementLoading;
window.showProgressBar = showProgressBar;

