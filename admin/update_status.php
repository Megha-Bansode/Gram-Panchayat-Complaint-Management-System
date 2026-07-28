<?php
/**
 * admin/update_status.php
 * GPCMS — Complaint Status Update Handler (POST-only Action Page)
 * Admin-only access controlled. Follows handbook.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

// Access Control check
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role_name'] !== 'Administrator') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db_connection();
    
    // Canonical field names
    $complaint_id = (int)($_POST['complaint_id'] ?? 0);
    $status       = trim($_POST['status'] ?? '');
    $note         = trim($_POST['note'] ?? '');
    
    // Validate status values
    $allowed_statuses = ['pending', 'assigned', 'in_progress', 'resolved'];
    if ($complaint_id <= 0 || !in_repeatable_array($status, $allowed_statuses)) {
        $_SESSION['error'] = 'Invalid complaint status change parameters.';
        header("Location: analytics_dashboard.php");
        exit;
    }
    
    // Check if complaint exists
    $stmtCheck = $conn->prepare("SELECT user_id, assigned_to FROM complaints WHERE complaint_id = ?");
    $stmtCheck->bind_param("i", $complaint_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    
    if ($stmtCheck->num_rows === 0) {
        $_SESSION['error'] = 'Complaint record not found.';
        $stmtCheck->close();
        header("Location: analytics_dashboard.php");
        exit;
    }
    $stmtCheck->bind_result($citizen_id, $assigned_officer_id);
    $stmtCheck->fetch();
    $stmtCheck->close();
    
    // Update complaint status
    $stmt = $conn->prepare("UPDATE complaints SET status = ?, updated_at = NOW() WHERE complaint_id = ?");
    $stmt->bind_param("si", $status, $complaint_id);
    
    if ($stmt->execute()) {
        $admin_user_id = $_SESSION['user_id'];
        $log_note = $note !== '' ? $note : "Status updated to " . $status . " by Administrator.";
        
        // Log status change in complaint_history table
        $hist = $conn->prepare("INSERT INTO complaint_history (complaint_id, status, note, updated_by, updated_at) VALUES (?, ?, ?, ?, NOW())");
        $hist->bind_param("issi", $complaint_id, $status, $log_note, $admin_user_id);
        $hist->execute();
        $hist->close();
        
        // Push notification to the citizen
        $citizen_msg = "Your complaint #" . $complaint_id . " status has been updated to: " . ucwords(str_replace('_', ' ', $status));
        $notifC = $conn->prepare("INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $notifC->bind_param("iis", $citizen_id, $complaint_id, $citizen_msg);
        $notifC->execute();
        $notifC->close();
        
        // Push notification to the assigned officer if one exists
        if ($assigned_officer_id > 0) {
            $officer_msg = "Complaint #" . $complaint_id . " status changed to: " . ucwords(str_replace('_', ' ', $status));
            $notifO = $conn->prepare("INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
            $notifO->bind_param("iis", $assigned_officer_id, $complaint_id, $officer_msg);
            $notifO->execute();
            $notifO->close();
        }
        
        $_SESSION['success'] = 'Complaint status updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update complaint status: ' . $conn->error;
    }
    $stmt->close();
}

// Helper function to check allowed array items in PHP
function in_repeatable_array(string $val, array $arr): bool {
    return in_array($val, $arr, true);
}

header("Location: analytics_dashboard.php");
exit;
