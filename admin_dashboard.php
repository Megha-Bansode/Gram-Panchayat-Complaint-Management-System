<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gram Panchayat Complaint Management System - Admin Dashboard">
  <title>Admin Dashboard | GPCMS Government Portal</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

<div id="app-container">
  <!-- Sidebar Navigation -->
  <aside id="app-sidebar" class="app-sidebar">
    <div class="sidebar-header">
      <a href="admin_dashboard.php" class="sidebar-brand">
        <div class="sidebar-brand-icon">
          <i class="bi bi-building-flag"></i>
        </div>
        <div class="sidebar-brand-text">
          <span class="title">Gram Panchayat</span>
          <span class="subtitle">GPCMS Portal</span>
        </div>
      </a>
      <button id="sidebar-toggle-btn" class="sidebar-toggle-btn" title="Toggle Sidebar">
        <i class="bi bi-layout-sidebar-inset"></i>
      </button>
    </div>

    <div class="sidebar-body">
      <div class="menu-label">Main Navigation</div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="admin_dashboard.php" class="nav-link active">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="manage_categories.php" class="nav-link">
            <i class="bi bi-tags-fill"></i>
            <span class="nav-text">Complaint Categories</span>
            <span class="badge bg-secondary sidebar-badge">14</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="reports.php" class="nav-link">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span class="nav-text">Reports</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="statistics_dashboard.php" class="nav-link">
            <i class="bi bi-pie-chart-fill"></i>
            <span class="nav-text">Statistics</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="system_settings.php" class="nav-link">
            <i class="bi bi-gear-fill"></i>
            <span class="nav-text">System Settings</span>
          </a>
        </li>
      </ul>

      <div class="menu-label mt-3">Complaints & Operations</div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="view_complaints.php" class="nav-link">
            <i class="bi bi-inbox-fill"></i>
            <span class="nav-text">All Complaints</span>
            <span class="badge bg-warning text-dark sidebar-badge">42</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="view_complaints.php?status=assigned" class="nav-link">
            <i class="bi bi-person-workspace"></i>
            <span class="nav-text">Assigned Work</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="view_complaints.php?status=pending" class="nav-link">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-text">Notifications</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="system_settings.php" class="nav-link">
            <i class="bi bi-question-circle-fill"></i>
            <span class="nav-text">Help &amp; Support</span>
          </a>
        </li>
      </ul>

      <div class="menu-label mt-3">Account</div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="system_settings.php" class="nav-link">
            <i class="bi bi-person-circle"></i>
            <span class="nav-text">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin_dashboard.php" class="nav-link">
            <i class="bi bi-box-arrow-right"></i>
            <span class="nav-text">Logout</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="sidebar-footer">
      <div class="user-profile-summary">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Admin Avatar" class="avatar-img">
        <div class="user-info">
          <span class="name">Rajesh Sharma</span>
          <span class="role">Gram Sevak / Admin</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Wrapper -->
  <div class="main-wrapper">
    <!-- Top Navigation Header -->
    <header class="app-header">
      <div class="header-left">
        <button id="mobile-nav-toggle" class="mobile-nav-toggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="header-search">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Search complaint ID, category..." id="global-header-search">
        </div>
      </div>

      <div class="header-right">
        <div class="header-clock">
          <span class="time" id="live-time">10:00:00 AM</span>
          <span class="date" id="live-date">Wed, 22 Jul 2026</span>
        </div>

        <button id="dark-mode-toggle" class="header-action-btn" title="Toggle Theme">
          <i class="bi bi-moon-stars-fill"></i>
        </button>

        <div class="dropdown">
          <button class="header-action-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="notification-badge"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 280px;">
            <li class="dropdown-header fw-bold">System Alerts</li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item small py-2" href="#"><i class="bi bi-exclamation-circle text-warning me-2"></i> 5 new water supply complaints added</a></li>
            <li><a class="dropdown-item small py-2" href="#"><i class="bi bi-check-circle text-success me-2"></i> Monthly complaint report generated</a></li>
          </ul>
        </div>

        <div class="dropdown">
          <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="User Avatar" class="avatar-img" style="width: 36px; height: 36px;">
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li class="dropdown-header fw-bold">Rajesh Sharma</li>
            <li><a class="dropdown-item" href="system_settings.php"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
            <li><a class="dropdown-item" href="system_settings.php"><i class="bi bi-sliders me-2"></i> Preferences</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="content-area">
      <div class="page-header-block">
        <div>
          <h1 class="page-title">Complaint Administration Dashboard</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </nav>
        </div>
        <div class="top-actions d-flex gap-2">
          <a href="manage_categories.php" class="btn btn-gpcms-secondary"><i class="bi bi-tags"></i> Manage Categories</a>
          <a href="reports.php" class="btn btn-gpcms-primary"><i class="bi bi-file-earmark-pdf"></i> Generate Report</a>
        </div>
      </div>

      <!-- Top Summary Metrics Row (5 cards: Total, Pending, Assigned, In Progress, Resolved) -->
      <div class="row g-3 mb-4">
        <div class="col-xl col-md-6">
          <div class="stat-card">
            <div class="stat-info">
              <div class="stat-value animate-num" data-target="584">584</div>
              <div class="stat-label">Total Complaints</div>
              <div class="stat-trend up"><i class="bi bi-arrow-up-right"></i> +12% this month</div>
            </div>
            <div class="stat-icon"><i class="bi bi-folder-check"></i></div>
          </div>
        </div>
        <div class="col-xl col-md-6">
          <div class="stat-card stat-pending">
            <div class="stat-info">
              <div class="stat-value animate-num" data-target="85">85</div>
              <div class="stat-label">Pending Review</div>
              <div class="stat-trend down"><i class="bi bi-arrow-down-right"></i> -5% today</div>
            </div>
            <div class="stat-icon text-warning" style="background-color: var(--gpcms-pending-bg);"><i class="bi bi-clock-history"></i></div>
          </div>
        </div>
        <div class="col-xl col-md-6">
          <div class="stat-card stat-assigned">
            <div class="stat-info">
              <div class="stat-value animate-num" data-target="110">110</div>
              <div class="stat-label">Assigned</div>
              <div class="stat-trend up"><i class="bi bi-arrow-up-right"></i> Field assigned</div>
            </div>
            <div class="stat-icon" style="background-color: var(--gpcms-assigned-bg); color: var(--gpcms-assigned);"><i class="bi bi-person-check"></i></div>
          </div>
        </div>
        <div class="col-xl col-md-6">
          <div class="stat-card stat-in-progress">
            <div class="stat-info">
              <div class="stat-value animate-num" data-target="145">145</div>
              <div class="stat-label">In Progress</div>
              <div class="stat-trend up"><i class="bi bi-arrow-up-right"></i> Active field work</div>
            </div>
            <div class="stat-icon text-purple" style="background-color: var(--gpcms-in-progress-bg);"><i class="bi bi-gear-wide-connected"></i></div>
          </div>
        </div>
        <div class="col-xl col-md-6">
          <div class="stat-card stat-resolved">
            <div class="stat-info">
              <div class="stat-value animate-num" data-target="330">330</div>
              <div class="stat-label">Resolved Grievances</div>
              <div class="stat-trend up"><i class="bi bi-arrow-up-right"></i> 89.4% SLA Pass</div>
            </div>
            <div class="stat-icon text-success" style="background-color: var(--gpcms-resolved-bg);"><i class="bi bi-check-circle"></i></div>
          </div>
        </div>
      </div>

      <!-- Charts & Recent Activity Grid -->
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-graph-up-arrow"></i> Monthly Complaint Trends</h5>
              <a href="statistics_dashboard.php" class="btn btn-sm btn-gpcms-secondary">View Analytics</a>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="chart-monthly-trend"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-pie-chart"></i> Status Breakdown</h5>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="chart-complaint-status"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Complaints & Recent Activity Timeline -->
      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card-panel mb-0">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-list-stars"></i> Recent Complaints Received</h5>
              <a href="view_complaints.php" class="btn btn-sm btn-gpcms-secondary">View All</a>
            </div>
            <div class="table-responsive">
              <table class="table table-custom align-middle">
                <thead>
                  <tr>
                    <th>Complaint ID</th>
                    <th>Citizen Name</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><a href="complaint_details.php?id=GPC-2026-0891" class="fw-bold text-primary">#GPC-2026-0891</a></td>
                    <td>Suresh Patil</td>
                    <td>Water Supply</td>
                    <td><span class="badge badge-priority-high">High</span></td>
                    <td><span class="badge-gpcms badge-pending"><i class="bi bi-clock"></i> Pending</span></td>
                    <td>22 Jul 2026</td>
                  </tr>
                  <tr>
                    <td><a href="complaint_details.php?id=GPC-2026-0890" class="fw-bold text-primary">#GPC-2026-0890</a></td>
                    <td>Meena Deshmukh</td>
                    <td>Street Lights</td>
                    <td><span class="badge badge-priority-medium">Medium</span></td>
                    <td><span class="badge-gpcms badge-assigned"><i class="bi bi-person-check"></i> Assigned</span></td>
                    <td>21 Jul 2026</td>
                  </tr>
                  <tr>
                    <td><a href="complaint_details.php?id=GPC-2026-0889" class="fw-bold text-primary">#GPC-2026-0889</a></td>
                    <td>Ramesh Shinde</td>
                    <td>Drainage Cleanliness</td>
                    <td><span class="badge badge-priority-high">High</span></td>
                    <td><span class="badge-gpcms badge-in-progress"><i class="bi bi-arrow-repeat"></i> In Progress</span></td>
                    <td>21 Jul 2026</td>
                  </tr>
                  <tr>
                    <td><a href="complaint_details.php?id=GPC-2026-0888" class="fw-bold text-primary">#GPC-2026-0888</a></td>
                    <td>Anita Kadam</td>
                    <td>Road Repair</td>
                    <td><span class="badge badge-priority-low">Low</span></td>
                    <td><span class="badge-gpcms badge-resolved"><i class="bi bi-check2"></i> Resolved</span></td>
                    <td>20 Jul 2026</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card-panel mb-0">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-activity"></i> Recent Activity Log</h5>
            </div>
            <ul class="timeline">
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Category Created</div>
                  <div class="text-muted small">New category "Solar Street Lights" added by Admin.</div>
                  <div class="timeline-time">10 mins ago</div>
                </div>
              </li>
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Complaint Assigned</div>
                  <div class="text-muted small">#GPC-2026-0890 assigned to Gram Sevak A. Patil.</div>
                  <div class="timeline-time">1 hour ago</div>
                </div>
              </li>
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Backup Generated</div>
                  <div class="text-muted small">Automatic database backup completed successfully.</div>
                  <div class="timeline-time">4 hours ago</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Quick Actions Section -->
      <div class="row g-3 mt-1">
        <div class="col-12">
          <div class="card-panel mb-0">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-lightning-charge-fill"></i> Quick Actions</h5>
            </div>
            <div class="row g-3">
              <div class="col-xl-3 col-md-6">
                <a href="view_complaints.php" class="quick-action-card">
                  <div class="quick-action-icon"><i class="bi bi-inbox-fill"></i></div>
                  <div class="quick-action-text">
                    <div class="quick-action-title">View All Complaints</div>
                    <div class="quick-action-desc">Browse, search &amp; filter complaints</div>
                  </div>
                </a>
              </div>
              <div class="col-xl-3 col-md-6">
                <a href="manage_categories.php" class="quick-action-card">
                  <div class="quick-action-icon"><i class="bi bi-tags-fill"></i></div>
                  <div class="quick-action-text">
                    <div class="quick-action-title">Manage Categories</div>
                    <div class="quick-action-desc">Add or edit complaint types</div>
                  </div>
                </a>
              </div>
              <div class="col-xl-3 col-md-6">
                <a href="reports.php" class="quick-action-card">
                  <div class="quick-action-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                  <div class="quick-action-text">
                    <div class="quick-action-title">Generate Report</div>
                    <div class="quick-action-desc">Export complaint analytics</div>
                  </div>
                </a>
              </div>
              <div class="col-xl-3 col-md-6">
                <a href="statistics_dashboard.php" class="quick-action-card">
                  <div class="quick-action-icon"><i class="bi bi-pie-chart-fill"></i></div>
                  <div class="quick-action-text">
                    <div class="quick-action-title">View Statistics</div>
                    <div class="quick-action-desc">Analytics &amp; performance data</div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>

    <!-- Footer -->
    <footer class="app-footer">
      <div>
        <strong>Gram Panchayat Complaint Management System (GPCMS)</strong> &copy; 2026 Government Portal. All Rights Reserved.
      </div>
      <ul class="footer-links">
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Use</a></li>
        <li><a href="system_settings.php">System Settings</a></li>
        <li><span class="badge bg-secondary">v2.0 Backend-Ready</span></li>
      </ul>
    </footer>
  </div>
</div>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/utils.js"></script>
<script src="js/admin.js"></script>
<script src="js/charts.js"></script>
</body>
</html>
