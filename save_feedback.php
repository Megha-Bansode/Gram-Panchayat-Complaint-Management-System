<?php
/**
 * FILE: save_feedback.php
 * MODULE: Citizen Module
 * PURPOSE: Save citizen feedback/rating for a resolved complaint.
 * CONTRACT: Citizen_Database_Contract.txt
 * FORM ACTION: ../save_feedback.php (POST)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/includes/auth_check.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Citizen') {
    auth_redirect('../includes/login.php', 'Unauthorized access.');
}

$user_id = (int) $user['user_id'];

// CSRF validation (Handbook §10)
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header('Location: citizen/track_complaint.php?complaint_id=' . intval($_POST['complaint_id'] ?? 0) . '&error=invalid_csrf');
    exit;
}

$complaint_id = isset($_POST['complaint_id']) ? (int) $_POST['complaint_id'] : 0;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$feedback_text = trim($_POST['feedback_text'] ?? '');

$errors = [];

// Validate complaint_id
if ($complaint_id <= 0) {
    $errors[] = 'Invalid complaint ID.';
}

// Validate rating (1-5 per handbook CHECK constraint)
if ($rating < 1 || $rating > 5) {
    $errors[] = 'Rating must be between 1 and 5.';
}

// Validate feedback_text length (max 500 per handbook schema)
if (mb_strlen($feedback_text) > 500) {
    $errors[] = 'Feedback text cannot exceed 500 characters.';
}

// Verify complaint belongs to this citizen and is resolved
$verify_stmt = $conn->prepare(
    "SELECT complaint_id, status FROM complaints WHERE complaint_id = ? AND user_id = ? LIMIT 1"
);
$verify_stmt->bind_param('ii', $complaint_id, $user_id);
$verify_stmt->execute();
$verify_res = $verify_stmt->get_result();
$complaint = $verify_res->fetch_assoc();
$verify_stmt->close();

if (!$complaint) {
    $errors[] = 'Complaint not found or access denied.';
} elseif ($complaint['status'] !== 'resolved') {
    $errors[] = 'Feedback can only be submitted for resolved complaints.';
}

// Check if feedback already exists
$existing_stmt = $conn->prepare(
    "SELECT feedback_id FROM feedback WHERE complaint_id = ? LIMIT 1"
);
$existing_stmt->bind_param('i', $complaint_id);
$existing_stmt->execute();
$existing_res = $existing_stmt->get_result();
if ($existing_res->num_rows > 0) {
    $errors[] = 'Feedback has already been submitted for this complaint.';
}
$existing_stmt->close();

if (!empty($errors)) {
    $_SESSION['error_msg'] = implode('<br>', $errors);
    header('Location: citizen/track_complaint.php?complaint_id=' . $complaint_id);
    exit;
}

// Insert feedback
// Canonical columns from handbook: complaint_id, user_id, rating, feedback_text, created_at
$stmt = $conn->prepare(
    "INSERT INTO feedback (complaint_id, user_id, rating, feedback_text, created_at)
     VALUES (?, ?, ?, ?, NOW())"
);
$stmt->bind_param('iiis', $complaint_id, $user_id, $rating, $feedback_text);
$stmt->execute();
$stmt->close();

// Create notification for the citizen
$notif_stmt = $conn->prepare(
    "INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at)
     VALUES (?, ?, CONCAT('Thank you for your feedback on complaint #', ?), 0, NOW())"
);
$notif_stmt->bind_param('iii', $user_id, $complaint_id, $complaint_id);
$notif_stmt->execute();
$notif_stmt->close();

$conn->close();

header('Location: citizen/track_complaint.php?complaint_id=' . $complaint_id . '&feedback=success');
exit;