<?php
/**
 * FILE: save_feedback.php
 * PURPOSE: Handles feedback submission for resolved complaints.
 * CONTRACT: Citizen_Database_Contract.txt
 */

session_start();

require_once 'config/db_connect.php';

// Only logged in Citizens can submit feedback
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id  = isset($_POST['complaint_id']) ? intval($_POST['complaint_id']) : 0;
    $rating        = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $feedback_text = isset($_POST['feedback_text']) ? trim($_POST['feedback_text']) : '';

    if ($complaint_id > 0 && $rating >= 1 && $rating <= 5) {
        
        // 1. Verify complaint belongs to user and is resolved
        $chk_stmt = $conn->prepare("SELECT status FROM complaints WHERE complaint_id = ? AND user_id = ? LIMIT 1");
        $chk_stmt->bind_param("ii", $complaint_id, $user_id);
        $chk_stmt->execute();
        $chk_res = $chk_stmt->get_result();
        
        if ($chk_res->num_rows > 0) {
            $comp = $chk_res->fetch_assoc();
            
            if ($comp['status'] === 'resolved') {
                // 2. Check if feedback already exists
                $f_chk = $conn->prepare("SELECT feedback_id FROM feedback WHERE complaint_id = ? LIMIT 1");
                $f_chk->bind_param("i", $complaint_id);
                $f_chk->execute();
                $f_res = $f_chk->get_result();
                
                if ($f_res->num_rows === 0) {
                    // 3. Insert feedback
                    $ins = $conn->prepare("INSERT INTO feedback (complaint_id, user_id, rating, feedback_text, submitted_at) VALUES (?, ?, ?, ?, NOW())");
                    if ($ins) {
                        $ins->bind_param("iiis", $complaint_id, $user_id, $rating, $feedback_text);
                        $ins->execute();
                        $ins->close();
                    }
                }
                $f_chk->close();
            }
        }
        $chk_stmt->close();
    }
    
    // Redirect back to track_complaint
    header("Location: citizen/track_complaint.php?complaint_id=" . $complaint_id);
    exit();
}

// Fallback redirect
header("Location: citizen/citizen_dashboard.php");
exit();
