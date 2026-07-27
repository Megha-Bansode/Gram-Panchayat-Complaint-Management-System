<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Dashboard
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Primary dashboard interface for Field Officers displaying workload metrics, recent assigned complaints, quick actions, and activity logs.
 */

$page_title = "Field Officer Dashboard - GPCMS";
require_once 'officer_header.php';
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
      <h2 class="fw-extrabold mb-2 text-white" style="font-size: 1.85rem; letter-spacing: -0.01em;">Welcome Back, Sunita Deshmukh</h2>
      <p class="mb-0 text-white-50" style="font-size: 0.92rem; max-width: 620px; line-height: 1.45;">
        Monitor public grievances, record inspection progress, and review field completions for Pimpalgaon Gram Panchayat.
      </p>
    </div>
    <div class="col-lg-3 text-end d-none d-lg-block">
      <i class="bi bi-shield-check display-3 text-white opacity-25"></i>
    </div>
  </div>
</div>

<!-- 6 KPI Metric Cards Grid Matching Reference UI -->
<div class="row g-3 mb-4">
  
  <!-- Total Cases Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #876E47 !important; border: 1px solid #E2D9CD;">
      <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL CASES</span>
      <div class="display-6 fw-extrabold text-dark mt-1" style="font-size: 1.9rem;">5</div>
    </div>
  </div>
  
  <!-- Pending Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #F1C40F !important; border: 1px solid #E2D9CD;">
      <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">PENDING</span>
      <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #F39C12;">0</div>
    </div>
  </div>
  
  <!-- Assigned Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <a href="assigned_complaints.php?status=assigned" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #3498DB !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">ASSIGNED</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #3498DB;">4</div>
      </div>
    </a>
  </div>
  
  <!-- In Progress Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <a href="assigned_complaints.php?status=in_progress" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #8E6E45 !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">IN PROGRESS</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #8E6E45;">2</div>
      </div>
    </a>
  </div>
  
  <!-- Resolved Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <a href="assigned_complaints.php?status=resolved" class="text-decoration-none">
      <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #2ECC71 !important; border: 1px solid #E2D9CD;">
        <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">RESOLVED</span>
        <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #2ECC71;">1</div>
      </div>
    </a>
  </div>
  
  <!-- Rejected Card -->
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 text-center py-3 px-2 shadow-sm h-100" style="background: #FFFFFF; border-radius: 14px; border-top: 4px solid #E74C3C !important; border: 1px solid #E2D9CD;">
      <span class="text-muted fw-bold extra-small" style="font-size: 0.72rem; letter-spacing: 0.04em;">REJECTED</span>
      <div class="display-6 fw-extrabold mt-1" style="font-size: 1.9rem; color: #E74C3C;">0</div>
    </div>
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
            <tr>
              <td class="fw-bold">CMP-0012</td>
              <td>Road Potholes Repair near Primary School</td>
              <td>Roads & Infrastructure</td>
              <td>Shivapur Ward 2, Near Primary School Gate</td>
              <td><span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-green" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #2E7D32 !important; display: inline-block !important;"></span> Resolved</span></td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <a href="save_progress.php?id=CMP-0012" class="btn-table-update">Update</a>
                  <i class="bi bi-three-dots-vertical table-three-dots"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0024</td>
              <td>Street Light Outage on Main Bazaar</td>
              <td>Electricity Supply</td>
              <td>Shivapur Ward 1, Main Bazaar</td>
              <td><span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-blue" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #1565C0 !important; display: inline-block !important;"></span> Assigned</span></td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <a href="save_progress.php?id=CMP-0024" class="btn-table-update">Update</a>
                  <i class="bi bi-three-dots-vertical table-three-dots"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0035</td>
              <td>Water Pipeline Leakage at Temple Road</td>
              <td>Water & Sanitation</td>
              <td>Shivapur Ward 3, Temple Road</td>
              <td><span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-orange" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #D35400 !important; display: inline-block !important;"></span> In Progress</span></td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <a href="save_progress.php?id=CMP-0035" class="btn-table-update">Update</a>
                  <i class="bi bi-three-dots-vertical table-three-dots"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0041</td>
              <td>Drainage Overflow at Naka No 2</td>
              <td>Sanitation & Drainage</td>
              <td>Shivapur Ward 1, Naka No 2</td>
              <td><span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-blue" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #1565C0 !important; display: inline-block !important;"></span> Assigned</span></td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <a href="save_progress.php?id=CMP-0041" class="btn-table-update">Update</a>
                  <i class="bi bi-three-dots-vertical table-three-dots"></i>
                </div>
              </td>
            </tr>
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
        
        <!-- Activity Item 1: Photo Uploaded -->
        <div class="timeline-item animate-fade-in-up delay-1" style="position: relative !important; margin-bottom: 1.25rem !important;">
          <div class="timeline-badge-node" style="width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #1E88E5 !important; color: #FFFFFF !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 0.95rem !important; flex-shrink: 0 !important; box-shadow: 0 3px 10px rgba(30, 136, 229, 0.25) !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;" title="Photo Uploaded">
            <i class="bi bi-camera-fill" style="color: #FFFFFF !important;"></i>
          </div>
          <div class="timeline-body" style="margin-left: 50px !important;">
            <div class="timeline-title" style="font-weight: 800 !important; color: #241D15 !important; font-size: 0.88rem !important; line-height: 1.2 !important;">Progress Photo Uploaded</div>
            <div class="timeline-desc" style="color: #6E6255 !important; font-size: 0.82rem !important; margin-top: 3px !important; line-height: 1.4 !important;">
              Uploaded work evidence photo for <a href="save_progress.php?id=CMP-0012" class="cmp-link" style="color: #6E5A3B !important; font-weight: 700 !important; text-decoration: none !important;">CMP-0012</a>
            </div>
            <div class="timeline-time" style="font-size: 0.75rem !important; color: #8C7B6B !important; margin-top: 3px !important; display: flex !important; align-items: center !important; gap: 0.25rem !important;"><i class="bi bi-clock me-1"></i>Just now</div>
          </div>
        </div>

        <!-- Activity Item 2: Complaint Resolved -->
        <div class="timeline-item animate-fade-in-up delay-2" style="position: relative !important; margin-bottom: 1.25rem !important;">
          <div class="timeline-badge-node" style="width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #E8F5E9 !important; border: 1.5px solid #A5D6A7 !important; color: #2E7D32 !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 1rem !important; flex-shrink: 0 !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;" title="Complaint Resolved">
            <i class="bi bi-check-lg" style="color: #2E7D32 !important;"></i>
          </div>
          <div class="timeline-body" style="margin-left: 50px !important;">
            <div class="timeline-title" style="font-weight: 800 !important; color: #241D15 !important; font-size: 0.88rem !important; line-height: 1.2 !important;">Complaint Resolved</div>
            <div class="timeline-desc" style="color: #6E6255 !important; font-size: 0.82rem !important; margin-top: 3px !important; line-height: 1.4 !important;">
              <a href="complaint_details.php?id=CMP-0012" class="cmp-link" style="color: #6E5A3B !important; font-weight: 700 !important; text-decoration: none !important;">CMP-0012</a> marked as Resolved
            </div>
            <div class="timeline-time" style="font-size: 0.75rem !important; color: #8C7B6B !important; margin-top: 3px !important; display: flex !important; align-items: center !important; gap: 0.25rem !important;"><i class="bi bi-clock me-1"></i>Just now</div>
          </div>
        </div>

        <!-- Activity Item 3: Status Updated -->
        <div class="timeline-item animate-fade-in-up delay-3" style="position: relative !important; margin-bottom: 1.25rem !important;">
          <div class="timeline-badge-node" style="width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #FFF3E0 !important; border: 1.5px solid #FFCC80 !important; color: #E65100 !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 0.95rem !important; flex-shrink: 0 !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;" title="Status Updated">
            <i class="bi bi-pencil-square" style="color: #E65100 !important;"></i>
          </div>
          <div class="timeline-body" style="margin-left: 50px !important;">
            <div class="timeline-title" style="font-weight: 800 !important; color: #241D15 !important; font-size: 0.88rem !important; line-height: 1.2 !important;">Status Updated</div>
            <div class="timeline-desc" style="color: #6E6255 !important; font-size: 0.82rem !important; margin-top: 3px !important; line-height: 1.4 !important;">
              CMP-0012 status changed to <span class="badge-status-subtle" style="font-size: 0.74rem !important; font-weight: 800 !important; color: #D35400 !important; background-color: #FFF3E0 !important; padding: 2px 6px !important; border-radius: 4px !important;">IN PROGRESS</span>
            </div>
            <div class="timeline-time" style="font-size: 0.75rem !important; color: #8C7B6B !important; margin-top: 3px !important; display: flex !important; align-items: center !important; gap: 0.25rem !important;"><i class="bi bi-clock me-1"></i>Just now</div>
          </div>
        </div>

      </div>
    </div>
    
  </div>
  
</div>

<?php
require_once 'officer_footer.php';
?>
