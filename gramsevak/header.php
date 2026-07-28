<?php
/**
 * Shared Header Component - Gram Panchayat Complaint Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = $page_title ?? 'Gram Sevak Portal';
$active_page = $active_page ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gram Panchayat Complaint Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Gram Sevak CSS -->
    <link rel="stylesheet" href="../css/gramsevak.css">
</head>
<body>
    <div class="gpcms-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="gpcms-sidebar" id="gpcmsSidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-building-fill-gear"></i></div>
                <div class="brand-text">
                    <span class="main-title">GPCMS</span>
                    <span class="sub-title">Gram Sevak Portal</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="gramsevak_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view_complaints.php" class="nav-link <?php echo ($active_page === 'view_complaints') ? 'active' : ''; ?>">
                            <i class="bi bi-card-checklist"></i> <span>View Complaints</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="assigned_complaints.php" class="nav-link <?php echo ($active_page === 'assigned_complaints' || $active_page === 'assign_complaints') ? 'active' : ''; ?>">
                            <i class="bi bi-person-plus-fill"></i> <span>Assign Complaints</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="verify_complaint.php" class="nav-link <?php echo ($active_page === 'verify_complaint' || $active_page === 'verify_completed_work') ? 'active' : ''; ?>">
                            <i class="bi bi-patch-check-fill"></i> <span>Verify Work</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="update_complaint_status.php" class="nav-link <?php echo ($active_page === 'update_status') ? 'active' : ''; ?>">
                            <i class="bi bi-arrow-repeat"></i> <span>Update Status</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="complaint_category_management.php" class="nav-link <?php echo ($active_page === 'categories') ? 'active' : ''; ?>">
                            <i class="bi bi-grid-3x3-gap-fill"></i> <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="system_settings.php" class="nav-link <?php echo ($active_page === 'settings') ? 'active' : ''; ?>">
                            <i class="bi bi-sliders"></i> <span>System Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link <?php echo ($active_page === 'reports') ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i> <span>Reports</span>
                        </a>
                    </li>
                </ul>

                <hr class="sidebar-divider">

                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#notificationModal">
                            <i class="bi bi-bell-fill"></i> <span>Notifications</span>
                            <span class="badge rounded-pill bg-danger ms-auto">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person-circle"></i> <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-danger-custom" id="btnLogout">
                            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="gpcms-main-content">
            <!-- Header Bar -->
            <header class="gpcms-header">
                <div class="header-left">
                    <button class="btn btn-sidebar-toggle" id="sidebarToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="header-app-title">
                        <h1 class="h5 mb-0 font-weight-bold">Gram Panchayat Complaint Management System</h1>
                        <span class="text-muted small">Official Gram Sevak Administration Portal</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-clock d-none d-md-flex" id="liveClock">
                        <i class="bi bi-clock me-2"></i><span id="clockTime">--:--:--</span>
                    </div>

                    <div class="dropdown header-user-dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2"><i class="bi bi-person-fill"></i></div>
                            <div class="user-info text-start d-none d-sm-block">
                                <span class="user-name d-block"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Gram Sevak'); ?></span>
                                <span class="user-role badge badge-role"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Gram Sevak'); ?></span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="system_settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="dropdownLogout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
