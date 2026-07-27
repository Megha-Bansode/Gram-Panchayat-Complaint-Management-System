<?php
/**
 * FILE: citizen/notifications.php
 * MODULE: Citizen Module
 * PURPOSE: Displays all notifications for the authenticated citizen.
 * CONTRACT: Citizen_Database_Contract.txt
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
require_once 'citizen_helpers.php';

$user_id = intval($_SESSION['user_id']);

// ── FETCH ALL NOTIFICATIONS ───────────────────────────────
$notifications = [];
$stmt = $conn->prepare(
    "SELECT notification_id, message, is_read, created_at
     FROM notifications
     WHERE user_id = ?
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// ── MARK ALL AS READ ──────────────────────────────────────
$mark = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
if ($mark) {
    $mark->bind_param("i", $user_id);
    $mark->execute();
    $mark->close();
}

$unread_total = 0;
foreach ($notifications as $n) {
    if (!(int) $n['is_read']) $unread_total++;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Notifications – GPCMS Citizen Portal">
    <title>Notifications | GPCMS Citizen Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/citizen.css">
</head>
<body class="citizen-body">

<div class="citizen-layout">
    <?php require_once 'sidebar.php'; ?>

    <div class="citizen-main-content">
        <?php require_once '../includes/header.php'; ?>

        <main class="citizen-page-wrapper" id="mainContent">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb citizen-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="citizen_dashboard.php"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title">
                    <i class="bi bi-bell me-2" aria-hidden="true"></i>Notifications
                </h1>
                <p class="citizen-page-subtitle">
                    All updates and alerts related to your complaints.
                </p>
            </div>
        </div>

        <!-- Notifications Feed -->
        <div class="citizen-card">
            <div class="citizen-card-header">
                <h2 class="citizen-card-title">
                    <i class="bi bi-bell-fill me-2" aria-hidden="true"></i>All Notifications
                    <?php if ($unread_total > 0): ?>
                    <span class="badge bg-danger ms-2" style="font-size:.7rem;"><?php echo $unread_total; ?> unread</span>
                    <?php endif; ?>
                </h2>
            </div>
            <div class="citizen-card-body p-0">
                <?php if (empty($notifications)): ?>
                <div class="citizen-empty-state py-5" id="notifications-empty">
                    <i class="bi bi-bell-slash citizen-empty-icon" aria-hidden="true"></i>
                    <h3 class="citizen-empty-title">No notifications yet</h3>
                    <p class="citizen-empty-text">
                        You have no notifications. Updates about your complaints will appear here.
                    </p>
                    <a href="citizen_dashboard.php" class="btn citizen-btn-primary mt-2">
                        <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Go to Dashboard
                    </a>
                </div>
                <?php else: ?>
                <ul class="citizen-notif-feed list-unstyled mb-0" id="notificationsList" aria-label="Notification list">
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
                                <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($notif['created_at']))); ?>
                            </time>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.container-fluid -->
</main>

        <?php require_once '../includes/footer.php'; ?>

