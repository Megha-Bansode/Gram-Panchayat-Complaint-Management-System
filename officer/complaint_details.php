<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Complaint Details View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Full detail view of a selected complaint including citizen info, location, evidence photos, and status history timeline.
 */

$page_title = "Complaint Details - GPCMS";
require_once '../includes/officer_header.php';
require_once '../includes/officer_sidebar.php';

$complaint_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'CMP-0012';
?>

<!-- Header Navigation & Action Bar -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 animate-fade-in-up delay-1">
  <div>
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);" id="detailTitle">Road Potholes Repair near Primary School</h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <span class="fw-bold text-dark fs-6" id="detailId"><?php echo $complaint_id; ?></span>
      <span class="text-muted">&bull;</span>
      <span class="status-badge in-progress" id="detailStatusBadge"><i class="bi bi-clock"></i> In Progress</span>
      <span class="text-muted">&bull;</span>
      <span class="priority-badge high" id="detailPriorityBadge">High Priority</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="assigned_complaints.php" class="btn-secondary-action">
      <i class="bi bi-arrow-left"></i> Back to Complaints
    </a>
    <a href="save_progress.php?id=<?php echo $complaint_id; ?>" class="btn-primary-action">
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
          <span class="fw-semibold text-dark" id="detailCategory">Roads & Infrastructure</span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Location / Ward</span>
          <span class="fw-semibold text-dark" id="detailLocation">Shivapur Ward 2, Near Primary School Gate</span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Filing Date</span>
          <span class="fw-semibold text-dark">18 July 2026</span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Assigned Officer</span>
          <span class="fw-semibold text-dark">Field Officer (You)</span>
        </div>
      </div>
      
      <div>
        <span class="text-muted d-block small mb-1">Description</span>
        <div class="p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color); font-size: 0.88rem; line-height: 1.6;" id="detailDesc">
          The main road leading to the Gram Panchayat Primary School has developed multiple deep potholes following recent rainfall, posing risks for commuting school buses and local commuters.
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
          <span class="fw-bold text-dark" id="detailCitizen">Ramesh Kumar</span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted d-block small">Contact Number</span>
          <span class="fw-semibold text-dark" id="detailPhone">+91 98765 43210</span>
        </div>
      </div>
    </div>
    
    <!-- Attached Photo Evidence Card -->
    <div class="card-panel animate-fade-in-up delay-4">
      <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2 pb-2 border-bottom" style="border-color: var(--accent-color) !important;">
        <i class="bi bi-images" style="color: var(--primary-color);"></i> Photo Evidence
      </h5>
      
      <div class="row g-3 text-center">
        <div class="col-6 col-sm-4">
          <div class="p-3 border rounded bg-light">
            <i class="bi bi-image fs-1 text-muted d-block mb-1"></i>
            <span class="text-muted extra-small d-block">pothole_before.jpg</span>
          </div>
        </div>
        <div class="col-6 col-sm-4">
          <div class="p-3 border rounded bg-light">
            <i class="bi bi-image fs-1 text-muted d-block mb-1"></i>
            <span class="text-muted extra-small d-block">road_inspection.jpg</span>
          </div>
        </div>
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
        <li class="timeline-feed-item">
          <div class="timeline-feed-marker current"></div>
          <div class="timeline-feed-content">
            <div class="timeline-feed-title">Status Updated to In Progress</div>
            <div class="timeline-feed-date">21 Jul 2026, 11:30 AM</div>
            <p class="text-muted small mb-0 mt-1">Ground inspection complete. Asphalt patching work started.</p>
          </div>
        </li>
        <li class="timeline-feed-item">
          <div class="timeline-feed-marker"></div>
          <div class="timeline-feed-content">
            <div class="timeline-feed-title">Assigned to Field Officer</div>
            <div class="timeline-feed-date">19 Jul 2026, 09:15 AM</div>
            <p class="text-muted small mb-0 mt-1">Complaint assigned by Gram Sevak for field resolution.</p>
          </div>
        </li>
        <li class="timeline-feed-item">
          <div class="timeline-feed-marker completed"></div>
          <div class="timeline-feed-content">
            <div class="timeline-feed-title">Complaint Registered</div>
            <div class="timeline-feed-date">18 Jul 2026, 02:45 PM</div>
            <p class="text-muted small mb-0 mt-1">Complaint submitted by citizen via portal.</p>
          </div>
        </li>
      </ul>
    </div>
  </div>
  
</div>

<?php
require_once '../includes/officer_footer.php';
?>
