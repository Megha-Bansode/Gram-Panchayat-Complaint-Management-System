<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Sidebar & Header Navigation Include
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Provides reusable sidebar navigation and top navbar matching GPCMS Portal v2.0 theme.
 */

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch notifications for the officer from the database
$officer_notifications = [];
$unread_officer_count = 0;
if (isset($conn) && $conn !== null && isset($officer_id)) {
    try {
        $notif_stmt = $conn->prepare("
            SELECT notification_id, complaint_id, message, is_read, created_at 
            FROM notifications 
            WHERE user_id = ? OR complaint_id IN (SELECT complaint_id FROM complaints WHERE assigned_to = ?)
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $notif_stmt->bind_param("ii", $officer_id, $officer_id);
        $notif_stmt->execute();
        $notif_res = $notif_stmt->get_result();
        if ($notif_res) {
            while ($row = $notif_res->fetch_assoc()) {
                $officer_notifications[] = $row;
                if ((int)$row['is_read'] === 0) {
                    $unread_officer_count++;
                }
            }
        }
        $notif_stmt->close();
    } catch (Throwable $e) {
        error_log("Sidebar notifications query error: " . $e->getMessage());
    }
}
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
    <li class="<?php echo ($current_page === 'notifications.php') ? 'active' : ''; ?>">
      <a href="notifications.php">
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
    

    
    <div class="navbar-right d-flex align-items-center gap-3">
      <!-- Live Date & Clock Display -->
      <div class="navbar-clock text-muted d-none d-md-block text-end" style="font-size: 0.78rem;">
        <div id="liveDateStr">Mon, 27 Jul, 2026</div>
        <div id="liveTimeStr" class="fw-semibold text-dark">01:36:55 pm</div>
      </div>
      
      <!-- Notification Bell -->
      <div class="notification-bell position-relative" id="notificationBellBtn" aria-label="Notifications" tabindex="0" style="cursor: pointer;">
        <i class="bi bi-bell-fill text-muted fs-5"></i>
        <?php if ($unread_officer_count > 0): ?>
          <span class="notification-badge" style="position: absolute; top: -4px; right: -4px; background: #D9534F; color: #FFF; font-size: 0.65rem; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;"><?php echo $unread_officer_count; ?></span>
        <?php endif; ?>
        
        <!-- Live Notifications Dropdown -->
        <div class="notification-dropdown" id="notificationDropdown" style="width: 280px; max-height: 380px; overflow-y: auto;">
          <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-2">
            <span class="fw-bold text-dark small">Notifications</span>
            <?php if ($unread_officer_count > 0): ?>
              <span class="badge bg-danger"><?php echo $unread_officer_count; ?> New</span>
            <?php else: ?>
              <span class="badge bg-secondary">0 New</span>
            <?php endif; ?>
          </div>
          <?php if (empty($officer_notifications)): ?>
            <div class="p-3 text-center text-muted small">No notifications found.</div>
          <?php else: ?>
            <?php foreach ($officer_notifications as $notif): ?>
              <?php
                $created_time = strtotime($notif['created_at']);
                $diff = time() - $created_time;
                if ($diff < 60) $time_str = "Just now";
                elseif ($diff < 3600) $time_str = floor($diff / 60) . " mins ago";
                elseif ($diff < 86400) $time_str = floor($diff / 3600) . " hours ago";
                else $time_str = date('d M Y', $created_time);
              ?>
              <div class="notification-item" style="padding: 8px 10px; border-bottom: 1px solid #F1ECE3; <?php echo (int)$notif['is_read'] === 0 ? 'background-color: #FCF8F2;' : ''; ?>">
                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">Complaint #<?php echo htmlspecialchars((string)$notif['complaint_id']); ?></div>
                <div class="text-muted extra-small" style="font-size: 0.72rem; line-height: 1.3; margin-top: 2px;"><?php echo htmlspecialchars($notif['message']); ?></div>
                <div class="text-primary extra-small mt-1" style="font-size: 0.68rem;"><?php echo $time_str; ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
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
