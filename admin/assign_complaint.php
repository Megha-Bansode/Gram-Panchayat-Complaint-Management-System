<?php
/**
 * admin/assign_complaint.php
 * GPCMS — Complaint Assignment Handler (POST-only Action Page)
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
    $assigned_to  = (int)($_POST['assigned_to'] ?? 0);
    
    if ($complaint_id <= 0 || $assigned_to <= 0) {
        $_SESSION['error'] = 'Invalid assignment input data.';
        header("Location: analytics_dashboard.php");
        exit;
    }
    
    // Check if user exists and has a role that can handle assignments (e.g. Field Officer/Gram Sevak)
    // For GPCMS logic: any valid target user is acceptable
    $stmtUser = $conn->prepare("SELECT user_id, role_id FROM users WHERE user_id = ?");
    $stmtUser->bind_param("i", $assigned_to);
    $stmtUser->execute();
    $stmtUser->store_result();
    
    if ($stmtUser->num_rows === 0) {
        $_SESSION['error'] = 'Selected officer does not exist.';
        $stmtUser->close();
        header("Location: analytics_dashboard.php");
        exit;
    }
    $stmtUser->close();
    
    // Update complaint assignment and status to 'assigned'
    $status = 'assigned';
    $stmt = $conn->prepare("UPDATE complaints SET assigned_to = ?, status = ?, updated_at = NOW() WHERE complaint_id = ?");
    $stmt->bind_param("isi", $assigned_to, $status, $complaint_id);
    
    if ($stmt->execute()) {
        // Log status change in complaint_history table
        $note = "Complaint assigned to officer ID " . $assigned_to;
        $admin_user_id = $_SESSION['user_id'];
        
        $hist = $conn->prepare("INSERT INTO complaint_history (complaint_id, status, note, updated_by, updated_at) VALUES (?, ?, ?, ?, NOW())");
        $hist->bind_param("issi", $complaint_id, $status, $note, $admin_user_id);
        $hist->execute();
        $hist->close();
        
        // Push notification to the assigned officer
        $message = "You have been assigned a new complaint: #" . $complaint_id;
        $notif = $conn->prepare("INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $notif->bind_param("iis", $assigned_to, $complaint_id, $message);
        $notif->execute();
        $notif->close();
        
        $_SESSION['success'] = 'Complaint assigned successfully.';
    } else {
        $_SESSION['error'] = 'Failed to assign complaint: ' . $conn->error;
    }
    $stmt->close();
}

header("Location: analytics_dashboard.php");
exit;
