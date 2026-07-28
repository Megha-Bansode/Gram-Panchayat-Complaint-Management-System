<?php
/**
 * admin/pending_resolved_report.php
 * GPCMS — Pending vs Resolved Analytics & Performance Scorecard
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

$conn = get_db_connection();

// Status breakdown
$statusRows = [];
$totalComplaints = 1;
$res = $conn->query("SELECT COUNT(*) c FROM complaints");
if ($res) $totalComplaints = max(1, (int)$res->fetch_assoc()['c']);

$res = $conn->query("SELECT status, COUNT(*) AS count FROM complaints GROUP BY status ORDER BY FIELD(status,'pending','assigned','in_progress','resolved')");
if ($res) while ($row = $res->fetch_assoc()) $statusRows[] = $row;

// Monthly trend data (last 6 months)
$monthlyTrend = [];
$res = $conn->query("
    SELECT DATE_FORMAT(submitted_at, '%b %Y') AS month_label,
           MONTH(submitted_at) AS mon, YEAR(submitted_at) AS yr,
           COUNT(*) AS total,
           SUM(CASE WHEN status='resolved' THEN 1 ELSE 0 END) AS resolved,
           SUM(CASE WHEN status='pending'  THEN 1 ELSE 0 END) AS pending
    FROM complaints
    WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY yr, mon
    ORDER BY yr, mon");
if ($res) while ($row = $res->fetch_assoc()) $monthlyTrend[] = $row;

// Calc KPI
$kpi = [
    'total'       => $totalComplaints,
    'pending'     => 0, 'assigned' => 0, 'in_progress' => 0, 'resolved' => 0
];
foreach ($statusRows as $r) {
    $st = strtolower($r['status']);
    if (isset($kpi[$st])) $kpi[$st] = (int)$r['count'];
}
$resolutionRate = round(($kpi['resolved'] / $totalComplaints) * 100, 1);

// Pie data
$pieAttr = sprintf('data-pending="%d" data-assigned="%d" data-progress="%d" data-resolved="%d"',
    $kpi['pending'], $kpi['assigned'], $kpi['in_progress'], $kpi['resolved']);

$adminName    = htmlspecialchars($_SESSION['full_name'] ?? 'Panchayat Admin');
$adminInitial = strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="GPCMS Pending vs Resolved Analytics Report">
<title>Resolution Analytics — GPCMS Enterprise</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/analytics.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

<div id="sidebarOverlay" class="sidebar-overlay" style="display:none;" aria-hidden="true"></div>

<!-- HEADER -->
<header class="app-header" role="banner">
    <button class="header-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar"><i class="bi bi-list fs-5"></i></button>
    <div class="header-brand">
        <div class="brand-icon"><i class="bi bi-bank2"></i></div>
        <div class="brand-name">GPCMS Analytics<small>Gram Panchayat Enterprise</small></div>
    </div>
    <nav class="ms-4 d-none d-lg-block" aria-label="Breadcrumb">
        <div class="breadcrumb-custom">
            <a href="analytics_dashboard.php"><i class="bi bi-house-door me-1"></i>Home</a>
            <span class="sep"><i class="bi bi-chevron-right"></i></span>
            <a href="analytics_dashboard.php">Analytics</a>
            <span class="sep"><i class="bi bi-chevron-right"></i></span>
            <span class="current">Resolution Status</span>
        </div>
    </nav>
    <div class="header-right">
        <div class="header-date-time d-none d-lg-flex">
            <span class="current-date" id="headerDate"></span>
            <span id="headerTime"></span>
        </div>
        <button class="icon-btn" onclick="window.printPage()"><i class="bi bi-printer-fill"></i></button>
        <div class="header-divider"></div>
        <div class="dropdown">
            <div class="profile-trigger" data-bs-toggle="dropdown">
                <div class="profile-avatar"><?= $adminInitial ?></div>
                <div class="profile-info d-none d-md-flex">
                    <span class="profile-name"><?= $adminName ?></span>
                    <span class="profile-role">Administrator</span>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                <li><a class="dropdown-item" href="analytics_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- SIDEBAR -->
<nav class="app-sidebar" id="appSidebar" aria-label="Main navigation">
    <div class="sidebar-header">
        <div class="system-health"><span class="health-dot"></span><span class="nav-text" style="font-size:0.72rem;">System Operational</span></div>
    </div>
    <div class="sidebar-nav">
        <div class="sidebar-label">MAIN NAVIGATION</div>
        <ul role="list">
            <li class="nav-item-custom"><a href="analytics_dashboard.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-speedometer2"></i></span><span class="nav-text">Dashboard</span></a></li>
            <li class="nav-item-custom"><a href="category_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span><span class="nav-text">Category Reports</span></a></li>
            <li class="nav-item-custom"><a href="village_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span><span class="nav-text">Village Reports</span></a></li>
            <li class="nav-item-custom"><a href="pending_resolved_report.php" class="nav-link-custom active" aria-current="page"><span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span><span class="nav-text">Resolution Status</span></a></li>
        </ul>
        <div class="sidebar-label mt-3">TOOLS</div>
        <ul role="list">
            <li class="nav-item-custom"><a href="#" class="nav-link-custom" onclick="exportTableCSV('tblResolutionReport','GPCMS_Resolution.csv'); return false;"><span class="nav-icon"><i class="bi bi-download"></i></span><span class="nav-text">Export CSV</span></a></li>
            <li class="nav-item-custom"><a href="#" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-gear-fill"></i></span><span class="nav-text">Settings</span></a></li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <a href="../logout.php" class="nav-link-custom nav-link-logout"><span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span><span class="nav-text">Logout</span></a>
    </div>
</nav>

<!-- MAIN -->
<main class="app-main" id="appMain" role="main">
<div class="page-content">
<div class="content-main">

    <!-- PAGE HEADER -->
    <div class="page-header anim-fadeInUp">
        <div>
            <h1 class="page-title"><i class="bi bi-pie-chart-fill me-2 text-primary-gp"></i>Pending vs Resolved Analytics</h1>
            <div class="page-subtitle">Resolution performance scorecard &amp; SLA tracking for grievance management</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-primary ripple-btn" onclick="exportTableCSV('tblResolutionReport','GPCMS_Resolution_Report.csv')"><i class="bi bi-filetype-csv me-1"></i>Export CSV</button>
            <button class="btn btn-outline-primary ripple-btn" onclick="exportTableExcel('tblResolutionReport','GPCMS_Resolution_Report.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            <button class="btn btn-surface ripple-btn" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
    </div>

    <!-- STATUS KPI CARDS -->
    <div class="kpi-grid anim-fadeInUp anim-delay-1">
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-folder-fill"></i></div></div>
            <div class="kpi-label">Total Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['total'] ?>">0</div>
            <div class="kpi-sub">All registered grievances</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c2"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="kpi-trend"><i class="bi bi-clock"></i>Awaiting</div></div>
            <div class="kpi-label">Pending</div>
            <div class="kpi-value" data-counter="<?= $kpi['pending'] ?>">0</div>
            <div class="kpi-sub"><span class="badge-status badge-pending">Needs Action</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c3"><i class="bi bi-person-badge-fill"></i></div></div>
            <div class="kpi-label">Assigned</div>
            <div class="kpi-value" data-counter="<?= $kpi['assigned'] ?>">0</div>
            <div class="kpi-sub"><span class="badge-status badge-assigned">Allocated</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c4"><i class="bi bi-gear-fill"></i></div></div>
            <div class="kpi-label">In Progress</div>
            <div class="kpi-value" data-counter="<?= $kpi['in_progress'] ?>">0</div>
            <div class="kpi-sub"><span class="badge-status badge-in_progress">Active Work</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-check-circle-fill"></i></div><div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+8%</div></div>
            <div class="kpi-label">Resolved</div>
            <div class="kpi-value" data-counter="<?= $kpi['resolved'] ?>">0</div>
            <div class="kpi-sub"><span class="badge-status badge-resolved">Completed</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c2"><i class="bi bi-percent"></i></div><div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>Good</div></div>
            <div class="kpi-label">Resolution Rate</div>
            <div class="kpi-value" data-counter="<?= $resolutionRate ?>" data-suffix="%">0%</div>
            <div class="kpi-sub">Overall closure rate</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-stopwatch-fill"></i></div></div>
            <div class="kpi-label">Avg. Resolution Time</div>
            <div class="kpi-value" data-counter="3" data-suffix=" days">0 days</div>
            <div class="kpi-sub">SLA performance target</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c3"><i class="bi bi-emoji-smile-fill"></i></div><div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+3%</div></div>
            <div class="kpi-label">Citizen Satisfaction</div>
            <div class="kpi-value" data-counter="92" data-suffix="%">0%</div>
            <div class="kpi-sub">Positive feedback index</div>
        </div>
    </div>

    <!-- CHART ROW 1: Pie + Trend -->
    <div class="row g-4 mb-4 anim-fadeInUp anim-delay-2">
        <div class="col-12 col-lg-4">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-pie-chart-fill"></i>Resolution Ratio</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartPendingResolved" <?= $pieAttr ?>></canvas></div>
                    <div class="text-center mt-3">
                        <div class="fw-700" style="font-size:1.8rem; color:var(--gp-primary);"><?= $resolutionRate ?>%</div>
                        <div class="fs-sm text-muted-gp">Overall Resolution Rate</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-graph-up-arrow"></i>Complaint Trend (12 Months)</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartComplaintTrend"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHART ROW 2: Stacked + Timeline -->
    <div class="row g-4 mb-4 anim-fadeInUp anim-delay-3">
        <div class="col-12 col-lg-6">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-steps"></i>Monthly Status Breakdown</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartStackedBar"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-stopwatch"></i>Resolution Velocity Timeline</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartResolutionTimeline"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCORECARD TABLE -->
    <div class="section-card mb-4 anim-fadeInUp anim-delay-4">
        <div class="section-card-header">
            <div class="section-card-title"><i class="bi bi-table"></i>Operational Status Scorecard</div>
            <div class="section-card-actions">
                <button class="btn btn-sm btn-surface" onclick="exportTableCSV('tblResolutionReport','GPCMS_Resolution.csv')"><i class="bi bi-filetype-csv me-1"></i>CSV</button>
                <button class="btn btn-sm btn-surface" onclick="exportTableExcel('tblResolutionReport','GPCMS_Resolution.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            </div>
        </div>
        <div class="section-card-body pb-0">
            <div class="table-toolbar">
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Search status..." data-table-search="tblResolutionReport">
                </div>
            </div>
            <div class="data-table-wrap">
                <table class="data-table" id="tblResolutionReport">
                    <thead>
                        <tr>
                            <th data-sort>Status State</th>
                            <th data-sort>Count</th>
                            <th data-sort>Percentage Share</th>
                            <th>Visual Distribution</th>
                            <th data-sort>SLA Target</th>
                            <th>System Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $statusConfig = [
                            'pending'     => ['label'=>'Pending',     'sla'=>'48 Hours', 'badge'=>'badge-pending'],
                            'assigned'    => ['label'=>'Assigned',    'sla'=>'24 Hours', 'badge'=>'badge-assigned'],
                            'in_progress' => ['label'=>'In Progress', 'sla'=>'72 Hours', 'badge'=>'badge-in_progress'],
                            'resolved'    => ['label'=>'Resolved',    'sla'=>'Completed','badge'=>'badge-resolved'],
                        ];
                        foreach ($statusRows as $row):
                            $st  = strtolower($row['status']);
                            $cnt = (int)$row['count'];
                            $pct = round(($cnt / $totalComplaints) * 100, 1);
                            $cfg = $statusConfig[$st] ?? ['label' => ucwords(str_replace('_',' ',$st)), 'sla'=>'—','badge'=>'badge-pending'];
                        ?>
                        <tr>
                            <td><span class="badge-status <?= $cfg['badge'] ?>"><?= $cfg['label'] ?></span></td>
                            <td class="fw-700"><?= number_format($cnt) ?></td>
                            <td class="fw-700 text-primary-gp"><?= $pct ?>%</td>
                            <td style="min-width:150px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-custom flex-grow-1" style="height:8px;">
                                        <div class="progress-bar-custom" data-progress="<?= $pct ?>"></div>
                                    </div>
                                    <span class="fs-xs fw-600"><?= $pct ?>%</span>
                                </div>
                            </td>
                            <td class="text-muted-gp"><?= $cfg['sla'] ?></td>
                            <td><i class="bi bi-check-circle-fill text-primary-gp me-1"></i>Operational</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($statusRows)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted-gp">No resolution data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-pagination">
            <span class="pager-info text-muted fs-xs"><?= count($statusRows) ?> status states</span>
            <div class="pagination-btns"></div>
        </div>
    </div>

</div>
</div>

<footer class="app-footer">
    <div><strong style="color:var(--gp-primary);">GPCMS Analytics</strong> &nbsp;|&nbsp; Resolution Status Report &nbsp;|&nbsp; v2.0</div>
    <div class="footer-links"><a href="#">Privacy Policy</a><a href="#">Support</a></div>
    <div class="text-muted-gp fs-xs">&copy; <?= date('Y') ?> Digital India Initiative</div>
</footer>
</main>

<button class="fab ripple-btn" id="fabBtn" title="Quick Actions"><i class="bi bi-plus-lg"></i></button>
<div class="fab-menu" id="fabMenu">
    <button class="fab-menu-btn" onclick="window.printPage()"><div class="fab-menu-icon"><i class="bi bi-printer-fill"></i></div>Print Report</button>
    <button class="fab-menu-btn" onclick="exportTableCSV('tblResolutionReport','GPCMS_Resolution.csv')"><div class="fab-menu-icon"><i class="bi bi-download"></i></div>Export CSV</button>
    <button class="fab-menu-btn" onclick="location.href='analytics_dashboard.php'"><div class="fab-menu-icon"><i class="bi bi-speedometer2"></i></div>Dashboard</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/analytics.js?v=<?= time() ?>"></script>
</body>
</html>
