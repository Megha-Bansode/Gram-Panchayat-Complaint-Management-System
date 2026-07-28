<?php
// GPCMS Gram Sevak Dashboard - Handbook Contract Aligned
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Gram Sevak' && (string) $user['role_name'] !== 'Gram Panchayat Admin') {
    auth_redirect('../includes/official_login.php', 'Unauthorized access.');
}

check_role([2, 'Gram Sevak', 'Gram Panchayat Admin']);

$page_title = "Gram Sevak Dashboard";
$active_page = "dashboard";

// Initialize counts
$count_pending = 0;
$count_assigned = 0;
$count_in_progress = 0;
$count_resolved = 0;
$count_total = 0;

$recent_complaints = [];

// Read from Database if PDO connection available
if (isset($pdo) && $pdo !== null) {
    try {
        // Status counts using canonical statuses: 'pending', 'assigned', 'in_progress', 'resolved'
        $stmt = $pdo->query("SELECT status, COUNT(*) as cnt FROM complaints GROUP BY status");
        $status_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $count_pending = $status_data['pending'] ?? 0;
        $count_assigned = $status_data['assigned'] ?? 0;
        $count_in_progress = $status_data['in_progress'] ?? 0;
        $count_resolved = $status_data['resolved'] ?? 0;
        $count_total = array_sum($status_data);

        // Fetch recent complaints
        $sql = "SELECT c.*, cat.category_name, u.full_name as officer_name 
                FROM complaints c
                LEFT JOIN categories cat ON c.category_id = cat.category_id
                LEFT JOIN users u ON c.assigned_to = u.user_id
                ORDER BY c.created_at DESC LIMIT 10";
        $recent_stmt = $pdo->query($sql);
        $recent_complaints = $recent_stmt->fetchAll();
    } catch (Exception $e) {
        // Fallback for initial UI rendering if tables empty
    }
}

require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="dashboard-welcome-card p-4 rounded-3 text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                                    <p class="mb-0 opacity-75">Overseeing complaint resolution across all villages under Gram Panchayat jurisdiction.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="assigned_complaints.php" class="btn btn-light btn-sm fw-semibold text-dark shadow-sm me-2">
                                        <i class="bi bi-person-plus me-1"></i> Assign Field Officers
                                    </a>
                                    <a href="verify_complaint.php" class="btn btn-outline-light btn-sm fw-semibold">
                                        <i class="bi bi-check2-circle me-1"></i> Verify Work
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-total h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Total Complaints</span>
                                        <h3 class="card-number" id="dashTotalComplaints"><?php echo $count_total; ?></h3>
                                    </div>
                                    <div class="card-icon"><i class="bi bi-folder-fill"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-success small"><i class="bi bi-graph-up"></i> Live DB Sync</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-pending h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Pending</span>
                                        <h3 class="card-number text-warning-custom" id="dashPendingComplaints"><?php echo $count_pending; ?></h3>
                                    </div>
                                    <div class="card-icon text-warning-custom"><i class="bi bi-clock-history"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-muted small">Requires Assignment</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-assigned h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Assigned</span>
                                        <h3 class="card-number text-info-custom" id="dashAssignedComplaints"><?php echo $count_assigned; ?></h3>
                                    </div>
                                    <div class="card-icon text-info-custom"><i class="bi bi-person-check-fill"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-muted small">Assigned to Officer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-in-progress h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">In Progress</span>
                                        <h3 class="card-number text-primary-custom" id="dashInProgressComplaints"><?php echo $count_in_progress; ?></h3>
                                    </div>
                                    <div class="card-icon text-primary-custom"><i class="bi bi-tools"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-muted small">Work Underway</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-8 col-sm-12">
                        <div class="summary-card card-resolved h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Resolved Complaints</span>
                                        <h3 class="card-number text-success-custom" id="dashResolvedComplaints"><?php echo $count_resolved; ?></h3>
                                    </div>
                                    <div class="card-icon text-success-custom"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-success small"><i class="bi bi-shield-check"></i> Resolution Target Met</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons Bar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gpcms-card p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h6 class="mb-0 fw-semibold text-secondary-custom"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Action Tools</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="assigned_complaints.php" class="btn btn-gpcms-primary btn-sm">
                                        <i class="bi bi-person-plus me-1"></i> Assign Field Officer
                                    </a>
                                    <a href="update_complaint_status.php" class="btn btn-gpcms-secondary btn-sm">
                                        <i class="bi bi-arrow-repeat me-1"></i> Change Status
                                    </a>
                                    <a href="verify_complaint.php" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-patch-check me-1"></i> Verify Completed Work
                                    </a>
                                    <button class="btn btn-outline-dark btn-sm" onclick="printElement('dashboardRecentTable', 'Dashboard Complaints Summary')">
                                        <i class="bi bi-printer me-1"></i> Print Log
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Charts & Activity -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-primary-custom"></i>Complaint Distribution Overview</h6>
                                <span class="badge bg-light text-dark border">Current Month</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-container position-relative" style="height: 280px;">
                                    <canvas id="dashboardOverviewChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-activity me-2 text-primary-custom"></i>Recent Activity Feed</h6>
                                <button class="btn btn-sm btn-link text-decoration-none" onclick="refreshActivityFeed()">Refresh</button>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush activity-feed-list" id="activityFeedList">
                                    <!-- Dynamic content populated via JS -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Complaints Table -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gpcms-card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h6 class="card-title mb-0"><i class="bi bi-table me-2 text-primary-custom"></i>Recent Complaints Log</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-dark" onclick="printElement('dashboardRecentTable', 'Recent Complaints Log')">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </button>
                                    <a href="view_complaints.php" class="btn btn-sm btn-gpcms-primary">View All Complaints <i class="bi bi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 gpcms-table" id="dashboardRecentTable">
                                        <thead>
                                            <tr>
                                                <th>Complaint ID</th>
                                                <th>Complainant Name</th>
                                                <th>Village / Ward</th>
                                                <th>Category</th>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Assigned Officer</th>
                                                <th>Date</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dashboardTableBody">
                                            <?php if (!empty($recent_complaints)): ?>
                                                <?php foreach ($recent_complaints as $row): ?>
                                                    <tr>
                                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['complaint_id']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($row['complainant_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['village_ward']); ?></td>
                                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['category_name'] ?? 'General'); ?></span></td>
                                                        <td><?php echo htmlspecialchars($row['complaint_title']); ?></td>
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
                                                        <td><?php echo htmlspecialchars($row['officer_name'] ?? 'Unassigned'); ?></td>
                                                        <td><small><?php echo date('d M Y', strtotime($row['created_at'])); ?></small></td>
                                                        <td class="text-end">
                                                            <a href="verify_complaint.php?id=<?php echo urlencode($row['complaint_id']); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- View Details Modal (Shared across dashboard & view complaints) -->
            <div class="modal fade" id="complaintDetailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalDetailHeaderTitle"><i class="bi bi-file-earmark-text me-2"></i>Complaint Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalDetailContent">
                            <!-- Loaded dynamically via JavaScript -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

<?php require_once __DIR__ . '/footer.php'; ?>
