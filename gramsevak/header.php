<?php
/**
 * Shared Header Component - Gram Panchayat Complaint Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = $page_title ?? 'Gram Sevak Portal';
$active_page = $active_page ?? 'dashboard';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';

$users_data = [];
$categories_data = [];
$complaints_data = [];
$history_data = [];
$notifications_data = [];

if (isset($conn) && $conn !== null) {
    // 1. Fetch Users (excluding Citizens)
    $u_stmt = $conn->query("
        SELECT u.user_id, u.full_name, u.mobile_number, u.role_id, r.role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id 
        WHERE LOWER(r.role_name) != 'citizen'
    ");
    if ($u_stmt) {
        while ($row = $u_stmt->fetch_assoc()) {
            $users_data[] = [
                'user_id' => (int)$row['user_id'],
                'full_name' => $row['full_name'],
                'mobile_number' => $row['mobile_number'] ?? '',
                'role_id' => (int)$row['role_id'],
                'role_name' => $row['role_name']
            ];
        }
    }

    // 2. Fetch Categories
    $cat_stmt = $conn->query("
        SELECT c.category_id, c.category_name, c.description,
               (SELECT COUNT(*) FROM complaints co WHERE co.category_id = c.category_id) AS cnt
        FROM categories c
    ");
    if ($cat_stmt) {
        while ($row = $cat_stmt->fetch_assoc()) {
            $categories_data[] = [
                'category_id' => (int)$row['category_id'],
                'category_name' => $row['category_name'],
                'description' => $row['description'] ?? '',
                'count' => (int)$row['cnt'],
                'status' => 'Active'
            ];
        }
    }

    // 3. Fetch Complaints
    // Note: complainant_name and mobile_number are in users table, not complaints table
    $comp_query = "
        SELECT c.complaint_id, c.category_id, c.assigned_to, c.status, c.complaint_title, c.complaint_description, c.village_ward, c.submitted_at,
               cat.category_name,
               u_cit.full_name AS complainant_name,
               u_cit.mobile_number AS complainant_mobile,
               u_off.full_name AS officer_name
        FROM complaints c
        LEFT JOIN categories cat ON c.category_id = cat.category_id
        LEFT JOIN users u_cit ON c.user_id = u_cit.user_id
        LEFT JOIN users u_off ON c.assigned_to = u_off.user_id
        ORDER BY c.submitted_at DESC
    ";
    $comp_stmt = $conn->query($comp_query);
    if ($comp_stmt) {
        while ($row = $comp_stmt->fetch_assoc()) {
            $cid = (int)$row['complaint_id'];
            
            // Fetch photos
            $citizen_photo = null;
            $before_photo = null;
            $after_photo = null;
            $photo_stmt = $conn->prepare("SELECT photo_type, photo_path FROM complaint_photos WHERE complaint_id = ?");
            if ($photo_stmt) {
                $photo_stmt->bind_param("i", $cid);
                $photo_stmt->execute();
                $photo_res = $photo_stmt->get_result();
                while ($p_row = $photo_res->fetch_assoc()) {
                    if ($p_row['photo_type'] === 'initial') {
                        $citizen_photo = '../' . $p_row['photo_path'];
                    } elseif ($p_row['photo_type'] === 'before') {
                        $before_photo = '../' . $p_row['photo_path'];
                    } elseif ($p_row['photo_type'] === 'after') {
                        $after_photo = '../' . $p_row['photo_path'];
                    }
                }
                $photo_stmt->close();
            }
            
            // Fetch latest remarks
            // Table columns: complaint_id, user_id, status_from, status_to, remarks, created_at
            $remarks = null;
            $hist_stmt = $conn->prepare("SELECT remarks FROM complaint_history WHERE complaint_id = ? AND remarks IS NOT NULL AND remarks != '' ORDER BY created_at DESC LIMIT 1");
            if ($hist_stmt) {
                $hist_stmt->bind_param("i", $cid);
                $hist_stmt->execute();
                $hist_res = $hist_stmt->get_result();
                if ($h_row = $hist_res->fetch_assoc()) {
                    $remarks = $h_row['remarks'];
                }
                $hist_stmt->close();
            }

            $complaints_data[] = [
                'complaint_id' => (string)$cid,
                'category_id' => (int)$row['category_id'],
                'category_name' => $row['category_name'] ?? 'General',
                'assigned_to' => $row['assigned_to'] ? (int)$row['assigned_to'] : null,
                'officer_name' => $row['officer_name'] ?? 'Unassigned',
                'status' => $row['status'],
                'complaint_title' => $row['complaint_title'],
                'complaint_description' => $row['complaint_description'],
                'complaint_image' => $citizen_photo,
                'complainant_name' => $row['complainant_name'] ?? 'Unknown Citizen',
                'mobile_number' => $row['complainant_mobile'] ?? 'N/A',
                'village_ward' => $row['village_ward'],
                'submission_date' => date('d M Y, h:i A', strtotime($row['submitted_at'])),
                'before_photo' => $before_photo,
                'after_photo' => $after_photo,
                'officer_remarks' => $remarks
            ];
        }
    }

    // 4. Fetch History
    // Table columns: history_id, complaint_id, user_id, status_from, status_to, remarks, created_at
    $hist_query = "
        SELECT h.history_id, h.complaint_id, h.status_to AS status, h.remarks AS note, h.created_at AS updated_at
        FROM complaint_history h
        ORDER BY h.created_at DESC
    ";
    $hist_stmt = $conn->query($hist_query);
    if ($hist_stmt) {
        while ($row = $hist_stmt->fetch_assoc()) {
            $history_data[] = [
                'history_id' => (int)$row['history_id'],
                'complaint_id' => (string)$row['complaint_id'],
                'status' => $row['status'],
                'remarks' => $row['note'] ?? '',
                'timestamp' => date('d M Y, h:i A', strtotime($row['updated_at']))
            ];
        }
    }

    // 5. Fetch Notifications
    // Table columns: notification_id, user_id, title, message, is_read, created_at
    $notif_query = "
        SELECT n.notification_id, n.user_id, n.title, n.message, n.is_read, n.created_at
        FROM notifications n
        ORDER BY n.created_at DESC
    ";
    $notif_stmt = $conn->query($notif_query);
    if ($notif_stmt) {
        while ($row = $notif_stmt->fetch_assoc()) {
            $notifications_data[] = [
                'notification_id' => (int)$row['notification_id'],
                'user_id' => (int)$row['user_id'],
                'title' => $row['title'],
                'message' => $row['message'],
                'is_read' => (int)$row['is_read'],
                'timestamp' => date('d M Y, h:i A', strtotime($row['created_at']))
            ];
        }
    }
}
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
    
    <!-- Dynamic Dataset for Gram Sevak JS -->
    <script>
    window.gpcmsDataset = {
        users: <?php echo json_encode($users_data); ?>,
        categories: <?php echo json_encode($categories_data); ?>,
        complaints: <?php echo json_encode($complaints_data); ?>,
        history: <?php echo json_encode($history_data); ?>,
        notifications: <?php echo json_encode($notifications_data); ?>
    };
    </script>
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
                            <?php
                            $unread_count = 0;
                            foreach ($notifications_data as $n) {
                                if (isset($n['is_read']) && $n['is_read'] === 0) {
                                    $unread_count++;
                                }
                            }
                            ?>
                            <span class="badge rounded-pill bg-danger ms-auto" id="notifBadgeCount"><?php echo $unread_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person-circle"></i> <span>Profile</span>
                        </a>
                    </li>
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
                            <li><a class="dropdown-item text-danger" href="../includes/logout.php" id="dropdownLogout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
