<?php
require_once __DIR__ . '/../includes/auth_check.php';

auth_start_session();

if (empty($_SESSION['is_logged_in'])) {
    auth_redirect('../includes/official_login.php', 'Please sign in to continue.');
}

// Integration Placeholders for Gram Sevak Module
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';

/* 
 * Database Contract Table References:
 * - users (user_id, full_name, mobile_number, role_id)
 * - roles (role_id, role_name)
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description, complaint_image, complainant_name, mobile_number, village_ward)
 * - categories (category_id, category_name)
 * - complaint_history (history_id, complaint_id, status, remarks)
 * - complaint_photos (photo_id, complaint_id, before_photo, after_photo)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "View Complaints";
$active_page = "view_complaints";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">All Village Complaints</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Complaints</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetComplaintsFilter()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                        </button>
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('viewComplaintsTable', 'Gram Panchayat Complaints Directory')">
                            <i class="bi bi-printer me-1"></i> Print Directory
                        </button>
                        <button class="btn btn-gpcms-primary btn-sm" onclick="exportComplaintsCSV()">
                            <i class="bi bi-download me-1"></i> Export Data
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Controls Card -->
                <div class="gpcms-card p-3 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label for="filterSearch" class="form-label small fw-semibold">Search Keywords</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="filterSearch" placeholder="Title, ID, Citizen Name...">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="filterCategory" class="form-label small fw-semibold">Category</label>
                            <select class="form-select form-select-sm" id="filterCategory">
                                <option value="">All Categories</option>
                                <option value="Water Supply">Water Supply</option>
                                <option value="Sanitation & Waste">Sanitation & Waste</option>
                                <option value="Road Repair">Road Repair</option>
                                <option value="Street Lighting">Street Lighting</option>
                                <option value="Drainage System">Drainage System</option>
                                <option value="Public Health">Public Health</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="filterVillage" class="form-label small fw-semibold">Village / Ward</label>
                            <select class="form-select form-select-sm" id="filterVillage">
                                <option value="">All Villages</option>
                                <option value="Shivaji Nagar">Shivaji Nagar</option>
                                <option value="Rampur Ward 1">Rampur Ward 1</option>
                                <option value="Rampur Ward 2">Rampur Ward 2</option>
                                <option value="Ganesh Wadi">Ganesh Wadi</option>
                                <option value="Hanuman Nagar">Hanuman Nagar</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="filterStatus" class="form-label small fw-semibold">Allowed Status</label>
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">All Statuses</option>
                                <option value="pending">pending</option>
                                <option value="assigned">assigned</option>
                                <option value="in_progress">in_progress</option>
                                <option value="resolved">resolved</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-12">
                            <label for="filterDate" class="form-label small fw-semibold">Submission Date</label>
                            <input type="date" class="form-control form-control-sm" id="filterDate">
                        </div>
                    </div>
                </div>

                <!-- Complaints Data Table Card -->
                <div class="gpcms-card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="card-title mb-0"><i class="bi bi-journal-text text-primary-custom me-2"></i>Complaints Log</h6>
                            <span class="badge bg-secondary-subtle text-secondary" id="complaintRecordCount">Showing 0 entries</span>
                        </div>
                        <button class="btn btn-sm btn-outline-dark" onclick="printElement('viewComplaintsTable', 'Gram Panchayat Complaints Directory')">
                            <i class="bi bi-printer me-1"></i> Print Table
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 gpcms-table" id="viewComplaintsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Citizen Details</th>
                                        <th>Village / Ward</th>
                                        <th>Category</th>
                                        <th>Complaint Title</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Assigned Officer</th>
                                        <th>Submitted On</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="viewComplaintsBody">
                                    <!-- Dynamic rows generated by gramsevak.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="small text-muted" id="paginationInfo">Showing page 1 of 1</span>
                        <ul class="pagination pagination-sm mb-0" id="tablePagination">
                            <!-- Dynamic pagination links -->
                        </ul>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="gpcms-footer">
                <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold">Shivaji Nagar Gram Panchayat</span> &copy; <?php echo date('Y'); ?>. All Rights Reserved.
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-secondary-subtle text-secondary">GPCMS Version 3.1</span>
                        <a href="system_settings.php" class="text-decoration-none text-muted small">Contact Admin</a>
                        <a href="https://panchayat.gov.in" target="_blank" rel="noopener" class="text-decoration-none text-muted small">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Government Portal
                        </a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Details & Timeline Modal -->
    <div class="modal fade" id="complaintDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailHeaderTitle"><i class="bi bi-info-circle text-primary-custom me-2"></i>Complaint Details & Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <!-- Populated via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark btn-sm me-auto" onclick="printElement('modalDetailContent', 'Complaint Details Sheet')">
                        <i class="bi bi-printer me-1"></i> Print Details
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close Window</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h6 class="modal-title text-white" id="imageModalTitle"><i class="bi bi-image me-2"></i>Complaint Photo Preview</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="" id="lightboxImage" class="img-fluid rounded shadow-sm" alt="Full Image Preview" style="max-height: 70vh;">
                    <p class="mt-2 mb-0 text-muted small" id="lightboxCaption"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell-fill text-warning me-2"></i>Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush" id="notificationList">
                        <!-- Loaded dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-circle text-primary-custom me-2"></i>Gram Sevak Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-secondary-custom"><i class="bi bi-person-badge-fill"></i></div>
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h5>
                        <span class="badge bg-primary-custom"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">User ID:</span>
                            <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Gram Panchayat:</span>
                            <span class="fw-semibold">Shivaji Nagar Gram Panchayat</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>

