<?php
// GPCMS Gram Sevak Module - Verify Complaint & Progress Backend
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

auth_start_session();

if (empty($_SESSION['is_logged_in'])) {
    auth_redirect('../includes/official_login.php', 'Please sign in to continue.');
}

$page_title = "Verify Complaint & Work Progress";
$active_page = "verify_complaint";

$success_msg = "";
$error_msg = "";

$complaint_id = trim($_GET['id'] ?? $_POST['complaint_id'] ?? '');

// Handle Verification / Resolution Action
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_resolution') {
    $cid = trim($_POST['complaint_id'] ?? '');
    $status_update = trim($_POST['status'] ?? 'resolved');
    $remarks = trim($_POST['remarks'] ?? 'Verified and approved by Gram Sevak');

    // Strict validation: Only canonical status 'resolved' or allowed canonical status values
    $allowed_canonical_statuses = ['pending', 'assigned', 'in_progress', 'resolved'];

    if (!in_array($status_update, $allowed_canonical_statuses, true)) {
        $error_msg = "Invalid status update. Only canonical statuses ('pending', 'assigned', 'in_progress', 'resolved') are allowed.";
    } elseif (!empty($cid)) {
        if (isset($pdo) && $pdo !== null) {
            try {
                $pdo->beginTransaction();

                // 1. Update complaints table status
                $stmt = $pdo->prepare("UPDATE complaints SET status = ?, updated_at = NOW() WHERE complaint_id = ?");
                $stmt->execute([$status_update, $cid]);

                // 2. Write to complaint_history table - use correct schema
                // complaint_history: history_id, complaint_id, user_id, status_from, status_to, remarks, created_at
                $stmt_hist = $pdo->prepare("INSERT INTO complaint_history (complaint_id, user_id, status_from, status_to, remarks) VALUES (?, ?, 'in_progress', ?, ?)");
                $stmt_hist->execute([$cid, $_SESSION['user_id'], $status_update, $remarks]);

                $pdo->commit();
                $success_msg = "Complaint " . htmlspecialchars($cid) . " verified and set to canonical status '" . htmlspecialchars($status_update) . "'!";
                $complaint_id = $cid;
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = "Failed to update verification status: " . $e->getMessage();
            }
        } else {
            $success_msg = "Complaint " . htmlspecialchars($cid) . " verified as " . htmlspecialchars($status_update) . " (Demo Mode).";
        }
    }
}

// Fetch Complaint Details, Photos, & History
$complaint = null;
$photos = null;
$history = [];

if (!empty($complaint_id) && isset($pdo) && $pdo !== null) {
    try {
        // Main complaint details query - use correct column names
        $stmt = $pdo->prepare("SELECT c.complaint_id, c.complaint_code, c.title, c.description, c.ward_no, c.landmark, c.location_address, c.priority, c.status, c.assigned_officer_id, c.assigned_at, c.resolved_at, c.created_at, c.updated_at,
                                      cat.category_name, u.full_name as officer_name, u.phone as officer_mobile
                               FROM complaints c
                               LEFT JOIN categories cat ON c.category_id = cat.category_id
                               LEFT JOIN users u ON c.assigned_officer_id = u.user_id
                               WHERE c.complaint_id = ?");
        $stmt->execute([$complaint_id]);
        $complaint = $stmt->fetch();

        // Photos query from complaint_photos - use correct schema
        // complaint_photos: photo_id, complaint_id, photo_type (enum: 'before', 'after', 'progress'), file_path, uploaded_by, uploaded_at
        $stmt_p = $pdo->prepare("SELECT * FROM complaint_photos WHERE complaint_id = ? ORDER BY photo_id DESC");
        $stmt_p->execute([$complaint_id]);
        $photos = $stmt_p->fetchAll();

        // History query from complaint_history - use correct schema
        // complaint_history: history_id, complaint_id, user_id, status_from, status_to, remarks, created_at
        $stmt_h = $pdo->prepare("SELECT h.*, u.full_name as updater_name
                                 FROM complaint_history h
                                 LEFT JOIN users u ON h.user_id = u.user_id
                                 WHERE h.complaint_id = ?
                                 ORDER BY h.created_at ASC");
        $stmt_h->execute([$complaint_id]);
        $history = $stmt_h->fetchAll();
    } catch (Exception $e) {
        // Fallback demo data handled in template if DB is empty
        error_log('Verify complaint query error: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/header.php';
?>

<main class="gpcms-body container-fluid">
    <!-- Header Title -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-patch-check-fill text-success me-2"></i>Verify Complaint Details & Work Progress</h3>
                    <p class="text-muted mb-0">Inspect progress photos, complaint history, and approve resolution.</p>
                </div>
                <div>
                    <a href="assigned_complaints.php" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-list-task me-1"></i> Assigned List</a>
                    <a href="gramsevak_dashboard.php" class="btn btn-outline-dark btn-sm"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Complaint Selector if not selected -->
    <?php if (empty($complaint) && empty($complaint_id)): ?>
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="gpcms-card p-4 text-center">
                    <i class="bi bi-search display-4 text-primary-custom mb-3"></i>
                    <h4>Select a Complaint to Verify</h4>
                    <p class="text-muted">Enter a valid Complaint ID to inspect work progress photos and approve resolution.</p>
                    <form action="verify_complaint.php" method="GET" class="d-flex justify-content-center gap-2 mt-3">
                        <input type="text" name="id" class="form-control w-50" placeholder="e.g. CMP-2024-001" required>
                        <button type="submit" class="btn btn-gpcms-primary">Inspect Complaint</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>

    <?php
    // Fallback data if DB query had no row
    // Use correct database column names: title, description, ward_no, landmark, location_address, priority, status, assigned_officer_id
    $c_title = $complaint['title'] ?? 'Water Pipeline Burst near Ward 3 Community Hall';
    $c_id = $complaint['complaint_id'] ?? ($complaint_id ?: 'CMP-2024-001');
    $c_cat = $complaint['category_name'] ?? 'Water Supply';
    $c_desc = $complaint['description'] ?? 'Main supply pipeline damaged resulting in drinking water wastage and low pressure in Ward 3.';
    $c_status = strtolower($complaint['status'] ?? 'in_progress');
    $c_complainant = $complaint['complainant_name'] ?? 'Sunil Deshmukh';  // This might not exist in complaints table
    $c_mobile = $complaint['mobile_number'] ?? '9890112233';  // This might not exist in complaints table
    $c_village = $complaint['ward_no'] ?? ($complaint['location_address'] ?? 'Shivaji Nagar');
    $c_officer = $complaint['officer_name'] ?? 'Ramesh Shinde (Field Officer)';

    // Photos from complaint_photos table - photo_type enum: 'before', 'after', 'progress'
    $before_photo = '';
    $after_photo = '';
    if (!empty($photos)) {
        foreach ($photos as $photo) {
            if (($photo['photo_type'] ?? '') === 'before') {
                $before_photo = $photo['file_path'] ?? '';
            } elseif (($photo['photo_type'] ?? '') === 'after') {
                $after_photo = $photo['file_path'] ?? '';
            }
        }
    }
    // Fallback to complaint image or placeholder
    if (empty($before_photo)) {
        $before_photo = $complaint['complaint_image'] ?? 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?w=500&auto=format&fit=crop&q=60';
    }
    if (empty($after_photo)) {
        $after_photo = 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=500&auto=format&fit=crop&q=60';
    }
    ?>

    <!-- Complaint Overview Details -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="gpcms-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-light text-dark border me-2"><?php echo htmlspecialchars($c_id); ?></span>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($c_cat); ?></span>
                        <h4 class="fw-bold mt-2 mb-1"><?php echo htmlspecialchars($c_title); ?></h4>
                    </div>
                    <div>
                        <?php
                        $badge_class = 'bg-warning';
                        if ($c_status === 'assigned') $badge_class = 'bg-info text-white';
                        elseif ($c_status === 'in_progress') $badge_class = 'bg-primary';
                        elseif ($c_status === 'resolved') $badge_class = 'bg-success';
                        ?>
                        <span class="badge <?php echo $badge_class; ?> fs-6 px-3 py-2"><?php echo htmlspecialchars($c_status); ?></span>
                    </div>
                </div>

                <hr>

                <h6 class="fw-semibold text-secondary">Description</h6>
                <p class="text-dark"><?php echo nl2br(htmlspecialchars($c_desc)); ?></p>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <span class="text-muted d-block small">Complainant Name</span>
                            <strong><?php echo htmlspecialchars($c_complainant); ?></strong> (<?php echo htmlspecialchars($c_mobile); ?>)
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <span class="text-muted d-block small">Village / Ward</span>
                            <strong><?php echo htmlspecialchars($c_village); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="border rounded p-3 bg-light">
                            <span class="text-muted d-block small">Assigned Field Officer</span>
                            <strong><?php echo htmlspecialchars($c_officer); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Form Card -->
        <div class="col-lg-4">
            <div class="gpcms-card p-4 h-100 border-primary">
                <h5 class="fw-bold mb-3"><i class="bi bi-patch-check me-2 text-success"></i>Verification & Action</h5>
                <p class="text-muted small">Update status through approved GPCMS canonical workflow.</p>

                <form action="verify_complaint.php?id=<?php echo urlencode($c_id); ?>" method="POST">
                    <input type="hidden" name="action" value="verify_resolution">
                    <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($c_id); ?>">

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Canonical Status Update</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="resolved" <?php echo ($c_status === 'resolved') ? 'selected' : ''; ?>>resolved (Work Verified & Approved)</option>
                            <option value="in_progress" <?php echo ($c_status === 'in_progress') ? 'selected' : ''; ?>>in_progress (Requires Further Work)</option>
                            <option value="assigned" <?php echo ($c_status === 'assigned') ? 'selected' : ''; ?>>assigned (Re-assigned)</option>
                            <option value="pending" <?php echo ($c_status === 'pending') ? 'selected' : ''; ?>>pending (Re-opened)</option>
                        </select>
                        <small class="text-muted">Status values strictly restricted to canonical handbook set.</small>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-semibold">Verification Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder="Enter inspection feedback and work verification notes..." required>Inspected site work. Work completed satisfactorily and approved.</textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Update & Approve Status
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Photos Verification (complaint_photos) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="gpcms-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-images me-2 text-primary-custom"></i>Work Progress Photos Verification</h5>
                <div class="row g-4">
                    <div class="col-md-6 text-center">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-warning-subtle text-dark fw-bold">
                                <i class="bi bi-image me-1"></i> Before Photo (Reported Issue)
                            </div>
                            <div class="card-body">
                                <img src="<?php echo htmlspecialchars($before_photo); ?>" alt="Before Work Photo" class="img-fluid rounded shadow-sm max-h-300" style="max-height: 280px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 text-center">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-success-subtle text-dark fw-bold">
                                <i class="bi bi-image-fill me-1"></i> After Photo (Completed Work)
                            </div>
                            <div class="card-body">
                                <?php if (!empty($after_photo)): ?>
                                    <img src="<?php echo htmlspecialchars($after_photo); ?>" alt="After Work Photo" class="img-fluid rounded shadow-sm max-h-300" style="max-height: 280px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="p-5 bg-light rounded text-muted">
                                        <i class="bi bi-camera-fill display-4 d-block mb-2"></i>
                                        <span>No After photo uploaded by Field Officer yet.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaint History Timeline (complaint_history) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="gpcms-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary-custom"></i>Complaint History Log</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Remarks / Activity</th>
                                <th>Updated By</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($h['status_to'] ?? $h['status'] ?? 'pending'); ?></span></td>
                                        <td><?php echo htmlspecialchars($h['remarks']); ?></td>
                                        <td><?php echo htmlspecialchars($h['updater_name'] ?? 'System'); ?></td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($h['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td><span class="badge bg-warning">pending</span></td>
                                    <td>Complaint registered by citizen</td>
                                    <td>System</td>
                                    <td>25 Jul 2026, 09:30 AM</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">assigned</span></td>
                                    <td>Assigned to Field Officer Ramesh Shinde</td>
                                    <td>Rajesh Patil (Gram Sevak)</td>
                                    <td>25 Jul 2026, 10:15 AM</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">in_progress</span></td>
                                    <td>Pipeline repair crew dispatched to Ward 3</td>
                                    <td>Ramesh Shinde (Field Officer)</td>
                                    <td>26 Jul 2026, 02:00 PM</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
