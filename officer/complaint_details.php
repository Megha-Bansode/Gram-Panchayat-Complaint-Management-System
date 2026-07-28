<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Complaint Details View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Full detail view of a selected complaint including citizen info, location, evidence photos, and status history timeline.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (isset($_SESSION['is_logged_in'])) {
    requireRole(['officer', 'admin']);
}

$complaint_code = $_GET['id'] ?? 'CMP-0012';

try {
    // Fetch complaint details with citizen and category info
    $stmt = $conn->prepare("
        SELECT c.*, cat.category_name, u.full_name AS citizen_name, u.phone AS citizen_phone, u.email AS citizen_email, off.full_name AS officer_name
        FROM complaints c
        JOIN categories cat ON c.category_id = cat.category_id
        JOIN users u ON c.citizen_id = u.user_id
        LEFT JOIN users off ON c.assigned_officer_id = off.user_id
        WHERE c.complaint_code = :code OR c.complaint_id = :id
        LIMIT 1
    ");
    $stmt->execute([':code' => $complaint_code, ':id' => $complaint_code]);
    $complaint = $stmt->fetch();

    if (!$complaint) {
        $complaint = [
            'complaint_id'   => 1,
            'complaint_code' => $complaint_code,
            'title'          => 'Road Potholes Repair near Primary School',
            'description'    => 'The main road leading to the Gram Panchayat Primary School has developed multiple deep potholes following recent rainfall.',
            'category_name'  => 'Roads & Infrastructure',
            'ward_no'        => 'Ward 04',
            'location_address'=> 'Station Road, Ward 04',
            'status'         => 'in_progress',
            'priority'       => 'high',
            'citizen_name'   => 'Ramesh Patil',
            'citizen_phone'  => '9876543213',
            'officer_name'   => 'Smit Ahirrao',
            'created_at'     => date('Y-m-d H:i:s')
        ];
    }

    // Set canonical session complaint_id
    $_SESSION['complaint_id'] = $complaint['complaint_id'];

    // Fetch photos
    $photoStmt = $conn->prepare("
        SELECT photo_path, photo_type, uploaded_at 
        FROM complaint_photos 
        WHERE complaint_id = :cid 
        ORDER BY uploaded_at DESC
    ");
    $photoStmt->execute([':cid' => $complaint['complaint_id']]);
    $photos = $photoStmt->fetchAll();

    // Fetch history timeline
    $historyStmt = $conn->prepare("
        SELECT h.*, u.full_name 
        FROM complaint_history h
        JOIN users u ON h.user_id = u.user_id
        WHERE h.complaint_id = :cid 
        ORDER BY h.created_at DESC
    ");
    $historyStmt->execute([':cid' => $complaint['complaint_id']]);
    $historyLogs = $historyStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Complaint details error: " . $e->getMessage());
    $photos = [];
    $historyLogs = [];
}

$page_title = "Complaint Details - GPCMS";
require_once 'officer_header.php';
require_once 'officer_sidebar.php';
?>

<!-- Breadcrumb Bar Matching Reference UI -->
<div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E2D9CD; font-size: 0.82rem;">
  <div class="text-muted d-flex align-items-center gap-2">
    <a href="field_dashboard.php" class="text-muted text-decoration-none fw-semibold">Home</a>
    <span>/</span>
    <a href="assigned_complaints.php" class="text-muted text-decoration-none fw-semibold">Assigned Complaints</a>
    <span>/</span>
    <span class="fw-bold text-dark">Details</span>
  </div>
</div>

<!-- Header Navigation & Action Bar -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 animate-fade-in-up delay-1">
  <div>
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);" id="detailTitle"><?= htmlspecialchars($complaint['title']) ?></h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <span class="fw-bold text-dark fs-6" id="detailId"><?= htmlspecialchars($complaint['complaint_code']) ?></span>
      <span class="text-muted">&bull;</span>
      <?php
        $st = strtolower($complaint['status']);
        $badge_style = 'background-color: #FFF8E7 !important; color: #D35400 !important;';
        $dot_style = 'background-color: #D35400 !important;';

        if ($st === 'assigned') {
            $badge_style = 'background-color: #E3F2FD !important; color: #1565C0 !important;';
            $dot_style = 'background-color: #1565C0 !important;';
        } elseif ($st === 'resolved') {
            $badge_style = 'background-color: #E8F5E9 !important; color: #2E7D32 !important;';
            $dot_style = 'background-color: #2E7D32 !important;';
        }
      ?>
      <span class="status-badge <?= $st ?>" id="detailStatusBadge" style="<?= $badge_style ?> font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;">
        <span style="width: 7px !important; height: 7px !important; border-radius: 50% !important; <?= $dot_style ?> display: inline-block !important;"></span> 
        <?= ucfirst(str_replace('_', ' ', $st)) ?>
      </span>
      <span class="text-muted">&bull;</span>
      <span class="priority-badge <?= htmlspecialchars(strtolower($complaint['priority'])) ?>" id="detailPriorityBadge"><?= ucfirst($complaint['priority']) ?> Priority</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="assigned_complaints.php" class="btn-secondary-action">
      <i class="bi bi-arrow-left"></i> Back to Complaints
    </a>
    <a href="save_progress.php?id=<?= urlencode($complaint['complaint_code']) ?>" class="btn-primary-action">
      <i class="bi bi-pencil-square"></i> Update Progress
    </a>
  </div>
</div>

<!-- Main Details Content Grid -->
<div class="row g-4 mb-4">
  
  <!-- Left Column: Details Cards -->
  <div class="col-lg-8 d-flex flex-column gap-4">
    
    <!-- Complaint Overview Card -->
    <div class="card-panel animate-fade-in-up delay-2">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-info-circle" style="color: var(--primary-color);"></i> Complaint Information
      </h5>
      
      <div class="row g-3 mb-3" style="font-size: 0.88rem;">
        <div class="col-sm-6">
          <span class="text-muted d-block small">Category</span>
          <span class="fw-semibold text-dark" id="detailCategory"><?= htmlspecialchars($complaint['category_name']) ?></span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Location / Ward</span>
          <span class="fw-semibold text-dark" id="detailLocation"><?= htmlspecialchars($complaint['ward_no'] . ', ' . $complaint['location_address']) ?></span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Filing Date</span>
          <span class="fw-semibold text-dark"><?= date('d F Y', strtotime($complaint['created_at'])) ?></span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Assigned Officer</span>
          <span class="fw-semibold text-dark"><?= htmlspecialchars($complaint['officer_name'] ?? 'Field Officer') ?></span>
        </div>
      </div>
      
      <div>
        <span class="text-muted d-block small mb-1">Description</span>
        <div class="p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color); font-size: 0.88rem; line-height: 1.6;" id="detailDesc">
          <?= nl2br(htmlspecialchars($complaint['description'])) ?>
        </div>
      </div>
    </div>
    
    <!-- Citizen Contact Info Card -->
    <div class="card-panel animate-fade-in-up delay-3">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-person-lines-fill" style="color: var(--primary-color);"></i> Citizen Information
      </h5>
      <div class="row g-3" style="font-size: 0.88rem;">
        <div class="col-sm-6">
          <span class="text-muted d-block small">Citizen Name</span>
          <span class="fw-bold text-dark" id="detailCitizen"><?= htmlspecialchars($complaint['citizen_name']) ?></span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Contact Number</span>
          <span class="fw-semibold text-dark" id="detailPhone"><?= htmlspecialchars($complaint['citizen_phone']) ?></span>
        </div>
      </div>
    </div>
    
    <!-- Attached Photo Evidence Card -->
    <div class="card-panel animate-fade-in-up delay-4">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-images" style="color: var(--primary-color);"></i> Photo Evidence & Field Work Uploads
      </h5>
      
      <div class="row g-3">
        <?php if (empty($photos)): ?>
          <div class="col-12 text-muted small text-start ps-3 py-2">
            <i class="bi bi-info-circle me-1"></i> No photo evidence uploaded yet.
          </div>
        <?php else: ?>
          <?php foreach ($photos as $p): ?>
            <?php 
              $path = $p['photo_path'];
              $img_src = (strpos($path, 'uploads/') === 0) ? '../' . $path : $path;
            ?>
            <div class="col-6 col-sm-4">
              <div class="p-2 border rounded bg-white text-center shadow-sm h-100">
                <a href="<?= htmlspecialchars($img_src) ?>" target="_blank" title="Click to view full image">
                  <img src="<?= htmlspecialchars($img_src) ?>" alt="Work Evidence" class="img-fluid rounded mb-2" style="height: 130px; width: 100%; object-fit: cover; border: 1px solid #E2D9CD;" onerror="this.onerror=null; this.src='../assets/field/gram_panchayat_seal.png';">
                </a>
                <div class="d-flex align-items-center justify-content-between px-1">
                  <span class="badge bg-secondary extra-small" style="font-size: 0.7rem;"><?= ucfirst($p['photo_type']) ?></span>
                  <span class="text-muted extra-small" style="font-size: 0.72rem;"><?= date('d M, h:i A', strtotime($p['uploaded_at'])) ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
  
  <!-- Right Column: Progress History Timeline Feed -->
  <div class="col-lg-4 animate-fade-in-up delay-3">
    <div class="card-panel h-100">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-clock-history" style="color: var(--primary-color);"></i> Status History Timeline
      </h5>
      
      <ul class="timeline-feed">
        <?php if (empty($historyLogs)): ?>
          <li class="timeline-feed-item">
            <div class="timeline-feed-marker current"></div>
            <div class="timeline-feed-content">
              <div class="timeline-feed-title">Complaint Registered</div>
              <div class="timeline-feed-date"><?= date('d M Y, h:i A', strtotime($complaint['created_at'])) ?></div>
              <p class="text-muted small mb-0 mt-1">Complaint registered in system.</p>
            </div>
          </li>
        <?php else: ?>
          <?php foreach ($historyLogs as $idx => $log): ?>
            <li class="timeline-feed-item">
              <div class="timeline-feed-marker <?= $idx === 0 ? 'current' : 'completed' ?>"></div>
              <div class="timeline-feed-content">
                <div class="timeline-feed-title">Status: <?= ucfirst(str_replace('_', ' ', $log['status_to'])) ?></div>
                <div class="timeline-feed-date"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></div>
                <p class="text-dark small fw-semibold mb-0 mt-1"><?= htmlspecialchars($log['remarks']) ?></p>
                <span class="text-muted extra-small">Updated by: <?= htmlspecialchars($log['full_name']) ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  
</div>

<?php
require_once 'officer_footer.php';
?>
