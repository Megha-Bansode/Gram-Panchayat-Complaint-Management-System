<?php
/**
 * FILE: save_complaint.php
 * MODULE: Citizen Module
 * PURPOSE: Backend handler for complaint registration.
 *          Validates input, handles image upload, creates complaint record,
 *          stores photo reference, and creates initial history entry.
 * CONTRACT: Citizen_Database_Contract.txt
 * FORM ACTION: POST from citizen/register_complaint.php
 * REDIRECT: citizen/my_complaints.php?created=<complaint_id> on success
 *           citizen/register_complaint.php with session errors on failure
 */

declare(strict_types=1);

require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/includes/auth_check.php';

// Start session and verify authentication
auth_start_session();

if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: includes/login.php?error=' . rawurlencode('Please sign in to continue.'));
    exit;
}

if ((string)($_SESSION['role_name'] ?? '') !== 'Citizen') {
    header('Location: includes/login.php?error=' . rawurlencode('Unauthorized access.'));
    exit;
}

// CSRF validation (Handbook §10)
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error_msg'] = 'Invalid CSRF token. Please refresh the page and try again.';
    $_SESSION['form_data'] = $_POST;
    header('Location: citizen/register_complaint.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ── INPUT VALIDATION ──────────────────────────────────────────────────────────
$errors = [];

// Required fields from handbook canonical form fields
$complainant_name     = trim($_POST['complainant_name'] ?? '');
$mobile_number        = trim($_POST['mobile_number'] ?? '');
$village_ward         = trim($_POST['village_ward'] ?? '');
$category_id          = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$address              = trim($_POST['address'] ?? '');
$complaint_title      = trim($_POST['complaint_title'] ?? '');
$complaint_description = trim($_POST['complaint_description'] ?? '');

// Validate complainant_name
if ($complainant_name === '' || mb_strlen($complainant_name) < 2) {
    $errors[] = 'Complainant name must be at least 2 characters.';
}

// Validate mobile_number - 10 digits, starts with 6-9
if ($mobile_number === '' || !preg_match('/^[6-9][0-9]{9}$/', $mobile_number)) {
    $errors[] = 'Enter a valid 10-digit Indian mobile number (starting with 6-9).';
}

// Validate village_ward
if ($village_ward === '' || mb_strlen($village_ward) < 2) {
    $errors[] = 'Village / Ward is required.';
}

// Validate category_id
if ($category_id <= 0) {
    $errors[] = 'Please select a complaint category.';
}

// Validate address
if ($address === '' || mb_strlen($address) < 5) {
    $errors[] = 'Address / Landmark must be at least 5 characters.';
}

// Validate complaint_title
if ($complaint_title === '' || mb_strlen($complaint_title) < 5) {
    $errors[] = 'Complaint title must be at least 5 characters.';
}

// Validate complaint_description
if ($complaint_description === '' || mb_strlen($complaint_description) < 10) {
    $errors[] = 'Complaint description must be at least 10 characters.';
}

// ── IMAGE UPLOAD HANDLING ─────────────────────────────────────────────────────
$uploaded_photo_path = null;
$upload_error = null;

if (isset($_FILES['complaint_image']) && $_FILES['complaint_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['complaint_image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_error = 'File upload failed with error code: ' . $file['error'];
    } else {
        // Validate file size (5 MB max per handbook)
        $max_size = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $max_size) {
            $upload_error = 'File size exceeds 5 MB limit.';
        }
        
        // Validate MIME type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types, true)) {
            $upload_error = 'Only JPG, JPEG, PNG, and WEBP images are allowed.';
        }
        
        // Validate extension as secondary check
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) {
            $upload_error = 'Invalid file extension. Allowed: JPG, JPEG, PNG, WEBP.';
        }
        
        if ($upload_error === null) {
            // Generate unique filename
            $upload_dir = __DIR__ . '/uploads/complaints/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = 'complaint_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Store relative path for database (relative to project root)
                $uploaded_photo_path = 'uploads/complaints/' . $filename;
            } else {
                $upload_error = 'Failed to save uploaded file.';
            }
        }
    }
    
    if ($upload_error !== null) {
        $errors[] = $upload_error;
    }
}

// ── HANDLE VALIDATION ERRORS ──────────────────────────────────────────────────
if (!empty($errors)) {
    $_SESSION['error_msg'] = implode('<br>', $errors);
    $_SESSION['form_data'] = $_POST;
    header('Location: citizen/register_complaint.php');
    exit;
}

// ── DATABASE TRANSACTION ──────────────────────────────────────────────────────
// Handbook flow: create_complaint -> generate_complaint_id -> initial status: pending
// All operations in a transaction for atomicity

$conn->begin_transaction();

try {
    // 1. Insert into complaints table
    // Canonical columns from handbook: user_id, category_id, complaint_title, complaint_description, village_ward, status, submitted_at
    // Note: address form field is appended to complaint_description since schema only has village_ward column
    $full_description = $complaint_description;
    if ($address !== '') {
        $full_description .= "\n\nAddress / Landmark: " . $address;
    }
    
    $stmt = $conn->prepare(
        "INSERT INTO complaints (user_id, category_id, complaint_title, complaint_description, village_ward, status, submitted_at, complainant_name, mobile_number)
         VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)"
    );
    $stmt->bind_param('iisssss', $user_id, $category_id, $complaint_title, $full_description, $village_ward, $complainant_name, $mobile_number);
    $stmt->execute();
    
    $complaint_id = $conn->insert_id;
    $stmt->close();
    
    if ($complaint_id === 0) {
        throw new Exception('Failed to create complaint record.');
    }
    
    // 2. Insert into complaint_photos if image was uploaded
    if ($uploaded_photo_path !== null) {
        $photo_stmt = $conn->prepare(
            "INSERT INTO complaint_photos (complaint_id, photo_type, photo_path, uploaded_by, uploaded_at)
             VALUES (?, 'initial', ?, ?, NOW())"
        );
        $photo_stmt->bind_param('isi', $complaint_id, $uploaded_photo_path, $user_id);
        $photo_stmt->execute();
        $photo_stmt->close();
    }
    
    // 3. Insert initial history entry into complaint_history (audit trail)
    // Canonical columns: complaint_id, status, note, updated_by, updated_at
    $history_stmt = $conn->prepare(
        "INSERT INTO complaint_history (complaint_id, status, note, updated_by, updated_at)
         VALUES (?, 'pending', 'Complaint registered by citizen', ?, NOW())"
    );
    $history_stmt->bind_param('ii', $complaint_id, $user_id);
    $history_stmt->execute();
    $history_stmt->close();
    
    // 4. Create notification for the citizen
    $notif_stmt = $conn->prepare(
        "INSERT INTO notifications (user_id, complaint_id, message, is_read, created_at)
         VALUES (?, ?, CONCAT('Your complaint has been registered successfully. Complaint ID: ', ?), 0, NOW())"
    );
    $notif_stmt->bind_param('iii', $user_id, $complaint_id, $complaint_id);
    $notif_stmt->execute();
    $notif_stmt->close();
    
    $conn->commit();
    
    // Success - redirect to my_complaints with created ID
    header('Location: citizen/my_complaints.php?created=' . $complaint_id);
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    
    // Clean up uploaded file if transaction failed
    if ($uploaded_photo_path !== null && file_exists(__DIR__ . '/' . $uploaded_photo_path)) {
        @unlink(__DIR__ . '/' . $uploaded_photo_path);
    }
    
    error_log('Complaint save failed: ' . $e->getMessage());
    $_SESSION['error_msg'] = 'Failed to save complaint. Please try again.';
    $_SESSION['form_data'] = $_POST;
    header('Location: citizen/register_complaint.php');
    exit;
}