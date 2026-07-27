<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Assigned Complaints View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: View, search, and filter assigned complaints list matching Reference Image 1.
 */

$page_title = "Assigned Complaints - GPCMS";
require_once 'officer_header.php';
require_once 'officer_sidebar.php';
?>

<!-- Hero Section Header Matching Reference Image 1 -->
<div class="assigned-hero-card mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #FBF8F2 0%, #F4ECE0 100%); border-radius: 20px; padding: 1.6rem 2.25rem; border: 1px solid rgba(220, 201, 167, 0.4); box-shadow: 0 4px 20px -4px rgba(80, 60, 30, 0.05);">
  <div class="position-relative z-2">
    <nav aria-label="breadcrumb" class="mb-2">
      <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
        <li class="breadcrumb-item"><a href="field_dashboard.php" class="text-decoration-none" style="color: #8C7B65;">Dashboard</a></li>
        <li class="breadcrumb-item active fw-semibold" aria-current="page" style="color: #4A3E31;">Assigned Complaints</li>
      </ol>
    </nav>
    <h2 class="fw-extrabold text-dark mb-1" style="font-size: 2.1rem; font-weight: 800; letter-spacing: -0.5px;">Assigned Complaints</h2>
    <p class="mb-0" style="color: #6E6255; font-size: 0.9rem;">View all complaints assigned to you. Update status and track progress.</p>
  </div>
</div>

<!-- Search & Filter Controls Bar Matching Reference Image 1 -->
<div class="card border-0 mb-4 shadow-sm" style="background: #FFFFFF; border-radius: 18px; padding: 1rem 1.25rem; border: 1px solid rgba(220, 201, 167, 0.5) !important;">
  <form id="filterForm" onsubmit="event.preventDefault();">
    <div class="d-flex flex-wrap align-items-center gap-3">
      
      <!-- Search Input Field -->
      <div class="flex-grow-1" style="min-width: 260px;">
        <div class="position-relative">
          <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
          <input type="text" id="searchComplaint" class="form-control ps-5 pe-4 py-2" placeholder="Search by ID, title or location..." style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; background-color: #FCFBF8;">
          <button type="button" id="btnClearSearch" class="btn-clear-search d-none position-absolute top-50 end-0 translate-middle-y me-3 border-0 bg-transparent text-muted" title="Clear Search">
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>
      </div>
      
      <!-- All Categories Dropdown -->
      <div class="dropdown custom-filter-dropdown" style="min-width: 170px;">
        <button type="button" id="categoryDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; color: #3A3226; font-weight: 500;">
          <span class="selected-label">All Categories</span>
          <i class="bi bi-chevron-down ms-2 small text-muted"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" style="border-radius: 12px; font-size: 0.85rem;">
          <li><button type="button" class="dropdown-item custom-dropdown-item active" data-value="" data-filter="category">All Categories</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="Roads & Infrastructure" data-filter="category">Roads & Infrastructure</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="Electricity Supply" data-filter="category">Electricity Supply</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="Water & Sanitation" data-filter="category">Water & Sanitation</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="Sanitation & Drainage" data-filter="category">Sanitation & Drainage</button></li>
        </ul>
        <select id="filterCategory" class="d-none">
          <option value="">All Categories</option>
          <option value="Roads & Infrastructure">Roads & Infrastructure</option>
          <option value="Electricity Supply">Electricity Supply</option>
          <option value="Water & Sanitation">Water & Sanitation</option>
          <option value="Sanitation & Drainage">Sanitation & Drainage</option>
        </select>
      </div>
      
      <!-- All Status Dropdown -->
      <div class="dropdown custom-filter-dropdown" style="min-width: 150px;">
        <button type="button" id="statusDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; color: #3A3226; font-weight: 500;">
          <span class="selected-label">All Status</span>
          <i class="bi bi-chevron-down ms-2 small text-muted"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" style="border-radius: 12px; font-size: 0.85rem;">
          <li><button type="button" class="dropdown-item custom-dropdown-item active" data-value="" data-filter="status">All Status</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="assigned" data-filter="status"><span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.75rem !important; font-weight: 700 !important; padding: 3px 9px !important; border-radius: 50px !important;"><span style="width: 6px; height: 6px; border-radius: 50%; background-color: #1565C0; display: inline-block;"></span> Assigned</span></button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="in_progress" data-filter="status"><span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.75rem !important; font-weight: 700 !important; padding: 3px 9px !important; border-radius: 50px !important;"><span style="width: 6px; height: 6px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span></button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="resolved" data-filter="status"><span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.75rem !important; font-weight: 700 !important; padding: 3px 9px !important; border-radius: 50px !important;"><span style="width: 6px; height: 6px; border-radius: 50%; background-color: #2E7D32; display: inline-block;"></span> Resolved</span></button></li>
        </ul>
        <select id="filterStatus" class="d-none">
          <option value="">All Status</option>
          <option value="assigned">Assigned</option>
          <option value="in_progress">In Progress</option>
          <option value="resolved">Resolved</option>
        </select>
      </div>

      <!-- Select Date Range Dropdown -->
      <div class="dropdown custom-filter-dropdown" style="min-width: 190px;">
        <button type="button" id="dateDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; color: #3A3226; font-weight: 500;">
          <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar-event text-muted"></i> <span class="selected-label">Select Date Range</span></span>
          <i class="bi bi-chevron-down ms-2 small text-muted"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" style="border-radius: 12px; font-size: 0.85rem;">
          <li><button type="button" class="dropdown-item custom-dropdown-item active" data-value="" data-filter="date">All Dates</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="today" data-filter="date">Today</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="this_week" data-filter="date">This Week</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item" data-value="this_month" data-filter="date">This Month</button></li>
        </ul>
        <select id="filterDate" class="d-none">
          <option value="">Select Date Range</option>
          <option value="today">Today</option>
          <option value="this_week">This Week</option>
          <option value="this_month">This Month</option>
        </select>
      </div>
      
      <!-- Reset Filters Button -->
      <div>
        <button type="reset" id="btnResetFilter" class="btn text-white d-flex align-items-center gap-2 py-2 px-3 fw-bold" style="background-color: #5E4D34; border-radius: 12px; font-size: 0.88rem; border: none; box-shadow: 0 4px 12px rgba(94, 77, 52, 0.2);">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
        </button>
      </div>

    </div>
  </form>
</div>

<!-- Data Table Card Matching Reference Image 1 -->
<div class="card border-0 shadow-sm" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(220, 201, 167, 0.5) !important; padding: 1.25rem;">
  
  <div id="emptyState" class="text-center py-5 d-none">
    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
    <h5 class="fw-bold text-dark mb-1">No Complaints Found</h5>
    <p class="text-muted small mb-3">No assigned complaints match your selected search or filter criteria.</p>
    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #5E4D34; border-radius: 8px;" onclick="document.getElementById('filterForm').reset();">
      <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search Filters
    </button>
  </div>

  <div id="tableContainer" class="table-responsive">
    <table class="table align-middle mb-0" style="font-size: 0.88rem;">
      <thead style="background-color: #FAF6EF; color: #3A3226; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.4px;">
        <tr>
          <th scope="col" class="py-3 px-3 fw-bold">ID</th>
          <th scope="col" class="py-3 px-3 fw-bold">Title</th>
          <th scope="col" class="py-3 px-3 fw-bold">Category</th>
          <th scope="col" class="py-3 px-3 fw-bold">Location</th>
          <th scope="col" class="py-3 px-3 fw-bold">Assigned Date</th>
          <th scope="col" class="py-3 px-3 fw-bold">Status</th>
          <th scope="col" class="py-3 px-3 text-end fw-bold">Action</th>
        </tr>
      </thead>
      <tbody id="assignedTableBody">
        <tr style="border-bottom: 1px solid rgba(220, 201, 167, 0.3);">
          <td class="fw-bold text-dark px-3 py-3">CMP-0012</td>
          <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;">Road Potholes Repair near Primary School</td>
          <td class="px-3 py-3">Roads & Infrastructure</td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> Shivapur Ward 2, Near Primary School Gate</span></td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> 23 May 2026<br><span class="text-muted extra-small ms-4">10:30 AM</span></span></td>
          <td class="px-3 py-3"><span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #2E7D32; display: inline-block;"></span> Resolved</span></td>
          <td class="text-end px-3 py-3">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=CMP-0012" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=CMP-0012" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid rgba(220, 201, 167, 0.3);">
          <td class="fw-bold text-dark px-3 py-3">CMP-0024</td>
          <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;">Street Light Outage on Main Bazaar</td>
          <td class="px-3 py-3">Electricity Supply</td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> Shivapur Ward 1, Main Bazaar</span></td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> 22 May 2026<br><span class="text-muted extra-small ms-4">07:15 PM</span></span></td>
          <td class="px-3 py-3"><span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #1565C0; display: inline-block;"></span> Assigned</span></td>
          <td class="text-end px-3 py-3">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=CMP-0024" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=CMP-0024" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid rgba(220, 201, 167, 0.3);">
          <td class="fw-bold text-dark px-3 py-3">CMP-0035</td>
          <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;">Water Pipeline Leakage at Temple Road</td>
          <td class="px-3 py-3">Water & Sanitation</td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> Shivapur Ward 3, Temple Road</span></td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> 21 May 2026<br><span class="text-muted extra-small ms-4">09:40 AM</span></span></td>
          <td class="px-3 py-3"><span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span></td>
          <td class="text-end px-3 py-3">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=CMP-0035" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=CMP-0035" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid rgba(220, 201, 167, 0.3);">
          <td class="fw-bold text-dark px-3 py-3">CMP-0041</td>
          <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;">Drainage Overflow at Naka No 2</td>
          <td class="px-3 py-3">Sanitation & Drainage</td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> Shivapur Ward 1, Naka No 2</span></td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> 20 May 2026<br><span class="text-muted extra-small ms-4">06:20 PM</span></span></td>
          <td class="px-3 py-3"><span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #1565C0; display: inline-block;"></span> Assigned</span></td>
          <td class="text-end px-3 py-3">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=CMP-0041" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=CMP-0041" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
        <tr>
          <td class="fw-bold text-dark px-3 py-3">CMP-0045</td>
          <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;">Garbage Collection Delayed</td>
          <td class="px-3 py-3">Sanitation & Drainage</td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> Shivapur Ward 4, Market Area</span></td>
          <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> 19 May 2026<br><span class="text-muted extra-small ms-4">11:05 AM</span></span></td>
          <td class="px-3 py-3"><span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span></td>
          <td class="text-end px-3 py-3">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=CMP-0045" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=CMP-0045" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <!-- Pagination Controls Matching Reference Image 1 -->
  <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: rgba(220, 201, 167, 0.4) !important;">
    <span class="text-muted small">Showing 1 to 5 of 18 complaints</span>
    <div class="d-inline-flex align-items-center gap-1">
      <button class="btn btn-sm btn-light border px-2 text-muted" style="border-radius: 8px;">&lt;</button>
      <button class="btn btn-sm text-white fw-bold px-3" style="background-color: #5E4D34; border-radius: 8px;">1</button>
      <button class="btn btn-sm btn-light border px-3 text-dark" style="border-radius: 8px;">2</button>
      <button class="btn btn-sm btn-light border px-3 text-dark" style="border-radius: 8px;">3</button>
      <button class="btn btn-sm btn-light border px-3 text-dark" style="border-radius: 8px;">4</button>
      <button class="btn btn-sm btn-light border px-2 text-dark" style="border-radius: 8px;">&gt;</button>
    </div>
  </div>
  
</div>

<?php
require_once 'officer_footer.php';
?>
