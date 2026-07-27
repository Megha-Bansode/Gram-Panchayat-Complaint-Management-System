<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Sidebar & Header Navigation Include
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Provides reusable sidebar navigation (with automatic active state detection) and sticky navbar header.
 */

// Automatically determine current page filename for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Responsive Sidebar Navigation -->
<aside class="app-sidebar" aria-label="Sidebar Navigation">
  <div class="sidebar-logo">
    <img src="../assets/field/gpcms_official_logo.png?v=<?php echo time(); ?>" alt="GPCMS Official Logo" class="sidebar-logo-img" style="width: 56px !important; height: 56px !important; max-width: 56px !important; max-height: 56px !important; object-fit: cover !important; border-radius: 50% !important; flex-shrink: 0 !important;">
    <div class="sidebar-logo-text">
      <h5>GPCMS</h5>
      <span>Gram Panchayat System</span>
    </div>
  </div>
  
  <ul class="sidebar-menu">
    <li class="<?php echo ($current_page === 'field_dashboard.php') ? 'active' : ''; ?>">
      <a href="field_dashboard.php">
        <i class="bi bi-house-door"></i>
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
    <li>
      <a href="../logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </a>
    </li>
  </ul>
  




  <div class="sidebar-footer">
    <div class="sidebar-avatar-card">
      <div class="sidebar-avatar-circle">
        <i class="bi bi-person-fill"></i>
      </div>
      <div>
        <div class="sidebar-user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Field Officer'); ?></div>
        <div class="sidebar-user-role"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Field Officer'); ?></div>
        <div class="sidebar-status-online">
          <span class="online-dot"></span> Online
        </div>
      </div>
    </div>
  </div>
</aside>

<!-- Main Content Area -->
<main class="main-content">
  
  <!-- Responsive Header Navigation -->
  <header class="app-navbar">
    <div class="navbar-left">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Navigation Sidebar">
        <i class="bi bi-list"></i>
      </button>
      <img src="../assets/field/gpcms_official_logo.png?v=<?php echo time(); ?>" alt="GPCMS Emblem" class="navbar-logo-icon me-2" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
      <span class="app-title d-none d-md-inline">Gram Panchayat Complaint Management System</span>
    </div>
    
    <div class="navbar-right">
      <!-- Notification Bell with Dropdown Menu -->
      <div class="notification-bell" id="notificationBellBtn" aria-label="Notifications" tabindex="0">
        <i class="bi bi-bell-fill"></i>
        <span class="notification-badge">3</span>
        
        <!-- Mock Notifications Dropdown -->
        <div class="notification-dropdown" id="notificationDropdown">
          <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-2">
            <span class="fw-bold text-dark small">Notifications</span>
            <span class="badge bg-danger">3 New</span>
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
          <div class="notification-item">
            <div class="fw-semibold text-dark">Verification Approved</div>
            <div class="text-muted extra-small">CMP-0008 marked resolved by Gram Sevak.</div>
            <div class="text-primary extra-small mt-1">Yesterday</div>
          </div>
        </div>
      </div>
      
      <!-- Logged-in Field Officer Profile Block -->
      <div class="user-profile">
        <i class="bi bi-person-circle fs-4 text-muted"></i>
        <div class="d-none d-sm-block">
          <!-- TODO: Replace fallback mock user info with session variables ($_SESSION['full_name'] and $_SESSION['role_name']) once authentication session middleware is integrated -->
          <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Field Officer'); ?></div>
          <div class="user-role"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Field Officer'); ?></div>
        </div>
      </div>
      
      <!-- Logout Link -->
      <a href="../logout.php" class="logout-link">
        <i class="bi bi-box-arrow-right"></i>
        <span class="d-none d-sm-inline">Logout</span>
      </a>
    </div>
  </header>
  
  <!-- Dynamic Page Content Area -->
  <section class="module-container">
