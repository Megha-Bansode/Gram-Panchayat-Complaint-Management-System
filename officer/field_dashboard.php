<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Dashboard
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Primary dashboard interface for Field Officers displaying workload metrics, recent assigned complaints, quick actions, and activity logs.
 */

$page_title = "Field Officer Dashboard - GPCMS";
require_once '../includes/officer_header.php';
require_once '../includes/officer_sidebar.php';
?>

<!-- Welcome Banner & Page Title Header -->
<div class="row align-items-center mb-4 animate-fade-in-up delay-1">
  <div class="col-md-8">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Welcome, Field Officer</h2>
    <p class="text-muted mb-0">Overview of assigned complaints, field progress updates, and activity logs.</p>
  </div>
  <div class="col-md-4 text-md-end mt-2 mt-md-0">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-md-end mb-0">
        <li class="breadcrumb-item"><a href="field_dashboard.php" class="text-decoration-none" style="color: var(--primary-color);">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Overview</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Four Summary Metric Cards Row (SaaS Style + Top Accent + Count-Up Animation) -->
<div class="row g-3 mb-4">
  
  <!-- Assigned Card -->
  <div class="col-12 col-sm-6 col-lg-3 animate-fade-in-up delay-1">
    <a href="assigned_complaints.php?status=assigned" class="text-decoration-none">
      <div class="metric-card accent-blue">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">ASSIGNED COMPLAINTS</span>
            <div class="metric-value counter-metric" data-target="4">0</div>
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
  <div class="col-12 col-sm-6 col-lg-3 animate-fade-in-up delay-2">
    <a href="assigned_complaints.php?status=in_progress" class="text-decoration-none">
      <div class="metric-card accent-orange">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">IN PROGRESS</span>
            <div class="metric-value counter-metric" data-target="2">0</div>
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
  <div class="col-12 col-sm-6 col-lg-3 animate-fade-in-up delay-3">
    <a href="assigned_complaints.php?status=resolved" class="text-decoration-none">
      <div class="metric-card accent-green">
        <div class="d-flex justify-content-between align-items-start w-100">
          <div>
            <span class="metric-label">RESOLVED</span>
            <div class="metric-value counter-metric" data-target="12">0</div>
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
  <div class="col-12 col-sm-6 col-lg-3 animate-fade-in-up delay-4">
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

<!-- Main Dashboard Content Grid -->
<div class="row g-4 mb-4">
  
  <!-- Left Column: Recent Assigned Complaints Table -->
  <div class="col-lg-8 animate-fade-in-up delay-3">
    <div class="card-panel h-100">
      <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
          <i class="bi bi-list-task" style="color: var(--primary-color);"></i> Recent Assigned Complaints
        </h5>
        <a href="assigned_complaints.php" class="text-decoration-none small fw-semibold" style="color: var(--primary-color);">
          View All <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size: 0.88rem;">
          <thead style="background-color: rgba(138, 114, 76, 0.06); color: var(--text-dark);">
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
              <td>Road Potholes Repair</td>
              <td>Roads & Infra</td>
              <td>Ward 2</td>
              <td><span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span></td>
              <td class="text-end">
                <a href="save_progress.php?id=CMP-0012" class="btn btn-sm text-white" style="background-color: var(--primary-color); border-radius: 6px;">Update</a>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0024</td>
              <td>Street Light Outage</td>
              <td>Electricity</td>
              <td>Ward 1</td>
              <td><span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span></td>
              <td class="text-end">
                <a href="save_progress.php?id=CMP-0024" class="btn btn-sm text-white" style="background-color: var(--primary-color); border-radius: 6px;">Update</a>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0035</td>
              <td>Water Pipeline Leakage</td>
              <td>Water & Sanitation</td>
              <td>Ward 3</td>
              <td><span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span></td>
              <td class="text-end">
                <a href="save_progress.php?id=CMP-0035" class="btn btn-sm text-white" style="background-color: var(--primary-color); border-radius: 6px;">Update</a>
              </td>
            </tr>
            <tr>
              <td class="fw-bold">CMP-0041</td>
              <td>Drainage Overflow</td>
              <td>Sanitation</td>
              <td>Ward 1</td>
              <td><span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span></td>
              <td class="text-end">
                <a href="save_progress.php?id=CMP-0041" class="btn btn-sm text-white" style="background-color: var(--primary-color); border-radius: 6px;">Update</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Right Column: Quick Actions & Recent Activity Vertical Timeline -->
  <div class="col-lg-4 d-flex flex-column gap-4">
    
    <!-- Quick Actions Section -->
    <div class="card-panel animate-fade-in-up delay-3">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
        <i class="bi bi-lightning-charge" style="color: var(--primary-color);"></i> Quick Actions
      </h5>
      <div class="d-grid gap-2">
        <a href="assigned_complaints.php" class="btn-primary-action justify-content-center py-2">
          <i class="bi bi-clipboard-check"></i> View Assigned Complaints
        </a>
        <a href="save_progress.php?id=CMP-0012" class="btn-secondary-action justify-content-center py-2">
          <i class="bi bi-pencil-square"></i> Update Complaint Progress
        </a>
      </div>
    </div>
    
    <!-- Recent Activity Vertical Timeline Section -->
    <div class="card-panel flex-grow-1 animate-fade-in-up delay-4">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-journal-text" style="color: var(--primary-color);"></i> Recent Activity
      </h5>
      
      <div class="activity-timeline" id="activityTimelineContainer">
        
        <!-- Activity Item 1: Photo Uploaded -->
        <div class="timeline-item animate-fade-in-up delay-1">
          <div class="timeline-badge-node node-blue" title="Photo Uploaded">
            <i class="bi bi-camera-fill"></i>
          </div>
          <div class="timeline-body">
            <div class="timeline-title">Progress Photo Uploaded</div>
            <div class="timeline-desc">
              Added before photo for <a href="save_progress.php?id=CMP-0012" class="cmp-link">CMP-0012</a>
            </div>
            <div class="timeline-time"><i class="bi bi-clock me-1"></i>2 hours ago</div>
          </div>
        </div>

        <!-- Activity Item 2: Status Updated -->
        <div class="timeline-item animate-fade-in-up delay-2">
          <div class="timeline-badge-node node-orange" title="Status Updated">
            <i class="bi bi-arrow-repeat"></i>
          </div>
          <div class="timeline-body">
            <div class="timeline-title">Status Updated</div>
            <div class="timeline-desc">
              <a href="save_progress.php?id=CMP-0035" class="cmp-link">CMP-0035</a> changed to <span class="badge-status-subtle">In Progress</span>
            </div>
            <div class="timeline-time"><i class="bi bi-clock me-1"></i>Yesterday at 4:30 PM</div>
          </div>
        </div>

        <!-- Activity Item 3: Complaint Resolved -->
        <div class="timeline-item animate-fade-in-up delay-3">
          <div class="timeline-badge-node node-green" title="Complaint Resolved">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <div class="timeline-body">
            <div class="timeline-title">Complaint Resolved</div>
            <div class="timeline-desc">
              <a href="complaint_details.php?id=CMP-0008" class="cmp-link">CMP-0008</a> marked as Resolved
            </div>
            <div class="timeline-time"><i class="bi bi-clock me-1"></i>20 July 2026</div>
          </div>
        </div>

      </div>
    </div>
    
  </div>
  
</div>

<?php
require_once '../includes/officer_footer.php';
?>
