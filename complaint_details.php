<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gram Panchayat Complaint Management System - Complaint Details">
  <title>Complaint Details | GPCMS Government Portal</title>

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
       Sidebar Navigation (reused structure)
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
        <!-- Active on parent "All Complaints" since this is a sub-detail page -->
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

    <!-- Top Navigation Header (reused) -->
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
    <!-- PHP: $complaint_id = $_GET['id'] ?? ''; $complaint = getComplaintById($complaint_id); -->
    <main class="content-area">

      <!-- Page Header with Back Button -->
      <div class="page-header-block">
        <div>
          <h1 class="page-title">Complaint Details</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="view_complaints.php">Complaints</a></li>
              <li class="breadcrumb-item active" aria-current="page">#GPC-2026-0891</li>
            </ol>
          </nav>
        </div>
        <div class="top-actions d-flex gap-2">
          <a href="view_complaints.php" class="btn btn-gpcms-secondary">
            <i class="bi bi-arrow-left"></i> Back to Complaints
          </a>
          <button class="btn btn-gpcms-primary" onclick="GPCMS.printCurrentPage()">
            <i class="bi bi-printer"></i> Print
          </button>
        </div>
      </div>

      <!-- ================================================
           Two-Column Detail Layout
           ================================================ -->
      <div class="row g-3">

        <!-- ============================================
             LEFT COLUMN: Complaint Info + Image
             ============================================ -->
        <div class="col-lg-8">

          <!-- Complaint Information Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title">
                <i class="bi bi-file-earmark-text-fill"></i> Complaint Information
              </h5>
              <!-- PHP: echo badge based on $complaint['status'] -->
              <span class="badge-gpcms badge-pending status-badge-large">
                <i class="bi bi-clock"></i> Pending
              </span>
            </div>

            <!-- Detail Info Grid -->
            <div class="detail-info-grid mb-4">

              <div class="detail-info-item">
                <span class="detail-info-label">Complaint ID</span>
                <!-- PHP: echo $complaint['complaint_id'] -->
                <span class="detail-info-value fw-bold text-primary" id="complaint-detail-id">#GPC-2026-0891</span>
              </div>

              <div class="detail-info-item">
                <span class="detail-info-label">Submission Date</span>
                <!-- PHP: echo date('d M Y', strtotime($complaint['created_at'])) -->
                <span class="detail-info-value">22 Jul 2026</span>
              </div>

              <div class="detail-info-item">
                <span class="detail-info-label">Category</span>
                <!-- PHP: echo $complaint['category_name'] -->
                <span class="detail-info-value">
                  <span class="badge-gpcms" style="background-color:var(--gpcms-primary-light);color:var(--gpcms-primary);">
                    <i class="bi bi-tag-fill"></i> Water Supply
                  </span>
                </span>
              </div>

              <div class="detail-info-item">
                <span class="detail-info-label">Village / Ward</span>
                <!-- PHP: echo $complaint['village'] -->
                <span class="detail-info-value"><i class="bi bi-geo-alt-fill text-muted me-1"></i>Rampur Ward 3</span>
              </div>

              <div class="detail-info-item">
                <span class="detail-info-label">Assigned Officer</span>
                <!-- PHP: echo $complaint['assigned_to'] ?? 'Unassigned' -->
                <span class="detail-info-value text-muted fst-italic" id="complaint-detail-officer">Unassigned</span>
              </div>

              <div class="detail-info-item">
                <span class="detail-info-label">Last Updated</span>
                <span class="detail-info-value">22 Jul 2026, 09:45 AM</span>
              </div>

            </div>

            <!-- Complaint Title -->
            <div class="mb-3">
              <span class="detail-info-label">Complaint Title</span>
              <div class="detail-info-value fs-6 fw-semibold mt-1">
                <!-- PHP: echo htmlspecialchars($complaint['title']) -->
                Water leakage near market area causing road damage
              </div>
            </div>

          </div>

          <!-- Citizen Details Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-person-badge-fill"></i> Citizen Details</h5>
            </div>
            <div class="detail-info-grid">
              <div class="detail-info-item">
                <span class="detail-info-label">Full Name</span>
                <!-- PHP: echo $complaint['citizen_name'] -->
                <span class="detail-info-value">Suresh Patil</span>
              </div>
              <div class="detail-info-item">
                <span class="detail-info-label">Mobile Number</span>
                <!-- PHP: echo $complaint['mobile'] -->
                <span class="detail-info-value">+91 98765 43210</span>
              </div>
              <div class="detail-info-item">
                <span class="detail-info-label">Email Address</span>
                <!-- PHP: echo $complaint['email'] -->
                <span class="detail-info-value">suresh.patil@email.com</span>
              </div>
              <div class="detail-info-item">
                <span class="detail-info-label">Address</span>
                <!-- PHP: echo $complaint['address'] -->
                <span class="detail-info-value">Near Market Road, Rampur Ward 3, 412 208</span>
              </div>
            </div>
          </div>

          <!-- Complaint Description Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-card-text"></i> Complaint Description</h5>
            </div>
            <!-- PHP: echo nl2br(htmlspecialchars($complaint['description'])) -->
            <p class="mb-0" style="font-size:0.9rem; line-height:1.8; color:var(--gpcms-text);">
              There is a major water pipe leakage near the Rampur main market area. The leak has been ongoing for
              the past 5 days and is causing severe road damage due to continuous water flow. The pothole formed
              has made the road dangerous for two-wheelers. Multiple residents have complained but no action has
              been taken. Immediate repair of the pipe and road is requested. The leakage is visible at the
              junction of Market Road and Gandhi Nagar Cross Road.
            </p>
          </div>

          <!-- Complaint Image Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-image-fill"></i> Complaint Image</h5>
              <span class="text-muted small">Submitted by citizen</span>
            </div>
            <div class="complaint-image-full">
              <!-- PHP: $img = $complaint['image_path'] ? $complaint['image_path'] : 'assets/complaints/no-image.png' -->
              <img src="assets/complaints/sample1.jpg" alt="Complaint Photo"
                   onerror="this.src='assets/complaints/no-image.png'" id="complaint-detail-image">
            </div>
          </div>

        </div>

        <!-- ============================================
             RIGHT COLUMN: Status Panel + Timeline + Remarks
             ============================================ -->
        <div class="col-lg-4">

          <!-- Status Update Panel -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-arrow-repeat"></i> Update Status</h5>
            </div>

            <div class="mb-3">
              <label class="form-label" for="detail-status-select">Current Status</label>
              <!-- PHP: status value from DB pre-selected -->
              <select class="form-select" id="detail-status-select" name="status">
                <option value="pending" selected>Pending</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label" for="detail-officer-select">Assign Officer</label>
              <!-- PHP: officers fetched from users table -->
              <select class="form-select" id="detail-officer-select" name="assigned_officer">
                <option value="">-- Select Officer --</option>
                <option value="A. Patil">A. Patil</option>
                <option value="R. Deshmukh">R. Deshmukh</option>
                <option value="S. Kulkarni">S. Kulkarni</option>
                <option value="M. Jadhav">M. Jadhav</option>
              </select>
            </div>

            <!-- PHP: action="update_complaint.php" method="POST" -->
            <button class="btn btn-gpcms-primary w-100" id="btn-update-status" type="button"
                    onclick="GPCMS.showToast('Status updated successfully!', 'success', 'Complaint Updated')">
              <i class="bi bi-check2-circle"></i> Save Changes
            </button>
          </div>

          <!-- Activity Timeline Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-clock-history"></i> Activity Timeline</h5>
            </div>
            <!-- PHP: foreach($timeline as $event): -->
            <ul class="timeline">
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Complaint Submitted</div>
                  <div class="text-muted small">Citizen Suresh Patil submitted the complaint online.</div>
                  <div class="timeline-time">22 Jul 2026 &mdash; 09:12 AM</div>
                </div>
              </li>
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Complaint Received</div>
                  <div class="text-muted small">Complaint registered in GPCMS system. Auto ID assigned.</div>
                  <div class="timeline-time">22 Jul 2026 &mdash; 09:13 AM</div>
                </div>
              </li>
              <li class="timeline-item">
                <div class="timeline-content">
                  <div class="timeline-title">Under Review</div>
                  <div class="text-muted small">Gram Sevak Rajesh Sharma reviewing complaint details.</div>
                  <div class="timeline-time">22 Jul 2026 &mdash; 10:30 AM</div>
                </div>
              </li>
              <!-- PHP: endforeach; -->
              <!-- Placeholder for future events -->
              <li class="timeline-item" style="opacity:0.45;">
                <div class="timeline-content">
                  <div class="timeline-title text-muted">Assignment Pending</div>
                  <div class="text-muted small">Awaiting officer assignment.</div>
                  <div class="timeline-time">— Pending</div>
                </div>
              </li>
            </ul>
          </div>

          <!-- Remarks / Notes Card -->
          <div class="card-panel">
            <div class="card-header-clean">
              <h5 class="card-title"><i class="bi bi-chat-text-fill"></i> Remarks</h5>
            </div>

            <!-- Existing Remarks List -->
            <!-- PHP: foreach($remarks as $remark): -->
            <ul class="remarks-list mb-3">
              <li class="remark-item">
                <div class="remark-author"><i class="bi bi-person-circle me-1"></i> Rajesh Sharma (Gram Sevak)</div>
                <div class="remark-time">22 Jul 2026 &mdash; 10:32 AM</div>
                <div class="remark-text">Complaint verified. Water leakage confirmed. Pipe repair team to be dispatched.</div>
              </li>
            </ul>
            <!-- PHP: endforeach; -->

            <!-- Add Remark Form -->
            <!-- PHP: action="add_remark.php" method="POST" -->
            <form id="complaint-remarks-form" onsubmit="submitRemark(event)">
              <!-- PHP: <input type="hidden" name="complaint_id" value="<?php echo isset($complaint_id) ? htmlspecialchars($complaint_id) : ''; ?>"> -->
              <input type="hidden" name="complaint_id" value="GPC-2026-0891">
              <div class="mb-2">
                <label class="form-label" for="remark-text">Add Remark / Observation</label>
                <textarea class="form-control" id="remark-text" name="remark"
                          rows="3" maxlength="500"
                          placeholder="Enter your remark or field observation..."></textarea>
                <div class="text-muted small mt-1" id="remark-char-counter">0 / 500</div>
              </div>
              <button type="submit" class="btn btn-gpcms-primary btn-sm">
                <i class="bi bi-send-fill"></i> Submit Remark
              </button>
            </form>
          </div>

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

<script>
  /* ------------------------------------------------
     Complaint Details Page — Inline Logic
     (Isolated to this page only, not duplicating admin.js)
  ------------------------------------------------ */

  // Remark character counter (reuses existing textarea counter pattern from admin.js)
  (function () {
    const textarea = document.getElementById('remark-text');
    const counter  = document.getElementById('remark-char-counter');
    if (textarea && counter) {
      textarea.addEventListener('input', function () {
        counter.textContent = this.value.length + ' / 500';
      });
    }
  })();

  // Remark form submit handler (placeholder for PHP integration)
  function submitRemark(e) {
    e.preventDefault();
    const txt = document.getElementById('remark-text');
    if (!txt || !txt.value.trim()) {
      GPCMS.showToast('Please enter a remark before submitting.', 'warning');
      return;
    }
    // PHP: form submits to add_remark.php via POST — this JS block is replaced by server response
    GPCMS.showToast('Remark submitted successfully!', 'success', 'Remark Added');
    txt.value = '';
    document.getElementById('remark-char-counter').textContent = '0 / 500';
  }

  // Status badge live update (visual feedback before backend save)
  document.getElementById('detail-status-select').addEventListener('change', function () {
    const statusMap = {
      pending:     { cls: 'badge-pending',     icon: 'bi-clock',        label: 'Pending' },
      assigned:    { cls: 'badge-assigned',    icon: 'bi-person-check', label: 'Assigned' },
      in_progress: { cls: 'badge-in-progress', icon: 'bi-arrow-repeat', label: 'In Progress' },
      resolved:    { cls: 'badge-resolved',    icon: 'bi-check2',       label: 'Resolved' }
    };
    const sel = statusMap[this.value];
    if (!sel) return;
    const badge = document.querySelector('.card-panel .badge-gpcms.status-badge-large');
    if (badge) {
      badge.className = `badge-gpcms ${sel.cls} status-badge-large`;
      badge.innerHTML = `<i class="bi ${sel.icon}"></i> ${sel.label}`;
    }
  });
</script>

</body>
</html>
