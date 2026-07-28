<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Save Progress View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Form interface for Field Officers to update complaint status, enter inspection notes, and upload photo evidence.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (isset($_SESSION['is_logged_in'])) {
    requireRole(['officer', 'admin']);
}

$officer_id = $_SESSION['user_id'] ?? 2;
$success_message = '';
$error_message   = '';

// Handle POST Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_code_input = filter_input(INPUT_POST, 'complaint_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $status_input         = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
    $note_input           = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate canonical status values per handbook
    $allowed_statuses = ['pending', 'assigned', 'in_progress', 'resolved'];
    if (!in_array($status_input, $allowed_statuses, true)) {
        $status_input = 'in_progress';
    }

    try {
        // Fetch current complaint
        $cStmt = $conn->prepare("SELECT complaint_id, complaint_code, status FROM complaints WHERE complaint_code = :code OR complaint_id = :id LIMIT 1");
        $cStmt->execute([':code' => $complaint_code_input, ':id' => $complaint_code_input]);
        $currentComplaint = $cStmt->fetch();

        if ($currentComplaint) {
            $real_complaint_id   = $currentComplaint['complaint_id'];
            $real_complaint_code = $currentComplaint['complaint_code'];
            $old_status          = $currentComplaint['status'];

            // Update Complaints Status
            $updateSql = "UPDATE complaints SET status = :status, updated_at = NOW()";
            if ($status_input === 'resolved') {
                $updateSql .= ", resolved_at = NOW()";
            }
            $updateSql .= " WHERE complaint_id = :cid";

            $uStmt = $conn->prepare($updateSql);
            $uStmt->execute([':status' => $status_input, ':cid' => $real_complaint_id]);

            // Insert into complaint_history
            if (!empty($note_input)) {
                $hStmt = $conn->prepare("
                    INSERT INTO complaint_history (complaint_id, user_id, status_from, status_to, remarks)
                    VALUES (:cid, :uid, :sfrom, :sto, :remarks)
                ");
                $hStmt->execute([
                    ':cid'     => $real_complaint_id,
                    ':uid'     => $officer_id,
                    ':sfrom'   => $old_status,
                    ':sto'     => $status_input,
                    ':remarks' => $note_input
                ]);
            }

            // Handle Photo Uploads
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Before Photo Upload
            if (!empty($_FILES['before_photo']['name']) && $_FILES['before_photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['before_photo']['name'], PATHINFO_EXTENSION);
                $file_name = 'before_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $target    = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['before_photo']['tmp_name'], $target)) {
                    $pStmt = $conn->prepare("
                        INSERT INTO complaint_photos (complaint_id, photo_path, uploaded_by, photo_type)
                        VALUES (:cid, :ppath, :uby, 'initial')
                    ");
                    $pStmt->execute([':cid' => $real_complaint_id, ':ppath' => 'uploads/' . $file_name, ':uby' => $officer_id]);
                }
            }

            // After Photo Upload
            if (!empty($_FILES['after_photo']['name']) && $_FILES['after_photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['after_photo']['name'], PATHINFO_EXTENSION);
                $file_name = 'after_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $target    = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['after_photo']['tmp_name'], $target)) {
                    $pStmt = $conn->prepare("
                        INSERT INTO complaint_photos (complaint_id, photo_path, uploaded_by, photo_type)
                        VALUES (:cid, :ppath, :uby, 'resolution')
                    ");
                    $pStmt->execute([':cid' => $real_complaint_id, ':ppath' => 'uploads/' . $file_name, ':uby' => $officer_id]);
                }
            }

            $success_message = "Inspection progress for " . htmlspecialchars($real_complaint_code) . " updated successfully!";
        } else {
            $error_message = "Complaint not found in database.";
        }
    } catch (PDOException $e) {
        error_log("Save progress error: " . $e->getMessage());
        $error_message = "Database error while saving progress updates: " . $e->getMessage();
    }
}

// GET Request Data Loading
$complaint_code = $_GET['id'] ?? 'CMP-0012';

try {
    $stmt = $conn->prepare("
        SELECT c.*, cat.category_name, u.full_name AS citizen_name
        FROM complaints c
        JOIN categories cat ON c.category_id = cat.category_id
        JOIN users u ON c.citizen_id = u.user_id
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
            'description'    => 'The main road near the primary school is heavily damaged with multiple deep potholes.',
            'category_name'  => 'Roads & Infrastructure',
            'ward_no'        => 'Ward 04',
            'status'         => 'in_progress',
        ];
    }

    // Fetch existing uploaded photos for current complaint
    $photoStmt = $conn->prepare("
        SELECT photo_path, photo_type, uploaded_at 
        FROM complaint_photos 
        WHERE complaint_id = :cid 
        ORDER BY uploaded_at DESC
    ");
    $photoStmt->execute([':cid' => $complaint['complaint_id']]);
    $currentPhotos = $photoStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Load complaint error: " . $e->getMessage());
    $currentPhotos = [];
}

$page_title = "Save Progress - GPCMS";
require_once 'header.php';
require_once 'officer_sidebar.php';
?>

<!-- Breadcrumb Bar Matching Reference UI -->
<div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E2D9CD; font-size: 0.82rem;">
  <div class="text-muted d-flex align-items-center gap-2">
    <a href="field_dashboard.php" class="text-muted text-decoration-none fw-semibold">Home</a>
    <span>/</span>
    <a href="assigned_complaints.php" class="text-muted text-decoration-none fw-semibold">Assigned Complaints</a>
    <span>/</span>
    <span class="fw-bold text-dark">Save Progress</span>
  </div>
</div>

<?php if (!empty($success_message)): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 14px;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> <?= $success_message ?>
    <a href="complaint_details.php?id=<?= urlencode($complaint['complaint_code']) ?>" class="btn btn-sm btn-outline-success ms-3 fw-bold">View Updated Complaint <i class="bi bi-arrow-right me-1"></i></a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 14px;" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Header -->
<div class="row align-items-center mb-4 animate-fade-in-up delay-1">
  <div class="col-md-12">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Field Inspection Workspace</h2>
    <p class="text-muted mb-0">Record inspection updates, update status, add work notes, and upload photo evidence.</p>
  </div>
</div>

<!-- Main Inspection Workspace Form Wrapper -->
<form id="saveProgressForm" action="save_progress.php?id=<?= urlencode($complaint['complaint_code']) ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
  
  <input type="hidden" name="complaint_id" id="hiddenComplaintId" value="<?= htmlspecialchars($complaint['complaint_code']) ?>">

  <div class="row g-4 mb-4">
    
    <!-- Left Column: Selected Complaint Summary Card & Existing Photos -->
    <div class="col-lg-6 d-flex flex-column gap-4 animate-fade-in-up delay-2">
      
      <!-- Summary Info Box -->
      <div class="card-panel">
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
          <i class="bi bi-file-earmark-text" style="color: var(--primary-color);"></i> Selected Complaint Inspection Summary
        </h5>
        
        <div class="row g-3 mb-3" style="font-size: 0.88rem;">
          <div class="col-6">
            <span class="text-muted d-block small">Complaint ID</span>
            <span class="fw-bold text-dark fs-6" id="summaryComplaintId"><?= htmlspecialchars($complaint['complaint_code']) ?></span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Current Status</span>
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
            <span class="status-badge <?= $st ?>" style="<?= $badge_style ?> font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;">
              <span style="width: 7px !important; height: 7px !important; border-radius: 50% !important; <?= $dot_style ?> display: inline-block !important;"></span> 
              <?= ucfirst(str_replace('_', ' ', $st)) ?>
            </span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Category</span>
            <span class="fw-semibold text-dark" id="summaryCategory"><?= htmlspecialchars($complaint['category_name']) ?></span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Village / Ward</span>
            <span class="fw-semibold text-dark" id="summaryLocation"><?= htmlspecialchars($complaint['ward_no']) ?></span>
          </div>
        </div>
        
        <div class="mb-3">
          <span class="text-muted d-block small mb-1">Complaint Title</span>
          <h6 class="fw-bold text-dark mb-0" id="summaryComplaintTitle"><?= htmlspecialchars($complaint['title']) ?></h6>
        </div>
        
        <div>
          <span class="text-muted d-block small mb-1">Description</span>
          <div class="p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color); font-size: 0.85rem; line-height: 1.5;" id="summaryDesc">
            <?= nl2br(htmlspecialchars($complaint['description'])) ?>
          </div>
        </div>
      </div>
      
      <!-- Existing / Current Photos Card -->
      <div class="card-panel">
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
          <i class="bi bi-images" style="color: var(--primary-color);"></i> Current Work Evidence Photos
        </h5>
        <div class="row g-3">
          <?php if (empty($currentPhotos)): ?>
            <div class="col-12 text-muted small py-2">No photo evidence uploaded yet for this complaint.</div>
          <?php else: ?>
            <?php foreach ($currentPhotos as $p): ?>
              <?php 
                $path = $p['photo_path'];
                $img_src = (strpos($path, 'uploads/') === 0) ? '../' . $path : $path;
              ?>
              <div class="col-6 col-sm-4">
                <div class="p-2 border rounded bg-white text-center shadow-sm h-100">
                  <a href="<?= htmlspecialchars($img_src) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($img_src) ?>" alt="Evidence" class="img-fluid rounded mb-2" style="height: 110px; width: 100%; object-fit: cover; border: 1px solid #E2D9CD;" onerror="this.onerror=null; this.src='../assets/field/gram_panchayat_seal.png';">
                  </a>
                  <div class="d-flex align-items-center justify-content-between px-1">
                    <span class="badge bg-secondary extra-small" style="font-size: 0.68rem;"><?= ucfirst($p['photo_type']) ?></span>
                    <span class="text-muted extra-small" style="font-size: 0.68rem;"><?= date('d M', strtotime($p['uploaded_at'])) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- Right Column: Upgraded Inspection Update & Upload Evidence Form Controls -->
    <div class="col-lg-6 animate-fade-in-up delay-3">
      <div class="card border-0 shadow-sm h-100 d-flex flex-column justify-content-between" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(220, 201, 167, 0.5) !important; padding: 1.6rem;">
        <div>
          <!-- Header Banner -->
          <div class="d-flex align-items-center justify-content-between p-3 mb-4" style="background: linear-gradient(135deg, #FBF8F2 0%, #F4ECE0 100%); border-radius: 14px; border: 1px solid rgba(220, 201, 167, 0.5);">
            <h5 class="fw-extrabold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
              <span class="d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-radius: 10px; background-color: #5E4D34; color: #FFFFFF;">
                <i class="bi bi-pencil-square fs-6"></i>
              </span>
              Log Inspection Update & Evidence
            </h5>
            <span class="badge bg-warning-subtle text-dark border border-warning px-2.5 py-1" style="border-radius: 50px; font-size: 0.75rem; font-weight: 700;">
              <i class="bi bi-shield-check me-1 text-warning"></i> Handbook Verified
            </span>
          </div>
          
          <!-- Complaint Status Selection -->
          <div class="mb-4">
            <label for="status" class="form-label fw-bold text-dark mb-1" style="font-size: 0.9rem;">
              Complaint Status <span class="text-danger">*</span>
            </label>
            <select name="status" id="status" class="form-select p-2.5" style="border-radius: 12px; border: 1.5px solid rgba(220, 201, 167, 0.6); font-size: 0.9rem;" required>
              <option value="assigned" <?= strtolower($complaint['status']) === 'assigned' ? 'selected' : '' ?>>Assigned</option>
              <option value="in_progress" <?= strtolower($complaint['status']) === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="resolved" <?= strtolower($complaint['status']) === 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>
            <div class="invalid-feedback">Please select a valid complaint status.</div>
          </div>
          
          <!-- Status Notes / Remarks Textarea -->
          <div class="mb-4">
            <label for="note" class="form-label fw-bold text-dark mb-1" style="font-size: 0.9rem;">
              Inspection Notes / Remarks <span class="text-danger">*</span>
            </label>
            <textarea name="note" id="note" class="form-control p-3" rows="4" maxlength="500" placeholder="Provide work details, materials used, or resolution notes..." style="border-radius: 14px; border: 1.5px solid rgba(220, 201, 167, 0.6); font-size: 0.9rem; background-color: #FCFBF8;" required></textarea>
            <div class="invalid-feedback">Please enter progress notes or inspection remarks.</div>
          </div>
          
          <!-- Photo Upload Cards -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.9rem;">
              Photo Evidence Uploads <span class="text-muted fw-normal small">(Before & After Work)</span>
            </label>
            
            <div class="row g-3">
              <!-- Before Work Photo -->
              <div class="col-sm-6">
                <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: #FAF7F2; border: 1.5px dashed #DCC9A7; border-radius: 16px; text-align: center;">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: rgba(220, 201, 167, 0.5) !important;">
                    <span class="fw-bold extra-small d-flex align-items-center gap-1" style="color: #5E4D34 !important; font-size: 0.78rem;">
                      <i class="bi bi-clock-history text-warning fs-6"></i> BEFORE WORK
                    </span>
                  </div>
                  
                  <div id="beforeUploadZone" onclick="document.getElementById('before_photo').click();" style="cursor: pointer;" class="py-2">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px; background: #FFF; color: #5E4D34; border-radius: 50%; font-size: 1.3rem; border: 1.5px solid rgba(220, 201, 167, 0.8);">
                      <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Upload Before Photo</div>
                    <div class="text-secondary extra-small mt-1">Click to browse gallery</div>
                    <input type="file" name="before_photo" id="before_photo" accept="image/*" class="form-control d-none">
                  </div>
                </div>
              </div>
              
              <!-- After Work Photo -->
              <div class="col-sm-6">
                <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: #F4FBF5; border: 1.5px dashed #A5D6A7; border-radius: 16px; text-align: center;">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: rgba(165, 214, 167, 0.5) !important;">
                    <span class="fw-bold extra-small d-flex align-items-center gap-1" style="color: #2E7D32 !important; font-size: 0.78rem;">
                      <i class="bi bi-check-circle-fill text-success fs-6"></i> AFTER WORK
                    </span>
                  </div>
                  
                  <div id="afterUploadZone" onclick="document.getElementById('after_photo').click();" style="cursor: pointer;" class="py-2">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px; background: #E8F5E9; color: #2E7D32; border-radius: 50%; font-size: 1.3rem; border: 1.5px solid #A5D6A7;">
                      <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Upload After Photo</div>
                    <div class="text-secondary extra-small mt-1">Click to browse gallery</div>
                    <input type="file" name="after_photo" id="after_photo" accept="image/*" class="form-control d-none">
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        
        <!-- Form Action Buttons -->
        <div class="d-flex align-items-center gap-3 pt-3 border-top" style="border-color: rgba(220, 201, 167, 0.4) !important;">
          <button type="submit" id="btnSaveSubmit" class="btn text-white fw-bold py-2.5 px-4 flex-grow-1 shadow-sm" style="background-color: #5E4D34; border-radius: 12px; font-size: 0.92rem; border: none;">
            <i class="bi bi-cloud-check-fill me-2"></i> Save Inspection Progress
          </button>
          <a href="assigned_complaints.php" class="btn btn-light text-secondary border py-2.5 px-4 fw-bold" style="border-radius: 12px; font-size: 0.88rem;">
            Cancel
          </a>
        </div>
        
      </div>
    </div>
    
  </div>

</form>

<?php
require_once 'footer.php';
?>
