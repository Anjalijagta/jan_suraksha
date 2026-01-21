/**
 * Automated Test Suite for Search History Feature
 * Issue #136
 * 
 * Run this in browser console on test-search-history.html
 */

class SearchHistoryTests {
    constructor() {
        this.testResults = [];
        this.passed = 0;
        this.failed = 0;
    }

    log(message, type = 'info') {
        const emoji = {
            'pass': '✅',
            'fail': '❌',
            'info': 'ℹ️',
            'warn': '⚠️'
        };
        console.log(`${emoji[type]} ${message}`);
    }

    assert(condition, testName) {
        if (condition) {
            this.passed++;
            this.log(`PASS: ${testName}`, 'pass');
            this.testResults.push({ name: testName, status: 'PASS' });
        } else {
            this.failed++;
            this.log(`FAIL: ${testName}`, 'fail');
            this.testResults.push({ name: testName, status: 'FAIL' });
        }
    }

    // Test 1: SearchHistory class exists
    testClassExists() {
        this.assert(
            typeof SearchHistory === 'function',
            'SearchHistory class is defined'
        );
    }

    // Test 2: Instance created
    testInstanceCreated() {
        this.assert(
            window.searchHistory instanceof SearchHistory,
            'SearchHistory instance exists'
        );
    }

    // Test 3: Save valid tracking ID
    testSaveValidID() {
        const testId = 'IN/2026/12345';
        window.searchHistory.clearHistory();
        const result = window.searchHistory.saveSearch(testId);
        const history = window.searchHistory.getHistory();
        
        this.assert(
            result === true && history.length === 1 && history[0].trackingId === testId,
            'Save valid tracking ID (IN/2026/12345)'
        );
    }

    // Test 4: Reject invalid tracking ID
    testRejectInvalidID() {
        window.searchHistory.clearHistory();
        const result = window.searchHistory.saveSearch('INVALID123');
        const history = window.searchHistory.getHistory();
        
        this.assert(
            result === false && history.length === 0,
            'Reject invalid tracking ID'
        );
    }

    // Test 5: Maximum 5 entries
    testMaxEntries() {
        window.searchHistory.clearHistory();
        
        // Add 7 entries
        window.searchHistory.saveSearch('IN/2026/001');
        window.searchHistory.saveSearch('IN/2026/002');
        window.searchHistory.saveSearch('IN/2026/003');
        window.searchHistory.saveSearch('IN/2026/004');
        window.searchHistory.saveSearch('IN/2026/005');
        window.searchHistory.saveSearch('IN/2026/006');
        window.searchHistory.saveSearch('IN/2026/007');
        
        const history = window.searchHistory.getHistory();
        
        this.assert(
            history.length === 5 && history[0].trackingId === 'IN/2026/007',
            'Limit to 5 entries (newest first)'
        );
    }

    // Test 6: No duplicates (move to top)
    testNoDuplicates() {
        window.searchHistory.clearHistory();
        
        window.searchHistory.saveSearch('IN/2026/001');
        window.searchHistory.saveSearch('IN/2026/002');
        window.searchHistory.saveSearch('IN/2026/001'); // Duplicate
        
        const history = window.searchHistory.getHistory();
        
        this.assert(
            history.length === 2 && history[0].trackingId === 'IN/2026/001',
            'No duplicates (moves to top)'
        );
    }

    // Test 7: localStorage persistence
    testLocalStorage() {
        window.searchHistory.clearHistory();
        window.searchHistory.saveSearch('IN/2026/99999');
        
        const stored = localStorage.getItem('jan_suraksha_search_history');
        let parsed;
        try {
            parsed = JSON.parse(stored);
        } catch (e) {
            parsed = null;
        }
        
        this.assert(
            stored !== null && Array.isArray(parsed) && parsed.length === 1,
            'localStorage persistence'
        );
    }

    // Test 8: Clear history
    testClearHistory() {
        window.searchHistory.saveSearch('IN/2026/001');
        window.searchHistory.clearHistory();
        const history = window.searchHistory.getHistory();
        
        this.assert(
            history.length === 0,
            'Clear history functionality'
        );
    }

    // Test 9: Valid format - Anonymous
    testAnonymousFormat() {
        window.searchHistory.clearHistory();
        const result = window.searchHistory.saveSearch('ANON-2026-ABC123');
        
        this.assert(
            result === true,
            'Accept anonymous format (ANON-2026-ABC123)'
        );
    }

    // Test 10: Valid format - Alternative
    testAlternativeFormat() {
        window.searchHistory.clearHistory();
        const result = window.searchHistory.saveSearch('JS_2026_001');
        
        this.assert(
            result === true,
            'Accept alternative format (JS_2026_001)'
        );
    }

    // Test 11: Date formatting
    testDateFormatting() {
        const testDate = new Date('2026-01-18T10:30:00');
        const formatted = window.searchHistory.formatDate(testDate);
        
        this.assert(
            formatted.includes('2026') && formatted.includes('Jan'),
            'Date formatting works'
        );
    }

    // Test 12: HTML escaping
    testHTMLEscaping() {
        const testString = '<script>alert("XSS")</script>';
        const escaped = window.searchHistory.escapeHtml(testString);
        
        this.assert(
            !escaped.includes('<script>') && escaped.includes('&lt;'),
            'HTML escaping for XSS protection'
        );
    }

    // Test 13: DOM elements exist
    testDOMElements() {
        const container = document.getElementById('search-history-container');
        const input = document.getElementById('code');
        
        this.assert(
            container !== null && input !== null,
            'Required DOM elements exist'
        );
    }

    // Test 14: Display history UI
    testDisplayHistory() {
        window.searchHistory.clearHistory();
        window.searchHistory.saveSearch('IN/2026/12345');
        window.searchHistory.displayHistory();
        
        const container = document.getElementById('search-history-container');
        const hasHistoryItem = container.querySelector('.history-item') !== null;
        
        this.assert(
            hasHistoryItem,
            'Display history in UI'
        );
    }

    // Test 15: Empty state display
    testEmptyState() {
        window.searchHistory.clearHistory();
        window.searchHistory.displayHistory();
        
        const container = document.getElementById('search-history-container');
        const hasNoHistory = container.querySelector('.no-history') !== null;
        
        this.assert(
            hasNoHistory,
            'Empty state displayed correctly'
        );
    }

    // Run all tests
    async runAllTests() {
        console.clear();
        console.log('🚀 Running Search History Test Suite...\n');
        
        this.testClassExists();
        this.testInstanceCreated();
        this.testSaveValidID();
        this.testRejectInvalidID();
        this.testMaxEntries();
        this.testNoDuplicates();
        this.testLocalStorage();
        this.testClearHistory();
        this.testAnonymousFormat();
        this.testAlternativeFormat();
        this.testDateFormatting();
        this.testHTMLEscaping();
        this.testDOMElements();
        this.testDisplayHistory();
        this.testEmptyState();
        
        console.log('\n' + '='.repeat(50));
        console.log(`📊 Test Results:`);
        console.log(`   ✅ Passed: ${this.passed}`);
        console.log(`   ❌ Failed: ${this.failed}`);
        console.log(`   📈 Total: ${this.passed + this.failed}`);
        console.log(`   🎯 Success Rate: ${((this.passed / (this.passed + this.failed)) * 100).toFixed(1)}%`);
        console.log('='.repeat(50));
        
        if (this.failed === 0) {
            console.log('\n🎉 All tests passed! Feature is working correctly.');
        } else {
            console.log('\n⚠️ Some tests failed. Please review the results above.');
        }
        
        return {
            passed: this.passed,
            failed: this.failed,
            total: this.passed + this.failed,
            results: this.testResults
        };
    }
}

// Auto-run tests if on test page
if (typeof SearchHistory !== 'undefined') {
    window.searchHistoryTests = new SearchHistoryTests();
    console.log('✅ Test suite loaded. Run: searchHistoryTests.runAllTests()');
    
    // Auto-run after 1 second
    setTimeout(() => {
        window.searchHistoryTests.runAllTests();
    }, 1000);
} else {
    console.error('❌ SearchHistory class not found. Make sure search-history.js is loaded.');
}
