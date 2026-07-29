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
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, village_ward)
 * - categories (category_id, category_name)
 * - users (user_id, full_name, mobile_number, role_id)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Complaint Statistics & Analytics Dashboard";
$active_page = "statistics";
require_once __DIR__ . '/header.php';
?>

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

<?php require_once __DIR__ . '/footer.php'; ?>

