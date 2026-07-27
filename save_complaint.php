<?php
/**
 * FILE: save_complaint.php
 * PURPOSE: Processes complaint registration POST requests from citizen/register_complaint.php
 * HANDBOOK: GPCMS Engineering Handbook V3.1
 */

session_start();

// ── AUTHENTICATION GUARD ──────────────────────────────────────────────────────
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Citizen') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: citizen/register_complaint.php");
    exit();
}

require_once 'config/db_connect.php';

$user_id               = intval($_SESSION['user_id']);
$complainant_name      = trim($_POST['complainant_name'] ?? '');
$mobile_number         = trim($_POST['mobile_number'] ?? '');
$village_ward          = trim($_POST['village_ward'] ?? '');
$category_id           = intval($_POST['category_id'] ?? 0);
$address               = trim($_POST['address'] ?? '');
$complaint_title       = trim($_POST['complaint_title'] ?? '');
$complaint_description = trim($_POST['complaint_description'] ?? '');

// ── VALIDATION ────────────────────────────────────────────────────────────────
$errors = [];

if (mb_strlen($complainant_name) < 2) {
    $errors[] = "Please enter a valid complainant name (at least 2 characters).";
}

if (!preg_match('/^[6-9][0-9]{9}$/', $mobile_number)) {
    $errors[] = "Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.";
}

if (mb_strlen($village_ward) < 2) {
    $errors[] = "Please enter a valid village or ward name.";
}

if ($category_id <= 0) {
    $errors[] = "Please select a valid complaint category.";
}

if (mb_strlen($address) < 5) {
    $errors[] = "Please enter a detailed address or landmark (at least 5 characters).";
}

if (mb_strlen($complaint_title) < 5) {
    $errors[] = "Complaint title must be at least 5 characters.";
}

if (mb_strlen($complaint_description) < 10) {
    $errors[] = "Complaint description must be at least 10 characters.";
}

if (!empty($errors)) {
    $_SESSION['error_msg'] = implode('<br>', $errors);
    $_SESSION['form_data'] = $_POST;
    header("Location: citizen/register_complaint.php");
    exit();
}

// ── INSERT INTO COMPLAINTS TABLE ──────────────────────────────────────────────
$full_description = $address . "\n\nDetails: " . $complaint_description;

$stmt = $conn->prepare(
    "INSERT INTO complaints (user_id, category_id, complaint_title, complaint_description, village_ward, status, submitted_at, updated_at)
     VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
);
$stmt->bind_param("iisss", $user_id, $category_id, $complaint_title, $full_description, $village_ward);

if (!$stmt->execute()) {
    $_SESSION['error_msg'] = "Database error while registering complaint: " . $stmt->error;
    $_SESSION['form_data'] = $_POST;
    $stmt->close();
    $conn->close();
    header("Location: citizen/register_complaint.php");
    exit();
}

$complaint_id = $stmt->insert_id;
$stmt->close();

// ── RECORD INITIAL HISTORY ENTRY ──────────────────────────────────────────────
$hist_stmt = $conn->prepare(
    "INSERT INTO complaint_history (complaint_id, status, note, updated_by, updated_at)
     VALUES (?, 'pending', 'Complaint submitted by citizen.', ?, NOW())"
);
$hist_stmt->bind_param("ii", $complaint_id, $user_id);
$hist_stmt->execute();
$hist_stmt->close();

// ── HANDLE IMAGE UPLOAD (OPTIONAL) ────────────────────────────────────────────
if (isset($_FILES['complaint_image']) && $_FILES['complaint_image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp  = $_FILES['complaint_image']['tmp_name'];
    $file_name = $_FILES['complaint_image']['name'];
    $file_size = $_FILES['complaint_image']['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($file_ext, $allowed_exts) && $file_size <= 5 * 1024 * 1024) {
        $target_dir = __DIR__ . '/uploads/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $new_filename = 'complaint_' . $complaint_id . '_' . time() . '.' . $file_ext;
        $target_path = $target_dir . $new_filename;
        $db_photo_path = 'uploads/' . $new_filename;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $img_stmt = $conn->prepare(
                "INSERT INTO complaint_photos (complaint_id, photo_type, photo_path, uploaded_by, uploaded_at)
                 VALUES (?, 'initial', ?, ?, NOW())"
            );
            $img_stmt->bind_param("isi", $complaint_id, $db_photo_path, $user_id);
            $img_stmt->execute();
            $img_stmt->close();
        }
    }
}

// ── CREATE NOTIFICATION ───────────────────────────────────────────────────────
$notif_msg = "Complaint #" . $complaint_id . " ('" . mb_substr($complaint_title, 0, 30) . "…') has been submitted successfully.";
$notif_stmt = $conn->prepare(
    "INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at)
     VALUES (?, ?, ?, 0, NOW())"
);
$notif_stmt->bind_param("iis", $user_id, $complaint_id, $notif_msg);
$notif_stmt->execute();
$notif_stmt->close();

$conn->close();

// ── REDIRECT TO MY COMPLAINTS PAGE WITH SUCCESS ID ────────────────────────────
header("Location: citizen/my_complaints.php?created=" . $complaint_id);
exit();
