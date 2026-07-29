<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Dashboard
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Primary dashboard interface for Field Officers displaying workload metrics, recent assigned complaints, quick actions, and activity logs.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

$user_data = requireRole(['officer', 'admin']);

$officer_id = (int) ($user_data['user_id'] ?? $_SESSION['user_id'] ?? 2);
$officer_name = (string) ($user_data['full_name'] ?? $_SESSION['full_name'] ?? 'Field Officer');

// Fetch dynamic KPI metrics from database
$kpis = [
    'total'       => 0,
    'pending'     => 0,
    'assigned'    => 0,
    'in_progress' => 0,
    'resolved'    => 0,
];

try {
    $kpiStmt = $conn->prepare("
        SELECT status, COUNT(*) AS total_count 
        FROM complaints 
        WHERE assigned_to = ?
        GROUP BY status
    ");
    $kpiStmt->bind_param('i', $officer_id);
    $kpiStmt->execute();
    $kpiRes = $kpiStmt->get_result();
    $kpiRows = $kpiRes ? $kpiRes->fetch_all(MYSQLI_ASSOC) : [];
    $kpiStmt->close();
    
    foreach ($kpiRows as $row) {
        $st = strtolower($row['status']);
        if (isset($kpis[$st])) {
            $kpis[$st] = (int)$row['total_count'];
        }
        $kpis['total'] += (int)$row['total_count'];
    }

    // Fetch recent assigned complaints
    $recentStmt = $conn->prepare("
        SELECT c.complaint_id, c.complaint_id AS complaint_code, c.complaint_title AS title, c.village_ward AS ward_no, c.village_ward AS location_address, c.status, cat.category_name, c.submitted_at AS created_at
        FROM complaints c
        LEFT JOIN categories cat ON c.category_id = cat.category_id
        WHERE c.assigned_to = ?
        ORDER BY c.submitted_at DESC
        LIMIT 5
    ");
    $recentStmt->bind_param('i', $officer_id);
    $recentStmt->execute();
    $recentRes = $recentStmt->get_result();
    $recentComplaints = $recentRes ? $recentRes->fetch_all(MYSQLI_ASSOC) : [];
    $recentStmt->close();

    // Fetch recent activity history
    $historyStmt = $conn->prepare("
        SELECT h.history_id, h.complaint_id, h.status AS status_to, h.note AS remarks, h.updated_at AS created_at
        FROM complaint_history h
        JOIN complaints c ON h.complaint_id = c.complaint_id
        WHERE h.updated_by = ? OR c.assigned_to = ?
        ORDER BY h.updated_at DESC
        LIMIT 5
    ");
    $historyStmt->bind_param('ii', $officer_id, $officer_id);
    $historyStmt->execute();
    $historyRes = $historyStmt->get_result();
    $activities = $historyRes ? $historyRes->fetch_all(MYSQLI_ASSOC) : [];
    $historyStmt->close();

} catch (Throwable $e) {
    error_log("Dashboard query error: " . $e->getMessage());
    $recentComplaints = [];
    $activities = [];
}

$page_title = "Field Officer Dashboard - GPCMS";
require_once 'header.php';
require_once 'officer_sidebar.php';
?>

<!-- Breadcrumb Bar Matching Reference Image -->
<div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E2D9CD; font-size: 0.82rem;">
  <div class="text-muted d-flex align-items-center gap-2">
    <a href="field_dashboard.php" class="text-muted text-decoration-none fw-semibold">Home</a>
    <span>/</span>
    <span class="text-muted">Field Officer</span>
    <span>/</span>
    <span class="fw-bold text-dark">Dashboard</span>
  </div>
</div>

<!-- Hero Welcome Banner Matching Reference UI -->
<div class="hero-welcome-card mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #876E47 0%, #A0855A 100%); border-radius: 18px; padding: 2rem 2.25rem; color: #FFFFFF; box-shadow: 0 6px 20px rgba(135, 110, 71, 0.15);">
  <div class="row align-items-center position-relative z-2">
    <div class="col-lg-9">
      <h2 class="fw-extrabold mb-2 text-white" style="font-size: 1.85rem; letter-spacing: -0.01em;">Welcome Back, <?= htmlspecialchars($officer_name) ?></h2>
      <p class="mb-0 text-white-50" style="font-size: 0.92rem; max-width: 620px; line-height: 1.45;">
        Monitor public grievances, record inspection progress, and review field completions for Gram Panchayat.
      </p>
    </div>
    <div class="col-lg-3 text-end d-none d-lg-block">
      <i class="bi bi-shield-check display-3 text-white opacity-25"></i>
    </div>
  </div>
</div>

<!-- KPI Metric Cards Grid (5 Equal Columns - Active Cases Removed) -->
<div class="row g-3 mb-4">
  
  <!-- Total Cases Card -->
  <div class="col-12 col-sm-6 col-md-4 col-xl">
    <a href="assigned_complaints.php" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #876E47 !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL CASES</span>
        <div class="display-6 fw-extrabold text-dark mt-1" style="font-size: 1.9rem;"><?= $kpis['total'] ?></div>
      </div>
    </a>
  </div>
  
  <!-- Pending Card -->
  <div class="col-12 col-sm-6 col-md-4 col-xl">
    <a href="assigned_complaints.php?status=pending" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #F1C40F !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">PENDING</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #F39C12;"><?= $kpis['pending'] ?></div>
      </div>
    </a>
  </div>
  
  <!-- Assigned Card -->
  <div class="col-12 col-sm-6 col-md-4 col-xl">
    <a href="assigned_complaints.php?status=assigned" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #3498DB !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">ASSIGNED</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #3498DB;"><?= $kpis['assigned'] ?></div>
      </div>
    </a>
  </div>
  
  <!-- In Progress Card -->
  <div class="col-12 col-sm-6 col-md-4 col-xl">
    <a href="assigned_complaints.php?status=in_progress" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #8E6E45 !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">IN PROGRESS</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #8E6E45;"><?= $kpis['in_progress'] ?></div>
      </div>
    </a>
  </div>
  
  <!-- Resolved Card -->
  <div class="col-12 col-sm-6 col-md-4 col-xl">
    <a href="assigned_complaints.php?status=resolved" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #2ECC71 !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">RESOLVED</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #2ECC71;"><?= $kpis['resolved'] ?></div>
      </div>
    </a>
  </div>
  
</div>

<!-- Main Dashboard Grid -->
<div class="row g-4 mb-4">
  
  <!-- Left Column: Recent Assigned Complaints Table -->
  <div class="col-lg-8 animate-fade-in-up delay-3">
    <div class="card-panel h-100">
      <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(220, 201, 167, 0.5) !important;">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
          <i class="bi bi-list-task" style="color: var(--primary-color);"></i> Recent Assigned Complaints
        </h5>
        <a href="assigned_complaints.php" class="text-decoration-none small fw-semibold" style="color: var(--primary-color);">
          View All <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      
      <div class="table-responsive">
        <table class="data-table table align-middle mb-0" style="font-size: 0.88rem;">
          <thead style="color: var(--text-dark);">
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Title</th>
              <th scope="col">Category</th>
              <th scope="col">Location</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-end">Action</th>
            </tr>
          </thead>
          <tbody id="dashboardTableBody">
            <?php if (empty($recentComplaints)): ?>
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">No assigned complaints found in database.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentComplaints as $c): ?>
                <?php
                  $st = strtolower($c['status']);
                  $badge_class = 'assigned';
                  $badge_style = 'background-color: #E3F2FD !important; color: #1565C0 !important;';
                  $dot_style = 'background-color: #1565C0 !important;';

                  if ($st === 'in_progress') {
                      $badge_class = 'in-progress';
                      $badge_style = 'background-color: #FFF8E7 !important; color: #D35400 !important;';
                      $dot_style = 'background-color: #D35400 !important;';
                  } elseif ($st === 'resolved') {
                      $badge_class = 'resolved';
                      $badge_style = 'background-color: #E8F5E9 !important; color: #2E7D32 !important;';
                      $dot_style = 'background-color: #2E7D32 !important;';
                  }
                ?>
                <tr>
                  <td class="fw-bold"><?= htmlspecialchars($c['complaint_code']) ?></td>
                  <td><?= htmlspecialchars($c['title']) ?></td>
                  <td><?= htmlspecialchars($c['category_name']) ?></td>
                  <td><?= htmlspecialchars($c['ward_no'] . ', ' . $c['location_address']) ?></td>
                  <td>
                    <span class="status-badge <?= $badge_class ?>" style="<?= $badge_style ?> font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;">
                      <span style="width: 7px !important; height: 7px !important; border-radius: 50% !important; <?= $dot_style ?> display: inline-block !important;"></span>
                      <?= ucfirst(str_replace('_', ' ', $st)) ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <div class="d-inline-flex align-items-center gap-2">
                      <a href="save_progress.php?id=<?= urlencode($c['complaint_code']) ?>" class="btn-table-update">Update</a>
                      <i class="bi bi-three-dots-vertical table-three-dots"></i>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Right Column: Quick Field Officer Actions & Activity Timeline -->
  <div class="col-lg-4 d-flex flex-column gap-4">
    
    <!-- Quick Field Officer Actions Card Matching Reference UI -->
    <div class="card border-0 shadow-sm p-3.5" style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2D9CD;">
      <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
        <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Field Officer Actions
      </h6>
      <div class="d-flex flex-column gap-2.5">
        <a href="assigned_complaints.php" class="btn text-white fw-bold py-2.5 px-3 text-center d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background-color: #876E47; border-radius: 10px; font-size: 0.88rem; border: none;">
          <i class="bi bi-clipboard-check"></i> View Assigned Complaints
        </a>
        <a href="save_progress.php" class="btn bg-white fw-bold py-2.5 px-3 text-center d-flex align-items-center justify-content-center gap-2 border" style="border-color: #DCC9A7 !important; border-radius: 10px; font-size: 0.88rem; color: #4A3C28 !important;">
          <i class="bi bi-pencil-square text-success"></i> Log Inspection Progress
        </a>
        <a href="assigned_complaints.php" class="btn bg-white fw-bold py-2.5 px-3 text-center d-flex align-items-center justify-content-center gap-2 border" style="border-color: #DCC9A7 !important; border-radius: 10px; font-size: 0.88rem; color: #4A3C28 !important;">
          <i class="bi bi-file-earmark-bar-graph-fill text-danger"></i> View Field Activity Logs
        </a>
      </div>
    </div>
    
    <!-- Recent Activity Timeline Section -->
    <div class="card-panel flex-grow-1 animate-fade-in-up delay-4">
      <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(220, 201, 167, 0.5) !important;">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
          <i class="bi bi-arrow-repeat" style="color: var(--primary-color);"></i> Recent Activity
        </h5>
        <a href="assigned_complaints.php" class="text-decoration-none small fw-semibold" style="color: var(--primary-color);">
          View All <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      
      <div class="activity-timeline" id="activityTimelineContainer">
        <?php if (empty($activities)): ?>
          <div class="text-muted small text-center py-3">No recent activities logged.</div>
        <?php else: ?>
          <?php foreach ($activities as $act): ?>
            <div class="timeline-item animate-fade-in-up delay-1" style="position: relative !important; margin-bottom: 1.25rem !important;">
              <div class="timeline-badge-node" style="width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #FFF3E0 !important; border: 1.5px solid #FFCC80 !important; color: #E65100 !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 0.95rem !important; flex-shrink: 0 !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;" title="Status Updated">
                <i class="bi bi-pencil-square" style="color: #E65100 !important;"></i>
              </div>
              <div class="timeline-body" style="margin-left: 50px !important;">
                <div class="timeline-title" style="font-weight: 800 !important; color: #241D15 !important; font-size: 0.88rem !important; line-height: 1.2 !important;">Status Updated</div>
                <div class="timeline-desc" style="color: #6E6255 !important; font-size: 0.82rem !important; margin-top: 3px !important; line-height: 1.4 !important;">
                  <a href="save_progress.php?id=<?= urlencode($act['complaint_code']) ?>" class="cmp-link" style="color: #6E5A3B !important; font-weight: 700 !important; text-decoration: none !important;"><?= htmlspecialchars($act['complaint_code']) ?></a> status changed to <span class="badge-status-subtle" style="font-size: 0.74rem !important; font-weight: 800 !important; color: #D35400 !important; background-color: #FFF3E0 !important; padding: 2px 6px !important; border-radius: 4px !important;"><?= strtoupper(str_replace('_', ' ', $act['status_to'])) ?></span>
                </div>
                <div class="timeline-time" style="font-size: 0.75rem !important; color: #8C7B6B !important; margin-top: 3px !important; display: flex !important; align-items: center !important; gap: 0.25rem !important;"><i class="bi bi-clock me-1"></i><?= date('M d, g:i a', strtotime($act['created_at'])) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    
  </div>
  
</div>

<?php
require_once 'footer.php';
?>
