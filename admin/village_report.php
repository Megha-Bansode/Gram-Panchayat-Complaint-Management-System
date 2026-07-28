<?php
/**
 * admin/village_report.php
 * GPCMS — Village-wise Complaint Analytics Report
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

$conn = get_db_connection();

// Village stats
$villageRows = [];
$res = $conn->query("
    SELECT village_ward,
           COUNT(*) AS total,
           SUM(CASE WHEN status='resolved'    THEN 1 ELSE 0 END) AS resolved,
           SUM(CASE WHEN status='pending'     THEN 1 ELSE 0 END) AS pending,
           SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress,
           SUM(CASE WHEN status='assigned'    THEN 1 ELSE 0 END) AS assigned
    FROM complaints
    WHERE village_ward IS NOT NULL AND village_ward != ''
    GROUP BY village_ward
    ORDER BY total DESC");
if ($res) while ($row = $res->fetch_assoc()) $villageRows[] = $row;

$totalVillages  = count($villageRows);
$grandTotal     = array_sum(array_column($villageRows, 'total'));
$grandResolved  = array_sum(array_column($villageRows, 'resolved'));
$grandPending   = array_sum(array_column($villageRows, 'pending'));
$topVillage     = $villageRows[0]['village_ward'] ?? '—';
$lowestVillage  = !empty($villageRows) ? $villageRows[count($villageRows)-1]['village_ward'] : '—';

$adminName = htmlspecialchars($_SESSION['full_name'] ?? 'Panchayat Admin');
$adminInitial = strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="GPCMS Village-wise Complaint Analytics Report">
<title>Village Analytics — GPCMS Enterprise</title>
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
            <span class="current">Village Report</span>
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
            <li class="nav-item-custom"><a href="village_report.php" class="nav-link-custom active" aria-current="page"><span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span><span class="nav-text">Village Reports</span></a></li>
            <li class="nav-item-custom"><a href="pending_resolved_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span><span class="nav-text">Resolution Status</span></a></li>
        </ul>
        <div class="sidebar-label mt-3">TOOLS</div>
        <ul role="list">
            <li class="nav-item-custom"><a href="#" class="nav-link-custom" onclick="exportTableCSV('tblVillageReport','GPCMS_Village.csv'); return false;"><span class="nav-icon"><i class="bi bi-download"></i></span><span class="nav-text">Export CSV</span></a></li>
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
            <h1 class="page-title"><i class="bi bi-geo-alt-fill me-2 text-primary-gp"></i>Village-wise Complaint Report</h1>
            <div class="page-subtitle">Demographic analysis of complaint density across Gram Panchayat wards</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-primary ripple-btn" onclick="exportTableCSV('tblVillageReport','GPCMS_Village_Report.csv')"><i class="bi bi-filetype-csv me-1"></i>Export CSV</button>
            <button class="btn btn-outline-primary ripple-btn" onclick="exportTableExcel('tblVillageReport','GPCMS_Village_Report.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            <button class="btn btn-surface ripple-btn" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-grid anim-fadeInUp anim-delay-1">
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-houses-fill"></i></div></div>
            <div class="kpi-label">Villages Covered</div>
            <div class="kpi-value" data-counter="<?= $totalVillages ?>">0</div>
            <div class="kpi-sub">Active GP wards</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c2"><i class="bi bi-folder2-open"></i></div></div>
            <div class="kpi-label">Total Complaints</div>
            <div class="kpi-value" data-counter="<?= $grandTotal ?>">0</div>
            <div class="kpi-sub">Across all wards</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c1"><i class="bi bi-trophy-fill"></i></div></div>
            <div class="kpi-label">Top Village</div>
            <div class="kpi-value" style="font-size:1.1rem;"><?= htmlspecialchars($topVillage) ?></div>
            <div class="kpi-sub">Highest complaint volume</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c4"><i class="bi bi-check-circle-fill"></i></div></div>
            <div class="kpi-label">Total Resolved</div>
            <div class="kpi-value" data-counter="<?= $grandResolved ?>">0</div>
            <div class="kpi-sub">Across all villages</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top"><div class="kpi-icon c3"><i class="bi bi-exclamation-triangle-fill"></i></div></div>
            <div class="kpi-label">Total Pending</div>
            <div class="kpi-value" data-counter="<?= $grandPending ?>">0</div>
            <div class="kpi-sub">Needs attention</div>
        </div>
    </div>

    <!-- CHART ROW -->
    <div class="row g-4 mb-4 anim-fadeInUp anim-delay-2">
        <div class="col-12 col-lg-7">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-fill"></i>Village Comparison Chart</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:300px;"><canvas id="chartVillageBar"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-diagram-3-fill"></i>Performance Radar</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:300px;"><canvas id="chartRadar"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- VILLAGE TABLE WITH RANKING -->
    <div class="section-card mb-4 anim-fadeInUp anim-delay-3">
        <div class="section-card-header">
            <div class="section-card-title"><i class="bi bi-table"></i>Village Performance Scorecard</div>
            <div class="section-card-actions">
                <button class="btn btn-sm btn-surface" onclick="exportTableCSV('tblVillageReport','GPCMS_Village.csv')"><i class="bi bi-filetype-csv me-1"></i>CSV</button>
                <button class="btn btn-sm btn-surface" onclick="exportTableExcel('tblVillageReport','GPCMS_Village.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            </div>
        </div>
        <div class="section-card-body pb-0">
            <div class="table-toolbar">
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Search village..." data-table-search="tblVillageReport">
                </div>
                <div class="text-muted fs-xs"><?= $totalVillages ?> villages</div>
            </div>
            <div class="data-table-wrap">
                <table class="data-table" id="tblVillageReport" data-paginate="10">
                    <thead>
                        <tr>
                            <th data-sort>Rank</th>
                            <th data-sort>Village / Ward</th>
                            <th data-sort>Total</th>
                            <th data-sort>Pending</th>
                            <th data-sort>Assigned</th>
                            <th data-sort>In Progress</th>
                            <th data-sort>Resolved</th>
                            <th data-sort>Resolution %</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($villageRows as $i => $vRow): ?>
                        <?php
                        $tot = (int)$vRow['total'];
                        $res = (int)$vRow['resolved'];
                        $pct = $tot > 0 ? round(($res/$tot)*100, 1) : 0;
                        $perf = $pct >= 80 ? 'resolved' : ($pct >= 50 ? 'in_progress' : 'pending');
                        $perfLabel = $pct >= 80 ? 'High Performer' : ($pct >= 50 ? 'Moderate' : 'Needs Focus');
                        ?>
                        <tr>
                            <td><span class="stat-rank d-inline-flex"><?= $i+1 ?></span></td>
                            <td class="fw-600"><i class="bi bi-geo-alt text-primary-gp me-1"></i><?= htmlspecialchars($vRow['village_ward']) ?></td>
                            <td class="fw-700"><?= $tot ?></td>
                            <td><span class="badge-status badge-pending"><?= (int)$vRow['pending'] ?></span></td>
                            <td><span class="badge-status badge-assigned"><?= (int)$vRow['assigned'] ?></span></td>
                            <td><span class="badge-status badge-in_progress"><?= (int)$vRow['in_progress'] ?></span></td>
                            <td><span class="badge-status badge-resolved"><?= $res ?></span></td>
                            <td class="fw-700 text-primary-gp"><?= $pct ?>%</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-custom flex-grow-1" style="height:8px; min-width:80px;">
                                        <div class="progress-bar-custom" data-progress="<?= $pct ?>"></div>
                                    </div>
                                    <span class="badge-status badge-<?= $perf ?>"><?= $perfLabel ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($villageRows)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted-gp">No village complaint data found.</td></tr>
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
    <div><strong style="color:var(--gp-primary);">GPCMS Analytics</strong> &nbsp;|&nbsp; Village Report Module &nbsp;|&nbsp; v2.0</div>
    <div class="footer-links"><a href="#">Privacy Policy</a><a href="#">Support</a></div>
    <div class="text-muted-gp fs-xs">&copy; <?= date('Y') ?> Digital India Initiative</div>
</footer>
</main>

<button class="fab ripple-btn" id="fabBtn" title="Quick Actions"><i class="bi bi-plus-lg"></i></button>
<div class="fab-menu" id="fabMenu">
    <button class="fab-menu-btn" onclick="window.printPage()"><div class="fab-menu-icon"><i class="bi bi-printer-fill"></i></div>Print Report</button>
    <button class="fab-menu-btn" onclick="exportTableCSV('tblVillageReport','GPCMS_Village.csv')"><div class="fab-menu-icon"><i class="bi bi-download"></i></div>Export CSV</button>
    <button class="fab-menu-btn" onclick="location.href='analytics_dashboard.php'"><div class="fab-menu-icon"><i class="bi bi-speedometer2"></i></div>Dashboard</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/analytics.js?v=<?= time() ?>"></script>
</body>
</html>
