<?php
/**
 * FILE: citizen/register_complaint.php
 * MODULE: Citizen Module
 * PURPOSE: Complaint registration form for authenticated citizens.
 *          Fetches categories from DB, displays validation errors
 *          returned from save_complaint.php via session, and
 *          repopulates fields on failure.
 * CONTRACT: Citizen_Database_Contract.txt
 * FORM ACTION: ../save_complaint.php (POST)
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
require_once 'citizen_helpers.php';


// ── FETCH CATEGORIES FOR DROPDOWN ────────────────────────────────────────────
$categories = [];
$cat_stmt   = $conn->prepare(
    "SELECT category_id, category_name
     FROM categories
     ORDER BY category_name ASC"
);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}
$cat_stmt->close();
$conn->close();

// ── CONSUME SESSION MESSAGES (from save_complaint.php) ────────────────────────
// Error messages set by save_complaint.php on validation failure
$error_msg = '';
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Repopulate form fields after a failed submission
$form_data = [];
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

/**
 * Helper: safely repopulate a form field value.
 * Returns the session-stored value or empty string.
 *
 * @param string $field  Form field name
 * @return string        HTML-escaped value
 */
function old(string $field, array $data): string
{
    return isset($data[$field]) ? htmlspecialchars($data[$field], ENT_QUOTES, 'UTF-8') : '';
}

/**
 * Helper: check if a category was previously selected.
 *
 * @param int    $id    Category ID to check
 * @param array  $data  Session form data
 * @return string       'selected' attribute or empty string
 */
function is_selected(int $id, array $data): string
{
    return (isset($data['category_id']) && (int)$data['category_id'] === $id)
        ? 'selected'
        : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register a new complaint – GPCMS Citizen Portal">
    <title>Register Complaint | GPCMS Citizen Portal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons 1.11 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts – Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Citizen Module Stylesheet -->
    <link rel="stylesheet" href="../css/citizen.css">
</head>
<body class="citizen-body">

<div class="citizen-layout">
    <?php require_once 'sidebar.php'; ?>

    <div class="citizen-main-content">
        <?php require_once '../includes/header.php'; ?>

        <!-- ═══════════════════════════════════════════════════════
             PAGE WRAPPER
             ═══════════════════════════════════════════════════════ -->
        <main class="citizen-page-wrapper" id="mainContent">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- ── Breadcrumb ─────────────────────────────────── -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb citizen-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="citizen_dashboard.php">
                        <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Register Complaint</li>
            </ol>
        </nav>

        <!-- ── Page Header ────────────────────────────────── -->
        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title">
                    <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>Register New Complaint
                </h1>
                <p class="citizen-page-subtitle">
                    Fill in the details below. A unique Complaint ID will be generated automatically.
                </p>
            </div>
        </div>

        <!-- ── Server-side Error Banner ───────────────────── -->
        <?php if (!empty($error_msg)): ?>
        <div class="citizen-alert citizen-alert-error mb-4"
             role="alert"
             id="formErrorBanner"
             data-auto-dismiss="12000">
            <span class="citizen-alert-icon" aria-hidden="true">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>
            <div class="citizen-alert-body">
                <strong>Please fix the following errors:</strong>
                <div class="mt-1"><?php echo $error_msg; ?></div>
            </div>
            <button type="button"
                    class="citizen-alert-close"
                    aria-label="Dismiss error">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- ── Information Notice ─────────────────────────── -->
        <div class="citizen-info-notice mb-4">
            <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>
            <span>
                <strong>Note:</strong> Fields marked with
                <span class="citizen-required-star" aria-hidden="true">*</span>
                are mandatory. Your Complaint ID will appear after successful submission.
            </span>
        </div>

        <!-- ══════════════════════════════════════════════════
             COMPLAINT REGISTRATION FORM
             action  → ../save_complaint.php
             method  → POST
             enctype → multipart/form-data (image upload)
             ══════════════════════════════════════════════════ -->
        <div class="citizen-card">
            <div class="citizen-card-header">
                <h2 class="citizen-card-title">
                    <i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Complaint Details
                </h2>
            </div>
            <div class="citizen-card-body">

                <form id="registerComplaintForm"
                      action="../save_complaint.php"
                      method="POST"
                      enctype="multipart/form-data"
                      novalidate>

                    <!-- CSRF token — Handbook §10 -->
                    <input type="hidden" name="csrf_token"
                           value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!-- ── SECTION 1: Complainant Information ─── -->
                    <div class="citizen-form-section mb-4">
                        <div class="citizen-form-section-title">
                            <span class="citizen-form-section-num" aria-hidden="true">1</span>
                            Complainant Information
                        </div>

                        <div class="row g-3">

                            <!-- Complainant Name -->
                            <div class="col-12 col-md-6">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="complainant_name">
                                        Complainant Name
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <input type="text"
                                           class="citizen-form-control"
                                           id="complainant_name"
                                           name="complainant_name"
                                           value="<?php echo old('complainant_name', $form_data) ?: htmlspecialchars($_SESSION['full_name']); ?>"
                                           placeholder="Enter full name"
                                           maxlength="150"
                                           required
                                           autocomplete="name">
                                    <div class="citizen-invalid-feedback" id="complainant_name-error"></div>
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-12 col-md-6">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="mobile_number">
                                        Mobile Number
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <input type="tel"
                                           class="citizen-form-control"
                                           id="mobile_number"
                                           name="mobile_number"
                                           value="<?php echo old('mobile_number', $form_data); ?>"
                                           placeholder="10-digit mobile number"
                                           maxlength="10"
                                           pattern="[6-9][0-9]{9}"
                                           required
                                           autocomplete="tel">
                                    <div class="citizen-form-hint">
                                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                        Must be a valid 10-digit Indian mobile number.
                                    </div>
                                    <div class="citizen-invalid-feedback" id="mobile_number-error"></div>
                                </div>
                            </div>

                        </div><!-- /.row -->
                    </div><!-- /.section 1 -->

                    <!-- ── SECTION 2: Location Details ───────── -->
                    <div class="citizen-form-section mb-4">
                        <div class="citizen-form-section-title">
                            <span class="citizen-form-section-num" aria-hidden="true">2</span>
                            Location Details
                        </div>

                        <div class="row g-3">

                            <!-- Village / Ward -->
                            <div class="col-12 col-md-6">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="village_ward">
                                        Village / Ward
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <input type="text"
                                           class="citizen-form-control"
                                           id="village_ward"
                                           name="village_ward"
                                           value="<?php echo old('village_ward', $form_data); ?>"
                                           placeholder="e.g. Ward 5, Village Rampur"
                                           maxlength="150"
                                           required>
                                    <div class="citizen-invalid-feedback" id="village_ward-error"></div>
                                </div>
                            </div>

                            <!-- Complaint Category -->
                            <div class="col-12 col-md-6">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="category_id">
                                        Complaint Category
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <select class="citizen-form-select"
                                            id="category_id"
                                            name="category_id"
                                            required>
                                        <option value="" disabled selected>— Select a category —</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo intval($cat['category_id']); ?>"
                                                <?php echo is_selected((int)$cat['category_id'], $form_data); ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="citizen-invalid-feedback" id="category_id-error"></div>
                                </div>
                            </div>

                            <!-- Address / Landmark -->
                            <div class="col-12">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="address">
                                        Exact Address / Landmark
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <textarea class="citizen-form-control"
                                              id="address"
                                              name="address"
                                              rows="2"
                                              placeholder="Provide the complete address or nearby landmark"
                                              maxlength="500"
                                              required><?php echo old('address', $form_data); ?></textarea>
                                    <div class="citizen-invalid-feedback" id="address-error"></div>
                                </div>
                            </div>

                        </div><!-- /.row -->
                    </div><!-- /.section 2 -->

                    <!-- ── SECTION 3: Complaint Details ──────── -->
                    <div class="citizen-form-section mb-4">
                        <div class="citizen-form-section-title">
                            <span class="citizen-form-section-num" aria-hidden="true">3</span>
                            Complaint Details
                        </div>

                        <div class="row g-3">

                            <!-- Complaint Title -->
                            <div class="col-12">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="complaint_title">
                                        Complaint Title
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <input type="text"
                                           class="citizen-form-control"
                                           id="complaint_title"
                                           name="complaint_title"
                                           value="<?php echo old('complaint_title', $form_data); ?>"
                                           placeholder="Brief, clear title describing the issue"
                                           maxlength="255"
                                           required>
                                    <div class="citizen-invalid-feedback" id="complaint_title-error"></div>
                                </div>
                            </div>

                            <!-- Complaint Description -->
                            <div class="col-12">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="complaint_description">
                                        Detailed Description
                                        <span class="citizen-required-star" aria-label="required">*</span>
                                    </label>
                                    <textarea class="citizen-form-control"
                                              id="complaint_description"
                                              name="complaint_description"
                                              rows="5"
                                              placeholder="Describe the issue in detail — when it started, how severe it is, who is affected…"
                                              maxlength="1000"
                                              required><?php echo old('complaint_description', $form_data); ?></textarea>
                                    <!-- Character counter (driven by citizen.js) -->
                                    <div class="citizen-char-counter">
                                        <span id="descCharCount">0</span> / 1000 characters
                                    </div>
                                    <div class="citizen-invalid-feedback" id="complaint_description-error"></div>
                                </div>
                            </div>

                        </div><!-- /.row -->
                    </div><!-- /.section 3 -->

                    <!-- ── SECTION 4: Supporting Image ────────── -->
                    <div class="citizen-form-section mb-4">
                        <div class="citizen-form-section-title">
                            <span class="citizen-form-section-num" aria-hidden="true">4</span>
                            Supporting Image
                            <span class="citizen-form-section-optional">(Optional)</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="citizen-form-group">
                                    <label class="citizen-form-label" for="complaint_image">
                                        Upload Image
                                    </label>

                                    <!-- Custom file drop zone -->
                                    <div class="citizen-file-zone" id="fileDropZone">
                                        <input type="file"
                                               class="citizen-file-input"
                                               id="complaint_image"
                                               name="complaint_image"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               aria-describedby="imageHint">
                                        <div class="citizen-file-zone-content" id="fileZoneContent">
                                            <i class="bi bi-cloud-upload citizen-file-zone-icon" aria-hidden="true"></i>
                                            <p class="citizen-file-zone-text mb-0">
                                                Drag &amp; drop or <span class="citizen-file-zone-link">browse</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="citizen-form-hint" id="imageHint">
                                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                        Accepted: JPG, JPEG, PNG, WEBP — Maximum size: 5 MB
                                    </div>
                                    <div class="citizen-invalid-feedback" id="complaint_image-error"></div>
                                </div>
                            </div>

                            <!-- Image Preview Panel -->
                            <div class="col-12 col-md-6">
                                <div class="citizen-image-preview-wrap" id="imagePreviewWrap">
                                    <img id="imagePreview"
                                         src="#"
                                         alt="Selected image preview"
                                         class="citizen-image-preview">
                                    <button type="button"
                                            class="citizen-image-remove-btn"
                                            id="removeImageBtn"
                                            aria-label="Remove selected image">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.section 4 -->

                    <!-- ── FORM ACTIONS ───────────────────────── -->
                    <div class="citizen-form-actions">
                        <a href="citizen_dashboard.php"
                           class="btn citizen-btn-secondary"
                           id="btn-cancel-form">
                            <i class="bi bi-x-circle me-2" aria-hidden="true"></i>Cancel
                        </a>

                        <button type="submit"
                                class="btn citizen-btn-primary"
                                id="btn-submit-complaint">
                            <span class="btn-submit-text">
                                <i class="bi bi-send me-2" aria-hidden="true"></i>Submit Complaint
                            </span>
                            <span class="btn-loading-text d-none" aria-live="polite">
                                <span class="spinner-border spinner-border-sm me-2"
                                      role="status"
                                      aria-hidden="true"></span>
                                Submitting…
                            </span>
                        </button>
                    </div>

                </form><!-- /#registerComplaintForm -->

            </div><!-- /.citizen-card-body -->
        </div><!-- /.citizen-card -->

    </div><!-- /.container-fluid -->
</main>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════ -->
<?php require_once '../includes/footer.php'; ?>

