<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Sidebar & Header Navigation Include
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Provides reusable sidebar navigation and top navbar matching GPCMS Portal v2.0 theme.
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Responsive Sidebar Navigation Matching Reference UI -->
<aside class="app-sidebar" aria-label="Sidebar Navigation">
  
  <!-- Sidebar Header Brand -->
  <div class="sidebar-logo">
    <div class="sidebar-brand-icon me-2">
      <i class="bi bi-bank"></i>
    </div>
    <div class="sidebar-logo-text">
      <h5>GPCMS PORTAL</h5>
    </div>
  </div>
  
  <!-- Section 1: Field Officer Panel -->
  <div class="sidebar-section-label">FIELD OFFICER PANEL</div>
  <ul class="sidebar-menu">
    <li class="<?php echo ($current_page === 'field_dashboard.php') ? 'active' : ''; ?>">
      <a href="field_dashboard.php">
        <i class="bi bi-columns-gap"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="<?php echo ($current_page === 'assigned_complaints.php' || $current_page === 'complaint_details.php') ? 'active' : ''; ?>">
      <a href="assigned_complaints.php">
        <i class="bi bi-clipboard-check"></i>
        <span>Assigned Complaints</span>
      </a>
    </li>
    <li class="<?php echo ($current_page === 'save_progress.php') ? 'active' : ''; ?>">
      <a href="save_progress.php">
        <i class="bi bi-pencil-square"></i>
        <span>Save Progress</span>
      </a>
    </li>
  </ul>
  
  <!-- Section 2: Account Options -->
  <div class="sidebar-section-label mt-4">ACCOUNT OPTIONS</div>
  <ul class="sidebar-menu">
    <li>
      <a href="#">
        <i class="bi bi-bell"></i>
        <span>Notifications</span>
      </a>
    </li>
  </ul>
  
  <!-- Footer Logout & Portal Metadata -->
  <div class="sidebar-footer mt-auto pt-4">
    <a href="../includes/logout.php" class="sidebar-logout-btn">
      <i class="bi bi-box-arrow-right me-2"></i> Log out
    </a>
    
    <div class="sidebar-meta-info mt-4">
      <div>Digital India Project</div>
      <div class="fw-bold">GPCMS Portal v2.0</div>
    </div>
  </div>

</aside>

<!-- Main Content Area -->
<main class="main-content">
  
  <!-- Top Navbar Header Matching Reference UI -->
  <header class="app-navbar">
    <div class="navbar-left d-flex align-items-center gap-3">
      <button class="sidebar-toggle border-0 bg-transparent text-muted me-1" id="sidebarToggle" aria-label="Toggle Navigation Sidebar">
        <i class="bi bi-list fs-4"></i>
      </button>
      <div class="d-flex align-items-center gap-2.5">
        <img src="../assets/field/gpcms_official_logo.png?v=<?php echo time(); ?>" alt="GPCMS Logo" class="navbar-emblem-logo" style="height: 38px; width: auto; object-fit: contain;">
        <div class="navbar-brand-titles">
          <div class="navbar-hindi-title fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.1;">ग्राम पंचायत डिजिटल पोर्टल</div>
          <div class="navbar-sub-title text-muted" style="font-size: 0.72rem;">Gram Panchayat Complaint Management System (GPCMS)</div>
        </div>
      </div>
    </div>
    
    <!-- Center Search Bar Pill -->
    <div class="navbar-search-pill d-none d-lg-flex align-items-center px-3 py-1.5" style="background: #FFFFFF; border: 1px solid #E2D9CD; border-radius: 50px;">
      <input type="text" class="form-control border-0 bg-transparent p-0 shadow-none" placeholder="Search complaints..." style="font-size: 0.82rem; width: 170px;">
      <i class="bi bi-search text-muted ms-2" style="font-size: 0.85rem;"></i>
    </div>
    
    <div class="navbar-right d-flex align-items-center gap-3">
      <!-- Live Date & Clock Display -->
      <div class="navbar-clock text-muted d-none d-md-block text-end" style="font-size: 0.78rem;">
        <div id="liveDateStr">Mon, 27 Jul, 2026</div>
        <div id="liveTimeStr" class="fw-semibold text-dark">01:36:55 pm</div>
      </div>
      
      <!-- Notification Bell -->
      <div class="notification-bell position-relative" id="notificationBellBtn" aria-label="Notifications" tabindex="0" style="cursor: pointer;">
        <i class="bi bi-bell-fill text-muted fs-5"></i>
        <span class="notification-badge" style="position: absolute; top: -4px; right: -4px; background: #D9534F; color: #FFF; font-size: 0.65rem; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">2</span>
        
        <!-- Mock Notifications Dropdown -->
        <div class="notification-dropdown" id="notificationDropdown">
          <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-2">
            <span class="fw-bold text-dark small">Notifications</span>
            <span class="badge bg-danger">2 New</span>
          </div>
          <div class="notification-item">
            <div class="fw-semibold text-dark">New Assignment: CMP-0041</div>
            <div class="text-muted extra-small">Drainage Overflow assigned to you.</div>
            <div class="text-primary extra-small mt-1">10 mins ago</div>
          </div>
          <div class="notification-item">
            <div class="fw-semibold text-dark">Update Pending: CMP-0012</div>
            <div class="text-muted extra-small">Road Potholes Repair progress report due today.</div>
            <div class="text-primary extra-small mt-1">1 hour ago</div>
          </div>
        </div>
      </div>
      
      <!-- User Profile Badge Dropdown -->
      <div class="dropdown">
        <div class="user-profile-badge d-flex align-items-center gap-2 p-1 pe-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
          <div class="user-avatar-circle d-flex align-items-center justify-content-center fw-bold text-dark" style="width: 34px; height: 34px; border-radius: 50%; background: #FAF6F0; border: 1.5px solid #DCC9A7; font-size: 0.78rem;">
            <?php 
              $name = $_SESSION['full_name'] ?? 'FO';
              $initials = mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
              echo htmlspecialchars($initials);
            ?>
          </div>
          <div class="user-info-text d-none d-sm-block text-start" style="line-height: 1.1;">
            <div class="user-name-title fw-bold text-dark" style="font-size: 0.84rem;"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Field Officer'); ?></div>
            <div class="user-role-title text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Field Officer'); ?></div>
          </div>
          <i class="bi bi-chevron-down extra-small text-muted ms-1" style="font-size: 0.75rem;"></i>
        </div>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 14px; min-width: 220px; font-size: 0.88rem; padding: 0.5rem; border: 1px solid #E2D9CD !important; margin-top: 8px;">
          <li class="px-3 py-2 border-bottom mb-1 bg-light rounded-top">
            <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></div>
            <div class="text-muted extra-small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Field Officer'); ?> Account</div>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold" href="../includes/logout.php" style="border-radius: 8px;">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </header>
  
  <!-- Module Container Wrap -->
  <section class="module-container">
