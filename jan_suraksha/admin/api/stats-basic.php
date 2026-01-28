<?php
/**
 * Real-Time Complaint Statistics API
 * REST API endpoint for admin dashboard statistics
 * 
 * @package Jan_Suraksha
 * @author Issue #154 Implementation
 * @version 1.0.0
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

// Disable output buffering for immediate response
if (ob_get_level()) {
    ob_end_clean();
}

/**
 * Send JSON error response and exit
 * 
 * @param int $code HTTP status code
 * @param string $message Error message
 * @return void
 */
function sendErrorResponse($code, $message) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message,
        'code' => $code,
        'generated_at' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    exit;
}

// ============================================
// SECURITY: Admin Authentication Check
// ============================================
if (empty($_SESSION['admin_id'])) {
    sendErrorResponse(403, 'Unauthorized access - Admin login required');
}

// ============================================
// Include Required Files
// ============================================
try {
    // Include database configuration
    $configPath = __DIR__ . '/../../config.php';
    if (!file_exists($configPath)) {
        throw new Exception('Configuration file not found');
    }
    require_once $configPath;
    
    // Include analytics functions
    $analyticsPath = __DIR__ . '/../../includes/analytics-functions.php';
    if (!file_exists($analyticsPath)) {
        throw new Exception('Analytics functions file not found');
    }
    require_once $analyticsPath;
    
} catch (Exception $e) {
    error_log('Stats API - File Include Error: ' . $e->getMessage());
    sendErrorResponse(500, 'Internal server error - Configuration issue');
}

// ============================================
// Validate Database Connection
// ============================================
if (!isset($mysqli) || $mysqli->connect_errno) {
    error_log('Stats API - Database connection failed: ' . ($mysqli->connect_error ?? 'Unknown error'));
    sendErrorResponse(500, 'Database connection failed');
}

// ============================================
// Fetch Statistics
// ============================================
try {
    // Get all statistics using analytics functions
    $totalComplaints = getTotalComplaints($mysqli);
    $monthlyComplaints = getComplaintsThisMonth($mysqli);
    $weeklyComplaints = getComplaintsThisWeek($mysqli);
    $statusBreakdown = getComplaintsByStatus($mysqli);
    $urgentStats = getUrgentComplaintsRatio($mysqli);
    $avgResolution = getAverageResolutionTime($mysqli);
    
    // Validate data integrity
    if ($totalComplaints < 0 || $monthlyComplaints < 0 || $weeklyComplaints < 0) {
        throw new Exception('Invalid statistics data returned');
    }
    
    // ============================================
    // Build Response Structure
    // ============================================
    $response = [
        'status' => 'success',
        'data' => [
            'total_complaints' => (int)$totalComplaints,
            'this_month' => (int)$monthlyComplaints,
            'this_week' => (int)$weeklyComplaints,
            'by_status' => [
                'submitted' => (int)$statusBreakdown['submitted'],
                'in_progress' => (int)$statusBreakdown['in_progress'],
                'resolved' => (int)$statusBreakdown['resolved'],
                'closed' => (int)$statusBreakdown['closed']
            ],
            'urgent_ratio' => $urgentStats['ratio'],
            'urgent_count' => (int)$urgentStats['urgent'],
            'normal_count' => (int)$urgentStats['normal'],
            'avg_resolution_days' => (float)$avgResolution
        ],
        'generated_at' => date('Y-m-d H:i:s'),
        'server_time' => time()
    ];
    
    // ============================================
    // Send Success Response
    // ============================================
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Log successful API call (optional - can be removed in production)
    error_log('Stats API - Success: Served stats to admin ID ' . $_SESSION['admin_id']);
    
} catch (Exception $e) {
    // ============================================
    // Error Handling
    // ============================================
    error_log('Stats API - Exception: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    sendErrorResponse(500, 'Internal server error - Failed to fetch statistics');
}

// ============================================
// Close Database Connection (optional, PHP does this automatically)
// ============================================
if (isset($mysqli) && !$mysqli->connect_errno) {
    $mysqli->close();
}
?>
