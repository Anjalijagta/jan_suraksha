<?php
/**
 * Analytics Functions for Jan Suraksha
 * SQL query functions for complaint statistics
 * 
 * @package Jan_Suraksha
 * @author Issue #154 Implementation
 */

/**
 * Get total count of all complaints (all time)
 * 
 * @param mysqli $mysqli Database connection object
 * @return int Total number of complaints
 */
function getTotalComplaints($mysqli) {
    $sql = "SELECT COUNT(*) as total FROM tblcomplaints";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        error_log('Analytics Error - getTotalComplaints: ' . $mysqli->error);
        return 0;
    }
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['total'];
    }
    
    return 0;
}

/**
 * Get count of complaints filed this month
 * 
 * @param mysqli $mysqli Database connection object
 * @return int Number of complaints this month
 */
function getComplaintsThisMonth($mysqli) {
    $sql = "SELECT COUNT(*) as total FROM tblcomplaints 
            WHERE MONTH(date_filed) = MONTH(CURRENT_DATE()) 
            AND YEAR(date_filed) = YEAR(CURRENT_DATE())";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        error_log('Analytics Error - getComplaintsThisMonth: ' . $mysqli->error);
        return 0;
    }
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['total'];
    }
    
    return 0;
}

/**
 * Get count of complaints filed in the last 7 days
 * 
 * @param mysqli $mysqli Database connection object
 * @return int Number of complaints this week
 */
function getComplaintsThisWeek($mysqli) {
    $sql = "SELECT COUNT(*) as total FROM complaints 
            WHERE date_filed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        error_log('Analytics Error - getComplaintsThisWeek: ' . $mysqli->error);
        return 0;
    }
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['total'];
    }
    
    return 0;
}

/**
 * Get breakdown of complaints by status
 * 
 * @param mysqli $mysqli Database connection object
 * @return array Associative array with status counts
 */
function getComplaintsByStatus($mysqli) {
    $sql = "SELECT status, COUNT(*) as count FROM tblcomplaints 
            GROUP BY status";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        error_log('Analytics Error - getComplaintsByStatus: ' . $mysqli->error);
        return [];
    }
    
    // Initialize default status counts
    $statusCounts = [
        'submitted' => 0,
        'in_progress' => 0,
        'resolved' => 0,
        'closed' => 0
    ];
    
    // Map database status values to lowercase with underscores
    while ($row = $result->fetch_assoc()) {
        $status = strtolower(str_replace(' ', '_', trim($row['status'])));
        $count = (int)$row['count'];
        
        // Handle common status variations
        switch ($status) {
            case 'submitted':
            case 'pending':
            case 'new':
                $statusCounts['submitted'] += $count;
                break;
            case 'in_progress':
            case 'inprogress':
            case 'in progress':
            case 'processing':
                $statusCounts['in_progress'] += $count;
                break;
            case 'resolved':
            case 'completed':
            case 'done':
                $statusCounts['resolved'] += $count;
                break;
            case 'closed':
            case 'rejected':
            case 'dismissed':
                $statusCounts['closed'] += $count;
                break;
            default:
                // If unknown status, add to submitted
                $statusCounts['submitted'] += $count;
                error_log('Analytics Warning - Unknown status: ' . $row['status']);
        }
    }
    
    return $statusCounts;
}

/**
 * Get urgent vs normal complaint counts and calculate ratio
 * 
 * @param mysqli $mysqli Database connection object
 * @return array Array with urgent count, normal count, and ratio percentage
 */
function getUrgentComplaintsRatio($mysqli) {
    $sql = "SELECT 
                SUM(CASE WHEN is_urgent = 1 THEN 1 ELSE 0 END) as urgent_count,
                SUM(CASE WHEN is_urgent = 0 OR is_urgent IS NULL THEN 1 ELSE 0 END) as normal_count,
                COUNT(*) as total_count
            FROM tblcomplaints";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        error_log('Analytics Error - getUrgentComplaintsRatio: ' . $mysqli->error);
        return [
            'urgent' => 0,
            'normal' => 0,
            'ratio' => '0.0%'
        ];
    }
    
    if ($row = $result->fetch_assoc()) {
        $urgentCount = (int)$row['urgent_count'];
        $normalCount = (int)$row['normal_count'];
        $totalCount = (int)$row['total_count'];
        
        // Calculate ratio percentage
        $ratio = 0.0;
        if ($totalCount > 0) {
            $ratio = ($urgentCount / $totalCount) * 100;
        }
        
        return [
            'urgent' => $urgentCount,
            'normal' => $normalCount,
            'ratio' => number_format($ratio, 1) . '%'
        ];
    }
    
    return [
        'urgent' => 0,
        'normal' => 0,
        'ratio' => '0.0%'
    ];
}

/**
 * Get average resolution time in days for resolved complaints
 * 
 * @param mysqli $mysqli Database connection object
 * @return float Average days to resolve complaints
 */
function getAverageResolutionTime($mysqli) {
    // Try to calculate with resolution_date if it exists
    $sql = "SELECT AVG(DATEDIFF(IFNULL(resolution_date, updated_at), date_filed)) as avg_days 
            FROM tblcomplaints 
            WHERE status IN ('Resolved', 'resolved', 'Completed', 'completed', 'Closed', 'closed')
            AND (resolution_date IS NOT NULL OR updated_at IS NOT NULL)";
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        // If resolution_date column doesn't exist, try with updated_at only
        $sql = "SELECT AVG(DATEDIFF(updated_at, date_filed)) as avg_days 
                FROM tblcomplaints 
                WHERE status IN ('Resolved', 'resolved', 'Completed', 'completed', 'Closed', 'closed')
                AND updated_at IS NOT NULL";
        $result = $mysqli->query($sql);
        
        if ($result === false) {
            error_log('Analytics Error - getAverageResolutionTime: ' . $mysqli->error);
            return 0.0;
        }
    }
    
    if ($row = $result->fetch_assoc()) {
        $avgDays = $row['avg_days'];
        
        if ($avgDays === null) {
            return 0.0;
        }
        
        // Round to 1 decimal place
        return round((float)$avgDays, 1);
    }
    
    return 0.0;
}

/**
 * Helper function to validate database connection
 * 
 * @param mysqli $mysqli Database connection object
 * @return bool True if connection is valid
 */
function validateDatabaseConnection($mysqli) {
    if (!$mysqli || $mysqli->connect_errno) {
        error_log('Analytics Error - Invalid database connection');
        return false;
    }
    return true;
}
?>
