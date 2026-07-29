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
$page_title = "Monthly Complaint Reports";
$active_page = "monthly_reports";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Reports - Gram Panchayat Complaint Management System</title>
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
                        <a href="complaint_statistics.php" class="nav-link">
                            <i class="bi bi-graph-up-arrow"></i> <span>Statistics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="monthly_reports.php" class="nav-link active">
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
                        <h1 class="h5 mb-0 font-weight-bold">Monthly Grievance Audit & Statement Portal</h1>
                        <span class="text-muted small">Periodic Performance Summaries and Compliance Reports</span>
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
                <!-- Page Breadcrumb & Export Tools Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Monthly Complaint Statement</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Monthly Reports</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm" onclick="exportMonthlyReportPDF()">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportMonthlyReportExcel()">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
                        </button>
                        <button class="btn btn-dark btn-sm" onclick="printElement('monthlyReportTable', 'Monthly Complaint Report Statement')">
                            <i class="bi bi-printer-fill me-1"></i> Print Statement
                        </button>
                    </div>
                </div>

                <!-- Monthly Filters Control Bar -->
                <div class="gpcms-card p-3 mb-4">
                    <h6 class="fw-bold mb-3 text-secondary-custom"><i class="bi bi-calendar-event me-2"></i>Filter Monthly Statement</h6>
                    <form id="formMonthlyFilters" onsubmit="event.preventDefault(); generateMonthlyReport();">
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterMonth" class="form-label small fw-semibold">Select Month</label>
                                <select class="form-select form-select-sm" id="mthFilterMonth">
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07" selected>July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterYear" class="form-label small fw-semibold">Select Year</label>
                                <select class="form-select form-select-sm" id="mthFilterYear">
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterVillage" class="form-label small fw-semibold">Village Filter</label>
                                <select class="form-select form-select-sm" id="mthFilterVillage">
                                    <option value="">All Villages</option>
                                    <option value="Shivaji Nagar">Shivaji Nagar</option>
                                    <option value="Rampur Ward 1">Rampur Ward 1</option>
                                    <option value="Rampur Ward 2">Rampur Ward 2</option>
                                    <option value="Ganesh Wadi">Ganesh Wadi</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterCategory" class="form-label small fw-semibold">Category Filter</label>
                                <select class="form-select form-select-sm" id="mthFilterCategory">
                                    <option value="">All Categories</option>
                                    <option value="Water Supply">Water Supply</option>
                                    <option value="Sanitation & Waste">Sanitation & Waste</option>
                                    <option value="Road Repair">Road Repair</option>
                                    <option value="Street Lighting">Street Lighting</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterStatus" class="form-label small fw-semibold">Allowed Status</label>
                                <select class="form-select form-select-sm" id="mthFilterStatus">
                                    <option value="">All Statuses</option>
                                    <option value="pending">pending</option>
                                    <option value="assigned">assigned</option>
                                    <option value="in_progress">in_progress</option>
                                    <option value="resolved">resolved</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label for="mthFilterOfficer" class="form-label small fw-semibold">Field Officer</label>
                                <select class="form-select form-select-sm" id="mthFilterOfficer">
                                    <option value="">All Officers</option>
                                    <option value="Ramesh Shinde">Ramesh Shinde</option>
                                    <option value="Suresh Patil">Suresh Patil</option>
                                    <option value="Anil Kadam">Anil Kadam</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-gpcms-primary btn-sm px-4">
                                <i class="bi bi-funnel-fill me-1"></i> Generate Monthly Statement
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Monthly Summary KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card card-total h-100">
                            <div class="card-body">
                                <span class="card-label">Monthly Total Received</span>
                                <h3 class="card-number" id="mthCardTotal">42</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card card-pending h-100">
                            <div class="card-body">
                                <span class="card-label">Monthly Pending</span>
                                <h3 class="card-number text-warning-custom" id="mthCardPending">6</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card card-in-progress h-100">
                            <div class="card-body">
                                <span class="card-label">Monthly In Progress</span>
                                <h3 class="card-number text-primary-custom" id="mthCardInProgress">12</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card card-resolved h-100">
                            <div class="card-body">
                                <span class="card-label">Monthly Resolved</span>
                                <h3 class="card-number text-success-custom" id="mthCardResolved">24</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Breakdown Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gpcms-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-primary-custom"></i>Selected Month Category & Status Breakdown</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container position-relative" style="height: 280px;">
                                    <canvas id="monthlyReportChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Statement Data Table -->
                <div class="gpcms-card mb-4 PrintableArea">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0" id="monthlyTableTitle"><i class="bi bi-table text-primary-custom me-2"></i>Monthly Complaints Log (July 2026)</h6>
                        <button class="btn btn-sm btn-outline-dark" onclick="printElement('monthlyReportTable', 'Monthly Complaints Statement Table')">
                            <i class="bi bi-printer me-1"></i> Print Table
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 gpcms-table" id="monthlyReportTable">
                                <thead>
                                    <tr>
                                        <th>Complaint ID</th>
                                        <th>Complainant Name</th>
                                        <th>Village / Ward</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Assigned Officer</th>
                                        <th>Submission Date</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlyReportTableBody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
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
