<?php
/**
 * admin/analytics_dashboard.php
 * GPCMS — Premium Analytics Dashboard
 * Main dashboard page with all analytics sections
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

$conn = get_db_connection();

// --- KPI Queries ---
$kpi = [];
$kpi['total']       = (int)($conn->query("SELECT COUNT(*) c FROM complaints")?->fetch_assoc()['c'] ?? 0);
$kpi['pending']     = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='pending'")?->fetch_assoc()['c'] ?? 0);
$kpi['assigned']    = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='assigned'")?->fetch_assoc()['c'] ?? 0);
$kpi['in_progress'] = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='in_progress'")?->fetch_assoc()['c'] ?? 0);
$kpi['resolved']    = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='resolved'")?->fetch_assoc()['c'] ?? 0);
$kpi['today']       = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE DATE(submitted_at)=CURDATE()")?->fetch_assoc()['c'] ?? 0);
$kpi['weekly']      = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE submitted_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)")?->fetch_assoc()['c'] ?? 0);
$kpi['monthly']     = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE MONTH(submitted_at)=MONTH(CURRENT_DATE()) AND YEAR(submitted_at)=YEAR(CURRENT_DATE())")?->fetch_assoc()['c'] ?? 0);
$kpi['yearly']      = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE YEAR(submitted_at)=YEAR(CURRENT_DATE())")?->fetch_assoc()['c'] ?? 0);
$kpi['categories']  = (int)($conn->query("SELECT COUNT(*) c FROM categories")?->fetch_assoc()['c'] ?? 0);
$kpi['villages']    = (int)($conn->query("SELECT COUNT(DISTINCT village_ward) c FROM complaints WHERE village_ward IS NOT NULL AND village_ward != ''")?->fetch_assoc()['c'] ?? 0);

// Average Resolution Time Calculation
$resTimeQuery = $conn->query("SELECT AVG(TIMESTAMPDIFF(SECOND, submitted_at, updated_at)) AS avg_time FROM complaints WHERE status = 'resolved'");
$avg_seconds = $resTimeQuery ? $resTimeQuery->fetch_assoc()['avg_time'] : null;
$kpi['avg_res_time_val'] = null;
$kpi['avg_res_time_suffix'] = '';
if ($avg_seconds !== null) {
    $avg_days = $avg_seconds / 86400;
    if ($avg_days < 1) {
        $hours = (int)round($avg_seconds / 3600);
        $kpi['avg_res_time_val'] = $hours;
        $kpi['avg_res_time_suffix'] = ' hours';
    } else {
        $days = round($avg_days, 1);
        $kpi['avg_res_time_val'] = $days;
        $kpi['avg_res_time_suffix'] = ' days';
    }
}

// Citizen Satisfaction Calculation
$satisfactionQuery = $conn->query("SELECT AVG(rating) as avg_rating FROM feedback");
$avg_rating = $satisfactionQuery ? $satisfactionQuery->fetch_assoc()['avg_rating'] : null;
$kpi['satisfaction_val'] = null;
if ($avg_rating !== null) {
    $kpi['satisfaction_val'] = (int)round(($avg_rating / 5) * 100);
}

// --- AI Insights Data from Database ---
// 1. Top high-load category+village combination
$aiHighLoad = $conn->query("
    SELECT cat.category_name, c.village_ward, COUNT(*) as cnt
    FROM complaints c
    JOIN categories cat ON c.category_id = cat.category_id
    GROUP BY c.category_id, c.village_ward
    ORDER BY cnt DESC
    LIMIT 1
");
$aiHighLoadRow = $aiHighLoad ? $aiHighLoad->fetch_assoc() : null;
$aiHighLoadText = $aiHighLoadRow
    ? htmlspecialchars($aiHighLoadRow['category_name']) . ' — ' . htmlspecialchars($aiHighLoadRow['village_ward']) . ' Ward'
    : 'No data available';

// 2. Resolution velocity score (resolved / total) mapped to 5-point scale
$aiResRate = $kpi['total'] > 0 ? round(($kpi['resolved'] / $kpi['total']) * 5, 1) : 0;
$aiResRateLabel = $aiResRate >= 4 ? 'Optimal' : ($aiResRate >= 3 ? 'Good' : ($aiResRate >= 2 ? 'Average' : 'Needs Improvement'));
$aiAvgResDays = $kpi['avg_res_time_val'] !== null ? $kpi['avg_res_time_val'] . $kpi['avg_res_time_suffix'] : 'N/A';

// 3. Highest backlog category recommendation
$aiBacklog = $conn->query("
    SELECT cat.category_name, COUNT(*) as pending_cnt
    FROM complaints c
    JOIN categories cat ON c.category_id = cat.category_id
    WHERE c.status IN ('pending','assigned')
    GROUP BY c.category_id
    ORDER BY pending_cnt DESC
    LIMIT 1
");
$aiBacklogRow = $aiBacklog ? $aiBacklog->fetch_assoc() : null;
$aiBacklogText = $aiBacklogRow
    ? 'Prioritize ' . htmlspecialchars($aiBacklogRow['category_name']) . ' (' . $aiBacklogRow['pending_cnt'] . ' pending)'
    : 'No backlog detected';

// --- Dynamic Chart Datasets Queries ---

// 1. Complaint Trend (12 Months)
$trend = [];
for ($i = 11; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i months"));
    $trend[$m] = ['label' => date('M', strtotime("-$i months")), 'total' => 0, 'resolved' => 0];
}
$resTrend = $conn->query("
    SELECT DATE_FORMAT(submitted_at, '%Y-%m') as ym,
           COUNT(*) as total,
           SUM(CASE WHEN status='resolved' THEN 1 ELSE 0 END) as resolved
    FROM complaints
    WHERE submitted_at >= DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL 11 MONTH)
    GROUP BY ym");
if ($resTrend) while ($row = $resTrend->fetch_assoc()) {
    if (isset($trend[$row['ym']])) {
        $trend[$row['ym']]['total'] = (int)$row['total'];
        $trend[$row['ym']]['resolved'] = (int)$row['resolved'];
    }
}
$trendLabels = array_column($trend, 'label');
$trendTotal = array_column($trend, 'total');
$trendResolved = array_column($trend, 'resolved');

// 2. Category Distribution
$catDist = [];
$resCat = $conn->query("
    SELECT cat.category_name, COUNT(c.complaint_id) as total
    FROM categories cat
    LEFT JOIN complaints c ON cat.category_id = c.category_id
    GROUP BY cat.category_id
    ORDER BY total DESC");
if ($resCat) while ($row = $resCat->fetch_assoc()) {
    $catDist[] = $row;
}
$catLabels = array_column($catDist, 'category_name');
$catValues = array_map('intval', array_column($catDist, 'total'));

// 3. Village Comparison
$villDist = [];
$resVill = $conn->query("
    SELECT village_ward, COUNT(*) as total
    FROM complaints
    WHERE village_ward IS NOT NULL AND village_ward != ''
    GROUP BY village_ward
    ORDER BY total DESC
    LIMIT 10");
if ($resVill) while ($row = $resVill->fetch_assoc()) {
    $villDist[] = $row;
}
$villLabels = array_column($villDist, 'village_ward');
$villValues = array_map('intval', array_column($villDist, 'total'));

// 4. Weekly Activity (Last 7 Days)
$weekly = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $weekly[$d] = ['label' => date('D', strtotime("-$i days")), 'total' => 0];
}
$resWeekly = $conn->query("
    SELECT DATE(submitted_at) as d, COUNT(*) as total
    FROM complaints
    WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY d");
if ($resWeekly) while ($row = $resWeekly->fetch_assoc()) {
    if (isset($weekly[$row['d']])) {
        $weekly[$row['d']]['total'] = (int)$row['total'];
    }
}
$weeklyLabels = array_column($weekly, 'label');
$weeklyValues = array_column($weekly, 'total');

// 5. Monthly Status Stacked (Last 6 Months)
$stacked = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i months"));
    $stacked[$m] = [
        'label' => date('M', strtotime("-$i months")),
        'pending' => 0, 'assigned' => 0, 'in_progress' => 0, 'resolved' => 0
    ];
}
$resStacked = $conn->query("
    SELECT DATE_FORMAT(submitted_at, '%Y-%m') as ym,
           status, COUNT(*) as count
    FROM complaints
    WHERE submitted_at >= DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL 5 MONTH)
    GROUP BY ym, status");
if ($resStacked) while ($row = $resStacked->fetch_assoc()) {
    if (isset($stacked[$row['ym']])) {
        $st = strtolower($row['status']);
        if ($st === 'in_progress') $st = 'in_progress';
        if (isset($stacked[$row['ym']][$st])) {
            $stacked[$row['ym']][$st] = (int)$row['count'];
        }
    }
}
$stackedLabels = array_column($stacked, 'label');
$stackedPending = array_column($stacked, 'pending');
$stackedAssigned = array_column($stacked, 'assigned');
$stackedProgress = array_column($stacked, 'in_progress');
$stackedResolved = array_column($stacked, 'resolved');

// 6. Resolution Timeline (Last 8 Weeks)
$resTimeline = [];
$resTimeL = $conn->query("
    SELECT YEARWEEK(submitted_at, 3) as ywk,
           AVG(TIMESTAMPDIFF(SECOND, submitted_at, updated_at)) as avg_time
    FROM complaints
    WHERE status='resolved' AND submitted_at >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
    GROUP BY ywk
    ORDER BY ywk DESC
    LIMIT 8");
if ($resTimeL) while ($row = $resTimeL->fetch_assoc()) {
    $resTimeline[] = [
        'label' => 'Wk ' . substr((string)$row['ywk'], 4),
        'val' => round((float)$row['avg_time'] / 86400, 1)
    ];
}
$resTimeline = array_reverse($resTimeline);
if (empty($resTimeline)) {
    $resTimeline = [
        ['label' => 'Wk 1', 'val' => 0], ['label' => 'Wk 2', 'val' => 0], ['label' => 'Wk 3', 'val' => 0], ['label' => 'Wk 4', 'val' => 0],
        ['label' => 'Wk 5', 'val' => 0], ['label' => 'Wk 6', 'val' => 0], ['label' => 'Wk 7', 'val' => 0], ['label' => 'Wk 8', 'val' => 0]
    ];
}
$timelineLabels = array_column($resTimeline, 'label');
$timelineValues = array_column($resTimeline, 'val');

// 7. Performance Radar
if (!function_exists('getRadarMetrics')) {
    function getRadarMetrics($conn, $monthOffset = 0) {
        $y = date('Y', strtotime("-$monthOffset month"));
        $m = date('m', strtotime("-$monthOffset month"));
        $total = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        if ($total === 0) return [0, 0, 0, 0, 0, 0];
        $resolved = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='resolved' AND MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        $resRate = round(($resolved / $total) * 100);
        $res = $conn->query("SELECT AVG(f.rating) as avg_rating FROM feedback f INNER JOIN complaints c ON f.complaint_id=c.complaint_id WHERE MONTH(c.submitted_at)='$m' AND YEAR(c.submitted_at)='$y'");
        $avg_rating = $res ? $res->fetch_assoc()['avg_rating'] : null;
        if ($avg_rating === null) {
            $res = $conn->query("SELECT AVG(rating) as avg_rating FROM feedback");
            $avg_rating = $res ? $res->fetch_assoc()['avg_rating'] : null;
        }
        $satisfaction = $avg_rating !== null ? round(($avg_rating / 5) * 100) : 0;
        $slaCount = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status='resolved' AND TIMESTAMPDIFF(DAY, submitted_at, updated_at) <= 3 AND MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        $sla = $resolved > 0 ? round(($slaCount / $resolved) * 100) : 0;
        $responseCount = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE status!='pending' AND TIMESTAMPDIFF(HOUR, submitted_at, updated_at) <= 24 AND MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        $respSpeed = round(($responseCount / $total) * 100);
        $totalCats = max(1, (int)($conn->query("SELECT COUNT(*) c FROM categories")?->fetch_assoc()['c'] ?? 1));
        $activeCats = (int)($conn->query("SELECT COUNT(DISTINCT category_id) c FROM complaints WHERE MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        $coverage = round(($activeCats / $totalCats) * 100);
        $docCount = (int)($conn->query("SELECT COUNT(*) c FROM complaints WHERE LENGTH(complaint_description) >= 15 AND MONTH(submitted_at)='$m' AND YEAR(submitted_at)='$y'")?->fetch_assoc()['c'] ?? 0);
        $documentation = round(($docCount / $total) * 100);
        return [(int)$respSpeed, (int)$resRate, (int)$satisfaction, (int)$coverage, (int)$documentation, (int)$sla];
    }
}
$thisMonthRadar = getRadarMetrics($conn, 0);
$lastMonthRadar = getRadarMetrics($conn, 1);

// 8. Category Resolution Rate
$catRes = [];
$resCatRes = $conn->query("
    SELECT cat.category_name,
           COUNT(c.complaint_id) as total,
           SUM(CASE WHEN c.status='resolved' THEN 1 ELSE 0 END) as resolved
    FROM categories cat
    LEFT JOIN complaints c ON cat.category_id = c.category_id
    GROUP BY cat.category_id");
if ($resCatRes) while ($row = $resCatRes->fetch_assoc()) {
    $total = (int)$row['total'];
    $resolved = (int)$row['resolved'];
    $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
    $catRes[] = ['name' => $row['category_name'], 'rate' => $rate];
}
$catResLabels = array_column($catRes, 'name');
$catResValues = array_column($catRes, 'rate');

// Pie chart data attributes
$pieAttr = sprintf(
    'data-pending="%d" data-assigned="%d" data-progress="%d" data-resolved="%d"',
    $kpi['pending'], $kpi['assigned'], $kpi['in_progress'], $kpi['resolved']
);

// Latest complaints
$latestComplaints = [];
$res = $conn->query("SELECT c.complaint_id, c.complaint_title, c.village_ward, c.status, c.submitted_at,
                            cat.category_name, u.full_name
                     FROM complaints c
                     LEFT JOIN categories cat ON c.category_id = cat.category_id
                     LEFT JOIN users u ON c.user_id = u.user_id
                     ORDER BY c.complaint_id DESC LIMIT 10");
if ($res) while ($row = $res->fetch_assoc()) $latestComplaints[] = $row;

// Top categories
$topCategories = [];
$res = $conn->query("SELECT cat.category_name, COUNT(c.complaint_id) AS total
                     FROM categories cat
                     LEFT JOIN complaints c ON cat.category_id = c.category_id
                     GROUP BY cat.category_id ORDER BY total DESC LIMIT 6");
if ($res) while ($row = $res->fetch_assoc()) $topCategories[] = $row;

// Top villages
$topVillages = [];
$res = $conn->query("SELECT village_ward, COUNT(*) AS total FROM complaints
                     WHERE village_ward IS NOT NULL AND village_ward != ''
                     GROUP BY village_ward ORDER BY total DESC LIMIT 5");
if ($res) while ($row = $res->fetch_assoc()) $topVillages[] = $row;

$adminName = htmlspecialchars($_SESSION['full_name'] ?? 'Panchayat Admin');
$adminInitial = strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="GPCMS Premium Analytics Dashboard - Gram Panchayat Complaint Monitoring">
<title>Analytics Dashboard — GPCMS Enterprise</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/analytics.css?v=<?= time() ?>" rel="stylesheet">

</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div id="sidebarOverlay" class="sidebar-overlay" style="display:none;" aria-hidden="true"></div>

<!-- ╔══════════════════════════════════╗
     ║            HEADER               ║
     ╚══════════════════════════════════╝ -->
<header class="app-header" role="banner">
    <button class="header-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <div class="header-brand">
        <div class="brand-icon"><i class="bi bi-bank2"></i></div>
        <div class="brand-name">
            GPCMS Analytics
            <small>Gram Panchayat Enterprise</small>
        </div>
    </div>

    <nav class="ms-4 d-none d-lg-block" aria-label="Breadcrumb">
        <div class="breadcrumb-custom">
            <a href="analytics_dashboard.php"><i class="bi bi-house-door me-1"></i>Home</a>
            <span class="sep"><i class="bi bi-chevron-right"></i></span>
            <span class="current">Analytics Dashboard</span>
        </div>
    </nav>

    <div class="header-search ms-3 d-none d-md-block" role="search">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="globalSearch" placeholder="Search complaints, villages, categories…" aria-label="Global search">
    </div>

    <div class="header-right">
        <div class="header-date-time d-none d-lg-flex">
            <span class="current-date" id="headerDate"></span>
            <span id="headerTime"></span>
        </div>

        <!-- Auto Refresh -->
        <div class="d-flex align-items-center gap-1 me-1" title="Auto-refresh every 30s">
            <small class="text-muted d-none d-xl-block" style="font-size:0.68rem;">Auto</small>
            <div class="form-check form-switch mb-0" style="transform:scale(0.8);">
                <input class="form-check-input" type="checkbox" id="autoRefreshToggle" style="cursor:pointer;">
            </div>
        </div>

        <button class="icon-btn" title="Notifications (Alt+N)" aria-label="Notifications" data-bs-toggle="modal" data-bs-target="#notificationsModal">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-badge" aria-label="3 notifications"></span>
        </button>
        <button class="icon-btn" title="Print Dashboard (Alt+P)" onclick="window.printPage()">
            <i class="bi bi-printer-fill"></i>
        </button>
        <div class="header-divider"></div>
        <div class="dropdown">
            <div class="profile-trigger" data-bs-toggle="dropdown" aria-expanded="false" role="button" aria-label="Profile menu">
                <div class="profile-avatar"><?= $adminInitial ?></div>
                <div class="profile-info d-none d-md-flex">
                    <span class="profile-name"><?= $adminName ?></span>
                    <span class="profile-role"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Administrator') ?></span>
                </div>
                <i class="bi bi-chevron-down ms-1" style="font-size:0.7rem; color:var(--gp-text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:180px; border-radius:12px; overflow:hidden;">
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="bi bi-person-circle text-primary-gp"></i> My Profile</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="bi bi-gear text-primary-gp"></i> Settings</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="../includes/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- ╔══════════════════════════════════╗
     ║            SIDEBAR              ║
     ╚══════════════════════════════════╝ -->
<nav class="app-sidebar" id="appSidebar" aria-label="Main navigation">
    <div class="sidebar-header">
        <div class="system-health">
            <span class="health-dot"></span>
            <span class="nav-text" style="font-size:0.72rem;">System Operational</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="sidebar-label">MAIN NAVIGATION</div>
        <ul role="list">
            <li class="nav-item-custom">
                <a href="analytics_dashboard.php" class="nav-link-custom active" aria-current="page">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="category_report.php" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                    <span class="nav-text">Category Reports</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="village_report.php" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="nav-text">Village Reports</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="pending_resolved_report.php" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span>
                    <span class="nav-text">Resolution Status</span>

                </a>
            </li>
        </ul>

        <div class="sidebar-label mt-3">TOOLS</div>
        <ul role="list">
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom" onclick="exportTableCSV('tblLatestComplaints','GPCMS_All_Complaints.csv'); return false;">
                    <span class="nav-icon"><i class="bi bi-download"></i></span>
                    <span class="nav-text">Export Center</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                    <span class="nav-icon"><i class="bi bi-bell-fill"></i></span>
                    <span class="nav-text">Notifications</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a href="../includes/logout.php" class="nav-link-custom nav-link-logout">
            <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</nav>

<!-- ╔══════════════════════════════════╗
     ║          MAIN CONTENT           ║
     ╚══════════════════════════════════╝ -->
<main class="app-main" id="appMain" role="main">
<div class="page-content">
<div class="content-main">

    <!-- WELCOME BANNER -->
    <div class="welcome-banner anim-fadeInUp">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="welcome-title"><i class="bi bi-shield-check me-2"></i>Welcome back, <?= $adminName ?>!</div>
                <div class="welcome-subtitle">Gram Panchayat Complaint Intelligence &amp; Monitoring System</div>
                <div class="welcome-meta mt-3">
                    <div class="welcome-meta-item"><i class="bi bi-calendar-event me-1"></i><?= date('l, d M Y') ?></div>
                    <div class="welcome-meta-item"><i class="bi bi-folder2-open me-1"></i><?= $kpi['total'] ?> Total Complaints</div>
                    <div class="welcome-meta-item"><i class="bi bi-check-circle me-1"></i><?= $kpi['today'] ?> Today</div>
                    <div class="system-health"><span class="health-dot"></span> All Systems Online</div>
                </div>
            </div>
            <div>
                <div class="welcome-actions">
                    <button class="btn btn-white ripple-btn" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</button>
                    <button class="btn btn-ghost-white ripple-btn" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
                    <button class="btn btn-ghost-white ripple-btn" onclick="exportTableCSV('tblLatestComplaints','GPCMS_Export.csv')"><i class="bi bi-download me-1"></i>Export CSV</button>
                </div>
                <div class="mt-2 text-white-50 fs-xs">Alt+D = Dashboard &nbsp;|&nbsp; Alt+P = Print &nbsp;|&nbsp; Alt+F = Search</div>
            </div>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-grid anim-fadeInUp anim-delay-1">
        <!-- Total Complaints -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c1"><i class="bi bi-folder-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+12%</div>
            </div>
            <div class="kpi-label">Total Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['total'] ?>">0</div>
            <div class="kpi-sub">Lifetime grievances registered</div>
        </div>
        <!-- Pending -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c2"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="kpi-trend"><i class="bi bi-clock-history"></i>Needs Action</div>
            </div>
            <div class="kpi-label">Pending</div>
            <div class="kpi-value" data-counter="<?= $kpi['pending'] ?>">0</div>
            <div class="kpi-sub">Awaiting assignment</div>
        </div>
        <!-- Resolved -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c1"><i class="bi bi-check-circle-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+8%</div>
            </div>
            <div class="kpi-label">Resolved</div>
            <div class="kpi-value" data-counter="<?= $kpi['resolved'] ?>">0</div>
            <div class="kpi-sub">Successfully closed</div>
        </div>
        <!-- In Progress -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c3"><i class="bi bi-arrow-repeat"></i></div>
                <div class="kpi-trend"><i class="bi bi-gear-fill"></i>Active</div>
            </div>
            <div class="kpi-label">In Progress</div>
            <div class="kpi-value" data-counter="<?= $kpi['in_progress'] ?>">0</div>
            <div class="kpi-sub">Currently being worked on</div>
        </div>
        <!-- Today -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c2"><i class="bi bi-brightness-high-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-plus"></i>New</div>
            </div>
            <div class="kpi-label">Today's Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['today'] ?>">0</div>
            <div class="kpi-sub">Submitted today</div>
        </div>
        <!-- Weekly -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c4"><i class="bi bi-calendar-week-fill"></i></div>
                <div class="kpi-trend"><i class="bi bi-bar-chart-fill"></i>7 Days</div>
            </div>
            <div class="kpi-label">Weekly Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['weekly'] ?>">0</div>
            <div class="kpi-sub">Last 7 days activity</div>
        </div>
        <!-- Monthly -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c1"><i class="bi bi-calendar-month-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+5%</div>
            </div>
            <div class="kpi-label">Monthly Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['monthly'] ?>">0</div>
            <div class="kpi-sub">Current month total</div>
        </div>
        <!-- Yearly -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c3"><i class="bi bi-calendar4-range"></i></div>
                <div class="kpi-trend"><i class="bi bi-graph-up-arrow"></i>Annual</div>
            </div>
            <div class="kpi-label">Yearly Complaints</div>
            <div class="kpi-value" data-counter="<?= $kpi['yearly'] ?>">0</div>
            <div class="kpi-sub">Year <?= date('Y') ?> total</div>
        </div>
        <!-- Categories -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c2"><i class="bi bi-tags-fill"></i></div>
                <div class="kpi-trend"><i class="bi bi-list-ul"></i>Types</div>
            </div>
            <div class="kpi-label">Complaint Categories</div>
            <div class="kpi-value" data-counter="<?= $kpi['categories'] ?>">0</div>
            <div class="kpi-sub">Active issue categories</div>
        </div>
        <!-- Villages -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c4"><i class="bi bi-houses-fill"></i></div>
                <div class="kpi-trend"><i class="bi bi-geo-alt-fill"></i>Wards</div>
            </div>
            <div class="kpi-label">Villages Covered</div>
            <div class="kpi-value" data-counter="<?= $kpi['villages'] ?>">0</div>
            <div class="kpi-sub">Active gram panchayat wards</div>
        </div>
        <!-- Avg Resolution Time -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c1"><i class="bi bi-stopwatch-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-arrow-down"></i>Improving</div>
            </div>
            <div class="kpi-label">Avg. Resolution Time</div>
            <?php if ($kpi['avg_res_time_val'] !== null): ?>
            <div class="kpi-value" data-counter="<?= $kpi['avg_res_time_val'] ?>" data-suffix="<?= htmlspecialchars($kpi['avg_res_time_suffix']) ?>">0<?= htmlspecialchars($kpi['avg_res_time_suffix']) ?></div>
            <?php else: ?>
            <div class="kpi-value">No Data</div>
            <?php endif; ?>
            <div class="kpi-sub">Average SLA compliance</div>
        </div>
        <!-- Citizen Satisfaction -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c2"><i class="bi bi-emoji-smile-fill"></i></div>
                <?php if ($kpi['satisfaction_val'] !== null): ?>
                <div class="kpi-trend <?= $kpi['satisfaction_val'] >= 70 ? 'up' : '' ?>"><i class="bi bi-graph-up-arrow"></i><?= $kpi['satisfaction_val'] >= 70 ? 'Good' : 'Needs Work' ?></div>
                <?php else: ?>
                <div class="kpi-trend"><i class="bi bi-dash"></i>N/A</div>
                <?php endif; ?>
            </div>
            <div class="kpi-label">Citizen Satisfaction</div>
            <?php if ($kpi['satisfaction_val'] !== null): ?>
            <div class="kpi-value" data-counter="<?= $kpi['satisfaction_val'] ?>" data-suffix="%">0%</div>
            <?php else: ?>
            <div class="kpi-value">No Data</div>
            <?php endif; ?>
            <div class="kpi-sub">Positive feedback score</div>
        </div>
    </div>

    <!-- AI INSIGHTS CARD -->
    <div class="ai-card mb-4 anim-fadeInUp anim-delay-2">
        <div class="ai-badge"><i class="bi bi-cpu-fill me-1"></i> AI Predictive Intelligence Engine</div>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">Highest Complaint Load</div>
                    <div class="ai-insight-value"><i class="bi bi-geo-alt-fill me-1"></i><?= $aiHighLoadText ?></div>
                    <div class="fs-xs text-muted-gp mt-1"><?= $aiHighLoadRow ? $aiHighLoadRow['cnt'] . ' complaints recorded' : '' ?></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">Resolution Velocity Score</div>
                    <div class="ai-insight-value"><i class="bi bi-speedometer me-1"></i><?= $aiResRate ?> / 5.0 — <?= $aiResRateLabel ?></div>
                    <div class="fs-xs text-muted-gp mt-1">Avg. resolution time: <?= $aiAvgResDays ?></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">System Recommendation</div>
                    <div class="ai-insight-value"><i class="bi bi-lightbulb-fill me-1"></i><?= $aiBacklogText ?></div>
                    <div class="fs-xs text-muted-gp mt-1"><?= $aiBacklogRow ? 'Focus resources to reduce backlog' : 'All categories within SLA' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="filter-panel anim-fadeInUp anim-delay-2">
        <div class="filter-panel-header">
            <div class="filter-panel-title"><i class="bi bi-funnel-fill text-primary-gp"></i> Advanced Filters</div>
            <div class="filter-actions">
                <button class="btn btn-sm btn-primary ripple-btn" type="submit" form="filterForm"><i class="bi bi-search me-1"></i>Apply</button>
                <a href="analytics_dashboard.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                <button class="btn btn-sm btn-surface" onclick="exportTableCSV('tblLatestComplaints','GPCMS_Filtered.csv')"><i class="bi bi-download me-1"></i>CSV</button>
                <button class="btn btn-sm btn-surface" onclick="exportTableExcel('tblLatestComplaints','GPCMS_Filtered.xls')"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel</button>
                <button class="btn btn-sm btn-surface" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
            </div>
        </div>
        <form id="filterForm" method="GET" action="analytics_dashboard.php">
            <div class="filter-row">
                <div>
                    <label class="form-label">Village / Ward</label>
                    <select name="village" class="form-select form-select-sm">
                        <option value="">All Villages</option>
                        <?php
                        $vres = $conn->query("SELECT DISTINCT village_ward FROM complaints WHERE village_ward IS NOT NULL AND village_ward != '' ORDER BY village_ward");
                        if ($vres) while ($v = $vres->fetch_assoc()) {
                            $sel = (($_GET['village'] ?? '') === $v['village_ward']) ? 'selected' : '';
                            echo "<option value=\"".htmlspecialchars($v['village_ward'])."\" {$sel}>".htmlspecialchars($v['village_ward'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <?php
                        $cres = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
                        if ($cres) while ($cat = $cres->fetch_assoc()) {
                            $sel = (($_GET['category'] ?? '') === $cat['category_name']) ? 'selected' : '';
                            echo "<option value=\"".htmlspecialchars($cat['category_name'])."\" {$sel}>".htmlspecialchars($cat['category_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <?php
                        $statuses = ['pending'=>'Pending','assigned'=>'Assigned','in_progress'=>'In Progress','resolved'=>'Resolved'];
                        foreach ($statuses as $v => $l) {
                            $sel = (($_GET['status'] ?? '') === $v) ? 'selected' : '';
                            echo "<option value=\"{$v}\" {$sel}>{$l}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['month'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
            </div>
        </form>
    </div>

    <!-- CHARTS — ROW 1 (Trend) -->
    <div id="sectionCharts" class="row g-4 mb-0">
        <div class="col-12">
            <div class="section-card anim-fadeInUp anim-delay-3">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-graph-up-arrow"></i>Complaint Trend (12 Months)</div>
                    <div class="section-card-actions">
                        <button class="btn btn-sm btn-surface" title="Download Chart"><i class="bi bi-download"></i></button>
                        <button class="btn btn-sm btn-surface" title="Fullscreen"><i class="bi bi-fullscreen"></i></button>
                    </div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartComplaintTrend" data-labels='<?= json_encode($trendLabels) ?>' data-total='<?= json_encode($trendTotal) ?>' data-resolved='<?= json_encode($trendResolved) ?>'></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS — ROW 2 (Category + Village) -->
    <div class="row g-4 mt-0 mb-0">
        <div class="col-12 col-lg-5">
            <div class="section-card anim-fadeInUp anim-delay-4">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-donut"></i>Category Distribution</div>
                    <a href="category_report.php" class="btn btn-sm btn-surface">View Full <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartCategoryDoughnut" data-labels='<?= json_encode($catLabels) ?>' data-values='<?= json_encode($catValues) ?>'></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="section-card anim-fadeInUp anim-delay-4">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-fill"></i>Village Comparison</div>
                    <a href="village_report.php" class="btn btn-sm btn-surface">Full Report <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartVillageBar" data-labels='<?= json_encode($villLabels) ?>' data-values='<?= json_encode($villValues) ?>'></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS — ROW 3 (Weekly + Stacked + Resolution) -->
    <div class="row g-4 mt-0 mb-0">
        <div class="col-12 col-md-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-calendar-week"></i>Weekly Activity</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartWeeklyActivity" data-labels='<?= json_encode($weeklyLabels) ?>' data-values='<?= json_encode($weeklyValues) ?>'></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-steps"></i>Monthly Stacked View</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartStackedBar" data-labels='<?= json_encode($stackedLabels) ?>' data-pending='<?= json_encode($stackedPending) ?>' data-assigned='<?= json_encode($stackedAssigned) ?>' data-progress='<?= json_encode($stackedProgress) ?>' data-resolved='<?= json_encode($stackedResolved) ?>'></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-stopwatch"></i>Resolution Timeline</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartResolutionTimeline" data-labels='<?= json_encode($timelineLabels) ?>' data-values='<?= json_encode($timelineValues) ?>'></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS — ROW 4 (Radar + Horizontal) -->
    <div class="row g-4 mt-0 mb-4">
        <div class="col-12 col-md-5">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-diagram-3-fill"></i>Performance Radar</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartRadar" data-this-month='<?= json_encode($thisMonthRadar) ?>' data-last-month='<?= json_encode($lastMonthRadar) ?>'></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-7">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-horizontal-fill"></i>Category Resolution Rate</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartHorizontalBar" data-labels='<?= json_encode($catResLabels) ?>' data-values='<?= json_encode($catResValues) ?>'></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- LATEST COMPLAINTS TABLE -->
    <div class="section-card mb-4" id="sectionTable">
        <div class="section-card-header">
            <div class="section-card-title"><i class="bi bi-table"></i>Latest Grievance Records</div>
            <div class="section-card-actions">
                <button class="btn btn-sm btn-surface" onclick="exportTableCSV('tblLatestComplaints','GPCMS_Complaints.csv')"><i class="bi bi-filetype-csv me-1"></i>CSV</button>
                <button class="btn btn-sm btn-surface" onclick="exportTableExcel('tblLatestComplaints','GPCMS_Complaints.xls')"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
                <button class="btn btn-sm btn-surface" onclick="window.printPage()"><i class="bi bi-printer me-1"></i>Print</button>
            </div>
        </div>
        <div class="section-card-body pb-0">
            <div class="table-toolbar">
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Search table..." data-table-search="tblLatestComplaints">
                </div>
                <div class="text-muted fs-xs"><?= count($latestComplaints) ?> records</div>
            </div>
            <div class="data-table-wrap">
                <table class="data-table" id="tblLatestComplaints" data-paginate="8">
                    <thead>
                        <tr>
                            <th data-sort>Complaint ID</th>
                            <th data-sort>Complainant</th>
                            <th data-sort>Village / Ward</th>
                            <th data-sort>Category</th>
                            <th data-sort>Status</th>
                            <th data-sort>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($latestComplaints) > 0): ?>
                            <?php foreach ($latestComplaints as $row): ?>
                            <tr>
                                <td><span class="fw-700">#<?= str_pad((string)$row['complaint_id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? $row['complaint_title'] ?? '—') ?></td>
                                <td><i class="bi bi-geo-alt text-primary-gp me-1"></i><?= htmlspecialchars($row['village_ward'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? 'General') ?></td>
                                <td><span class="badge-status badge-<?= htmlspecialchars(strtolower($row['status'])) ?>"><?= ucwords(str_replace('_',' ',$row['status'])) ?></span></td>
                                <td><?= !empty($row['submitted_at']) ? date('d M Y', strtotime($row['submitted_at'])) : '—' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-surface py-0 px-2" title="View"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted-gp">No grievance records found in the system.</td></tr>
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

</div><!-- /content-main -->


</div><!-- /page-content -->

<!-- FOOTER -->
<?php require_once __DIR__ . 'footer.php'; ?>
</main>

<!-- FAB Menu -->
<div class="fab-menu" id="fabMenu" aria-label="Quick actions">
    <button class="fab-menu-btn" onclick="window.printPage()">
        <div class="fab-menu-icon"><i class="bi bi-printer-fill"></i></div> Print Dashboard
    </button>
    <button class="fab-menu-btn" onclick="exportTableCSV('tblLatestComplaints','GPCMS_Export.csv')">
        <div class="fab-menu-icon"><i class="bi bi-download"></i></div> Export CSV
    </button>
    <button class="fab-menu-btn" onclick="location.href='pending_resolved_report.php'">
        <div class="fab-menu-icon"><i class="bi bi-pie-chart-fill"></i></div> Resolution Report
    </button>
</div>
<button class="fab ripple-btn" id="fabBtn" title="Quick Actions" aria-label="Open quick actions">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#8A724C,#A0875C); border:none; padding:1.25rem 1.5rem;">
                <h5 class="modal-title text-white fw-700" id="notificationsModalLabel"><i class="bi bi-bell-fill me-2"></i>Notifications</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#FAF7F0; padding:1.5rem;">
                <?php
                // Fetch recent complaint activities as notifications
                $notifItems = [];
                $notifRes = $conn->query("
                    SELECT c.complaint_id, c.complaint_title, c.status, c.submitted_at, c.updated_at,
                           u.full_name, cat.category_name
                    FROM complaints c
                    LEFT JOIN users u ON c.user_id = u.user_id
                    LEFT JOIN categories cat ON c.category_id = cat.category_id
                    ORDER BY c.updated_at DESC LIMIT 8
                ");
                if ($notifRes) while ($nr = $notifRes->fetch_assoc()) $notifItems[] = $nr;
                ?>
                <?php if (!empty($notifItems)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($notifItems as $ni):
                        $statusColors = ['pending'=>'#E8A849','assigned'=>'#5B9BD5','in_progress'=>'#8A724C','resolved'=>'#5CAF6E'];
                        $statusIcons = ['pending'=>'bi-clock','assigned'=>'bi-person-check','in_progress'=>'bi-arrow-repeat','resolved'=>'bi-check-circle'];
                        $st = strtolower($ni['status']);
                        $stColor = $statusColors[$st] ?? '#8A724C';
                        $stIcon = $statusIcons[$st] ?? 'bi-info-circle';
                        $timeAgo = '';
                        if ($ni['updated_at']) {
                            $diff = time() - strtotime($ni['updated_at']);
                            if ($diff < 60) $timeAgo = 'Just now';
                            elseif ($diff < 3600) $timeAgo = floor($diff/60) . 'm ago';
                            elseif ($diff < 86400) $timeAgo = floor($diff/3600) . 'h ago';
                            else $timeAgo = floor($diff/86400) . 'd ago';
                        }
                    ?>
                    <div class="d-flex align-items-start gap-3 p-3" style="background:#fff; border-radius:12px; border-left:4px solid <?= $stColor ?>;">
                        <div style="width:36px;height:36px;border-radius:10px;background:<?= $stColor ?>15;color:<?= $stColor ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi <?= $stIcon ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-600" style="font-size:0.85rem; color:#3D3225;"><?= htmlspecialchars($ni['complaint_title'] ?? 'Complaint #'.$ni['complaint_id']) ?></div>
                            <div style="font-size:0.75rem; color:#8A724C;">
                                <span class="badge" style="background:<?= $stColor ?>20;color:<?= $stColor ?>;font-size:0.68rem;padding:2px 8px;border-radius:6px;"><?= ucfirst(str_replace('_',' ',$st)) ?></span>
                                <?php if ($ni['category_name']): ?>
                                <span class="ms-1">• <?= htmlspecialchars($ni['category_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size:0.7rem; color:#A09080; margin-top:4px;"><i class="bi bi-clock me-1"></i><?= $timeAgo ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash" style="font-size:3rem; color:#D5C9B4;"></i>
                    <div class="mt-3 fw-600" style="color:#8A724C;">No notifications yet</div>
                    <div class="mt-1" style="font-size:0.82rem; color:#A09080;">Activity will appear here as complaints are updated.</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer" style="background:#FAF7F0; border-top:1px solid #E8E0D0; padding:0.75rem 1.5rem;">
                <button type="button" class="btn btn-sm btn-surface" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#8A724C,#A0875C); border:none; padding:1.25rem 1.5rem;">
                <h5 class="modal-title text-white fw-700" id="settingsModalLabel"><i class="bi bi-gear-fill me-2"></i>Dashboard Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#FAF7F0; padding:1.5rem;">
                <div class="d-flex flex-column gap-4">
                    <!-- Auto Refresh -->
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:#fff; border-radius:12px;">
                        <div>
                            <div class="fw-600" style="font-size:0.88rem; color:#3D3225;"><i class="bi bi-arrow-clockwise me-2 text-primary-gp"></i>Auto Refresh</div>
                            <div style="font-size:0.75rem; color:#A09080;">Automatically refresh dashboard data every 30 seconds</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="settingsAutoRefresh" style="cursor:pointer;width:3em;height:1.5em;">
                        </div>
                    </div>
                    <!-- Animations -->
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:#fff; border-radius:12px;">
                        <div>
                            <div class="fw-600" style="font-size:0.88rem; color:#3D3225;"><i class="bi bi-stars me-2 text-primary-gp"></i>Animations</div>
                            <div style="font-size:0.75rem; color:#A09080;">Enable smooth transitions and counter animations</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="settingsAnimations" checked style="cursor:pointer;width:3em;height:1.5em;">
                        </div>
                    </div>
                    <!-- Compact Mode -->
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:#fff; border-radius:12px;">
                        <div>
                            <div class="fw-600" style="font-size:0.88rem; color:#3D3225;"><i class="bi bi-layout-sidebar-inset me-2 text-primary-gp"></i>Compact Sidebar</div>
                            <div style="font-size:0.75rem; color:#A09080;">Collapse the sidebar to show icons only</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="settingsCompact" style="cursor:pointer;width:3em;height:1.5em;">
                        </div>
                    </div>
                    <!-- Table Rows per Page -->
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:#fff; border-radius:12px;">
                        <div>
                            <div class="fw-600" style="font-size:0.88rem; color:#3D3225;"><i class="bi bi-table me-2 text-primary-gp"></i>Rows per Page</div>
                            <div style="font-size:0.75rem; color:#A09080;">Number of rows shown in complaint tables</div>
                        </div>
                        <select class="form-select form-select-sm" id="settingsRowsPerPage" style="width:80px;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <!-- System Info -->
                    <div class="p-3" style="background:#fff; border-radius:12px;">
                        <div class="fw-600 mb-2" style="font-size:0.88rem; color:#3D3225;"><i class="bi bi-info-circle me-2 text-primary-gp"></i>System Information</div>
                        <div class="d-flex flex-column gap-1" style="font-size:0.78rem; color:#A09080;">
                            <div class="d-flex justify-content-between"><span>Version</span><span class="fw-600" style="color:#3D3225;">GPCMS v3.1</span></div>
                            <div class="d-flex justify-content-between"><span>PHP Version</span><span class="fw-600" style="color:#3D3225;"><?= phpversion() ?></span></div>
                            <div class="d-flex justify-content-between"><span>Database</span><span class="fw-600" style="color:#3D3225;">MySQL <?= $conn->server_info ?></span></div>
                            <div class="d-flex justify-content-between"><span>Server</span><span class="fw-600" style="color:#3D3225;"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Apache') ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#FAF7F0; border-top:1px solid #E8E0D0; padding:0.75rem 1.5rem;">
                <button type="button" class="btn btn-sm btn-surface" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary ripple-btn" onclick="applySettings()" data-bs-dismiss="modal"><i class="bi bi-check-lg me-1"></i>Apply</button>
            </div>
        </div>
    </div>
</div>

<script>
// Settings apply handler
function applySettings() {
    const autoRefresh = document.getElementById('settingsAutoRefresh')?.checked;
    const headerToggle = document.getElementById('autoRefreshToggle');
    if (headerToggle && autoRefresh !== undefined) headerToggle.checked = autoRefresh;

    const compact = document.getElementById('settingsCompact')?.checked;
    const sidebar = document.getElementById('appSidebar');
    if (sidebar) sidebar.classList.toggle('collapsed', !!compact);

    const animations = document.getElementById('settingsAnimations')?.checked;
    document.body.classList.toggle('no-animations', !animations);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/analytics.js?v=<?= time() ?>"></script>
</body>
</html>
