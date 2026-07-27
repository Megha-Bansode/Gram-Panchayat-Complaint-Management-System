<?php
/**
 * FILE: citizen/citizen_dashboard.php
 * MODULE: Citizen Module
 * PURPOSE: Main dashboard for authenticated citizens.
 *          Shows complaint statistics, quick actions,
 *          recent complaints, and latest notifications.
 * CONTRACT: Citizen_Database_Contract.txt
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
require_once '../includes/citizen_helpers.php';

$user_id = intval($_SESSION['user_id']);

// ── COMPLAINT STATISTICS ──────────────────────────────────────────────────────
// Fetch per-status counts for this citizen only
$stats = [
    'total'       => 0,
    'pending'     => 0,
    'assigned'    => 0,
    'in_progress' => 0,
    'resolved'    => 0,
];

$stats_stmt = $conn->prepare(
    "SELECT status, COUNT(*) AS cnt
     FROM complaints
     WHERE user_id = ?
     GROUP BY status"
);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();

while ($row = $stats_result->fetch_assoc()) {
    $s = $row['status'];
    if (array_key_exists($s, $stats)) {
        $stats[$s] = (int) $row['cnt'];
    }
    $stats['total'] += (int) $row['cnt'];
}
$stats_stmt->close();

// ── LATEST NOTIFICATIONS ──────────────────────────────────────────────────────
// Fetch the 5 most recent notifications for this citizen
$notifications  = [];
$notif_stmt = $conn->prepare(
    "SELECT notification_id, message, is_read, created_at
     FROM notifications
     WHERE user_id = ?
     ORDER BY created_at DESC
     LIMIT 5"
);
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result();

while ($row = $notif_result->fetch_assoc()) {
    $notifications[] = $row;
}
$notif_stmt->close();

// Count unread notifications for the badge
$unread_count = 0;
foreach ($notifications as $n) {
    if (!(int) $n['is_read']) {
        $unread_count++;
    }
}

// ── RECENT COMPLAINTS ─────────────────────────────────────────────────────────
// Fetch the 5 most recent complaints for this citizen with category name
$recent_complaints = [];
$recent_stmt = $conn->prepare(
    "SELECT c.complaint_id,
            c.complaint_title,
            c.status,
            c.submitted_at,
            cat.category_name
     FROM complaints c
     LEFT JOIN categories cat ON c.category_id = cat.category_id
     WHERE c.user_id = ?
     ORDER BY c.submitted_at DESC, c.complaint_id DESC
     LIMIT 5"
);
$recent_stmt->bind_param("i", $user_id);
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();

while ($row = $recent_result->fetch_assoc()) {
    $recent_complaints[] = $row;
}
$recent_stmt->close();

$conn->close();

// ── HELPERS ───────────────────────────────────────────────────────────────────
// (Moved to includes/citizen_helpers.php to avoid duplication per Handbook §12)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Citizen Dashboard – Gram Panchayat Complaint Management System">
    <title>My Dashboard | GPCMS Citizen Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/citizen.css">
</head>
<body class="citizen-body">

<div class="citizen-layout">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="citizen-main-content">
        <?php require_once '../includes/header.php'; ?>

        <main class="citizen-page-wrapper" id="mainContent">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- ── Page Header ────────────────────────────────── -->
        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title">
                    <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>My Dashboard
                </h1>
                <p class="citizen-page-subtitle">
                    Welcome back,
                    <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>.
                    Here is an overview of all your complaints.
                </p>
            </div>
            <div class="citizen-header-cta">
                <a href="register_complaint.php"
                   class="btn citizen-btn-primary"
                   id="btn-dashboard-register">
                    <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>Register New Complaint
                </a>
            </div>
        </div>

        <!-- ── Statistics Cards ───────────────────────────── -->
        <!--
             5 cards: Total / Pending / Assigned / In Progress / Resolved
             xs: 2-column grid   |  md: 3+2   |  xl: 5-column
        -->
        <div class="row g-3 mb-4" role="region" aria-label="Complaint Statistics">

            <!-- Total -->
            <div class="col-6 col-md-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-total" id="stat-card-total">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number" id="stat-number-total">
                            <?php echo $stats['total']; ?>
                        </div>
                        <div class="citizen-stat-label">Total</div>
                    </div>
                </article>
            </div>

            <!-- Pending -->
            <div class="col-6 col-md-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-pending" id="stat-card-pending">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number" id="stat-number-pending">
                            <?php echo $stats['pending']; ?>
                        </div>
                        <div class="citizen-stat-label">Pending</div>
                    </div>
                </article>
            </div>

            <!-- Assigned -->
            <div class="col-6 col-md-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-assigned" id="stat-card-assigned">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number" id="stat-number-assigned">
                            <?php echo $stats['assigned']; ?>
                        </div>
                        <div class="citizen-stat-label">Assigned</div>
                    </div>
                </article>
            </div>

            <!-- In Progress -->
            <div class="col-6 col-md-6 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-inprogress" id="stat-card-inprogress">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number" id="stat-number-inprogress">
                            <?php echo $stats['in_progress']; ?>
                        </div>
                        <div class="citizen-stat-label">In Progress</div>
                    </div>
                </article>
            </div>

            <!-- Resolved -->
            <div class="col-12 col-md-6 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-resolved" id="stat-card-resolved">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number" id="stat-number-resolved">
                            <?php echo $stats['resolved']; ?>
                        </div>
                        <div class="citizen-stat-label">Resolved</div>
                    </div>
                </article>
            </div>

        </div><!-- /.row stats -->

        <!-- ── Quick Actions ──────────────────────────────── -->
        <section class="citizen-card mb-4" aria-label="Quick Actions">
            <div class="citizen-card-header">
                <h2 class="citizen-card-title">
                    <i class="bi bi-lightning-fill me-2" aria-hidden="true"></i>Quick Actions
                </h2>
            </div>
            <div class="citizen-card-body">
                <div class="row g-3">

                    <!-- Register Complaint -->
                    <div class="col-6 col-sm-3">
                        <a href="register_complaint.php"
                           class="citizen-quick-action"
                           id="qa-register-complaint">
                            <span class="citizen-qa-icon citizen-qa-register" aria-hidden="true">
                                <i class="bi bi-plus-circle-fill"></i>
                            </span>
                            <span class="citizen-qa-label">Register Complaint</span>
                        </a>
                    </div>

                    <!-- Track Complaint -->
                    <div class="col-6 col-sm-3">
                        <a href="track_complaint.php"
                           class="citizen-quick-action"
                           id="qa-track-complaint">
                            <span class="citizen-qa-icon citizen-qa-track" aria-hidden="true">
                                <i class="bi bi-search"></i>
                            </span>
                            <span class="citizen-qa-label">Track Complaint</span>
                        </a>
                    </div>

                    <!-- View Complaint History -->
                    <div class="col-6 col-sm-3">
                        <a href="track_complaint.php"
                           class="citizen-quick-action"
                           id="qa-view-history">
                            <span class="citizen-qa-icon citizen-qa-history" aria-hidden="true">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <span class="citizen-qa-label">View History</span>
                        </a>
                    </div>

                    <!-- My Profile -->
                    <div class="col-6 col-sm-3">
                        <a href="profile.php"
                           class="citizen-quick-action"
                           id="qa-profile">
                            <span class="citizen-qa-icon citizen-qa-profile" aria-hidden="true">
                                <i class="bi bi-person-circle"></i>
                            </span>
                            <span class="citizen-qa-label">My Profile</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- ── Two-Column: Recent Complaints + Notifications ─ -->
        <div class="row g-4">

            <!-- Recent Complaints -->
            <div class="col-12 col-xl-8">
                <section class="citizen-card h-100" aria-label="Recent Complaints">
                    <div class="citizen-card-header d-flex justify-content-between align-items-center">
                        <h2 class="citizen-card-title mb-0">
                            <i class="bi bi-list-ul me-2" aria-hidden="true"></i>Recent Complaints
                        </h2>
                        <a href="my_complaints.php"
                           class="citizen-btn-link"
                           id="link-view-all-complaints">
                            View All <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="citizen-card-body p-0">
                        <?php if (empty($recent_complaints)): ?>
                        <!-- Empty State -->
                        <div class="citizen-empty-state py-5" id="recent-complaints-empty">
                            <i class="bi bi-inbox citizen-empty-icon" aria-hidden="true"></i>
                            <h3 class="citizen-empty-title">No complaints yet</h3>
                            <p class="citizen-empty-text">
                                You have not registered any complaints. Submit your first one to get started.
                            </p>
                            <a href="register_complaint.php"
                               class="btn citizen-btn-primary mt-2"
                               id="btn-first-complaint">
                                <i class="bi bi-plus me-1" aria-hidden="true"></i>
                                Register Your First Complaint
                            </a>
                        </div>

                        <?php else: ?>
                        <!-- Complaints Table -->
                        <div class="table-responsive">
                            <table class="citizen-table w-100" id="recentComplaintsTable"
                                   aria-label="Your recent complaints">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recent_complaints as $cmp): ?>
                                <tr>
                                    <td>
                                        <span class="citizen-complaint-id">
                                            #<?php echo htmlspecialchars($cmp['complaint_id']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="citizen-complaint-title">
                                            <?php echo htmlspecialchars($cmp['complaint_title']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted citizen-small">
                                            <?php echo htmlspecialchars($cmp['category_name'] ?? '—'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted citizen-small">
                                            <?php echo htmlspecialchars(
                                                date('d M Y', strtotime($cmp['submitted_at']))
                                            ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="citizen-badge <?php echo status_badge_class($cmp['status']); ?>">
                                            <?php echo status_label($cmp['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="track_complaint.php?complaint_id=<?php echo intval($cmp['complaint_id']); ?>"
                                           class="btn citizen-btn-action-sm"
                                           aria-label="Track complaint #<?php echo intval($cmp['complaint_id']); ?>">
                                            <i class="bi bi-search me-1" aria-hidden="true"></i>Track
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div><!-- /.citizen-card-body -->
                </section>
            </div><!-- /.col recent complaints -->

            <!-- Latest Notifications -->
            <div class="col-12 col-xl-4">
                <section class="citizen-card h-100" aria-label="Latest Notifications">
                    <div class="citizen-card-header">
                        <h2 class="citizen-card-title">
                            <i class="bi bi-bell me-2" aria-hidden="true"></i>Latest Notifications
                        </h2>
                    </div>
                    <div class="citizen-card-body p-0">

                        <?php if (empty($notifications)): ?>
                        <div class="citizen-empty-state py-4" id="notifications-empty">
                            <i class="bi bi-bell-slash citizen-empty-icon" style="font-size:2.5rem;" aria-hidden="true"></i>
                            <p class="citizen-empty-text mt-2 mb-0">No notifications yet.</p>
                        </div>

                        <?php else: ?>
                        <ul class="citizen-notif-feed list-unstyled mb-0"
                            id="notificationsList"
                            aria-label="Notification list">
                            <?php foreach ($notifications as $notif): ?>
                            <li class="citizen-notif-feed-item <?php echo (int)$notif['is_read'] ? 'is-read' : 'is-unread'; ?>">
                                <span class="citizen-notif-dot" aria-hidden="true"></span>
                                <div class="citizen-notif-content">
                                    <p class="citizen-notif-msg mb-0">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </p>
                                    <time class="citizen-notif-time"
                                          datetime="<?php echo htmlspecialchars($notif['created_at']); ?>">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                        <?php echo htmlspecialchars(
                                            date('d M Y, h:i A', strtotime($notif['created_at']))
                                        ); ?>
                                    </time>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>

                    </div>
                </section>
            </div><!-- /.col notifications -->

        </div><!-- /.row bottom -->

    </div><!-- /.container-fluid -->
</main>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════ -->
<?php require_once '../includes/footer.php'; ?>

