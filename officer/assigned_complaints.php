<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Assigned Complaints View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: View, search, and filter assigned complaints list with action triggers.
 */

$page_title = "Assigned Complaints - GPCMS";
require_once '../includes/officer_header.php';
require_once '../includes/officer_sidebar.php';
?>

<!-- Header & Breadcrumbs -->
<div class="row align-items-center mb-4 animate-fade-in-up delay-1">
  <div class="col-md-8">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Assigned Complaints</h2>
    <p class="text-muted mb-0">Search, filter, and review complaints assigned to your field jurisdiction.</p>
  </div>
  <div class="col-md-4 text-md-end mt-2 mt-md-0">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-md-end mb-0">
        <li class="breadcrumb-item"><a href="field_dashboard.php" class="text-decoration-none" style="color: var(--primary-color);">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Assigned Complaints</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Search & Filter Bar Card Container -->
<div class="filter-bar-card mb-4 animate-fade-in-up delay-2">
  <form id="filterForm" onsubmit="event.preventDefault();">
    <div class="row g-3 align-items-end">
      
      <!-- Search Input Field with Icon & Clear Button 'X' -->
      <div class="col-lg-5 col-md-6">
        <label for="searchComplaint" class="form-label fw-semibold small text-dark mb-1">Search Complaints</label>
        <div class="search-input-wrapper">
          <i class="bi bi-search search-icon"></i>
          <input type="text" id="searchComplaint" class="form-control filter-input" placeholder="Search by ID, Citizen Name, or Title..." aria-label="Search Complaints">
          <button type="button" id="btnClearSearch" class="btn-clear-search d-none" title="Clear Search">
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>
      </div>
      
      <!-- Custom Status Filter Dropdown (Bootstrap 5 + JS) -->
      <div class="col-lg-3 col-md-3">
        <label for="statusDropdownTrigger" class="form-label fw-semibold small text-dark mb-1">Status Filter</label>
        <div class="dropdown custom-filter-dropdown">
          <button type="button" id="statusDropdownTrigger" class="btn custom-dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="selected-label">All Statuses</span>
            <i class="bi bi-chevron-down dropdown-chevron"></i>
          </button>
          <ul class="dropdown-menu custom-dropdown-menu w-100" aria-labelledby="statusDropdownTrigger">
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item active" data-value="" data-filter="status">
                <span class="d-flex align-items-center gap-2">
                  <span class="status-badge-dot neutral"></span> All Statuses
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="assigned" data-filter="status">
                <span class="d-flex align-items-center gap-2">
                  <span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="in_progress" data-filter="status">
                <span class="d-flex align-items-center gap-2">
                  <span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="resolved" data-filter="status">
                <span class="d-flex align-items-center gap-2">
                  <span class="status-badge resolved"><i class="bi bi-check-circle-fill"></i> Resolved</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
          </ul>
          <!-- Hidden Native Select for 100% Backend Form Sync -->
          <select id="filterStatus" class="d-none">
            <option value="">All Statuses</option>
            <option value="assigned">Assigned</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
          </select>
        </div>
      </div>
      
      <!-- Custom Priority Filter Dropdown (Bootstrap 5 + JS) -->
      <div class="col-lg-2 col-md-3">
        <label for="priorityDropdownTrigger" class="form-label fw-semibold small text-dark mb-1">Priority Filter</label>
        <div class="dropdown custom-filter-dropdown">
          <button type="button" id="priorityDropdownTrigger" class="btn custom-dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="selected-label">All Priorities</span>
            <i class="bi bi-chevron-down dropdown-chevron"></i>
          </button>
          <ul class="dropdown-menu custom-dropdown-menu w-100" aria-labelledby="priorityDropdownTrigger">
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item active" data-value="" data-filter="priority">
                <span class="d-flex align-items-center gap-2">
                  <span class="status-badge-dot neutral"></span> All Priorities
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="high" data-filter="priority">
                <span class="d-flex align-items-center gap-2">
                  <span class="priority-badge high">High</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="medium" data-filter="priority">
                <span class="d-flex align-items-center gap-2">
                  <span class="priority-badge medium">Medium</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item custom-dropdown-item" data-value="low" data-filter="priority">
                <span class="d-flex align-items-center gap-2">
                  <span class="priority-badge low">Low</span>
                </span>
                <i class="bi bi-check2 check-icon"></i>
              </button>
            </li>
          </ul>
          <!-- Hidden Native Select for 100% Backend Form Sync -->
          <select id="filterPriority" class="d-none">
            <option value="">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>
      
      <!-- Reset Filters Button -->
      <div class="col-lg-2 col-md-12 text-md-end">
        <button type="reset" id="btnResetFilter" class="btn btn-filter-reset w-100">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </button>
      </div>
      
    </div>
  </form>
</div>

<!-- Data Table Panel Container -->
<div class="card-panel animate-fade-in-up delay-3">
  
  <!-- Empty State Banner (Shown when filters return zero results) -->
  <div id="emptyState" class="text-center py-5 d-none">
    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
    <h5 class="fw-bold text-dark mb-1">No Complaints Found</h5>
    <p class="text-muted small mb-3">No assigned complaints match your selected search or filter criteria.</p>
    <button type="button" class="btn-secondary-action btn-sm" onclick="document.getElementById('filterForm').reset();">
      <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search Filters
    </button>
  </div>

  <!-- Data Table -->
  <div id="tableContainer" class="table-responsive">
    <table class="table align-middle mb-0" style="font-size: 0.88rem;">
      <thead style="background-color: rgba(138, 114, 76, 0.06); color: var(--text-dark);">
        <tr>
          <th scope="col">Complaint ID</th>
          <th scope="col">Citizen Name</th>
          <th scope="col">Category</th>
          <th scope="col">Priority</th>
          <th scope="col">Status</th>
          <th scope="col">Assigned Date</th>
          <th scope="col" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="assignedTableBody">
        <tr>
          <td class="fw-bold text-dark">CMP-0012</td>
          <td>Ramesh Kumar</td>
          <td>Roads & Infrastructure</td>
          <td><span class="priority-badge high">High</span></td>
          <td><span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span></td>
          <td>18 Jul 2026</td>
          <td class="text-end">
            <a href="complaint_details.php?id=CMP-0012" class="btn btn-sm btn-outline-secondary me-1">
              <i class="bi bi-eye"></i> View
            </a>
            <a href="save_progress.php?id=CMP-0012" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
              <i class="bi bi-pencil-square"></i> Update Progress
            </a>
          </td>
        </tr>
        <tr>
          <td class="fw-bold text-dark">CMP-0024</td>
          <td>Suresh Patil</td>
          <td>Electricity Supply</td>
          <td><span class="priority-badge medium">Medium</span></td>
          <td><span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span></td>
          <td>20 Jul 2026</td>
          <td class="text-end">
            <a href="complaint_details.php?id=CMP-0024" class="btn btn-sm btn-outline-secondary me-1">
              <i class="bi bi-eye"></i> View
            </a>
            <a href="save_progress.php?id=CMP-0024" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
              <i class="bi bi-pencil-square"></i> Update Progress
            </a>
          </td>
        </tr>
        <tr>
          <td class="fw-bold text-dark">CMP-0035</td>
          <td>Sunita Deshmukh</td>
          <td>Water & Sanitation</td>
          <td><span class="priority-badge high">High</span></td>
          <td><span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span></td>
          <td>21 Jul 2026</td>
          <td class="text-end">
            <a href="complaint_details.php?id=CMP-0035" class="btn btn-sm btn-outline-secondary me-1">
              <i class="bi bi-eye"></i> View
            </a>
            <a href="save_progress.php?id=CMP-0035" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
              <i class="bi bi-pencil-square"></i> Update Progress
            </a>
          </td>
        </tr>
        <tr>
          <td class="fw-bold text-dark">CMP-0041</td>
          <td>Anil Gawande</td>
          <td>Sanitation & Drainage</td>
          <td><span class="priority-badge low">Low</span></td>
          <td><span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span></td>
          <td>21 Jul 2026</td>
          <td class="text-end">
            <a href="complaint_details.php?id=CMP-0041" class="btn btn-sm btn-outline-secondary me-1">
              <i class="bi bi-eye"></i> View
            </a>
            <a href="save_progress.php?id=CMP-0041" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
              <i class="bi bi-pencil-square"></i> Update Progress
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <!-- Pagination Controls -->
  <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: var(--accent-color) !important;">
    <span class="text-muted small">Showing 1 to 4 of 4 entries</span>
    <nav aria-label="Page navigation">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="#" style="background-color: var(--primary-color); border-color: var(--primary-color);">1</a></li>
        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
      </ul>
    </nav>
  </div>
  
</div>

<?php
require_once '../includes/officer_footer.php';
?>
