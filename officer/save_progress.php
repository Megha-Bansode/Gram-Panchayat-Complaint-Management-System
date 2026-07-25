<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Save Progress View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Form interface for Field Officers to update complaint status, enter inspection notes, and upload photo evidence.
 */

$page_title = "Save Progress - GPCMS";
require_once '../includes/officer_header.php';
require_once '../includes/officer_sidebar.php';

$complaint_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'CMP-0012';
?>

<!-- Header & Breadcrumbs -->
<div class="row align-items-center mb-4 animate-fade-in-up delay-1">
  <div class="col-md-8">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Update Complaint Progress</h2>
    <p class="text-muted mb-0">Record inspection updates, update status, add work notes, and upload photo evidence.</p>
  </div>
  <div class="col-md-4 text-md-end mt-2 mt-md-0">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-md-end mb-0">
        <li class="breadcrumb-item"><a href="field_dashboard.php" class="text-decoration-none" style="color: var(--primary-color);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="assigned_complaints.php" class="text-decoration-none" style="color: var(--primary-color);">Assigned Complaints</a></li>
        <li class="breadcrumb-item active" aria-current="page">Save Progress</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Main Form Wrapper -->
<form id="saveProgressForm" action="save_progress.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
  
  <input type="hidden" name="complaint_id" id="hiddenComplaintId" value="<?php echo $complaint_id; ?>">

  <div class="row g-4 mb-4">
    
    <!-- Left Column: Selected Complaint Summary Card & Existing Photos -->
    <div class="col-lg-6 d-flex flex-column gap-4 animate-fade-in-up delay-2">
      
      <!-- Summary Info Box -->
      <div class="card-panel">
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
          <i class="bi bi-file-earmark-text" style="color: var(--primary-color);"></i> Selected Complaint Details
        </h5>
        
        <div class="row g-3 mb-3" style="font-size: 0.88rem;">
          <div class="col-6">
            <span class="text-muted d-block small">Complaint ID</span>
            <span class="fw-bold text-dark fs-6" id="summaryComplaintId"><?php echo $complaint_id; ?></span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Current Status</span>
            <span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Category</span>
            <span class="fw-semibold text-dark" id="summaryCategory">Roads & Infrastructure</span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Village / Ward</span>
            <span class="fw-semibold text-dark" id="summaryLocation">Shivapur Ward 2</span>
          </div>
        </div>
        
        <div class="mb-3">
          <span class="text-muted d-block small mb-1">Complaint Title</span>
          <h6 class="fw-bold text-dark mb-0" id="summaryComplaintTitle">Road Potholes Repair near Primary School</h6>
        </div>
        
        <div>
          <span class="text-muted d-block small mb-1">Description</span>
          <div class="p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color); font-size: 0.85rem; line-height: 1.5;" id="summaryDesc">
            The main road near the primary school is heavily damaged with multiple deep potholes causing safety concerns for commuting school buses.
          </div>
        </div>
      </div>
      
      <!-- Existing / Current Photos Card -->
      <div class="card-panel">
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
          <i class="bi bi-images" style="color: var(--primary-color);"></i> Current Work Evidence
        </h5>
        <div class="row g-3 text-center">
          <div class="col-6">
            <span class="text-muted d-block small mb-2">Before Work Evidence</span>
            <div class="p-3 border rounded bg-light">
              <i class="bi bi-image fs-1 text-muted d-block mb-1"></i>
              <span class="text-muted extra-small d-block">pothole_before.jpg</span>
            </div>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small mb-2">After Work Evidence</span>
            <div class="p-3 border rounded bg-light">
              <i class="bi bi-image fs-1 text-muted d-block mb-1"></i>
              <span class="text-muted extra-small d-block">No photo uploaded yet</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Right Column: Update Progress Form Controls -->
    <div class="col-lg-6 animate-fade-in-up delay-3">
      <div class="card-panel h-100 d-flex flex-column justify-content-between">
        <div>
          <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
            <i class="bi bi-pencil-square" style="color: var(--primary-color);"></i> Update Status & Evidence
          </h5>
          
          <!-- Custom Status Save Dropdown (Bootstrap 5 + JS) -->
          <div class="mb-3">
            <label for="statusSaveDropdownTrigger" class="form-label fw-semibold text-dark">Complaint Status <span class="text-danger">*</span></label>
            <div class="dropdown custom-filter-dropdown">
              <button type="button" id="statusSaveDropdownTrigger" class="btn custom-dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="selected-label"><span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span></span>
                <i class="bi bi-chevron-down dropdown-chevron"></i>
              </button>
              <ul class="dropdown-menu custom-dropdown-menu w-100" aria-labelledby="statusSaveDropdownTrigger">
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item" data-value="assigned" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span>
                    </span>
                    <i class="bi bi-check2 check-icon"></i>
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item active" data-value="in_progress" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span>
                    </span>
                    <i class="bi bi-check2 check-icon"></i>
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item" data-value="resolved" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge resolved"><i class="bi bi-check-circle-fill"></i> Resolved</span>
                    </span>
                    <i class="bi bi-check2 check-icon"></i>
                  </button>
                </li>
              </ul>
              <!-- Hidden native select tag for 100% PHP/MySQL form submission -->
              <select name="status" id="status" class="d-none" required>
                <option value="assigned">Assigned</option>
                <option value="in_progress" selected>In Progress</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>
            <div class="invalid-feedback">Please select a valid complaint status.</div>
            <div class="form-text small text-muted">Allowed statuses per handbook contract: Assigned, In Progress, Resolved.</div>
          </div>
          
          <!-- Status Notes / Remarks Textarea -->
          <div class="mb-3">
            <label for="note" class="form-label fw-semibold text-dark">Inspection Notes / Remarks <span class="text-danger">*</span></label>
            <textarea name="note" id="note" class="form-control" rows="4" maxlength="500" placeholder="Provide work details, materials used, or resolution notes..." required>Compaction and asphalt patching started. Ground repair in progress.</textarea>
            <div class="invalid-feedback">Please enter progress notes or inspection remarks.</div>
            <div class="char-counter"><span id="charCount">68</span> / 500 characters</div>
          </div>
          
          <!-- Photo Upload Section -->
          <div class="row g-3 mb-3">
            <!-- Before Photo Upload Zone -->
            <div class="col-sm-6">
              <label class="form-label fw-semibold text-dark small">Before Work Photo</label>
              <div class="upload-zone" id="beforeUploadZone" style="cursor: pointer;">
                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                <div class="small fw-semibold text-dark">Upload Before Photo</div>
                <div class="text-muted extra-small">Click to browse gallery (JPG, PNG)</div>
                <input type="file" name="before_photo" id="before_photo" accept="image/jpeg,image/png" class="d-none">
              </div>
            </div>
            
            <!-- After Photo Upload Zone -->
            <div class="col-sm-6">
              <label class="form-label fw-semibold text-dark small">After Work Photo</label>
              <div class="upload-zone" id="afterUploadZone" style="cursor: pointer;">
                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                <div class="small fw-semibold text-dark">Upload After Photo</div>
                <div class="text-muted extra-small">Click to browse gallery (JPG, PNG)</div>
                <input type="file" name="after_photo" id="after_photo" accept="image/jpeg,image/png" class="d-none">
              </div>
            </div>
          </div>

        </div>
        
        <!-- Form Action Buttons -->
        <div class="d-flex align-items-center gap-3 pt-3 mt-3 border-top" style="border-color: var(--accent-color) !important;">
          <button type="submit" id="btnSaveSubmit" class="btn-primary-action py-2 flex-grow-1 justify-content-center">
            <i class="bi bi-save me-1"></i> Save Progress
          </button>
          <a href="assigned_complaints.php" class="btn-secondary-action py-2 flex-grow-1 justify-content-center">
            Cancel
          </a>
        </div>
        
      </div>
    </div>
    
  </div>

</form>

<?php
require_once '../includes/officer_footer.php';
?>
