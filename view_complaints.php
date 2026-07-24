<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gram Panchayat Complaint Management System - View All Complaints">
  <title>View Complaints | GPCMS Government Portal</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Custom CSS (reused from existing project) -->
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

<div id="app-container">

  <!-- ================================================
       Sidebar Navigation (reused structure from admin_dashboard.php)
       ================================================ -->
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

      <div class="menu-label mt-3">Complaints &amp; Operations</div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="view_complaints.php" class="nav-link active">
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

  <!-- ================================================
       Main Wrapper
       ================================================ -->
  <div class="main-wrapper">

    <!-- Top Navigation Header (reused structure) -->
    <header class="app-header">
      <div class="header-left">
        <button id="mobile-nav-toggle" class="mobile-nav-toggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="header-search">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Search complaint ID, citizen name..." id="global-header-search" autocomplete="off">
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

      <!-- Page Header -->
      <div class="page-header-block">
        <div>
          <h1 class="page-title">All Complaints</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Complaints</li>
            </ol>
          </nav>
        </div>
        <div class="top-actions d-flex gap-2">
          <button class="btn btn-gpcms-secondary" onclick="GPCMS.exportTableToCSV('complaints-table', 'complaints_export.csv')">
            <i class="bi bi-download"></i> Export CSV
          </button>
          <button class="btn btn-gpcms-primary" onclick="GPCMS.printCurrentPage()">
            <i class="bi bi-printer"></i> Print
          </button>
        </div>
      </div>

      <!-- ================================================
           Filter Bar
           PHP Integration: Replace option values with PHP foreach loops
           ================================================ -->
      <div class="card-panel filter-bar mb-3">
        <div class="row g-2 align-items-end">

          <!-- Search -->
          <div class="col-xl-3 col-md-12">
            <label class="form-label" for="complaint-search">Search Complaints</label>
            <div class="position-relative">
              <i class="bi bi-search position-absolute"
                 style="left:0.9rem;top:50%;transform:translateY(-50%);color:var(--gpcms-text-muted);font-size:0.85rem;z-index:2;"></i>
              <input type="text" class="form-control ps-5" id="complaint-search"
                     placeholder="Complaint ID, citizen name, title..." autocomplete="off">
            </div>
          </div>

          <!-- Status Filter -->
          <div class="col-xl-2 col-md-6 col-sm-6">
            <label class="form-label" for="filter-status">Status</label>
            <select class="form-select" id="filter-status">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="assigned">Assigned</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
            </select>
          </div>

          <!-- Category Filter -->
          <div class="col-xl-2 col-md-6 col-sm-6">
            <label class="form-label" for="filter-category">Category</label>
            <!-- PHP: populate from categories table -->
            <select class="form-select" id="filter-category">
              <option value="">All Categories</option>
              <option value="Water Supply">Water Supply</option>
              <option value="Street Lights">Street Lights</option>
              <option value="Drainage Cleanliness">Drainage Cleanliness</option>
              <option value="Road Repair">Road Repair</option>
              <option value="Sanitation">Sanitation</option>
              <option value="Health &amp; Hygiene">Health &amp; Hygiene</option>
            </select>
          </div>

          <!-- Village / Ward Filter -->
          <div class="col-xl-2 col-md-6 col-sm-6">
            <label class="form-label" for="filter-village">Village / Ward</label>
            <!-- PHP: populate from villages table -->
            <select class="form-select" id="filter-village">
              <option value="">All Villages</option>
              <option value="Rampur Ward 3">Rampur Ward 3</option>
              <option value="Navghar Ward 1">Navghar Ward 1</option>
              <option value="Shivar Ward 2">Shivar Ward 2</option>
              <option value="Kamlapur Ward 4">Kamlapur Ward 4</option>
              <option value="Dhanora Ward 5">Dhanora Ward 5</option>
            </select>
          </div>

          <!-- Assigned Officer Filter -->
          <div class="col-xl-2 col-md-6 col-sm-6">
            <label class="form-label" for="filter-officer">Assigned Officer</label>
            <!-- PHP: populate from officers/users table -->
            <select class="form-select" id="filter-officer">
              <option value="">All Officers</option>
              <option value="A. Patil">A. Patil</option>
              <option value="R. Deshmukh">R. Deshmukh</option>
              <option value="S. Kulkarni">S. Kulkarni</option>
              <option value="M. Jadhav">M. Jadhav</option>
              <option value="Unassigned">Unassigned</option>
            </select>
          </div>

          <!-- Date Filter -->
          <div class="col-xl-1 col-md-6 col-sm-6">
            <label class="form-label" for="filter-date">Date</label>
            <input type="date" class="form-control" id="filter-date">
          </div>

          <!-- Clear Button -->
          <div class="col-xl-auto col-md-6 col-sm-6">
            <label class="form-label d-none d-xl-block">&nbsp;</label>
            <button class="btn btn-gpcms-secondary w-100" id="btn-clear-filters" title="Clear all filters">
              <i class="bi bi-x-circle"></i> Clear
            </button>
          </div>

        </div>
      </div>

      <!-- ================================================
           Complaints Table
           PHP Integration: Replace tbody rows with PHP while/foreach loop
           ================================================ -->
      <div class="card-panel mb-0">
        <div class="card-header-clean">
          <h5 class="card-title">
            <i class="bi bi-list-task"></i> Complaints Register
            <span class="badge bg-secondary ms-2" id="complaints-count">9</span>
          </h5>
          <span class="text-muted small">
            Showing <span id="complaints-visible-count">9</span> of 9 records
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-custom align-middle" id="complaints-table">
            <thead>
              <tr>
                <th>Complaint ID</th>
                <th>Image</th>
                <th>Citizen Name</th>
                <th>Complaint Title</th>
                <th>Category</th>
                <th>Village / Ward</th>
                <th>Date</th>
                <th>Assigned Officer</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody id="complaints-table-body">
            <?php
            if (isset($result) && $result instanceof mysqli_result) {
              while($row = mysqli_fetch_assoc($result)) {
                $status   = isset($row['status'])   ? htmlspecialchars($row['status'])   : 'pending';
                $category = isset($row['category']) ? htmlspecialchars($row['category']) : 'General';
                $village  = isset($row['village'])  ? htmlspecialchars($row['village'])  : '';
                $officer  = isset($row['officer'])  ? htmlspecialchars($row['officer'])  : 'Unassigned';
                $dateVal  = isset($row['date'])     ? htmlspecialchars($row['date'])     : '';
                $id       = isset($row['id'])       ? htmlspecialchars($row['id'])       : '';
                $title    = isset($row['title'])    ? htmlspecialchars($row['title'])    : '';
                $citizen  = isset($row['citizen_name']) ? htmlspecialchars($row['citizen_name']) : '';
                $image    = isset($row['image'])    ? htmlspecialchars($row['image'])    : 'assets/complaints/no-image.png';
                ?>
                <tr data-status="<?php echo $status; ?>"
                    data-category="<?php echo $category; ?>"
                    data-village="<?php echo $village; ?>"
                    data-officer="<?php echo $officer; ?>"
                    data-date="<?php echo $dateVal; ?>">
                  <td><a href="complaint_details.php?id=<?php echo $id; ?>" class="fw-bold text-primary">#<?php echo $id; ?></a></td>
                  <td>
                    <div class="complaint-thumb">
                      <img src="<?php echo $image; ?>" alt="Complaint Photo" onerror="this.src='assets/complaints/no-image.png'">
                    </div>
                  </td>
                  <td><?php echo $citizen; ?></td>
                  <td><?php echo $title; ?></td>
                  <td><?php echo $category; ?></td>
                  <td><?php echo $village; ?></td>
                  <td><?php echo $dateVal; ?></td>
                  <td><?php echo $officer === 'Unassigned' ? '<span class="text-muted small fst-italic">Unassigned</span>' : $officer; ?></td>
                  <td>
                    <span class="badge-gpcms badge-<?php echo str_replace('_', '-', $status); ?>">
                      <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                    </span>
                  </td>
                  <td>
                    <a href="complaint_details.php?id=<?php echo $id; ?>" class="btn btn-sm btn-gpcms-primary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
                <?php
              }
            } else {
              // Static Fallback Data
              ?>

              <tr data-status="pending"
                  data-category="Water Supply"
                  data-village="Rampur Ward 3"
                  data-officer="Unassigned"
                  data-date="2026-07-22">
                <td><a href="complaint_details.php?id=GPC-2026-0891" class="fw-bold text-primary">#GPC-2026-0891</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample1.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Suresh Patil</td>
                <td>Water leakage near market area</td>
                <td>Water Supply</td>
                <td>Rampur Ward 3</td>
                <td>22 Jul 2026</td>
                <td><span class="text-muted small fst-italic">Unassigned</span></td>
                <td><span class="badge-gpcms badge-pending"><i class="bi bi-clock"></i> Pending</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0891" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="assigned"
                  data-category="Street Lights"
                  data-village="Navghar Ward 1"
                  data-officer="R. Deshmukh"
                  data-date="2026-07-21">
                <td><a href="complaint_details.php?id=GPC-2026-0890" class="fw-bold text-primary">#GPC-2026-0890</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample2.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Meena Deshmukh</td>
                <td>Street light not working for 2 weeks</td>
                <td>Street Lights</td>
                <td>Navghar Ward 1</td>
                <td>21 Jul 2026</td>
                <td>R. Deshmukh</td>
                <td><span class="badge-gpcms badge-assigned"><i class="bi bi-person-check"></i> Assigned</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0890" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="in_progress"
                  data-category="Drainage Cleanliness"
                  data-village="Shivar Ward 2"
                  data-officer="A. Patil"
                  data-date="2026-07-21">
                <td><a href="complaint_details.php?id=GPC-2026-0889" class="fw-bold text-primary">#GPC-2026-0889</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample3.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Ramesh Shinde</td>
                <td>Drainage blocked causing flooding in lane</td>
                <td>Drainage Cleanliness</td>
                <td>Shivar Ward 2</td>
                <td>21 Jul 2026</td>
                <td>A. Patil</td>
                <td><span class="badge-gpcms badge-in-progress"><i class="bi bi-arrow-repeat"></i> In Progress</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0889" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="resolved"
                  data-category="Road Repair"
                  data-village="Kamlapur Ward 4"
                  data-officer="S. Kulkarni"
                  data-date="2026-07-20">
                <td><a href="complaint_details.php?id=GPC-2026-0888" class="fw-bold text-primary">#GPC-2026-0888</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/no-image.png" alt="No Image">
                  </div>
                </td>
                <td>Anita Kadam</td>
                <td>Pothole on main road causing accidents</td>
                <td>Road Repair</td>
                <td>Kamlapur Ward 4</td>
                <td>20 Jul 2026</td>
                <td>S. Kulkarni</td>
                <td><span class="badge-gpcms badge-resolved"><i class="bi bi-check2"></i> Resolved</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0888" class="btn btn-sm btn-gpcms-secondary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="pending"
                  data-category="Sanitation"
                  data-village="Dhanora Ward 5"
                  data-officer="Unassigned"
                  data-date="2026-07-19">
                <td><a href="complaint_details.php?id=GPC-2026-0887" class="fw-bold text-primary">#GPC-2026-0887</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample1.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Vijay Nair</td>
                <td>Garbage not collected for 10 days</td>
                <td>Sanitation</td>
                <td>Dhanora Ward 5</td>
                <td>19 Jul 2026</td>
                <td><span class="text-muted small fst-italic">Unassigned</span></td>
                <td><span class="badge-gpcms badge-pending"><i class="bi bi-clock"></i> Pending</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0887" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="assigned"
                  data-category="Water Supply"
                  data-village="Rampur Ward 3"
                  data-officer="M. Jadhav"
                  data-date="2026-07-19">
                <td><a href="complaint_details.php?id=GPC-2026-0886" class="fw-bold text-primary">#GPC-2026-0886</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample2.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Priya Joshi</td>
                <td>Low water pressure in entire colony</td>
                <td>Water Supply</td>
                <td>Rampur Ward 3</td>
                <td>19 Jul 2026</td>
                <td>M. Jadhav</td>
                <td><span class="badge-gpcms badge-assigned"><i class="bi bi-person-check"></i> Assigned</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0886" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="in_progress"
                  data-category="Health &amp; Hygiene"
                  data-village="Navghar Ward 1"
                  data-officer="A. Patil"
                  data-date="2026-07-18">
                <td><a href="complaint_details.php?id=GPC-2026-0885" class="fw-bold text-primary">#GPC-2026-0885</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/no-image.png" alt="No Image">
                  </div>
                </td>
                <td>Mohan Rane</td>
                <td>Open drain near school posing health risk</td>
                <td>Health &amp; Hygiene</td>
                <td>Navghar Ward 1</td>
                <td>18 Jul 2026</td>
                <td>A. Patil</td>
                <td><span class="badge-gpcms badge-in-progress"><i class="bi bi-arrow-repeat"></i> In Progress</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0885" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="resolved"
                  data-category="Street Lights"
                  data-village="Shivar Ward 2"
                  data-officer="R. Deshmukh"
                  data-date="2026-07-17">
                <td><a href="complaint_details.php?id=GPC-2026-0884" class="fw-bold text-primary">#GPC-2026-0884</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample3.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Kavita Mane</td>
                <td>All street lights broken in Ward 2</td>
                <td>Street Lights</td>
                <td>Shivar Ward 2</td>
                <td>17 Jul 2026</td>
                <td>R. Deshmukh</td>
                <td><span class="badge-gpcms badge-resolved"><i class="bi bi-check2"></i> Resolved</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0884" class="btn btn-sm btn-gpcms-secondary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

              <tr data-status="pending"
                  data-category="Road Repair"
                  data-village="Kamlapur Ward 4"
                  data-officer="Unassigned"
                  data-date="2026-07-16">
                <td><a href="complaint_details.php?id=GPC-2026-0883" class="fw-bold text-primary">#GPC-2026-0883</a></td>
                <td>
                  <div class="complaint-thumb">
                    <img src="assets/complaints/sample1.jpg" alt="Complaint Photo"
                         onerror="this.src='assets/complaints/no-image.png'">
                  </div>
                </td>
                <td>Arun Bhosale</td>
                <td>Road broken outside post office</td>
                <td>Road Repair</td>
                <td>Kamlapur Ward 4</td>
                <td>16 Jul 2026</td>
                <td><span class="text-muted small fst-italic">Unassigned</span></td>
                <td><span class="badge-gpcms badge-pending"><i class="bi bi-clock"></i> Pending</span></td>
                <td>
                  <a href="complaint_details.php?id=GPC-2026-0883" class="btn btn-sm btn-gpcms-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>

            <?php
            }
            ?>
            </tbody>

            <!-- Empty state (shown when no rows match filters) -->
            <tbody id="no-complaints-data" style="display: none;">
              <tr>
                <td colspan="10" class="text-center py-5">
                  <div class="table-empty-icon">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                  </div>
                  <div class="text-muted mt-2 fw-500">No complaints found matching your current filters.</div>
                  <button class="btn btn-sm btn-gpcms-secondary mt-3" id="btn-reset-filters">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset All Filters
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>

    <!-- Footer (reused) -->
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

<!-- JS Dependencies (same as all other pages) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/utils.js"></script>
<script src="js/admin.js"></script>

</body>
</html>
