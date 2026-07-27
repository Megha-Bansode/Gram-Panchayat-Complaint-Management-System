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

<!-- Hero Banner Matching Reference Screenshot Pixel-for-Pixel -->
<div class="hero-welcome-card animate-fade-in-up delay-1">
  <!-- Village Landscape Line-Art Background SVG Watermark (NOT the logo) -->
  <svg class="hero-landscape-bg" viewBox="0 0 600 240" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Winding Rural Road -->
    <path d="M260 240 C340 210, 420 180, 460 170 C500 160, 540 165, 600 170" stroke="#8A724C" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.35"/>
    <path d="M300 240 C370 218, 435 195, 470 185 C505 175, 540 180, 600 185" stroke="#8A724C" stroke-width="1.2" stroke-dasharray="3 3" opacity="0.25"/>
    <!-- Water Tower / Windmill -->
    <path d="M510 170 L520 85 L530 170 M513 140 H527 M516 115 H524" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <ellipse cx="520" cy="75" rx="14" ry="10" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <path d="M520 65 V55 M513 60 H527" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <!-- Houses and Barns -->
    <path d="M430 170 V130 L455 110 L480 130 V170 H430 Z" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <path d="M445 170 V145 H465 V170" stroke="#8A724C" stroke-width="1.2" opacity="0.3"/>
    <path d="M360 175 V145 L380 130 L400 145 V175 H360 Z" stroke="#8A724C" stroke-width="1.5" opacity="0.25"/>
    <!-- Deciduous & Pine Trees -->
    <path d="M340 175 C330 155 330 135 345 125 C360 115 375 130 370 145 C380 145 385 160 375 175 Z" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <path d="M350 175 V140" stroke="#8A724C" stroke-width="1.5" opacity="0.3"/>
    <path d="M410 172 C402 155 405 140 418 132 C430 125 440 138 435 152 Z" stroke="#8A724C" stroke-width="1.2" opacity="0.25"/>
    <!-- Sun and Birds in Sky -->
    <circle cx="470" cy="60" r="18" stroke="#8A724C" stroke-width="1.5" stroke-dasharray="3 3" opacity="0.2"/>
    <path d="M390 70 Q395 62 400 70 Q405 62 410 70" stroke="#8A724C" stroke-width="1.2" opacity="0.3"/>
    <path d="M420 58 Q424 52 428 58 Q432 52 436 58" stroke="#8A724C" stroke-width="1.2" opacity="0.25"/>
  </svg>
  
  <div class="row align-items-center position-relative z-2">
    <div class="col-lg-8">
      <div class="hero-welcome-greeting">
        <span>👋</span> Welcome back,
      </div>
      <h1 class="hero-welcome-title">Field Officer</h1>
      <p class="hero-welcome-subtitle">
        Overview of assigned complaints, field progress updates, and activity logs.
      </p>
    </div>
    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
      <a href="assigned_complaints.php" class="btn-hero-manage">
        <i class="bi bi-box-seam me-1"></i> Manage Queue
      </a>
    </div>
  </div>
</div>

<!-- Four Summary Metric Cards Side-by-Side (Assigned, In Progress, Resolved, Overdue) -->
<div class="row g-3 mb-4">
  
  <!-- Assigned Card -->
  <div class="col-12 col-sm-6 col-xl-3 animate-fade-in-up delay-1">
    <a href="assigned_complaints.php?status=assigned" class="text-decoration-none">
      <div class="metric-card accent-blue">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">ASSIGNED COMPLAINTS</span>
            <div class="metric-value counter-metric" data-target="2">0</div>
            <span class="metric-subtext">4 active in queue</span>
          </div>
          <div class="metric-icon icon-assigned">
            <i class="bi bi-clipboard-check"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
  
  <!-- In Progress Card -->
  <div class="col-12 col-sm-6 col-xl-3 animate-fade-in-up delay-2">
    <a href="assigned_complaints.php?status=in_progress" class="text-decoration-none">
      <div class="metric-card accent-orange">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">IN PROGRESS</span>
            <div class="metric-value counter-metric" data-target="1">0</div>
            <span class="metric-subtext">2 under repair</span>
          </div>
          <div class="metric-icon icon-progress">
            <i class="bi bi-clock-history"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Resolved Card -->
  <div class="col-12 col-sm-6 col-xl-3 animate-fade-in-up delay-3">
    <a href="assigned_complaints.php?status=resolved" class="text-decoration-none">
      <div class="metric-card accent-green">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">RESOLVED</span>
            <div class="metric-value counter-metric" data-target="13">0</div>
            <span class="metric-subtext">12 resolved this month</span>
          </div>
          <div class="metric-icon icon-completed">
            <i class="bi bi-check-circle"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Overdue Card -->
  <div class="col-12 col-sm-6 col-xl-3 animate-fade-in-up delay-4">
    <a href="assigned_complaints.php" class="text-decoration-none">
      <div class="metric-card accent-red">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">OVERDUE</span>
            <div class="metric-value counter-metric" data-target="1">0</div>
            <span class="metric-subtext">1 pending action</span>
          </div>
          <div class="metric-icon icon-overdue">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
        </div>
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
  
  <!-- Right Column: Field Command Center & Recent Activity -->
  <div class="col-lg-4 d-flex flex-column gap-4">
    
    <!-- Field Command Center Section -->
    <div class="card-panel animate-fade-in-up delay-3">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
        <i class="bi bi-lightning-charge-fill" style="color: #8A724C;"></i> Field Command Center
      </h5>
      <div class="d-flex flex-column">
        <a href="assigned_complaints.php" class="command-card blue-card" style="display: flex !important; align-items: center !important; justify-content: space-between !important; padding: 1rem 1.25rem !important; border-radius: 16px !important; background-color: #ECF3FC !important; text-decoration: none !important; color: #1E1B18 !important; margin-bottom: 0.85rem !important; border: none !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="command-card-icon" style="width: 52px !important; height: 52px !important; border-radius: 16px !important; background-color: #D6E6F9 !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                <line x1="9" y1="12" x2="15" y2="12"></line>
                <line x1="9" y1="16" x2="13" y2="16"></line>
              </svg>
            </div>
            <div>
              <div class="command-card-title" style="font-weight: 700 !important; font-size: 0.95rem !important; color: #1E1B18 !important; text-decoration: none !important;">View Assigned Complaints</div>
              <div class="command-card-desc" style="font-size: 0.82rem !important; color: #6E6255 !important; font-weight: 500 !important; line-height: 1.35 !important; text-decoration: none !important; margin-top: 2px !important;">Review all complaints<br>assigned to you</div>
            </div>
          </div>
          <i class="bi bi-chevron-right command-card-chevron" style="font-size: 1.15rem !important; color: #3D3328 !important; font-weight: 700 !important;"></i>
        </a>
        <a href="save_progress.php?id=CMP-0012" class="command-card orange-card" style="display: flex !important; align-items: center !important; justify-content: space-between !important; padding: 1rem 1.25rem !important; border-radius: 16px !important; background-color: #FDF5E8 !important; text-decoration: none !important; color: #1E1B18 !important; border: none !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="command-card-icon" style="width: 52px !important; height: 52px !important; border-radius: 16px !important; background-color: #FCE6CE !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
              </svg>
            </div>
            <div>
              <div class="command-card-title" style="font-weight: 700 !important; font-size: 0.95rem !important; color: #1E1B18 !important; text-decoration: none !important;">Update Complaint Progress</div>
              <div class="command-card-desc" style="font-size: 0.82rem !important; color: #6E6255 !important; font-weight: 500 !important; line-height: 1.35 !important; text-decoration: none !important; margin-top: 2px !important;">Log progress updates &amp;<br>upload photos</div>
            </div>
          </div>
          <i class="bi bi-chevron-right command-card-chevron" style="font-size: 1.15rem !important; color: #3D3328 !important; font-weight: 700 !important;"></i>
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
