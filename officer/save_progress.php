<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Save Progress View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Form interface for Field Officers to update complaint status, enter inspection notes, and upload photo evidence.
 */

$page_title = "Save Progress - GPCMS";
require_once 'officer_header.php';
require_once 'officer_sidebar.php';

$complaint_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'CMP-0012';
?>

<!-- Header & Breadcrumbs -->
<div class="row align-items-center mb-4 animate-fade-in-up delay-1">
  <div class="col-md-8">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Field Inspection Workspace</h2>
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

<!-- 8. Main Inspection Workspace Form Wrapper -->
<form id="saveProgressForm" action="save_progress.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
  
  <input type="hidden" name="complaint_id" id="hiddenComplaintId" value="<?php echo $complaint_id; ?>">

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
            <span class="fw-bold text-dark fs-6" id="summaryComplaintId"><?php echo $complaint_id; ?></span>
          </div>
          <div class="col-6">
            <span class="text-muted d-block small">Current Status</span>
            <span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-orange" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #D35400 !important; display: inline-block !important;"></span> In Progress</span>
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
          <i class="bi bi-images" style="color: var(--primary-color);"></i> Current Work Evidence Photos
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

    <!-- Right Column: Upgraded Attractive Log Inspection Update & Upload Evidence Form Controls -->
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
              <i class="bi bi-shield-check me-1 text-warning"></i> Contract Verified
            </span>
          </div>
          
          <!-- Custom Status Save Dropdown -->
          <div class="mb-4">
            <label for="statusSaveDropdownTrigger" class="form-label fw-bold text-dark mb-1" style="font-size: 0.9rem;">
              Complaint Status <span class="text-danger">*</span>
            </label>
            <div class="dropdown custom-filter-dropdown">
              <button type="button" id="statusSaveDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2.5 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1.5px solid rgba(220, 201, 167, 0.6); font-size: 0.9rem;">
                <span class="selected-label"><span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span></span>
                <i class="bi bi-chevron-down text-muted"></i>
              </button>
              <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" aria-labelledby="statusSaveDropdownTrigger" style="border-radius: 12px;">
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item py-2" data-value="assigned" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #1565C0; display: inline-block;"></span> Assigned</span>
                    </span>
                    <i class="bi bi-check2 check-icon"></i>
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item active py-2" data-value="in_progress" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span>
                    </span>
                    <i class="bi bi-check2 check-icon"></i>
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item custom-dropdown-item py-2" data-value="resolved" data-target-input="status">
                    <span class="d-flex align-items-center gap-2">
                      <span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #2E7D32; display: inline-block;"></span> Resolved</span>
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
            <div class="form-text extra-small text-muted mt-1">Allowed statuses per handbook contract: Assigned, In Progress, Resolved.</div>
          </div>
          
          <!-- Status Notes / Remarks Textarea -->
          <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="note" class="form-label fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                Inspection Notes / Remarks <span class="text-danger">*</span>
              </label>
              <span class="badge bg-light text-dark border px-2.5 py-1" style="border-radius: 6px; font-size: 0.75rem; font-weight: 600;"><span id="charCount">68</span> / 500 characters</span>
            </div>
            <textarea name="note" id="note" class="form-control p-3" rows="4" maxlength="500" placeholder="Provide work details, materials used, or resolution notes..." style="border-radius: 14px; border: 1.5px solid rgba(220, 201, 167, 0.6); font-size: 0.9rem; background-color: #FCFBF8;" required>Compaction and asphalt patching started. Ground repair in progress.</textarea>
            <div class="invalid-feedback">Please enter progress notes or inspection remarks.</div>
          </div>
          
          <!-- Separate Side-by-Side Dedicated Photo Upload Cards (Before & After Work) -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.9rem;">
              Photo Evidence Uploads <span class="text-muted fw-normal small">(Before & After Work)</span>
            </label>
            
            <div class="row g-3">
              <!-- Card 1: Before Work Photo -->
              <div class="col-sm-6">
                <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: #FAF7F2; border: 1.5px dashed #DCC9A7; border-radius: 16px; text-align: center; transition: all 0.3s ease;">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: rgba(220, 201, 167, 0.5) !important;">
                    <span class="fw-bold extra-small d-flex align-items-center gap-1" style="color: #5E4D34 !important; font-size: 0.78rem;">
                      <i class="bi bi-clock-history text-warning fs-6"></i> BEFORE WORK
                    </span>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-2" style="font-size: 0.65rem; border-radius: 50px;">Required</span>
                  </div>
                  
                  <div id="beforeUploadZone" style="cursor: pointer;" class="py-2">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px; background: #FFF; color: #5E4D34; border-radius: 50%; font-size: 1.3rem; border: 1.5px solid rgba(220, 201, 167, 0.8); box-shadow: 0 4px 10px rgba(94, 77, 52, 0.08);">
                      <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Upload Before Photo</div>
                    <div class="text-secondary extra-small mt-1">Click to browse gallery (JPG, PNG)</div>
                    <input type="file" name="before_photo" id="before_photo" accept="image/jpeg,image/png" class="d-none">
                  </div>
                </div>
              </div>
              
              <!-- Card 2: After Work Photo -->
              <div class="col-sm-6">
                <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: #F4FBF5; border: 1.5px dashed #A5D6A7; border-radius: 16px; text-align: center; transition: all 0.3s ease;">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: rgba(165, 214, 167, 0.5) !important;">
                    <span class="fw-bold extra-small d-flex align-items-center gap-1" style="color: #2E7D32 !important; font-size: 0.78rem;">
                      <i class="bi bi-check-circle-fill text-success fs-6"></i> AFTER WORK
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success px-2" style="font-size: 0.65rem; border-radius: 50px;">Resolution</span>
                  </div>
                  
                  <div id="afterUploadZone" style="cursor: pointer;" class="py-2">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px; background: #E8F5E9; color: #2E7D32; border-radius: 50%; font-size: 1.3rem; border: 1.5px solid #A5D6A7; box-shadow: 0 4px 10px rgba(46, 125, 50, 0.12);">
                      <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Upload After Photo</div>
                    <div class="text-secondary extra-small mt-1">Click to browse gallery (JPG, PNG)</div>
                    <input type="file" name="after_photo" id="after_photo" accept="image/jpeg,image/png" class="d-none">
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        
        <!-- Form Action Buttons -->
        <div class="d-flex align-items-center gap-3 pt-3 border-top" style="border-color: rgba(220, 201, 167, 0.4) !important;">
          <button type="submit" id="btnSaveSubmit" class="btn text-white fw-bold py-2.5 px-4 flex-grow-1 shadow-sm" style="background-color: #5E4D34; border-radius: 12px; font-size: 0.92rem; border: none; box-shadow: 0 6px 18px rgba(94, 77, 52, 0.25);">
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
require_once 'officer_footer.php';
?>
