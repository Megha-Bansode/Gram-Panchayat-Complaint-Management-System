<?php
/**
 * admin/category_report.php
 * GPCMS — Category-wise Complaint Analytics Report
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

$conn = get_db_connection();

// Category stats
$catRows = [];
$res = $conn->query("
    SELECT cat.category_id, cat.category_name,
           COUNT(c.complaint_id) AS total,
           SUM(CASE WHEN c.status='resolved'    THEN 1 ELSE 0 END) AS resolved,
           SUM(CASE WHEN c.status='pending'     THEN 1 ELSE 0 END) AS pending,
           SUM(CASE WHEN c.status='in_progress' THEN 1 ELSE 0 END) AS in_progress,
           SUM(CASE WHEN c.status='assigned'    THEN 1 ELSE 0 END) AS assigned
    FROM categories cat
    LEFT JOIN complaints c ON cat.category_id = c.category_id
    GROUP BY cat.category_id
    ORDER BY total DESC");
if ($res) while ($row = $res->fetch_assoc()) $catRows[] = $row;

$totalCats = count($catRows);
$grandTotal = array_sum(array_column($catRows, 'total'));
$grandResolved = array_sum(array_column($catRows, 'resolved'));
$grandPending  = array_sum(array_column($catRows, 'pending'));

$adminName = htmlspecialchars($_SESSION['full_name'] ?? 'Panchayat Admin');
$adminInitial = strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="GPCMS Category-wise Complaint Analytics Report">
<title>Category Analytics — GPCMS Enterprise</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/analytics.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

<div id="sidebarOverlay" class="sidebar-overlay" style="display:none;" aria-hidden="true"></div>

<!-- HEADER -->
<header class="app-header" role="banner">
    <button class="header-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>
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
            <span class="current">Category Report</span>
        </div>
    </nav>
    <div class="header-right">
        <div class="header-date-time d-none d-lg-flex">
            <span class="current-date" id="headerDate"></span>
            <span id="headerTime"></span>
        </div>
        <button class="icon-btn" title="Print" onclick="window.printPage()"><i class="bi bi-printer-fill"></i></button>
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
            <li class="nav-item-custom"><a href="category_report.php" class="nav-link-custom active" aria-current="page"><span class="nav-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span><span class="nav-text">Category Reports</span></a></li>
            <li class="nav-item-custom"><a href="village_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span><span class="nav-text">Village Reports</span></a></li>
            <li class="nav-item-custom"><a href="pending_resolved_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span><span class="nav-text">Resolution Status</span></a></li>
        </ul>
        <div class="sidebar-label mt-3">TOOLS</div>
        <ul role="list">
            <li class="nav-item-custom"><a href="#" class="nav-link-custom" onclick="exportTableCSV('tblCategoryReport','GPCMS_Category.csv'); return false;"><span class="nav-icon"><i class="bi bi-download"></i></span><span class="nav-text">Export CSV</span></a></li>
            <li class="nav-item-custom"><a href="#" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-bell-fill"></i></span><span class="nav-text">Notifications</span></a></li>
            <li class="nav-item-custom"><a href="#" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-gear-fill"></i></span><span class="nav-text">Settings</span></a></li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <a href="../logout.php" class="nav-link-custom nav-link-logout"><span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span><span class="nav-text">Logout</span></a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="app-main" id="appMain" role="main">
<div class="page-content">
<div class="content-main">

    <!-- PAGE HEADER -->
    <div class="page-header anim-fadeInUp">
        <div>
            <h1 class="page-title"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary-gp"></i>Category-wise Complaint Report</h1>
            <div class="page-subtitle">Detailed breakdown of grievances across all administrative categories</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-primary ripple-btn" onclick="exportTableCSV('tblCategoryReport','GPCMS_Category_Report.csv')"><i class="bi bi-filetype-csv me-1"></i>Export CSV</button>
            <button class="btn btn-outline-primary ripple-btn" onclick="exportTableExcel('tblCategoryReport','GPCMS_Category_Report.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            <button class="btn btn-surface ripple-btn" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
    </div>

    <!-- KPI SUMMARY CARDS -->
    <div class="kpi-grid anim-fadeInUp anim-delay-1" style="grid-template-columns: repeat(auto-fill, minmax(200px,1fr));">
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-tags-fill"></i></div></div>
            <div class="kpi-label">Total Categories</div>
            <div class="kpi-value" data-counter="<?= $totalCats ?>">0</div>
            <div class="kpi-sub">Active issue types</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c2"><i class="bi bi-folder2-open"></i></div></div>
            <div class="kpi-label">Total Complaints</div>
            <div class="kpi-value" data-counter="<?= $grandTotal ?>">0</div>
            <div class="kpi-sub">Across all categories</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-check-circle-fill"></i></div></div>
            <div class="kpi-label">Total Resolved</div>
            <div class="kpi-value" data-counter="<?= $grandResolved ?>">0</div>
            <div class="kpi-sub">Successfully closed</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c3"><i class="bi bi-exclamation-triangle-fill"></i></div></div>
            <div class="kpi-label">Total Pending</div>
            <div class="kpi-value" data-counter="<?= $grandPending ?>">0</div>
            <div class="kpi-sub">Awaiting resolution</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c2"><i class="bi bi-percent"></i></div></div>
            <div class="kpi-label">Overall Resolution Rate</div>
            <div class="kpi-value" data-counter="<?= $grandTotal > 0 ? round(($grandResolved/$grandTotal)*100) : 0 ?>" data-suffix="%">0%</div>
            <div class="kpi-sub">Across all categories</div>
        </div>
    </div>

    <!-- CHART ROW: Doughnut + Horizontal Bar -->
    <div class="row g-4 mb-4 anim-fadeInUp anim-delay-2">
        <div class="col-12 col-lg-5">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-donut"></i>Category Volume Share</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:300px;"><canvas id="chartCategoryDoughnut"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-horizontal-fill"></i>Category Resolution Rate</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:300px;"><canvas id="chartHorizontalBar"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CATEGORY TABLE -->
    <div class="section-card mb-4 anim-fadeInUp anim-delay-3">
        <div class="section-card-header">
            <div class="section-card-title"><i class="bi bi-table"></i>Category Grievance Summary</div>
            <div class="section-card-actions">
                <button class="btn btn-sm btn-surface" onclick="exportTableCSV('tblCategoryReport','GPCMS_Category.csv')"><i class="bi bi-filetype-csv me-1"></i>CSV</button>
                <button class="btn btn-sm btn-surface" onclick="exportTableExcel('tblCategoryReport','GPCMS_Category.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            </div>
        </div>
        <div class="section-card-body pb-0">
            <div class="table-toolbar">
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Search category..." data-table-search="tblCategoryReport">
                </div>
                <div class="text-muted fs-xs"><?= $totalCats ?> categories</div>
            </div>
            <div class="data-table-wrap">
                <table class="data-table" id="tblCategoryReport" data-paginate="10">
                    <thead>
                        <tr>
                            <th data-sort>#</th>
                            <th data-sort>Category Name</th>
                            <th data-sort>Total</th>
                            <th data-sort>Pending</th>
                            <th data-sort>Assigned</th>
                            <th data-sort>In Progress</th>
                            <th data-sort>Resolved</th>
                            <th data-sort>Resolution %</th>
                            <th>Visual Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catRows as $i => $cat): ?>
                        <?php
                        $tot = (int)$cat['total'];
                        $res = (int)$cat['resolved'];
                        $pct = $tot > 0 ? round(($res/$tot)*100, 1) : 0;
                        ?>
                        <tr>
                            <td class="fw-700 text-muted-gp"><?= $i+1 ?></td>
                            <td class="fw-600"><?= htmlspecialchars($cat['category_name']) ?></td>
                            <td class="fw-700"><?= $tot ?></td>
                            <td><span class="badge-status badge-pending"><?= (int)$cat['pending'] ?></span></td>
                            <td><span class="badge-status badge-assigned"><?= (int)$cat['assigned'] ?></span></td>
                            <td><span class="badge-status badge-in_progress"><?= (int)$cat['in_progress'] ?></span></td>
                            <td><span class="badge-status badge-resolved"><?= $res ?></span></td>
                            <td class="fw-700 text-primary-gp"><?= $pct ?>%</td>
                            <td style="min-width:120px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-custom flex-grow-1" style="height:8px;">
                                        <div class="progress-bar-custom" data-progress="<?= $pct ?>"></div>
                                    </div>
                                    <span class="fs-xs fw-600"><?= $pct ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($catRows)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted-gp">No category data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-pagination">
            <span class="pager-info text-muted fs-xs">Loading…</span>
            <div class="pagination-btns"></div>
        </div>
    </div>

</div>
</div>

<footer class="app-footer">
    <div><strong style="color:var(--gp-primary);">GPCMS Analytics</strong> &nbsp;|&nbsp; Category Report Module &nbsp;|&nbsp; v2.0</div>
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Support</a>
    </div>
    <div class="text-muted-gp fs-xs">&copy; <?= date('Y') ?> Digital India Initiative</div>
</footer>
</main>

<button class="fab ripple-btn" id="fabBtn" title="Quick Actions"><i class="bi bi-plus-lg"></i></button>
<div class="fab-menu" id="fabMenu">
    <button class="fab-menu-btn" onclick="window.printPage()"><div class="fab-menu-icon"><i class="bi bi-printer-fill"></i></div>Print Report</button>
    <button class="fab-menu-btn" onclick="exportTableCSV('tblCategoryReport','GPCMS_Category.csv')"><div class="fab-menu-icon"><i class="bi bi-download"></i></div>Export CSV</button>
    <button class="fab-menu-btn" onclick="location.href='analytics_dashboard.php'"><div class="fab-menu-icon"><i class="bi bi-speedometer2"></i></div>Dashboard</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/analytics.js?v=<?= time() ?>"></script>
</body>
</html>
