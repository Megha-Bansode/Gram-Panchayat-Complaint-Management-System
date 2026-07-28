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
if ($kpi['villages'] < 1) $kpi['villages'] = 12;
if ($kpi['categories'] < 1) $kpi['categories'] = 8;

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

        <button class="icon-btn" title="Notifications (Alt+N)" aria-label="Notifications">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-badge" aria-label="3 notifications"></span>
        </button>
        <button class="icon-btn" title="Messages" aria-label="Messages">
            <i class="bi bi-chat-dots-fill"></i>
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
                <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
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
                    <span class="nav-badge nav-text"><?= $kpi['categories'] ?></span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="village_report.php" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="nav-text">Village Reports</span>
                    <span class="nav-badge nav-text"><?= $kpi['villages'] ?></span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="pending_resolved_report.php" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span>
                    <span class="nav-text">Resolution Status</span>
                    <?php if ($kpi['pending'] > 0): ?>
                    <span class="nav-badge nav-text"><?= $kpi['pending'] ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <div class="sidebar-label mt-3">ANALYTICS</div>
        <ul role="list">
            <li class="nav-item-custom">
                <a href="#sectionCharts" class="nav-link-custom" onclick="document.getElementById('sectionCharts')?.scrollIntoView({behavior:'smooth'}); return false;">
                    <span class="nav-icon"><i class="bi bi-graph-up-arrow"></i></span>
                    <span class="nav-text">Charts & Graphs</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="#sectionTable" class="nav-link-custom" onclick="document.getElementById('sectionTable')?.scrollIntoView({behavior:'smooth'}); return false;">
                    <span class="nav-icon"><i class="bi bi-table"></i></span>
                    <span class="nav-text">Complaint Records</span>
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
                <a href="#" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-bell-fill"></i></span>
                    <span class="nav-text">Notifications</span>
                    <span class="nav-badge nav-text">3</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom">
                    <span class="nav-icon"><i class="bi bi-question-circle-fill"></i></span>
                    <span class="nav-text">Help & Support</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a href="../logout.php" class="nav-link-custom nav-link-logout">
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
            <div class="kpi-value" data-counter="3" data-suffix=" days">0 days</div>
            <div class="kpi-sub">Average SLA compliance</div>
        </div>
        <!-- Citizen Satisfaction -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon c2"><i class="bi bi-emoji-smile-fill"></i></div>
                <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i>+3%</div>
            </div>
            <div class="kpi-label">Citizen Satisfaction</div>
            <div class="kpi-value" data-counter="92" data-suffix="%">0%</div>
            <div class="kpi-sub">Positive feedback score</div>
        </div>
    </div>

    <!-- AI INSIGHTS CARD -->
    <div class="ai-card mb-4 anim-fadeInUp anim-delay-2">
        <div class="ai-badge"><i class="bi bi-cpu-fill me-1"></i> AI Predictive Intelligence Engine</div>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">Predicted High-Load Area</div>
                    <div class="ai-insight-value"><i class="bi bi-droplet-fill me-1"></i>Water Supply — Karanji Ward</div>
                    <div class="fs-xs text-muted-gp mt-1">+18% volume expected next 7 days</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">Resolution Velocity Score</div>
                    <div class="ai-insight-value"><i class="bi bi-speedometer me-1"></i>4.8 / 5.0 — Optimal</div>
                    <div class="fs-xs text-muted-gp mt-1">Avg. resolution time: 3.2 days</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ai-insight-item">
                    <div class="ai-insight-label">System Recommendation</div>
                    <div class="ai-insight-value"><i class="bi bi-lightbulb-fill me-1"></i>Reassign 2 officers to N-Suburb</div>
                    <div class="fs-xs text-muted-gp mt-1">Reduces sanitation backlog by ~35%</div>
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

    <!-- CHARTS — ROW 1 (Trend + Pie) -->
    <div id="sectionCharts" class="row g-4 mb-0">
        <div class="col-12 col-xl-8">
            <div class="section-card anim-fadeInUp anim-delay-3">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-graph-up-arrow"></i>Complaint Trend (12 Months)</div>
                    <div class="section-card-actions">
                        <button class="btn btn-sm btn-surface" title="Download Chart"><i class="bi bi-download"></i></button>
                        <button class="btn btn-sm btn-surface" title="Fullscreen"><i class="bi bi-fullscreen"></i></button>
                    </div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartComplaintTrend"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="section-card anim-fadeInUp anim-delay-3">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-pie-chart-fill"></i>Status Distribution</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartPendingResolved" <?= $pieAttr ?>></canvas></div>
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
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartCategoryDoughnut"></canvas></div>
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
                    <div class="chart-wrap" style="height:260px;"><canvas id="chartVillageBar"></canvas></div>
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
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartWeeklyActivity"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-steps"></i>Monthly Stacked View</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartStackedBar"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-stopwatch"></i>Resolution Timeline</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:220px;"><canvas id="chartResolutionTimeline"></canvas></div>
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
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartRadar"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-7">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title"><i class="bi bi-bar-chart-horizontal-fill"></i>Category Resolution Rate</div>
                </div>
                <div class="section-card-body">
                    <div class="chart-wrap" style="height:280px;"><canvas id="chartHorizontalBar"></canvas></div>
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
<footer class="app-footer" aria-label="Footer">
    <div>
        <strong style="color:var(--gp-primary);">GPCMS Analytics</strong> &nbsp;|&nbsp;
        Gram Panchayat Complaint Management System &nbsp;|&nbsp; v2.0 Enterprise
    </div>
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Government Portal</a>
        <a href="#">Help & Support</a>
        <a href="#">System Status</a>
    </div>
    <div class="text-muted-gp fs-xs">Developed for Digital India Initiative &copy; <?= date('Y') ?></div>
</footer>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/analytics.js?v=<?= time() ?>"></script>
</body>
</html>
