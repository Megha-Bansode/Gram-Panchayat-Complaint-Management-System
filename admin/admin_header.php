<?php
/**
 * Admin Portal Header - Gram Panchayat Complaint Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = $page_title ?? 'Admin Portal';
$active_page = $active_page ?? 'dashboard';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Enforce Super Admin role
$user = requireRole(['Super Admin', 'Gram Panchayat Admin']);

// Ensure global session values for layout
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 103;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Super Admin User';
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Super Admin';

// Count notifications
$unread_count = 0;
if (isset($conn) && $conn !== null) {
    $notif_stmt = $conn->query("SELECT COUNT(*) as unread FROM notifications WHERE is_read = 0");
    if ($notif_stmt) {
        $notif_row = $notif_stmt->fetch_assoc();
        $unread_count = (int)$notif_row['unread'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gram Panchayat Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="gpcms-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="gpcms-sidebar" id="gpcmsSidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <div class="brand-text">
                    <span class="main-title">GPCMS</span>
                    <span class="sub-title">Admin Console</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i> <span>Admin Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pending_resolved_report.php" class="nav-link <?php echo ($active_page === 'pending_resolved') ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i> <span>Pending / Resolved</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="village_report.php" class="nav-link <?php echo ($active_page === 'village_report') ? 'active' : ''; ?>">
                            <i class="bi bi-geo-alt-fill"></i> <span>Village Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../gramsevak/gramsevak_dashboard.php" class="nav-link">
                            <i class="bi bi-arrow-left-right"></i> <span>Gram Sevak Portal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../gramsevak/complaint_category_management.php" class="nav-link">
                            <i class="bi bi-grid-3x3-gap-fill"></i> <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../gramsevak/system_settings.php" class="nav-link">
                            <i class="bi bi-sliders"></i> <span>System Settings</span>
                        </a>
                    </li>
                </ul>

                <hr class="sidebar-divider">

                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="../includes/logout.php" class="nav-link text-danger-custom" id="btnLogout">
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
                        <h1 class="h5 mb-0 font-weight-bold">Gram Panchayat Admin Panel</h1>
                        <span class="text-muted small">System Operations & Reports Console</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-clock d-none d-md-flex" id="liveClock">
                        <i class="bi bi-clock me-2"></i><span id="clockTime">--:--:--</span>
                    </div>

                    <div class="dropdown header-user-dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2"><i class="bi bi-shield-fill"></i></div>
                            <div class="user-info text-start d-none d-sm-block">
                                <span class="user-name d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <span class="user-role badge badge-role"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../gramsevak/system_settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../includes/logout.php" id="dropdownLogout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
