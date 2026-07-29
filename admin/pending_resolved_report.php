<?php
/**
 * Pending vs Resolved Analytical Report - GPCMS v3.1
 */
declare(strict_types=1);

$page_title = "Pending vs Resolved Report";
$active_page = "pending_resolved";

require_once __DIR__ . '/admin_header.php';

// Retrieve filter inputs
$filter_start_date = isset($_GET['start_date']) ? auth_sanitize_input($_GET['start_date']) : '';
$filter_end_date = isset($_GET['end_date']) ? auth_sanitize_input($_GET['end_date']) : '';
$filter_category = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;
$filter_ward = isset($_GET['ward']) ? auth_sanitize_input($_GET['ward']) : '';
$filter_status = isset($_GET['status']) ? auth_sanitize_input($_GET['status']) : '';

// Fetch categories for the filter dropdown
$categories = [];
if (isset($pdo)) {
    try {
        $cat_stmt = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
        $categories = $cat_stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Category fetch error: ' . $e->getMessage());
    }
}

// Fetch unique wards for filter dropdown
$wards = [];
if (isset($pdo)) {
    try {
        $ward_stmt = $pdo->query("SELECT DISTINCT village_ward FROM complaints WHERE village_ward IS NOT NULL AND village_ward != '' ORDER BY village_ward ASC");
        $wards = $ward_stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        error_log('Ward fetch error: ' . $e->getMessage());
    }
}

// Build query
$where_clauses = [];
$params = [];

if ($filter_start_date !== '') {
    $where_clauses[] = "c.submitted_at >= ?";
    $params[] = $filter_start_date . " 00:00:00";
}
if ($filter_end_date !== '') {
    $where_clauses[] = "c.submitted_at <= ?";
    $params[] = $filter_end_date . " 23:59:59";
}
if ($filter_category !== null) {
    $where_clauses[] = "c.category_id = ?";
    $params[] = $filter_category;
}
if ($filter_ward !== '') {
    $where_clauses[] = "c.village_ward = ?";
    $params[] = $filter_ward;
}
if ($filter_status !== '') {
    $where_clauses[] = "c.status = ?";
    $params[] = $filter_status;
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Fetch complaints
$complaints = [];
$counts = [
    'pending' => 0,
    'assigned' => 0,
    'in_progress' => 0,
    'resolved' => 0
];

if (isset($pdo)) {
    try {
        // Query matching filters
        $sql = "SELECT c.complaint_id, c.complaint_title AS title, c.village_ward AS ward_no, c.status, c.submitted_at AS created_at,
                       cat.category_name, u_cit.full_name as complainant_name, u_off.full_name as officer_name
                FROM complaints c
                LEFT JOIN categories cat ON c.category_id = cat.category_id
                LEFT JOIN users u_cit ON c.user_id = u_cit.user_id
                LEFT JOIN users u_off ON c.assigned_to = u_off.user_id
                $where_sql
                ORDER BY c.submitted_at DESC";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $complaints = $stmt->fetchAll();

        // Calculate counts for this subset
        foreach ($complaints as $c) {
            $status = $c['status'];
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }
    } catch (Exception $e) {
        error_log('Report query error: ' . $e->getMessage());
    }
}

$count_total = array_sum($counts);
?>

<main class="gpcms-body container-fluid p-4">
    <!-- Breadcrumbs & Global Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-secondary-custom">Pending & Resolved Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pending vs Resolved</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-danger btn-sm" onclick="printElement('reportTablePrintArea', 'Pending and Resolved Complaints Statement')">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
            </button>
            <button class="btn btn-success btn-sm" onclick="exportCSVReport('reportDataTable', 'pending_resolved_report.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
            </button>
            <button class="btn btn-dark btn-sm" onclick="printElement('reportTablePrintArea', 'Pending and Resolved Complaints Statement')">
                <i class="bi bi-printer-fill me-1"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filter Criteria Panel -->
    <div class="gpcms-card p-3 mb-4 bg-white border rounded shadow-sm">
        <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-funnel-fill me-2"></i>Filter Criteria</h6>
        <form method="GET" action="pending_resolved_report.php">
            <div class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-4">
                    <label for="start_date" class="form-label small fw-semibold">Start Date</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="end_date" class="form-label small fw-semibold">End Date</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="category_id" class="form-label small fw-semibold">Category</label>
                    <select class="form-select form-select-sm" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo $filter_category === (int)$cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endphp ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="ward" class="form-label small fw-semibold">Village / Ward</label>
                    <select class="form-select form-select-sm" id="ward" name="ward">
                        <option value="">All Wards</option>
                        <?php foreach ($wards as $wd): ?>
                            <option value="<?php echo htmlspecialchars($wd); ?>" <?php echo $filter_ward === $wd ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($wd); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="status" class="form-label small fw-semibold">Status</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>pending</option>
                        <option value="assigned" <?php echo $filter_status === 'assigned' ? 'selected' : ''; ?>>assigned</option>
                        <option value="in_progress" <?php echo $filter_status === 'in_progress' ? 'selected' : ''; ?>>in_progress</option>
                        <option value="resolved" <?php echo $filter_status === 'resolved' ? 'selected' : ''; ?>>resolved</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-play-fill me-1"></i> Run
                    </button>
                    <a href="pending_resolved_report.php" class="btn btn-outline-secondary btn-sm flex-fill">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Charts & Metrics Row -->
    <div class="row g-4 mb-4">
        <!-- Visual Distribution Chart -->
        <div class="col-lg-8">
            <div class="gpcms-card p-4 bg-white border rounded shadow-sm h-100">
                <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-pie-chart-fill me-2"></i>Status Distribution Visual Breakdown</h6>
                <div style="height: 280px; position: relative;">
                    <canvas id="pendingResolvedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Metric Highlight Card -->
        <div class="col-lg-4">
            <div class="gpcms-card p-4 bg-white border rounded shadow-sm h-100">
                <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-calculator-fill me-2"></i>Summary Highlights</h6>
                
                <div class="p-3 bg-light border rounded mb-3">
                    <span class="text-muted small d-block">Total Filtered Complaints</span>
                    <h4 class="fw-bold mb-0 text-primary-custom" id="summaryTotalCount"><?php echo $count_total; ?></h4>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-warning bg-opacity-10 text-center">
                            <span class="text-muted small d-block">Pending</span>
                            <span class="fw-bold text-warning fs-5"><?php echo $counts['pending']; ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-success bg-opacity-10 text-center">
                            <span class="text-muted small d-block">Resolved</span>
                            <span class="fw-bold text-success fs-5"><?php echo $counts['resolved']; ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-info bg-opacity-10 text-center">
                            <span class="text-muted small d-block">Assigned</span>
                            <span class="fw-bold text-info fs-5"><?php echo $counts['assigned']; ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-primary bg-opacity-10 text-center">
                            <span class="text-muted small d-block">In Progress</span>
                            <span class="fw-bold text-primary fs-5"><?php echo $counts['in_progress']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="gpcms-card bg-white border rounded shadow-sm" id="reportTablePrintArea">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-secondary-custom"><i class="bi bi-table me-2"></i>Report Data Statement</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reportDataTable">
                    <thead class="table-light text-secondary-custom">
                        <tr>
                            <th>Complaint ID</th>
                            <th>Complainant</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Village / Ward</th>
                            <th>Status</th>
                            <th>Assigned Officer</th>
                            <th>Submission Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($complaints)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No complaints matched the filter criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($complaints as $c): ?>
                                <tr>
                                    <td><strong class="text-primary">#<?php echo $c['complaint_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($c['complainant_name'] ?? 'Unknown'); ?></td>
                                    <td><span class="text-truncate d-inline-block" style="max-width: 250px;"><?php echo htmlspecialchars($c['title']); ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($c['category_name'] ?? 'General'); ?></span></td>
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
                                    <td><?php echo htmlspecialchars($c['officer_name'] ?? 'Unassigned'); ?></td>
                                    <td><small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($c['created_at'])); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('pendingResolvedChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Assigned', 'In Progress', 'Resolved'],
            datasets: [{
                data: [
                    <?php echo $counts['pending']; ?>,
                    <?php echo $counts['assigned']; ?>,
                    <?php echo $counts['in_progress']; ?>,
                    <?php echo $counts['resolved']; ?>
                ],
                backgroundColor: ['#d97706', '#0284c7', '#8A724C', '#16a34a'],
                borderWidth: 1,
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
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
