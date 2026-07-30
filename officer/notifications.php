<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Notifications View
 * File Ownership: Field Team
 * Purpose: View all notifications related to the Field Officer.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

$user_data = requireRole(['officer', 'admin']);
$officer_id = (int) ($user_data['user_id'] ?? $_SESSION['user_id'] ?? 2);

$officer_notifications = [];
try {
    // Fetch notifications
    $notif_stmt = $conn->prepare("
        SELECT notification_id, complaint_id, message, is_read, created_at 
        FROM notifications 
        WHERE user_id = ? OR complaint_id IN (SELECT complaint_id FROM complaints WHERE assigned_to = ?)
        ORDER BY created_at DESC
    ");
    $notif_stmt->bind_param("ii", $officer_id, $officer_id);
    $notif_stmt->execute();
    $notif_res = $notif_stmt->get_result();
    if ($notif_res) {
        while ($row = $notif_res->fetch_assoc()) {
            $officer_notifications[] = $row;
        }
    }
    $notif_stmt->close();

    // Mark all as read
    $update_stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE (user_id = ? OR complaint_id IN (SELECT complaint_id FROM complaints WHERE assigned_to = ?)) AND is_read = 0
    ");
    $update_stmt->bind_param("ii", $officer_id, $officer_id);
    $update_stmt->execute();
    $update_stmt->close();
} catch (Throwable $e) {
    error_log("Notifications page query error: " . $e->getMessage());
}

$page_title = "Notifications - GPCMS";
require_once 'header.php';
require_once 'officer_sidebar.php';
?>

<!-- Breadcrumb Bar Matching Reference UI -->
<div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E2D9CD; font-size: 0.82rem;">
  <div class="text-muted d-flex align-items-center gap-2">
    <a href="field_dashboard.php" class="text-muted text-decoration-none fw-semibold">Home</a>
    <span>/</span>
    <span class="text-muted">Field Officer</span>
    <span>/</span>
    <span class="fw-bold text-dark">Notifications</span>
  </div>
</div>

<!-- Hero Welcome Banner -->
<div class="hero-welcome-card mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #876E47 0%, #A0855A 100%); border-radius: 18px; padding: 1.75rem 2.25rem; color: #FFFFFF; box-shadow: 0 6px 20px rgba(135, 110, 71, 0.15);">
  <div class="position-relative z-2">
    <h2 class="fw-extrabold text-white mb-1" style="font-size: 1.85rem; letter-spacing: -0.01em;">Notifications</h2>
    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">View all alerts and update logs sent to you.</p>
  </div>
</div>

<!-- Notifications Card Listing -->
<div class="card border-0 shadow-sm" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(220, 201, 167, 0.5) !important; padding: 1.5rem;">
  <?php if (empty($officer_notifications)): ?>
    <div class="text-center py-5">
      <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
      <h5 class="fw-bold text-dark mb-1">No Notifications Yet</h5>
      <p class="text-muted small">You don't have any notifications at the moment.</p>
    </div>
  <?php else: ?>
    <div class="d-flex flex-column gap-3">
      <?php foreach ($officer_notifications as $notif): ?>
        <?php
          $created_time = strtotime($notif['created_at']);
          $diff = time() - $created_time;
          if ($diff < 60) $time_str = "Just now";
          elseif ($diff < 3600) $time_str = floor($diff / 60) . " mins ago";
          elseif ($diff < 86400) $time_str = floor($diff / 3600) . " hours ago";
          else $time_str = date('d M Y, h:i A', $created_time);
        ?>
        <div class="p-3 border rounded-3 d-flex align-items-start gap-3" style="border-color: #E2D9CD !important; background-color: <?php echo (int)$notif['is_read'] === 0 ? '#FCF8F2;' : '#FFF;'; ?>">
          <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; border: 1.5px solid #DCC9A7; flex-shrink: 0; background-color: #FAF6F0;">
            <i class="bi bi-bell-fill" style="color: #876E47;"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                <?php if ($notif['complaint_id']): ?>
                  Complaint #<?php echo htmlspecialchars((string)$notif['complaint_id']); ?>
                <?php else: ?>
                  System Alert
                <?php endif; ?>
              </span>
              <span class="text-muted extra-small" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?php echo $time_str; ?></span>
            </div>
            <p class="mb-0 text-secondary mt-1.5" style="font-size: 0.84rem; line-height: 1.45;">
              <?php echo htmlspecialchars($notif['message']); ?>
            </p>
            <?php if ($notif['complaint_id']): ?>
              <div class="mt-2">
                <a href="save_progress.php?id=<?php echo urlencode((string)$notif['complaint_id']); ?>" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                  <i class="bi bi-pencil-square me-1"></i> Update Progress
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php
require_once 'footer.php';
?>
