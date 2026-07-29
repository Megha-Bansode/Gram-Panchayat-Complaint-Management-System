<?php
require_once __DIR__ . '/../includes/auth_check.php';

auth_start_session();

if (empty($_SESSION['is_logged_in'])) {
    auth_redirect('../includes/official_login.php', 'Please sign in to continue.');
}

// Integration Placeholders for Gram Sevak Module (fallback for display)
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';

/* 
 * Database Contract Table References:
 * - users (user_id, full_name, mobile_number, role_id)
 * - roles (role_id, role_name)
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description, complaint_image, complainant_name, mobile_number, village_ward)
 * - categories (category_id, category_name)
 * - complaint_history (history_id, complaint_id, status, remarks)
 * - complaint_photos (photo_id, complaint_id, before_photo, after_photo)
 * - notifications (notification_id, user_id, message)
 * - settings (setting_id, application_name, gram_panchayat_name)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Gram Sevak Dashboard";
$active_page = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gram Panchayat Complaint Management System</title>
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
                        <a href="gramsevak_dashboard.php" class="nav-link active">
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
                        <a href="complaint_statistics.php" class="nav-link">
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

        <!-- Main Content Area -->
        <div class="gpcms-main-content">
            <!-- Header Bar -->
            <header class="gpcms-header">
                <div class="header-left">
                    <button class="btn btn-sidebar-toggle" id="sidebarToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="header-app-title">
                        <h1 class="h5 mb-0 font-weight-bold">Gram Panchayat Complaint Management System</h1>
                        <span class="text-muted small">Official Gram Sevak Administration Portal</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-clock d-none d-md-flex" id="liveClock">
                        <i class="bi bi-clock me-2"></i><span id="clockTime">--:--:--</span>
                    </div>

                    <div class="header-search d-none d-lg-block">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="globalSearchInput" placeholder="Search complaints, village...">
                        </div>
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
                                    <a href="assign_complaints.php" class="btn btn-light btn-sm fw-semibold text-dark shadow-sm me-2">
                                        <i class="bi bi-person-plus me-1"></i> Assign Field Officers
                                    </a>
                                    <a href="verify_completed_work.php" class="btn btn-outline-light btn-sm fw-semibold">
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
                                        <h3 class="card-number" id="dashTotalComplaints">148</h3>
                                    </div>
                                    <div class="card-icon"><i class="bi bi-folder-fill"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-success small"><i class="bi bi-graph-up"></i> +12% this month</span>
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
                                        <h3 class="card-number text-warning-custom" id="dashPendingComplaints">24</h3>
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
                                        <h3 class="card-number text-info-custom" id="dashAssignedComplaints">38</h3>
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
                                        <h3 class="card-number text-primary-custom" id="dashInProgressComplaints">31</h3>
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
                                        <h3 class="card-number text-success-custom" id="dashResolvedComplaints">55</h3>
                                    </div>
                                    <div class="card-icon text-success-custom"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="card-footer-info mt-2">
                                    <span class="text-success small"><i class="bi bi-shield-check"></i> 88% Resolution Efficiency Target Met</span>
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
                                    <a href="assign_complaints.php" class="btn btn-gpcms-primary btn-sm">
                                        <i class="bi bi-person-plus me-1"></i> Assign Field Officer
                                    </a>
                                    <a href="update_complaint_status.php" class="btn btn-gpcms-secondary btn-sm">
                                        <i class="bi bi-arrow-repeat me-1"></i> Change Status
                                    </a>
                                    <a href="verify_completed_work.php" class="btn btn-outline-success btn-sm">
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
                                            <!-- Dynamically populated by JS -->
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
                        <a href="system_settings.php" class="text-decoration-none text-muted small">Contact Admin</a>
                        <a href="https://panchayat.gov.in" target="_blank" rel="noopener" class="text-decoration-none text-muted small">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Government Portal
                        </a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell-fill text-warning me-2"></i>System Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush" id="notificationList">
                        <div class="list-group-item p-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-primary-custom">New Complaint Submitted</h6>
                                <small class="text-muted">10 mins ago</small>
                            </div>
                            <p class="mb-1 small">Water pipeline leakage reported near Ward 3 Community Center.</p>
                            <small class="text-warning-custom"><i class="bi bi-exclamation-triangle me-1"></i>Status: pending</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-circle text-primary-custom me-2"></i>Gram Sevak Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-secondary-custom"><i class="bi bi-person-badge-fill"></i></div>
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h5>
                        <span class="badge bg-primary-custom"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">User ID:</span>
                            <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Gram Panchayat:</span>
                            <span class="fw-semibold">Shivaji Nagar Gram Panchayat</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Gram Sevak JS -->
    <script src="../js/gramsevak.js"></script>
</body>
</html>
