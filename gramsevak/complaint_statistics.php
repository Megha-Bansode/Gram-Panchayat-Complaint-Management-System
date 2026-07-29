<?php
require_once __DIR__ . '/../includes/auth_check.php';

auth_start_session();

if (empty($_SESSION['is_logged_in'])) {
    auth_redirect('../includes/official_login.php', 'Please sign in to continue.');
}

// Integration Placeholders for Gram Sevak Module
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';

/* 
 * Database Contract Table References:
 * - complaints (complaint_id, category_id, assigned_to, status, village_ward)
 * - categories (category_id, category_name)
 * - users (user_id, full_name, mobile_number, role_id)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Complaint Statistics Analytics Dashboard";
$active_page = "statistics";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Statistics - Gram Panchayat Complaint Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Gram Sevak CSS -->
    <link rel="stylesheet" href="../css/gramsevak.css">
</head>
<body>
    <div class="gpcms-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="gpcms-sidebar" id="gpcmsSidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-building-fill-gear"></i></div>
                <div class="brand-text">
                    <span class="main-title">GPCMS</span>
                    <span class="sub-title">Gram Sevak Portal</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="gramsevak_dashboard.php" class="nav-link">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view_complaints.php" class="nav-link">
                            <i class="bi bi-card-checklist"></i> <span>View Complaints</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="assign_complaints.php" class="nav-link">
                            <i class="bi bi-person-plus-fill"></i> <span>Assign Complaints</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="update_complaint_status.php" class="nav-link">
                            <i class="bi bi-arrow-repeat"></i> <span>Update Status</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="verify_completed_work.php" class="nav-link">
                            <i class="bi bi-patch-check-fill"></i> <span>Verify Work</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="complaint_category_management.php" class="nav-link">
                            <i class="bi bi-grid-3x3-gap-fill"></i> <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="system_settings.php" class="nav-link">
                            <i class="bi bi-sliders"></i> <span>System Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i> <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="complaint_statistics.php" class="nav-link active">
                            <i class="bi bi-graph-up-arrow"></i> <span>Statistics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="monthly_reports.php" class="nav-link">
                            <i class="bi bi-calendar3"></i> <span>Monthly Reports</span>
                        </a>
                    </li>
                </ul>

                <hr class="sidebar-divider">

                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#notificationModal">
                            <i class="bi bi-bell-fill"></i> <span>Notifications</span>
                            <span class="badge rounded-pill bg-danger ms-auto">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person-circle"></i> <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../includes/logout.php" class="nav-link text-danger-custom" id="btnLogout">
                            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="gpcms-main-content">
            <!-- Header Bar -->
            <header class="gpcms-header">
                <div class="header-left">
                    <button class="btn btn-sidebar-toggle" id="sidebarToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="header-app-title">
                        <h1 class="h5 mb-0 font-weight-bold">Complaint Statistics & Analytics Dashboard</h1>
                        <span class="text-muted small">Deep analytical Insights on Category, Village Trends, and Field Officer KPIs</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-clock d-none d-md-flex" id="liveClock">
                        <i class="bi bi-clock me-2"></i><span id="clockTime">--:--:--</span>
                    </div>

                    <div class="dropdown header-user-dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2"><i class="bi bi-person-fill"></i></div>
                            <div class="user-info text-start d-none d-sm-block">
                                <span class="user-name d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <span class="user-role badge badge-role"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="system_settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../includes/logout.php" id="dropdownLogout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Header Breadcrumb & Controls -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Complaint Analytics</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Statistics</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('officerPerformanceTableBody', 'Analytics Matrix Statement')">
                            <i class="bi bi-printer me-1"></i> Print Matrix
                        </button>
                        <select class="form-select form-select-sm" id="statsTimeframeSelect" onchange="updateStatisticsCharts()" style="width: auto;">
                            <option value="this_year" selected>This Calendar Year (2026)</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="all_time">All Time Historical</option>
                        </select>
                    </div>
                </div>

                <!-- 5 Summary KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-total h-100">
                            <div class="card-body">
                                <span class="card-label">Total Complaints</span>
                                <h3 class="card-number">148</h3>
                                <small class="text-muted"><i class="bi bi-database me-1"></i>gpcms_db</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-pending h-100">
                            <div class="card-body">
                                <span class="card-label">Pending</span>
                                <h3 class="card-number text-warning-custom">24</h3>
                                <small class="text-muted">Requires Assignment</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-assigned h-100">
                            <div class="card-body">
                                <span class="card-label">Assigned</span>
                                <h3 class="card-number text-info-custom">38</h3>
                                <small class="text-muted">In Queue</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="summary-card card-in-progress h-100">
                            <div class="card-body">
                                <span class="card-label">In Progress</span>
                                <h3 class="card-number text-primary-custom">31</h3>
                                <small class="text-muted">On Ground Work</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-8 col-sm-12">
                        <div class="summary-card card-resolved h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Resolved Complaints</span>
                                        <h3 class="card-number text-success-custom">55</h3>
                                    </div>
                                    <div class="text-end">
                                        <span class="card-label d-block">Avg Resolution Time</span>
                                        <h4 class="fw-bold text-dark mb-0">2.4 Days</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 1 Charts: Category Pie Chart & Line Chart (Monthly Trend) -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-5">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-pie-chart-fill me-2 text-primary-custom"></i>Category Distribution (Pie Chart)</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container position-relative" style="height: 300px;">
                                    <canvas id="categoryPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-graph-up me-2 text-primary-custom"></i>Monthly Complaint Trend (Line Chart)</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container position-relative" style="height: 300px;">
                                    <canvas id="monthlyTrendLineChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2 Charts: Village-wise Bar Chart & Field Officer Performance -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary-custom"></i>Village-wise Complaint Analysis (Bar Chart)</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container position-relative" style="height: 300px;">
                                    <canvas id="villageBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-primary-custom"></i>Officer Performance Matrix</h6>
                                <button class="btn btn-sm btn-outline-dark" onclick="printElement('officerPerformanceTableBody', 'Field Officer Performance Matrix')">
                                    <i class="bi bi-printer me-1"></i> Print Matrix
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 gpcms-table">
                                        <thead>
                                            <tr>
                                                <th>Field Officer Name</th>
                                                <th>Assigned</th>
                                                <th>In Progress</th>
                                                <th>Resolved</th>
                                                <th>Resolution Efficiency</th>
                                            </tr>
                                        </thead>
                                        <tbody id="officerPerformanceTableBody">
                                            <!-- Dynamically populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="gpcms-footer">
                <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold">Shivaji Nagar Gram Panchayat</span> &copy; <?php echo date('Y'); ?>. All Rights Reserved.
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-secondary-subtle text-secondary">GPCMS Version 3.1</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Gram Sevak JS -->
    <script src="../js/gramsevak.js"></script>
</body>
</html>
