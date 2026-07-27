<?php
/**
 * FILE: citizen/sidebar.php
 * PURPOSE: Reusable Vertical Sidebar Component
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- ═══════════════════════════════════════════════════════
     VERTICAL SIDEBAR
     ═══════════════════════════════════════════════════════ -->
<aside class="citizen-sidebar" id="citizenSidebar">
    <!-- Sidebar Header (Brand Logo) -->
    <div class="citizen-sidebar-header">
        <a href="citizen_dashboard.php" class="citizen-sidebar-brand">
            <span class="citizen-brand-icon"><i class="bi bi-building"></i></span>
            <span>GPCMS</span>
        </a>
        <button class="citizen-nav-toggler d-lg-none" type="button" id="sidebarCloseBtn" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Vertical Navigation Menu -->
    <nav class="citizen-sidebar-nav">
        <a href="citizen_dashboard.php" class="citizen-sidebar-link <?php echo $current_page === 'citizen_dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="my_complaints.php" class="citizen-sidebar-link <?php echo $current_page === 'my_complaints.php' ? 'active' : ''; ?>">
            <i class="bi bi-list-ul"></i>
            <span>My Complaints</span>
        </a>

        <a href="register_complaint.php" class="citizen-sidebar-link <?php echo $current_page === 'register_complaint.php' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Register Complaint</span>
        </a>

        <a href="track_complaint.php" class="citizen-sidebar-link <?php echo $current_page === 'track_complaint.php' ? 'active' : ''; ?>">
            <i class="bi bi-search"></i>
            <span>Track Complaint</span>
        </a>

        <a href="notifications.php" class="citizen-sidebar-link <?php echo $current_page === 'notifications.php' ? 'active' : ''; ?>">
            <i class="bi bi-bell"></i>
            <span>Notifications</span>
        </a>

        <a href="profile.php" class="citizen-sidebar-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>

        <div class="mt-auto pt-3">
            <a href="../includes/logout.php" class="citizen-sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>
