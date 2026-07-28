<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer - Assigned Complaints View
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: View, search, and filter assigned complaints list matching Reference Image 1.
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

// Get Filter Parameters
$filter_status   = $_GET['status'] ?? '';
$filter_category = $_GET['category'] ?? '';
$search_query    = $_GET['search'] ?? '';

// Build Query
$sql = "
    SELECT c.complaint_id, c.complaint_code, c.title, c.ward_no, c.location_address, c.status, c.created_at, c.assigned_at, cat.category_name
    FROM complaints c
    JOIN categories cat ON c.category_id = cat.category_id
    WHERE (c.assigned_officer_id = :officer_id OR c.assigned_officer_id IS NULL)
";

$params = [':officer_id' => $officer_id];

if (!empty($filter_status)) {
    $sql .= " AND c.status = :status";
    $params[':status'] = $filter_status;
}

if (!empty($filter_category)) {
    $sql .= " AND cat.category_name = :category";
    $params[':category'] = $filter_category;
}

if (!empty($search_query)) {
    $sql .= " AND (c.complaint_code LIKE :search OR c.title LIKE :search OR c.location_address LIKE :search)";
    $params[':search'] = '%' . $search_query . '%';
}

$sql .= " ORDER BY c.created_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $assignedComplaints = $stmt->fetchAll();

    // Fetch all categories for filter dropdown
    $catStmt = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
    $categoriesList = $catStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Assigned complaints query error: " . $e->getMessage());
    $assignedComplaints = [];
    $categoriesList = [];
}

$page_title = "Assigned Complaints - GPCMS";
require_once 'header.php';
require_once 'officer_sidebar.php';
?>

<!-- Breadcrumb Bar Matching Reference UI -->
<div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E2D9CD; font-size: 0.82rem;">
  <div class="text-muted d-flex align-items-center gap-2">
    <a href="field_dashboard.php" class="text-muted text-decoration-none fw-semibold">Home</a>
    <span>/</span>
    <span class="text-muted">Field Officer</span>
    <span>/</span>
    <span class="fw-bold text-dark">Assigned Complaints</span>
  </div>
</div>

<!-- Hero Banner Matching Reference UI -->
<div class="hero-welcome-card mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #876E47 0%, #A0855A 100%); border-radius: 18px; padding: 1.75rem 2.25rem; color: #FFFFFF; box-shadow: 0 6px 20px rgba(135, 110, 71, 0.15);">
  <div class="position-relative z-2">
    <h2 class="fw-extrabold text-white mb-1" style="font-size: 1.85rem; letter-spacing: -0.01em;">Assigned Complaints</h2>
    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">View all complaints assigned to you. Update status and track field progress.</p>
  </div>
</div>

<!-- Search & Filter Controls Bar Matching Reference Image 1 -->
<div class="card border-0 mb-4 shadow-sm" style="background: #FFFFFF; border-radius: 18px; padding: 1rem 1.25rem; border: 1px solid rgba(220, 201, 167, 0.5) !important;">
  <form id="filterForm" method="GET" action="assigned_complaints.php">
    <div class="d-flex flex-wrap align-items-center gap-3">
      
      <!-- Search Input Field -->
      <div class="flex-grow-1" style="min-width: 260px;">
        <div class="position-relative">
          <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
          <input type="text" name="search" id="searchComplaint" class="form-control ps-5 pe-4 py-2" placeholder="Search by ID, title or location..." value="<?= htmlspecialchars($search_query) ?>" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; background-color: #FCFBF8;">
          <?php if (!empty($search_query)): ?>
            <a href="assigned_complaints.php" id="btnClearSearch" class="btn-clear-search position-absolute top-50 end-0 translate-middle-y me-3 border-0 bg-transparent text-muted" title="Clear Search">
              <i class="bi bi-x-circle-fill"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- All Categories Dropdown -->
      <div class="dropdown custom-filter-dropdown" style="min-width: 170px;">
        <button type="button" id="categoryDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; color: #3A3226; font-weight: 500;">
          <span class="selected-label"><?= !empty($filter_category) ? htmlspecialchars($filter_category) : 'All Categories' ?></span>
          <i class="bi bi-chevron-down ms-2 small text-muted"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" style="border-radius: 12px; font-size: 0.85rem;">
          <li><button type="button" class="dropdown-item custom-dropdown-item <?= empty($filter_category) ? 'active' : '' ?>" data-value="" data-filter="category">All Categories</button></li>
          <?php foreach ($categoriesList as $catName): ?>
            <li><button type="button" class="dropdown-item custom-dropdown-item <?= $filter_category === $catName ? 'active' : '' ?>" data-value="<?= htmlspecialchars($catName) ?>" data-filter="category"><?= htmlspecialchars($catName) ?></button></li>
          <?php endforeach; ?>
        </ul>
        <select name="category" id="filterCategory" class="d-none">
          <option value="">All Categories</option>
          <?php foreach ($categoriesList as $catName): ?>
            <option value="<?= htmlspecialchars($catName) ?>" <?= $filter_category === $catName ? 'selected' : '' ?>><?= htmlspecialchars($catName) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <!-- All Status Dropdown -->
      <div class="dropdown custom-filter-dropdown" style="min-width: 150px;">
        <button type="button" id="statusDropdownTrigger" class="btn bg-white w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; border: 1px solid rgba(220, 201, 167, 0.6); font-size: 0.88rem; color: #3A3226; font-weight: 500;">
          <span class="selected-label"><?= !empty($filter_status) ? ucfirst(str_replace('_', ' ', $filter_status)) : 'All Status' ?></span>
          <i class="bi bi-chevron-down ms-2 small text-muted"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown-menu w-100 shadow-sm" style="border-radius: 12px; font-size: 0.85rem;">
          <li><button type="button" class="dropdown-item custom-dropdown-item <?= empty($filter_status) ? 'active' : '' ?>" data-value="" data-filter="status">All Status</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item <?= $filter_status === 'assigned' ? 'active' : '' ?>" data-value="assigned" data-filter="status">Assigned</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item <?= $filter_status === 'in_progress' ? 'active' : '' ?>" data-value="in_progress" data-filter="status">In Progress</button></li>
          <li><button type="button" class="dropdown-item custom-dropdown-item <?= $filter_status === 'resolved' ? 'active' : '' ?>" data-value="resolved" data-filter="status">Resolved</button></li>
        </ul>
        <select name="status" id="filterStatus" class="d-none">
          <option value="">All Status</option>
          <option value="assigned" <?= $filter_status === 'assigned' ? 'selected' : '' ?>>Assigned</option>
          <option value="in_progress" <?= $filter_status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
          <option value="resolved" <?= $filter_status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
      </div>

      <!-- Submit & Reset Buttons -->
      <div class="d-flex align-items-center gap-2">
        <button type="submit" class="btn text-white py-2 px-3 fw-bold" style="background-color: #876E47; border-radius: 12px; font-size: 0.88rem; border: none;">
          <i class="bi bi-funnel"></i> Apply
        </button>
        <a href="assigned_complaints.php" id="btnResetFilter" class="btn text-white d-flex align-items-center gap-2 py-2 px-3 fw-bold text-decoration-none" style="background-color: #5E4D34; border-radius: 12px; font-size: 0.88rem;">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      </div>

    </div>
  </form>
</div>

<!-- Data Table Card Matching Reference Image 1 -->
<div class="card border-0 shadow-sm" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(220, 201, 167, 0.5) !important; padding: 1.25rem;">
  
  <?php if (empty($assignedComplaints)): ?>
    <div id="emptyState" class="text-center py-5">
      <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
      <h5 class="fw-bold text-dark mb-1">No Complaints Found</h5>
      <p class="text-muted small mb-3">No assigned complaints match your selected search or filter criteria.</p>
      <a href="assigned_complaints.php" class="btn btn-sm text-white px-3" style="background-color: #5E4D34; border-radius: 8px;">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search Filters
      </a>
    </div>
  <?php else: ?>
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
          <?php foreach ($assignedComplaints as $c): ?>
            <?php
              $st = strtolower($c['status']);
              $badge_style = 'background-color: #E3F2FD !important; color: #1565C0 !important;';
              $dot_style = 'background-color: #1565C0 !important;';

              if ($st === 'in_progress') {
                  $badge_style = 'background-color: #FFF8E7 !important; color: #D35400 !important;';
                  $dot_style = 'background-color: #D35400 !important;';
              } elseif ($st === 'resolved') {
                  $badge_style = 'background-color: #E8F5E9 !important; color: #2E7D32 !important;';
                  $dot_style = 'background-color: #2E7D32 !important;';
              }

              $date_display = $c['assigned_at'] ? date('d M Y', strtotime($c['assigned_at'])) : date('d M Y', strtotime($c['created_at']));
              $time_display = $c['assigned_at'] ? date('h:i A', strtotime($c['assigned_at'])) : date('h:i A', strtotime($c['created_at']));
            ?>
            <tr style="border-bottom: 1px solid rgba(220, 201, 167, 0.3);">
              <td class="fw-bold text-dark px-3 py-3"><?= htmlspecialchars($c['complaint_code']) ?></td>
              <td class="fw-semibold text-dark px-3 py-3" style="max-width: 220px;"><?= htmlspecialchars($c['title']) ?></td>
              <td class="px-3 py-3"><?= htmlspecialchars($c['category_name']) ?></td>
              <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($c['ward_no'] . ', ' . $c['location_address']) ?></span></td>
              <td class="px-3 py-3"><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> <?= $date_display ?><br><span class="text-muted extra-small ms-4"><?= $time_display ?></span></span></td>
              <td class="px-3 py-3">
                <span class="status-badge <?= $st ?>" style="<?= $badge_style ?> font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;">
                  <span style="width: 7px; height: 7px; border-radius: 50%; <?= $dot_style ?> display: inline-block;"></span>
                  <?= ucfirst(str_replace('_', ' ', $st)) ?>
                </span>
              </td>
              <td class="text-end px-3 py-3">
                <div class="d-inline-flex align-items-center gap-2">
                  <a href="complaint_details.php?id=<?= urlencode($c['complaint_code']) ?>" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                    <i class="bi bi-eye me-1"></i> View
                  </a>
                  <a href="save_progress.php?id=<?= urlencode($c['complaint_code']) ?>" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.38rem 0.85rem; font-size: 0.78rem;">
                    <i class="bi bi-pencil-square me-1"></i> Update Progress
                  </a>
                  <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
  
</div>

<?php
require_once 'footer.php';
?>
