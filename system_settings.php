<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="System Settings - GPCMS Admin Portal">
  <title>System Settings | GPCMS Government Portal</title>

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
          <a href="system_settings.php" class="nav-link active">
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
          <input type="text" placeholder="Search settings..." id="global-header-search">
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
          <h1 class="page-title">System Settings & Configuration</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">System Settings</li>
            </ol>
          </nav>
        </div>
      </div>

      <!-- Settings Main Form -->
      <form id="systemSettingsForm" action="save_settings.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_system_settings">

        <!-- 1. General Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-buildings"></i> General Organization Settings</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="organization_name" class="form-label">Gram Panchayat Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="organization_name" name="organization_name" value="Gram Panchayat Khed Shivapur" required>
            </div>
            <div class="col-md-6">
              <label for="system_name" class="form-label">Application System Title</label>
              <input type="text" class="form-control" id="system_name" name="system_name" value="GPCMS - Grievance Portal" required>
            </div>
            <div class="col-md-4">
              <label for="village_name" class="form-label">Village / Gaon Name</label>
              <input type="text" class="form-control" id="village_name" name="village_name" value="Khed Shivapur">
            </div>
            <div class="col-md-4">
              <label for="taluka" class="form-label">Taluka / Sub-district</label>
              <input type="text" class="form-control" id="taluka" name="taluka" value="Haveli">
            </div>
            <div class="col-md-4">
              <label for="district" class="form-label">District</label>
              <input type="text" class="form-control" id="district" name="district" value="Pune">
            </div>
            <div class="col-md-4">
              <label for="state" class="form-label">State</label>
              <input type="text" class="form-control" id="state" name="state" value="Maharashtra">
            </div>
            <div class="col-md-4">
              <label for="pincode" class="form-label">Pincode</label>
              <input type="text" class="form-control" id="pincode" name="pincode" value="412205" pattern="[0-9]{6}">
            </div>
            <div class="col-md-4">
              <label for="logo_upload" class="form-label">Panchayat Logo Upload</label>
              <input type="file" class="form-control" id="logo_upload" name="logo_upload" accept="image/*">
            </div>
          </div>
        </div>

        <!-- 2. Complaint Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Complaint & Workflow Settings</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="default_status" class="form-label">Default Complaint Status</label>
              <select class="form-select" id="default_status" name="default_status">
                <option value="pending" selected>Pending Review</option>
                <option value="assigned">Auto Assign</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="complaint_prefix" class="form-label">Complaint Number Prefix</label>
              <input type="text" class="form-control" id="complaint_prefix" name="complaint_prefix" value="GPC-2026-">
            </div>
            <div class="col-md-4">
              <label for="auto_complaint_number" class="form-label">Auto Complaint Numbering</label>
              <select class="form-select" id="auto_complaint_number" name="auto_complaint_number">
                <option value="1" selected>Enabled (Sequential ID)</option>
                <option value="0">Disabled</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="default_department" class="form-label">Default Routing Department</label>
              <select class="form-select" id="default_department" name="default_department">
                <option value="Public Works">Public Works (PWD)</option>
                <option value="Water Supply" selected>Water Supply Cell</option>
                <option value="Sanitation">Sanitation Cell</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="priority_levels" class="form-label">Available Priority Levels</label>
              <input type="text" class="form-control" id="priority_levels" name="priority_levels" value="High, Medium, Low" readonly>
            </div>
          </div>
        </div>

        <!-- 3. Notification Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-bell"></i> Notification & Alert Preferences</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" checked>
                <label class="form-check-label fw-medium" for="email_notifications">Email Notifications</label>
                <div class="small text-muted">Send email to Gram Sevak on new submission</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1" checked>
                <label class="form-check-label fw-medium" for="sms_notifications">SMS Alerts (Citizen & Officer)</label>
                <div class="small text-muted">SMS confirmation on complaint registration</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" value="1">
                <label class="form-check-label fw-medium" for="push_notifications">Browser Push Alerts</label>
                <div class="small text-muted">Real-time admin desktop alerts</div>
              </div>
            </div>
            <div class="col-md-6 mt-3">
              <label for="reminder_frequency" class="form-label">SLA Overdue Reminder Frequency</label>
              <select class="form-select" id="reminder_frequency" name="reminder_frequency">
                <option value="24">Every 24 Hours</option>
                <option value="48" selected>Every 48 Hours</option>
                <option value="72">Every 72 Hours</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 4. Security Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-shield-lock"></i> Security & Session Controls</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="password_policy" class="form-label">Password Policy Level</label>
              <select class="form-select" id="password_policy" name="password_policy">
                <option value="strong" selected>Strong (Min 8 chars, 1 Special, 1 Num)</option>
                <option value="medium">Medium</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="session_timeout" class="form-label">Session Idle Timeout (Minutes)</label>
              <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="30" min="5" max="180">
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch pt-4">
                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1">
                <label class="form-check-label fw-medium text-danger" for="maintenance_mode">Maintenance Mode</label>
                <div class="small text-muted">Temporarily disable citizen submission</div>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. Backup Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-database-down"></i> Database Backup & Recovery</h5>
          </div>
          <div class="row g-3 align-items-center">
            <div class="col-md-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" value="1" checked>
                <label class="form-check-label fw-medium" for="auto_backup">Enable Auto Database Backup</label>
              </div>
            </div>
            <div class="col-md-4">
              <select class="form-select" id="backup_frequency" name="backup_frequency">
                <option value="daily" selected>Daily Midnight Backup</option>
                <option value="weekly">Weekly Backup</option>
              </select>
            </div>
            <div class="col-md-4 text-end">
              <button type="button" class="btn btn-outline-primary" onclick="GPCMS.showToast('Initiating MySQL Database Dump...', 'info')">
                <i class="bi bi-download"></i> Manual Database Backup
              </button>
            </div>
          </div>
        </div>

        <!-- 6. Theme & Localization Settings Card -->
        <div class="card-panel">
          <div class="card-header-clean">
            <h5 class="card-title"><i class="bi bi-palette"></i> Theme & Localization</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="primary_color" class="form-label">Primary Branding Color</label>
              <input type="color" class="form-control form-control-color w-100" id="primary_color" name="primary_color" value="#8A724C">
            </div>
            <div class="col-md-4">
              <label for="language" class="form-label">Portal Language</label>
              <select class="form-select" id="language" name="language">
                <option value="en" selected>English</option>
                <option value="mr">Marathi (मराठी)</option>
                <option value="hi">Hindi (हिंदी)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Sticky Action Controls Row -->
        <div class="card-panel bg-light p-3 border d-flex align-items-center justify-content-between">
          <span class="small text-muted"><i class="bi bi-info-circle"></i> Click Save to apply system configuration updates.</span>
          <div class="d-flex gap-2">
            <button type="reset" class="btn btn-gpcms-secondary">Reset Defaults</button>
            <button type="button" class="btn btn-outline-secondary" onclick="location.href='admin_dashboard.php'">Cancel</button>
            <button type="submit" class="btn btn-gpcms-primary"><i class="bi bi-check-lg"></i> Save All Settings</button>
          </div>
        </div>
      </form>
    </main>

    <!-- Footer -->
    <footer class="app-footer">
      <div>
        <strong>Gram Panchayat Complaint Management System (GPCMS)</strong> &copy; 2026 Government Portal.
      </div>
      <ul class="footer-links">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_categories.php">Categories</a></li>
        <li><span class="badge bg-secondary">Settings v2.0</span></li>
      </ul>
    </footer>
  </div>
</div>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/utils.js"></script>
<script src="js/admin.js"></script>
</body>
</html>
