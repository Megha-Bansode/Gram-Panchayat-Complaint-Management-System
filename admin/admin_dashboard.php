<?php
/**
 * Super Admin Dashboard - GPCMS v3.1
 */
declare(strict_types=1);

$page_title = "Admin Dashboard";
$active_page = "dashboard";

require_once __DIR__ . '/admin_header.php';

// Initialize counts
$count_pending = 0;
$count_assigned = 0;
$count_in_progress = 0;
$count_resolved = 0;
$count_total = 0;
$recent_complaints = [];

// Read from Database if connection is available
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->query("SELECT status, COUNT(*) as cnt FROM complaints GROUP BY status");
        $status_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $count_pending = $status_data['pending'] ?? 0;
        $count_assigned = $status_data['assigned'] ?? 0;
        $count_in_progress = $status_data['in_progress'] ?? 0;
        $count_resolved = $status_data['resolved'] ?? 0;
        $count_total = array_sum($status_data);

        // Fetch recent complaints
        $sql = "SELECT c.complaint_id, c.complaint_title AS title, c.village_ward AS ward_no, c.status, c.submitted_at AS created_at,
                       cat.category_name, u.full_name as officer_name
                FROM complaints c
                LEFT JOIN categories cat ON c.category_id = cat.category_id
                LEFT JOIN users u ON c.assigned_to = u.user_id
                ORDER BY c.submitted_at DESC LIMIT 6";
        $recent_stmt = $pdo->query($sql);
        $recent_complaints = $recent_stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Dashboard query error: ' . $e->getMessage());
    }
}
?>

<!-- Page Body -->
<main class="gpcms-body container-fluid p-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="admin-welcome-card p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-1">Welcome to GPCMS Admin Console</h2>
                        <p class="mb-0 opacity-75">You are logged in as Super Admin. Access portal statistics and generated reports.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="pending_resolved_report.php" class="btn btn-light btn-sm fw-semibold text-dark shadow-sm me-2">
                            <i class="bi bi-file-earmark-bar-graph me-1"></i> Pending & Resolved Report
                        </a>
                        <a href="village_report.php" class="btn btn-outline-light btn-sm fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> Village Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-card card-total h-100 p-3 bg-white border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold">Total Complaints</span>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $count_total; ?></h3>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-folder-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card card-pending h-100 p-3 bg-white border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold">Pending Verification</span>
                        <h3 class="fw-bold mb-0 text-warning"><?php echo $count_pending; ?></h3>
                    </div>
                    <div class="fs-1 text-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card card-in-progress h-100 p-3 bg-white border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold">In Progress</span>
                        <h3 class="fw-bold mb-0 text-info"><?php echo $count_in_progress; ?></h3>
                    </div>
                    <div class="fs-1 text-info"><i class="bi bi-gear-wide-connected"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card card-resolved h-100 p-3 bg-white border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold">Resolved</span>
                        <h3 class="fw-bold mb-0 text-success"><?php echo $count_resolved; ?></h3>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Layout -->
    <div class="row g-4 mb-4">
        <!-- Status Breakdown Chart -->
        <div class="col-lg-6">
            <div class="gpcms-card p-4 bg-white border rounded shadow-sm h-100">
                <h5 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-pie-chart-fill me-2"></i>Status Distribution</h5>
                <div style="height: 280px; position: relative;">
                    <canvas id="adminOverviewChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Complaints Grid -->
        <div class="col-lg-6">
            <div class="gpcms-card p-4 bg-white border rounded shadow-sm h-100">
                <h5 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-clock-history me-2"></i>Recent Complaints</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Ward</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_complaints)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No complaints registered yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_complaints as $c): ?>
                                    <tr>
                                        <td><strong class="text-primary">#<?php echo $c['complaint_id']; ?></strong></td>
                                        <td><span class="text-truncate d-inline-block" style="max-width: 180px;"><?php echo htmlspecialchars($c['title']); ?></span></td>
                                        <td><?php echo htmlspecialchars($c['ward_no']); ?></td>
                                        <td>
                                            <?php
                                            $status = $c['status'];
                                            $badge_class = 'bg-secondary';
                                            if ($status === 'pending') $badge_class = 'bg-warning text-dark';
                                            elseif ($status === 'assigned') $badge_class = 'bg-info text-white';
                                            elseif ($status === 'in_progress') $badge_class = 'bg-primary';
                                            elseif ($status === 'resolved') $badge_class = 'bg-success';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
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
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('adminOverviewChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Assigned', 'In Progress', 'Resolved'],
            datasets: [{
                data: [
                    <?php echo $count_pending; ?>,
                    <?php echo $count_assigned; ?>,
                    <?php echo $count_in_progress; ?>,
                    <?php echo $count_resolved; ?>
                ],
                backgroundColor: ['#d97706', '#0284c7', '#8A724C', '#16a34a'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: { family: 'Poppins', size: 12 }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
