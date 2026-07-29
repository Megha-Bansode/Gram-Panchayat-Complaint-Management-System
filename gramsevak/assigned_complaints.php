<?php
// GPCMS Gram Sevak Module - Assigned Complaints & Officer Assignment
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

auth_start_session();

if (empty($_SESSION['is_logged_in'])) {
    auth_redirect('../includes/official_login.php', 'Please sign in to continue.');
}

$page_title = "Assigned Complaints - Gram Sevak";
$active_page = "assigned_complaints";

$success_msg = "";
$error_msg = "";

// Handle Assignment Form Submit
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assign_officer') {
    $complaint_id = trim($_POST['complaint_id'] ?? '');
    $officer_id = intval($_POST['officer_id'] ?? 0);
    $remarks = trim($_POST['remarks'] ?? 'Assigned by Gram Sevak');

    if (!empty($complaint_id) && $officer_id > 0) {
        if (isset($pdo) && $pdo !== null) {
            try {
                $pdo->beginTransaction();

                // Update complaints table: status set to canonical 'assigned'
                // Use correct column name: assigned_officer_id (not assigned_to)
                $stmt = $pdo->prepare("UPDATE complaints SET assigned_officer_id = ?, status = 'assigned', assigned_at = NOW(), updated_at = NOW() WHERE complaint_id = ?");
                $stmt->execute([$officer_id, $complaint_id]);

                // Record in complaint_history table - use correct schema
                // complaint_history: history_id, complaint_id, user_id, status_from, status_to, remarks, created_at
                $stmt_hist = $pdo->prepare("INSERT INTO complaint_history (complaint_id, user_id, status_from, status_to, remarks) VALUES (?, ?, 'pending', 'assigned', ?)");
                $stmt_hist->execute([$complaint_id, $_SESSION['user_id'], $remarks]);

                $pdo->commit();
                $success_msg = "Complaint " . htmlspecialchars($complaint_id) . " successfully assigned!";
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = "Failed to assign complaint: " . $e->getMessage();
            }
        } else {
            $success_msg = "Complaint " . htmlspecialchars($complaint_id) . " assigned (Demo Mode).";
        }
    } else {
        $error_msg = "Please select a valid complaint and field officer.";
    }
}

// Fetch Field Officers (role_id = 2 for 'officer' role in database)
$field_officers = [];
// Fetch Complaints
$complaints_list = [];

if (isset($pdo) && $pdo !== null) {
    try {
        // Fetch field officers - database uses role_id=2 for 'officer' role
        $officer_stmt = $pdo->query("SELECT u.user_id, u.full_name, u.phone as mobile_number
                                    FROM users u
                                    LEFT JOIN roles r ON u.role_id = r.role_id
                                    WHERE u.role_id = 2 OR r.role_name = 'officer'");
        $field_officers = $officer_stmt->fetchAll();

        // Fetch assigned & pending complaints - use correct column names
        $sql = "SELECT c.complaint_id, c.complaint_code, c.title, c.description, c.ward_no, c.landmark, c.location_address, c.priority, c.status, c.assigned_officer_id, c.assigned_at, c.resolved_at, c.created_at, c.updated_at,
                       cat.category_name, u.full_name as officer_name
                FROM complaints c
                LEFT JOIN categories cat ON c.category_id = cat.category_id
                LEFT JOIN users u ON c.assigned_officer_id = u.user_id
                WHERE c.status IN ('pending', 'assigned', 'in_progress', 'resolved')
                ORDER BY c.created_at DESC";
        $comp_stmt = $pdo->query($sql);
        $complaints_list = $comp_stmt->fetchAll();
    } catch (Exception $e) {
        // Fallback for UI if DB tables are empty
        error_log('Assigned complaints query error: ' . $e->getMessage());
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
                    <h3 class="fw-bold mb-1"><i class="bi bi-person-plus-fill text-primary-custom me-2"></i>Assigned Complaints Management</h3>
                    <p class="text-muted mb-0">Assign pending complaints to Field Officers and monitor assignments.</p>
                </div>
                <a href="gramsevak_dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
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

    <!-- Assignment Form & Quick Assign -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="gpcms-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-gear me-2 text-primary-custom"></i>Assign Field Officer</h5>
                <form action="assigned_complaints.php" method="POST" class="row g-3">
                    <input type="hidden" name="action" value="assign_officer">
                    
                    <div class="col-md-4">
                        <label for="complaint_id" class="form-label fw-semibold">Select Complaint ID</label>
                        <select class="form-select" id="complaint_id" name="complaint_id" required>
                            <option value="">-- Choose Complaint --</option>
                            <?php if (!empty($complaints_list)): ?>
                                <?php foreach ($complaints_list as $cmp): ?>
                                    <option value="<?php echo htmlspecialchars($cmp['complaint_id']); ?>">
                                        <?php echo htmlspecialchars(($cmp['complaint_code'] ?? $cmp['complaint_id']) . ' - ' . $cmp['title'] . ' (' . $cmp['status'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">CMP-0012 - Major Water Pipeline Burst Near Primary School (in_progress)</option>
                                <option value="3">CMP-0035 - Non-functional Street Lights in Residential Area (assigned)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="officer_id" class="form-label fw-semibold">Assign Field Officer</label>
                        <select class="form-select" id="officer_id" name="officer_id" required>
                            <option value="">-- Choose Officer --</option>
                            <?php if (!empty($field_officers)): ?>
                                <?php foreach ($field_officers as $officer): ?>
                                    <option value="<?php echo htmlspecialchars($officer['user_id']); ?>">
                                        <?php echo htmlspecialchars($officer['full_name'] . ' (' . ($officer['mobile_number'] ?? $officer['phone'] ?? 'N/A') . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="2">Smit Ahirrao (Field Officer)</option>
                                <option value="3">Mukund Thorat (Sanitation Inspector)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="remarks" class="form-label fw-semibold">Assignment Remarks</label>
                        <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Instructions for officer..." value="Assigned for immediate field inspection">
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gpcms-primary">
                            <i class="bi bi-person-check-fill me-1"></i> Confirm & Assign Officer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assigned Complaints Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="gpcms-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0"><i class="bi bi-table me-2 text-primary-custom"></i>Assigned & Pending Complaints Listing</h6>
                    <button class="btn btn-sm btn-outline-dark" onclick="printElement('assignedTable', 'Assigned Complaints List')">
                        <i class="bi bi-printer me-1"></i> Print Listing
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 gpcms-table" id="assignedTable">
                            <thead>
                                <tr>
                                    <th>Complaint ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Village / Ward</th>
                                    <th>Assigned Officer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($complaints_list)): ?>
                                    <?php foreach ($complaints_list as $row): ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['complaint_code'] ?? $row['complaint_id']); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['category_name'] ?? 'General'); ?></span></td>
                                            <td><?php echo htmlspecialchars($row['ward_no'] ?? $row['location_address'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($row['officer_name'] ?? 'Unassigned'); ?></td>
                                            <td>
                                                <?php
                                                $st = strtolower($row['status']);
                                                $badge_class = 'bg-warning';
                                                if ($st === 'assigned') $badge_class = 'bg-info text-white';
                                                elseif ($st === 'in_progress') $badge_class = 'bg-primary';
                                                elseif ($st === 'resolved') $badge_class = 'bg-success';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                            </td>
                                            <td><small><?php echo date('d M Y', strtotime($row['created_at'])); ?></small></td>
                                            <td class="text-end">
                                                <a href="verify_complaint.php?id=<?php echo urlencode($row['complaint_id']); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border">CMP-0012</span></td>
                                        <td><strong>Major Water Pipeline Burst Near Primary School</strong></td>
                                        <td><span class="badge bg-secondary">Water Supply</span></td>
                                        <td>Ward 04</td>
                                        <td>Smit Ahirrao</td>
                                        <td><span class="badge bg-primary">in_progress</span></td>
                                        <td>25 Jul 2026</td>
                                        <td class="text-end">
                                            <a href="verify_complaint.php?id=1" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Details</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
