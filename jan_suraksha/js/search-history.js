/**
 * Search History Manager for Tracking Page
 * Issue #136 - Add Search History for Tracking Page
 * 
 * Features:
 * - Saves last 5 tracking IDs in localStorage
 * - Click to auto-fill tracking ID
 * - Clear history button
 * - Privacy-focused (client-side only)
 * - Validates tracking ID format
 */

class SearchHistory {
    constructor() {
        this.storageKey = 'jan_suraksha_search_history';
        this.maxEntries = 5;
        this.init();
    }

    /**
     * Initialize the search history
     */
    init() {
        // Display existing history on page load
        this.displayHistory();
        
        // Attach form submit listener
        this.attachFormListener();
    }

    /**
     * Save tracking ID to history
     * @param {string} trackingId - The tracking ID to save
     * @returns {boolean} - Whether the save was successful
     */
    saveSearch(trackingId) {
        // Trim and validate
        trackingId = trackingId.trim();
        
        if (!this.isValidTrackingId(trackingId)) {
            console.warn('Invalid tracking ID format:', trackingId);
            return false;
        }

        let history = this.getHistory();
        
        // Remove if already exists (to avoid duplicates and move to top)
        history = history.filter(item => item.trackingId !== trackingId);
        
        // Add new entry at the beginning
        history.unshift({
            trackingId: trackingId,
            searchedAt: new Date().toISOString(),
            displayDate: this.formatDate(new Date())
        });
        
        // Keep only last 5 entries
        history = history.slice(0, this.maxEntries);
        
        // Save to localStorage
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(history));
            return true;
        } catch (error) {
            console.error('Error saving search history:', error);
            return false;
        }
    }

    /**
     * Get search history from localStorage
     * @returns {Array} - Array of history items
     */
    getHistory() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (error) {
            console.error('Error reading search history:', error);
            return [];
        }
    }

    /**
     * Clear all history
     */
    clearHistory() {
        try {
            localStorage.removeItem(this.storageKey);
            this.displayHistory();
            console.log('Search history cleared');
        } catch (error) {
            console.error('Error clearing search history:', error);
        }
    }

    /**
     * Display history on page
     */
    displayHistory() {
        const container = document.getElementById('search-history-container');
        const history = this.getHistory();
        
        if (!container) {
            console.warn('Search history container not found');
            return;
        }
        
        if (history.length === 0) {
            container.innerHTML = `
                <div class="no-history">
                    <i class="bi bi-clock-history"></i>
                    <p>No recent searches</p>
                    <small>Your search history will appear here</small>
                </div>
            `;
            return;
        }
        
        let html = '<h3><i class="bi bi-clock-history me-2"></i>Recent Searches</h3>';
        html += '<div class="history-items">';
        
        history.forEach((item, index) => {
            html += `
                <div class="history-item" data-tracking-id="${this.escapeHtml(item.trackingId)}" role="button" tabindex="0">
                    <div class="history-item-content">
                        <span class="tracking-id">${this.escapeHtml(item.trackingId)}</span>
                        <span class="search-date">
                            <i class="bi bi-calendar-event me-1"></i>${this.escapeHtml(item.displayDate)}
                        </span>
                    </div>
                    <div class="history-item-actions">
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        html += `
            <button id="clear-history-btn" class="clear-btn">
                <i class="bi bi-trash me-2"></i>Clear History
            </button>
        `;
        
        container.innerHTML = html;
        
        // Attach click listeners
        this.attachHistoryListeners();
    }

    /**
     * Attach event listeners to history items
     */
    attachHistoryListeners() {
        // Click on history item to auto-fill
        document.querySelectorAll('.history-item').forEach(item => {
            // Click handler
            item.addEventListener('click', () => {
                this.fillTrackingId(item);
            });
            
            // Keyboard accessibility
            item.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.fillTrackingId(item);
                }
            });
        });
        
        // Clear history button
        const clearBtn = document.getElementById('clear-history-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to clear all search history?')) {
                    this.clearHistory();
                }
            });
        }
    }

    /**
     * Fill tracking ID input with selected history item
     * @param {HTMLElement} item - The history item element
     */
    fillTrackingId(item) {
        const trackingId = item.getAttribute('data-tracking-id');
        const input = document.getElementById('code');
        
        if (input && trackingId) {
            input.value = trackingId;
            input.focus();
            
            // Add visual feedback
            item.classList.add('history-item-selected');
            setTimeout(() => {
                item.classList.remove('history-item-selected');
            }, 300);
            
            // Scroll to form if needed
            const form = input.closest('form');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    /**
     * Attach form submit listener
     */
    attachFormListener() {
        const form = document.querySelector('form[method="post"]');
        const input = document.getElementById('code');
        
        if (form && input) {
            form.addEventListener('submit', (e) => {
                const trackingId = input.value.trim();
                if (trackingId) {
                    this.saveSearch(trackingId);
                    // Update display after a short delay to show the new entry
                    setTimeout(() => {
                        this.displayHistory();
                    }, 100);
                }
            });
        }
    }

    /**
     * Validate tracking ID format
     * Accepts formats:
     * - IN/YYYY/XXXXX (regular complaints)
     * - ANON-YYYY-XXXXXX (anonymous complaints)
     * - JS_YYYY_XXX (alternative format from issue description)
     * 
     * @param {string} trackingId - The tracking ID to validate
     * @returns {boolean} - Whether the tracking ID is valid
     */
    isValidTrackingId(trackingId) {
        if (!trackingId || typeof trackingId !== 'string') {
            return false;
        }
        
        // Regular complaint format: IN/2026/12345
        const regularFormat = /^IN\/\d{4}\/\d{3,}$/;
        
        // Anonymous format: ANON-2026-ABC123
        const anonymousFormat = /^ANON-\d{4}-[A-F0-9]{6}$/;
        
        // Alternative format from issue: JS_2026_001
        const alternativeFormat = /^JS_\d{4}_\d{3,}$/;
        
        return regularFormat.test(trackingId) || 
               anonymousFormat.test(trackingId) || 
               alternativeFormat.test(trackingId);
    }

    /**
     * Format date for display
     * @param {Date} date - The date to format
     * @returns {string} - Formatted date string
     */
    formatDate(date) {
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} - Escaped text
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on track-status page
    if (document.getElementById('code')) {
        const searchHistory = new SearchHistory();
        
        // Make it globally accessible for debugging
        window.searchHistory = searchHistory;
    }
});
