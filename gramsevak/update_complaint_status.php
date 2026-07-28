<?php
// Session check & Backend Placeholders
declare(strict_types=1);

require_once __DIR__ . '../includes/auth_check.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== ''Gram Sevak', 'Gram Panchayat Admin'') {
    auth_redirect('../includes/official_login.php', 'Unauthorized access.');
}

// Integration Placeholders for Gram Sevak Module
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';
$_SESSION['is_logged_in'] = $_SESSION['is_logged_in'] ?? true;

/* 
 * Database Contract Table References:
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description)
 * - complaint_history (history_id, complaint_id, status, remarks)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Update Complaint Status";
$active_page = "update_complaint_status";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Update Complaint Status</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Update Status</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('statusTrackingTable', 'Status Audit & Tracking Log')">
                            <i class="bi bi-printer me-1"></i> Print Log
                        </button>
                        <div class="alert alert-secondary py-1 px-3 mb-0 small rounded-pill">
                            <i class="bi bi-shield-check text-success me-1"></i> Contract Enforced: <code>pending</code> | <code>assigned</code> | <code>in_progress</code> | <code>resolved</code>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Column: Complaint Select & Update Form -->
                    <div class="col-lg-7">
                        <div class="gpcms-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0"><i class="bi bi-pencil-square me-2 text-primary-custom"></i>Status Modification Form</h6>
                            </div>
                            <div class="card-body">
                                <form id="formUpdateStatus" onsubmit="event.preventDefault(); submitStatusUpdate();">
                                    <div class="mb-3">
                                        <label for="selectComplaintForUpdate" class="form-label font-weight-bold">Select Active Complaint <span class="text-danger">*</span></label>
                                        <select class="form-select" id="selectComplaintForUpdate" onchange="onComplaintSelectChange(this.value)" required>
                                            <option value="">-- Choose Complaint --</option>
                                        </select>
                                    </div>

                                    <!-- Complaint Info Card Summary -->
                                    <div id="selectedComplaintCard" class="card bg-light border-0 p-3 mb-3 d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-secondary-custom" id="scId">#CMP-000</span>
                                            <span class="badge text-uppercase" id="scCurrentStatusBadge">pending</span>
                                        </div>
                                        <h6 class="fw-bold mb-1" id="scTitle">Complaint Title</h6>
                                        <p class="small text-muted mb-2" id="scDescription">Complaint details and summary...</p>
                                        <div class="row text-xs text-muted">
                                            <div class="col-6">Complainant: <strong id="scComplainant" class="text-dark">---</strong></div>
                                            <div class="col-6">Assigned: <strong id="scOfficer" class="text-dark">---</strong></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="newStatusSelect" class="form-label font-weight-bold">New Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="newStatusSelect" required>
                                            <option value="">-- Select Allowed Status --</option>
                                            <option value="pending">pending</option>
                                            <option value="assigned">assigned</option>
                                            <option value="in_progress">in_progress</option>
                                            <option value="resolved">resolved</option>
                                        </select>
                                        <div class="form-text small text-muted">Only strict database contract values are permitted.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="statusRemarks" class="form-label font-weight-bold">Official Remarks / Comments <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="statusRemarks" rows="4" placeholder="Enter detailed official notes regarding status change..." required></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-outline-secondary" onclick="resetStatusForm()">Clear</button>
                                        <button type="submit" class="btn btn-gpcms-primary">
                                            <i class="bi bi-check2-circle me-1"></i> Update Status
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Status History & Timeline Card -->
                    <div class="col-lg-5">
                        <div class="gpcms-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-primary-custom"></i>Status History Timeline</h6>
                                <button class="btn btn-sm btn-outline-dark" onclick="printElement('timelineContainer', 'Status Audit Timeline')">
                                    <i class="bi bi-printer me-1"></i> Print Timeline
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="timelinePlaceholder" class="text-center py-5 text-muted">
                                    <i class="bi bi-arrow-left-circle display-4 d-block mb-2 text-secondary-custom"></i>
                                    Select a complaint on the left to inspect its complete status change timeline and history audit trail.
                                </div>
                                <div id="timelineContainer" class="d-none">
                                    <h6 class="fw-bold mb-3 text-secondary-custom">Audit Trail for <span id="timelineComplaintId"></span></h6>
                                    <ul class="timeline-list" id="timelineList">
                                        <!-- Rendered via JS -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaints Status Summary Table -->
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <div class="gpcms-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="bi bi-table me-2 text-primary-custom"></i>All Complaints Status Tracking Table</h6>
                                <button class="btn btn-sm btn-outline-dark" onclick="printElement('statusTrackingTable', 'All Complaints Status Log')">
                                    <i class="bi bi-printer me-1"></i> Print Log
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 gpcms-table" id="statusTrackingTable">
                                        <thead>
                                            <tr>
                                                <th>Complaint ID</th>
                                                <th>Title</th>
                                                <th>Village</th>
                                                <th>Category</th>
                                                <th>Current Status</th>
                                                <th>Assigned Officer</th>
                                                <th>Last Updated</th>
                                                <th class="text-end">Quick Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="statusTrackingTableBody">
                                            <!-- Dynamically filled by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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

    <!-- Status Confirmation Dialog Modal -->
    <div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Confirm Status Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">You are modifying complaint <strong id="modalStatusCmpId">#CMP-000</strong> status to <span class="badge text-uppercase bg-primary-custom" id="modalTargetStatus">resolved</span>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gpcms-primary btn-sm" id="btnExecuteStatusUpdate">Execute Status Update</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>

