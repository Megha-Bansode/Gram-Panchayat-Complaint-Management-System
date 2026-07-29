<?php
/**
 * Village-wise Aggregate Report - GPCMS v3.1
 */
declare(strict_types=1);

$page_title = "Village-wise Reports";
$active_page = "village_report";

require_once __DIR__ . '/admin_header.php';

// Retrieve filter inputs
$filter_start_date = isset($_GET['start_date']) ? auth_sanitize_input($_GET['start_date']) : '';
$filter_end_date = isset($_GET['end_date']) ? auth_sanitize_input($_GET['end_date']) : '';
$filter_category = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;

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

// Build query params and conditions
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

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Fetch aggregate data grouped by village_ward
$village_stats = [];
if (isset($pdo)) {
    try {
        $sql = "SELECT c.village_ward,
                       COUNT(*) as total,
                       SUM(CASE WHEN c.status = 'pending' THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN c.status = 'assigned' THEN 1 ELSE 0 END) as assigned,
                       SUM(CASE WHEN c.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                       SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved
                FROM complaints c
                $where_sql
                GROUP BY c.village_ward
                ORDER BY total DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $village_stats = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Village aggregate query error: ' . $e->getMessage());
    }
}

// Separate data for Chart.js
$chart_labels = [];
$chart_totals = [];
$chart_resolved = [];

foreach ($village_stats as $stat) {
    $chart_labels[] = $stat['village_ward'] !== '' ? $stat['village_ward'] : 'Unassigned Ward';
    $chart_totals[] = (int)$stat['total'];
    $chart_resolved[] = (int)$stat['resolved'];
}
?>

<main class="gpcms-body container-fluid p-4">
    <!-- Breadcrumbs & Global Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-secondary-custom">Village / Ward summary report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Village Reports</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-danger btn-sm" onclick="printElement('villageTablePrintArea', 'Village Ward Complaints Summary Statement')">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
            </button>
            <button class="btn btn-success btn-sm" onclick="exportCSVReport('villageDataTable', 'village_complaints_report.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
            </button>
            <button class="btn btn-dark btn-sm" onclick="printElement('villageTablePrintArea', 'Village Ward Complaints Summary Statement')">
                <i class="bi bi-printer-fill me-1"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filter Criteria Panel -->
    <div class="gpcms-card p-3 mb-4 bg-white border rounded shadow-sm">
        <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-funnel-fill me-2"></i>Filter Criteria</h6>
        <form method="GET" action="village_report.php">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-4">
                    <label for="start_date" class="form-label small fw-semibold">Start Date</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                </div>
                <div class="col-lg-3 col-md-4">
                    <label for="end_date" class="form-label small fw-semibold">End Date</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                </div>
                <div class="col-lg-3 col-md-4">
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
                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-play-fill me-1"></i> Run
                    </button>
                    <a href="village_report.php" class="btn btn-outline-secondary btn-sm flex-fill">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="gpcms-card p-4 bg-white border rounded shadow-sm">
                <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-bar-chart-line-fill me-2"></i>Ward / Village Breakdown comparison</h6>
                <div style="height: 300px; position: relative;">
                    <canvas id="villageReportsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="gpcms-card bg-white border rounded shadow-sm" id="villageTablePrintArea">
        <div class="card-header p-3 bg-light border-bottom">
            <h6 class="fw-bold mb-0 text-secondary-custom"><i class="bi bi-table me-2"></i>Village/Ward Aggregated Statistics Table</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="villageDataTable">
                    <thead class="table-light text-secondary-custom">
                        <tr>
                            <th>Village / Ward</th>
                            <th>Total Complaints</th>
                            <th>Pending</th>
                            <th>Assigned</th>
                            <th>In Progress</th>
                            <th>Resolved</th>
                            <th>Resolution Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($village_stats)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No records found matching criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($village_stats as $stat): ?>
                                <?php
                                $total = (int)$stat['total'];
                                $resolved = (int)$stat['resolved'];
                                $rate = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($stat['village_ward'] !== '' ? $stat['village_ward'] : 'Unassigned Ward'); ?></strong></td>
                                    <td><?php echo $total; ?></td>
                                    <td><span class="text-warning font-weight-bold"><?php echo $stat['pending']; ?></span></td>
                                    <td><span class="text-info font-weight-bold"><?php echo $stat['assigned']; ?></span></td>
                                    <td><span class="text-primary font-weight-bold"><?php echo $stat['in_progress']; ?></span></td>
                                    <td><span class="text-success font-weight-bold"><?php echo $resolved; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; min-width: 80px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $rate; ?>%" aria-valuenow="<?php echo $rate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="fw-bold small"><?php echo $rate; ?>%</span>
                                        </div>
                                    </td>
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
    const ctx = document.getElementById('villageReportsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [
                {
                    label: 'Total Complaints',
                    data: <?php echo json_encode($chart_totals); ?>,
                    backgroundColor: '#B99668',
                    borderRadius: 6
                },
                {
                    label: 'Resolved Complaints',
                    data: <?php echo json_encode($chart_resolved); ?>,
                    backgroundColor: '#16a34a',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { family: 'Poppins', size: 12 } }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
