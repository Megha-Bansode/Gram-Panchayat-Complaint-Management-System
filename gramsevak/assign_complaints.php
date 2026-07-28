<?php
// Session check & Backend Placeholders
declare(strict_types=1);

require_once __DIR__ . '../includes/auth_check.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Gram Sevak') {
    auth_redirect('../includes/official_login.php', 'Unauthorized access.');
}

/* 
 * Database Contract Table References:
 * - users (user_id, full_name, mobile_number, role_id)
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description, village_ward)
 * - complaint_history (history_id, complaint_id, status, remarks)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Assign Complaints to Field Officers";
$active_page = "assign_complaints";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Assign Complaints</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Assign Complaints</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('assignComplaintsTableBody', 'Unassigned Pending Complaints List')">
                            <i class="bi bi-printer me-1"></i> Print Unassigned
                        </button>
                        <span class="badge bg-warning text-dark p-2 fs-6 shadow-sm" id="unassignedBadgeCount">
                            <i class="bi bi-exclamation-circle me-1"></i> 24 Unassigned Pending Complaints
                        </span>
                    </div>
                </div>

                <!-- Section 1: Pending Unassigned Complaints Table -->
                <div class="gpcms-card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="card-title mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>Pending Complaints Requiring Assignment</h6>
                        
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-dark" onclick="printElement('assignComplaintsTableBody', 'Pending Complaints for Assignment')">
                                <i class="bi bi-printer me-1"></i> Print Table
                            </button>
                            <input type="text" class="form-control form-control-sm" id="searchAssignTable" placeholder="Search unassigned..." style="max-width: 180px;">
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 gpcms-table">
                                <thead>
                                    <tr>
                                        <th>Complaint ID</th>
                                        <th>Complainant</th>
                                        <th>Village / Ward</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th class="text-center" style="min-width: 240px;">Assign Field Officer</th>
                                    </tr>
                                </thead>
                                <tbody id="assignComplaintsTableBody">
                                    <!-- Dynamic rows loaded via gramsevak.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Recent Assignment History Log -->
                <div class="gpcms-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-primary-custom"></i>Assignment History Log</h6>
                        <button class="btn btn-sm btn-outline-dark" onclick="printElement('assignmentHistoryBody', 'Officer Assignment History Log')">
                            <i class="bi bi-printer me-1"></i> Print Log
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0 gpcms-table">
                                <thead>
                                    <tr>
                                        <th>Complaint ID</th>
                                        <th>Complaint Title</th>
                                        <th>Village</th>
                                        <th>Assigned Field Officer</th>
                                        <th>Assigned By</th>
                                        <th>Updated Status</th>
                                        <th>Assignment Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody id="assignmentHistoryBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
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
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Assignment Confirmation Dialog Modal -->
    <div class="modal fade" id="assignConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-check-fill text-primary-custom me-2"></i>Confirm Officer Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to assign complaint <strong id="modalAssignComplaintId" class="text-primary-custom">#CMP-000</strong> to Field Officer <strong id="modalAssignOfficerName" class="text-secondary-custom">Officer Name</strong>?</p>
                    
                    <div class="mb-3">
                        <label for="assignRemarks" class="form-label small fw-semibold">Assignment Remarks / Work Instructions</label>
                        <textarea class="form-control" id="assignRemarks" rows="2" placeholder="e.g. Inspect pipeline site within 24 hours..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gpcms-primary btn-sm" id="btnConfirmAssignment">Confirm & Assign</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>
