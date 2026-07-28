<?php
/**
 * FILE: citizen/track_complaint.php
 * MODULE: Citizen Module
 * PURPOSE: Track complaint status and view full audit history.
 * CONTRACT: Citizen_Database_Contract.txt
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
declare(strict_types=1);

require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
require_once 'citizen_helpers.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Citizen') {
    auth_redirect('../includes/login.php', 'Unauthorized access.');
}


$user_id = intval($_SESSION['user_id']);
$search_id = isset($_GET['complaint_id']) ? intval($_GET['complaint_id']) : null;

$complaint = null;
$photos    = [];
$history   = [];

if ($search_id && $search_id > 0) {
    // Fetch complaint for this citizen only
    $stmt = $conn->prepare(
        "SELECT c.*, cat.category_name, u.full_name AS officer_name
         FROM complaints c
         LEFT JOIN categories cat ON c.category_id = cat.category_id
         LEFT JOIN users u ON c.assigned_to = u.user_id
         WHERE c.complaint_id = ? AND c.user_id = ?
         LIMIT 1"
    );
    $stmt->bind_param("ii", $search_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $complaint = $res->fetch_assoc();
    }
    $stmt->close();

    if ($complaint) {
        // Fetch photos
        $p_stmt = $conn->prepare(
            "SELECT * FROM complaint_photos WHERE complaint_id = ? ORDER BY uploaded_at ASC"
        );
        $p_stmt->bind_param("i", $search_id);
        $p_stmt->execute();
        $p_res = $p_stmt->get_result();
        while ($row = $p_res->fetch_assoc()) {
            $photos[] = $row;
        }
        $p_stmt->close();

        // Fetch history timeline
        $h_stmt = $conn->prepare(
            "SELECT ch.*, u.full_name AS updated_by_name
             FROM complaint_history ch
             LEFT JOIN users u ON ch.updated_by = u.user_id
             WHERE ch.complaint_id = ?
             ORDER BY ch.updated_at ASC"
        );
        $h_stmt->bind_param("i", $search_id);
        $h_stmt->execute();
        $h_res = $h_stmt->get_result();
        while ($row = $h_res->fetch_assoc()) {
            $history[] = $row;
        }
        $h_stmt->close();

        // Fetch feedback
        $f_stmt = $conn->prepare("SELECT * FROM feedback WHERE complaint_id = ? LIMIT 1");
        $f_stmt->bind_param("i", $search_id);
        $f_stmt->execute();
        $f_res = $f_stmt->get_result();
        if ($f_res->num_rows > 0) {
            $complaint['feedback'] = $f_res->fetch_assoc();
        }
        $f_stmt->close();
    }
}

$conn->close();

// Helpers moved to citizen/citizen_helpers.php per Handbook §12
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track Complaint – GPCMS Citizen Portal">
    <title>Track Complaint | GPCMS Citizen Portal</title>

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
        <?php require_once 'header.php'; ?>

        <main class="citizen-page-wrapper">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb citizen-breadcrumb">
                <li class="breadcrumb-item"><a href="citizen_dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="my_complaints.php">My Complaints</a></li>
                <li class="breadcrumb-item active">Track Complaint</li>
            </ol>
        </nav>

        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title"><i class="bi bi-search me-2"></i>Track Complaint</h1>
                <p class="citizen-page-subtitle">Search by Complaint ID or review status details below.</p>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <div class="citizen-card mb-4">
            <div class="citizen-card-body">
                <form method="GET" action="track_complaint.php" class="row g-2 align-items-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="number" name="complaint_id" class="form-control citizen-form-control" placeholder="Enter Complaint ID (e.g. 1)" value="<?php echo $search_id ? htmlspecialchars((string)$search_id) : ''; ?>" required min="1">
                            <button type="submit" class="btn citizen-btn-primary"><i class="bi bi-arrow-right me-1"></i>Track</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($search_id && !$complaint): ?>
        <div class="citizen-card mb-4">
            <div class="citizen-card-body text-center py-5">
                <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                <h3 class="mt-3">Complaint #<?php echo htmlspecialchars((string)$search_id); ?> Not Found</h3>
                <p class="text-muted">No complaint with this ID was found under your account. Please check the ID or visit <a href="my_complaints.php">My Complaints</a>.</p>
            </div>
        </div>
        <?php elseif ($complaint): ?>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-8">
                <div class="citizen-card mb-4">
                    <div class="citizen-card-header d-flex justify-content-between align-items-center">
                        <h2 class="citizen-card-title mb-0">Complaint #<?php echo intval($complaint['complaint_id']); ?> Details</h2>
                        <div class="citizen-badge <?php echo status_badge_class($complaint['status']); ?>">
                            <?php echo status_label($complaint['status']); ?>
                        </div>
                    </div>
                    <div class="citizen-card-body">
                        <h4 class="mb-3 fw-bold text-dark"><?php echo htmlspecialchars($complaint['complaint_title']); ?></h4>
                        <div class="mb-3 text-secondary" style="white-space: pre-line; line-height: 1.6;">
                            <?php echo htmlspecialchars($complaint['complaint_description']); ?>
                        </div>
                        <hr class="my-3">
                        <div class="row g-3 citizen-small text-muted">
                            <div class="col-6 col-md-4">
                                <i class="bi bi-tag me-1"></i><strong>Category:</strong><br>
                                <?php echo htmlspecialchars($complaint['category_name'] ?? '—'); ?>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-geo-alt me-1"></i><strong>Village / Ward:</strong><br>
                                <?php echo htmlspecialchars($complaint['village_ward']); ?>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-person me-1"></i><strong>Assigned Officer:</strong><br>
                                <?php echo htmlspecialchars($complaint['officer_name'] ?? 'Not Yet Assigned'); ?>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-calendar me-1"></i><strong>Submitted:</strong><br>
                                <?php echo date('d M Y, h:i A', strtotime($complaint['submitted_at'])); ?>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-clock-history me-1"></i><strong>Last Updated:</strong><br>
                                <?php echo date('d M Y, h:i A', strtotime($complaint['updated_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($photos)): ?>
                <div class="citizen-card mb-4">
                    <div class="citizen-card-header"><h3 class="citizen-card-title">Uploaded Media</h3></div>
                    <div class="citizen-card-body">
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($photos as $ph): ?>
                            <a href="../<?php echo htmlspecialchars($ph['photo_path']); ?>" target="_blank">
                                <img src="../<?php echo htmlspecialchars($ph['photo_path']); ?>" alt="Complaint Photo" style="width: 140px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                            </a>
                            <?php endforeach; ?>
                        </div>
                </div>
                <?php endif; ?>

                <?php if ($complaint['status'] === 'resolved'): ?>
                <div class="citizen-card mb-4" id="feedbackSection">
                    <div class="citizen-card-header"><h3 class="citizen-card-title"><i class="bi bi-star me-2"></i>Feedback & Rating</h3></div>
                    <div class="citizen-card-body">
                        <?php if (isset($complaint['feedback'])): ?>
                            <div class="mb-2">
                                <?php for ($i=1; $i<=5; $i++): ?>
                                    <i class="bi bi-star-fill <?php echo $i <= $complaint['feedback']['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-2 fw-semibold"><?php echo $complaint['feedback']['rating']; ?>/5 Stars</span>
                            </div>
                            <?php if (!empty($complaint['feedback']['feedback_text'])): ?>
                                <p class="text-secondary mt-2 mb-0" style="background: #f8f9fa; padding: 10px; border-radius: 6px; border-left: 3px solid #8A724C;"><?php echo htmlspecialchars($complaint['feedback']['feedback_text']); ?></p>
                            <?php endif; ?>
                            <div class="citizen-small text-muted mt-2">Submitted on <?php echo date('d M Y, h:i A', strtotime($complaint['feedback']['created_at'])); ?></div>
                        <?php else: ?>
                            <p class="text-muted mb-3">This complaint has been resolved. Please provide your feedback.</p>
                            <form action="../save_feedback.php" method="POST">
                                <!-- CSRF token — Handbook §10 -->
                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Rating <span class="text-danger">*</span></label>
                                    <select name="rating" class="form-select citizen-form-control" required>
                                        <option value="" disabled selected>Select a rating (1-5)</option>
                                        <option value="5">5 - Excellent</option>
                                        <option value="4">4 - Good</option>
                                        <option value="3">3 - Average</option>
                                        <option value="2">2 - Poor</option>
                                        <option value="1">1 - Very Poor</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Comments (Optional)</label>
                                    <textarea name="feedback_text" class="form-control citizen-form-control" rows="3" placeholder="Tell us about your experience..." maxlength="500"></textarea>
                                </div>
                                <button type="submit" class="btn citizen-btn-primary"><i class="bi bi-send me-1"></i>Submit Feedback</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-4">
                <div class="citizen-card">
                    <div class="citizen-card-header"><h3 class="citizen-card-title"><i class="bi bi-diagram-3 me-2"></i>Status History</h3></div>
                    <div class="citizen-card-body">
                        <?php if (empty($history)): ?>
                        <p class="text-muted citizen-small">No status changes recorded yet.</p>
                        <?php else: ?>
                        <ul class="list-unstyled mb-0 position-relative" style="padding-left: 20px; border-left: 2px solid #e2e8f0;">
                            <?php foreach ($history as $h): ?>
                            <li class="mb-4 position-relative">
                                <span class="position-absolute translate-middle bg-primary rounded-circle" style="left: -21px; top: 8px; width: 12px; height: 12px;"></span>
                                <div class="fw-bold text-dark"><?php echo status_label($h['status']); ?></div>
                                <div class="citizen-small text-muted mb-1"><?php echo date('d M Y, h:i A', strtotime($h['updated_at'])); ?></div>
                                <?php if (!empty($h['note'])): ?>
                                <div class="citizen-small bg-light p-2 rounded border mt-1 text-secondary"><?php echo htmlspecialchars($h['note']); ?></div>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>

    </div>
</main>

<?php require_once 'footer.php'; ?>

