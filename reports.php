<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Advanced Reports Module - GPCMS Admin Portal">
  <title>Reports & Analytics | GPCMS Government Portal</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
          <a href="admin_dashboard.php" class="nav-link">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="manage_categories.php" class="nav-link">
            <i class="bi bi-tags-fill"></i>
            <span class="nav-text">Complaint Categories</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="reports.php" class="nav-link active">
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

      <div class="menu-label mt-3">Complaints &amp; Operations</div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="view_complaints.php" class="nav-link">
            <i class="bi bi-inbox-fill"></i>
            <span class="nav-text">All Complaints</span>
            <span class="badge bg-warning text-dark sidebar-badge">9</span>
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
    <!-- Header -->
    <header class="app-header">
      <div class="header-left">
        <button id="mobile-nav-toggle" class="mobile-nav-toggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="header-search">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Search reports..." id="global-header-search">
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
          <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Avatar" class="avatar-img" style="width: 36px; height: 36px;">
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li><a class="dropdown-item" href="system_settings.php">Settings</a></li>
            <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Content Area -->
    <main class="content-area">
      <div class="page-header-block">
        <div>
          <h1 class="page-title">Grievance & Performance Reports</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
          </nav>
        </div>
        <div class="top-actions d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-gpcms-primary" onclick="GPCMS.exportTableToCSV('reports-table', 'gpcms_reports.csv')">
            <i class="bi bi-file-earmark-excel"></i> Export Excel/CSV
          </button>
          <button type="button" class="btn btn-gpcms-secondary" onclick="GPCMS.printCurrentPage()">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF / Print
          </button>
        </div>
      </div>

      <!-- Advanced Filter Panel -->
      <div class="card-panel p-3 mb-4">
        <form action="generate_report.php" method="GET" id="reportFilterForm">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="date_range" class="form-label">Date Range Preset</label>
              <select class="form-select" id="date_range" name="date_range">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="7days">Last 7 Days</option>
                <option value="30days" selected>Last 30 Days</option>
                <option value="custom">Custom Date Range</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="village_ward" class="form-label">Village / Ward</label>
              <select class="form-select" id="village_ward" name="village_ward">
                <option value="">All Wards (1-6)</option>
                <option value="Ward 1">Ward 1 (Station Area)</option>
                <option value="Ward 2">Ward 2 (Gramthan)</option>
                <option value="Ward 3">Ward 3 (Industrial Zone)</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="category_id" class="form-label">Complaint Category</label>
              <select class="form-select" id="category_id" name="category_id">
                <option value="">All Categories</option>
                <option value="CAT-001">Water Supply</option>
                <option value="CAT-002">Sanitation</option>
                <option value="CAT-003">Streetlights</option>
                <option value="CAT-004">Road Repair</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="assigned_to" class="form-label">Assigned Officer</label>
              <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">All Officers</option>
                <option value="officer_1">Gram Sevak A. Patil</option>
                <option value="officer_2">Engineer R. Deshmukh</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="priority" class="form-label">Priority</label>
              <select class="form-select" id="priority" name="priority">
                <option value="">All Priorities</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="citizen_gender" class="form-label">Citizen Gender</label>
              <select class="form-select" id="citizen_gender" name="citizen_gender">
                <option value="">All</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
              <button type="submit" class="btn btn-gpcms-primary w-100"><i class="bi bi-funnel"></i> Generate</button>
              <button type="reset" class="btn btn-gpcms-secondary"><i class="bi bi-x-lg"></i></button>
            </div>
          </div>
        </form>
      </div>

      <!-- KPI Summary Cards Row -->
      <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card p-3">
            <div class="stat-info">
              <div class="stat-value text-primary fs-3">584</div>
              <div class="stat-label">Total Filtered</div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card stat-pending p-3">
            <div class="stat-info">
              <div class="stat-value text-warning fs-3">85</div>
              <div class="stat-label">Pending</div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card stat-assigned p-3">
            <div class="stat-info">
              <div class="stat-value text-info fs-3">110</div>
              <div class="stat-label">Assigned</div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card stat-in-progress p-3">
            <div class="stat-info">
              <div class="stat-value text-purple fs-3">145</div>
              <div class="stat-label">In Progress</div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card stat-resolved p-3">
            <div class="stat-info">
              <div class="stat-value text-success fs-3">220</div>
              <div class="stat-label">Resolved</div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card p-3">
            <div class="stat-info">
              <div class="stat-value text-danger fs-3">2.4 Days</div>
              <div class="stat-label">Avg Res. Time</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Report Data Table -->
      <div class="table-custom-wrapper shadow-sm">
        <div class="table-responsive">
          <table class="table table-custom align-middle" id="reports-table">
            <thead>
              <tr>
                <th>Complaint ID</th>
                <th>Complainant Name</th>
                <th>Category</th>
                <th>Ward/Village</th>
                <th>Assigned Officer</th>
                <th>Submission Date</th>
                <th>Resolution Days</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-bold">#GPC-2026-0891</td>
                <td>Suresh Patil</td>
                <td>Water Supply</td>
                <td>Ward 1 (Station Area)</td>
                <td>Gram Sevak A. Patil</td>
                <td>22 Jul 2026</td>
                <td>1 Day</td>
                <td><span class="badge-gpcms badge-pending">Pending</span></td>
              </tr>
              <tr>
                <td class="fw-bold">#GPC-2026-0890</td>
                <td>Meena Deshmukh</td>
                <td>Street Lights</td>
                <td>Ward 2 (Gramthan)</td>
                <td>Engineer R. Deshmukh</td>
                <td>21 Jul 2026</td>
                <td>2 Days</td>
                <td><span class="badge-gpcms badge-assigned">Assigned</span></td>
              </tr>
              <tr>
                <td class="fw-bold">#GPC-2026-0889</td>
                <td>Ramesh Shinde</td>
                <td>Drainage Cleanliness</td>
                <td>Ward 3 (Industrial)</td>
                <td>Gram Sevak A. Patil</td>
                <td>21 Jul 2026</td>
                <td>3 Days</td>
                <td><span class="badge-gpcms badge-in-progress">In Progress</span></td>
              </tr>
              <tr>
                <td class="fw-bold">#GPC-2026-0888</td>
                <td>Anita Kadam</td>
                <td>Road Repair</td>
                <td>Ward 1 (Station Area)</td>
                <td>Engineer R. Deshmukh</td>
                <td>20 Jul 2026</td>
                <td>1.5 Days</td>
                <td><span class="badge-gpcms badge-resolved">Resolved</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 d-flex align-items-center justify-content-between border-top bg-light">
          <span class="small text-muted">Page 1 of 12</span>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="app-footer">
      <div>
        <strong>Gram Panchayat Complaint Management System (GPCMS)</strong> &copy; 2026 Government Portal.
      </div>
    </footer>
  </div>
</div>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/utils.js"></script>
<script src="js/admin.js"></script>
</body>
</html>
