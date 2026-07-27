<?php
/**
 * FILE: citizen/profile.php
 * MODULE: Citizen Module
 * PURPOSE: Displays and updates the authenticated citizen's profile.
 *          Shows user account details from the users table per contract.
 * CONTRACT: Citizen_Database_Contract.txt
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
require_once 'citizen_helpers.php';

$user_id = intval($_SESSION['user_id']);

$success_msg = '';
$error_msg   = '';

// ── HANDLE PROFILE UPDATE (POST) ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $full_name     = trim($_POST['full_name']     ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');

    $errors = [];
    if (strlen($full_name) < 2)                           $errors[] = "Full name must be at least 2 characters.";
    if (!preg_match('/^[6-9][0-9]{9}$/', $mobile_number)) $errors[] = "Enter a valid 10-digit mobile number.";

    if (empty($errors)) {
        $upd = $conn->prepare(
            "UPDATE users SET full_name = ?, mobile_number = ? WHERE user_id = ?"
        );
        $upd->bind_param("ssi", $full_name, $mobile_number, $user_id);
        if ($upd->execute()) {
            $_SESSION['full_name'] = $full_name; // sync session
            $success_msg = "Profile updated successfully.";
        } else {
            $error_msg = "Failed to update profile. Please try again.";
        }
        $upd->close();
    } else {
        $error_msg = implode(" ", $errors);
    }
}

// ── FETCH CURRENT USER DETAILS ────────────────────────────
$user = null;
$stmt = $conn->prepare(
    "SELECT full_name, login_id, mobile_number, status, created_at
     FROM users WHERE user_id = ? LIMIT 1"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
}
$stmt->close();

// ── COMPLAINT COUNT SUMMARY ───────────────────────────────
$counts = ['total' => 0, 'pending' => 0, 'resolved' => 0];
$c_stmt = $conn->prepare(
    "SELECT status, COUNT(*) AS cnt FROM complaints WHERE user_id = ? GROUP BY status"
);
$c_stmt->bind_param("i", $user_id);
$c_stmt->execute();
$c_res = $c_stmt->get_result();
while ($row = $c_res->fetch_assoc()) {
    $counts['total'] += (int)$row['cnt'];
    if ($row['status'] === 'pending')  $counts['pending']  = (int)$row['cnt'];
    if ($row['status'] === 'resolved') $counts['resolved'] = (int)$row['cnt'];
}
$c_stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My Profile – GPCMS Citizen Portal">
    <title>My Profile | GPCMS Citizen Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/citizen.css">
</head>
<body class="citizen-body">

<div class="citizen-layout">
    <?php require_once 'sidebar.php'; ?>

    <div class="citizen-main-content">
        <?php require_once '../includes/header.php'; ?>

        <main class="citizen-page-wrapper" id="mainContent">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb citizen-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="citizen_dashboard.php"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title">
                    <i class="bi bi-person-circle me-2" aria-hidden="true"></i>My Profile
                </h1>
                <p class="citizen-page-subtitle">View and update your account details.</p>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success_msg)): ?>
        <div class="citizen-alert citizen-alert-success mb-4" role="alert" data-auto-dismiss="8000">
            <span class="citizen-alert-icon" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
            <div class="citizen-alert-body"><?php echo htmlspecialchars($success_msg); ?></div>
            <button type="button" class="citizen-alert-close" aria-label="Dismiss"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
        <div class="citizen-alert citizen-alert-error mb-4" role="alert">
            <span class="citizen-alert-icon" aria-hidden="true"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div class="citizen-alert-body"><?php echo htmlspecialchars($error_msg); ?></div>
            <button type="button" class="citizen-alert-close" aria-label="Dismiss"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Summary Cards -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-4">
                        <article class="citizen-stat-card citizen-stat-total">
                            <div class="citizen-stat-icon"><i class="bi bi-clipboard-data-fill"></i></div>
                            <div class="citizen-stat-body">
                                <div class="citizen-stat-number"><?php echo $counts['total']; ?></div>
                                <div class="citizen-stat-label">Total</div>
                            </div>
                        </article>
                    </div>
                    <div class="col-4">
                        <article class="citizen-stat-card citizen-stat-pending">
                            <div class="citizen-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                            <div class="citizen-stat-body">
                                <div class="citizen-stat-number"><?php echo $counts['pending']; ?></div>
                                <div class="citizen-stat-label">Pending</div>
                            </div>
                        </article>
                    </div>
                    <div class="col-4">
                        <article class="citizen-stat-card citizen-stat-resolved">
                            <div class="citizen-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                            <div class="citizen-stat-body">
                                <div class="citizen-stat-number"><?php echo $counts['resolved']; ?></div>
                                <div class="citizen-stat-label">Resolved</div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="col-12 col-lg-7">
                <section class="citizen-card" aria-label="Profile Information">
                    <div class="citizen-card-header">
                        <h2 class="citizen-card-title">
                            <i class="bi bi-person me-2" aria-hidden="true"></i>Account Details
                        </h2>
                    </div>
                    <div class="citizen-card-body">
                        <form method="POST" action="profile.php" id="profileForm">
                            <input type="hidden" name="action" value="update_profile">

                            <!-- Avatar -->
                            <div class="text-center mb-4">
                                <span class="citizen-avatar" style="width:72px;height:72px;font-size:2rem;display:inline-flex;">
                                    <?php echo htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'] ?? 'U', 0, 1, 'UTF-8'))); ?>
                                </span>
                                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></div>
                                <small class="text-muted">Citizen Account</small>
                            </div>

                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-12">
                                    <div class="citizen-form-group">
                                        <label class="citizen-form-label" for="full_name">
                                            Full Name <span class="citizen-required-star">*</span>
                                        </label>
                                        <input type="text" class="citizen-form-control"
                                               id="full_name" name="full_name"
                                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                               maxlength="150" required>
                                    </div>
                                </div>

                                <!-- Login ID (read-only) -->
                                <div class="col-12 col-md-6">
                                    <div class="citizen-form-group">
                                        <label class="citizen-form-label" for="login_id_display">Login ID</label>
                                        <input type="text" class="citizen-form-control"
                                               id="login_id_display"
                                               value="<?php echo htmlspecialchars($user['login_id'] ?? ''); ?>"
                                               readonly>
                                        <div class="citizen-form-hint">Login ID cannot be changed.</div>
                                    </div>
                                </div>

                                <!-- Mobile Number -->
                                <div class="col-12 col-md-6">
                                    <div class="citizen-form-group">
                                        <label class="citizen-form-label" for="mobile_number">
                                            Mobile Number <span class="citizen-required-star">*</span>
                                        </label>
                                        <input type="tel" class="citizen-form-control"
                                               id="mobile_number" name="mobile_number"
                                               value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>"
                                               maxlength="10" pattern="[6-9][0-9]{9}" required>
                                    </div>
                                </div>

                                <!-- Member Since -->
                                <div class="col-12">
                                    <div class="citizen-form-group">
                                        <label class="citizen-form-label">Member Since</label>
                                        <input type="text" class="citizen-form-control"
                                               value="<?php echo htmlspecialchars(date('d M Y', strtotime($user['created_at'] ?? 'now'))); ?>"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="citizen-form-actions mt-3">
                                <a href="citizen_dashboard.php" class="btn citizen-btn-secondary" id="btn-cancel-profile">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn citizen-btn-primary" id="btn-save-profile">
                                    <i class="bi bi-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Quick Links -->
            <div class="col-12 col-lg-5">
                <section class="citizen-card" aria-label="Quick Links">
                    <div class="citizen-card-header">
                        <h2 class="citizen-card-title">
                            <i class="bi bi-lightning-fill me-2" aria-hidden="true"></i>Quick Links
                        </h2>
                    </div>
                    <div class="citizen-card-body">
                        <div class="d-grid gap-2">
                            <a href="register_complaint.php" class="btn citizen-btn-primary" id="btn-profile-register">
                                <i class="bi bi-plus-circle me-2"></i>Register New Complaint
                            </a>
                            <a href="my_complaints.php" class="btn citizen-btn-secondary" id="btn-profile-mycomp">
                                <i class="bi bi-list-ul me-2"></i>View My Complaints
                            </a>
                            <a href="track_complaint.php" class="btn citizen-btn-secondary" id="btn-profile-track">
                                <i class="bi bi-search me-2"></i>Track a Complaint
                            </a>
                            <a href="../includes/logout.php" class="btn btn-outline-danger" id="btn-profile-logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </section>
            </div>

        </div><!-- /.row -->

    </div><!-- /.container-fluid -->
</main>

        <?php require_once '../includes/footer.php'; ?>

