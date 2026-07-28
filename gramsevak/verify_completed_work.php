<?php
// Session check & Backend Placeholders
require_once __DIR__ . '/../includes/auth_check.php';

// Integration Placeholders for Gram Sevak Module
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';
$_SESSION['is_logged_in'] = $_SESSION['is_logged_in'] ?? true;

/* 
 * Database Contract Table References:
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description, village_ward)
 * - complaint_photos (photo_id, complaint_id, before_photo, after_photo)
 * - complaint_history (history_id, complaint_id, status, remarks)
 *
 * Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
 */
$page_title = "Verify Completed Work";
$active_page = "verify_completed_work";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Verify Completed Work</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Verify Completed Work</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('verificationLogBody', 'Verified Work Audit Log')">
                            <i class="bi bi-printer me-1"></i> Print Verification Log
                        </button>
                        <span class="badge bg-success-subtle text-success p-2 fs-6 shadow-sm">
                            <i class="bi bi-check-all me-1"></i> Quality Audit Portal
                        </span>
                    </div>
                </div>

                <div id="verifyAlertArea"></div>

                <!-- Responsive Verification Cards Grid -->
                <div class="row g-4 mb-4" id="verificationCardsContainer">
                    <!-- Dynamic Work Evidence Cards rendered via JavaScript -->
                </div>

                <!-- Verified Work History Log Table -->
                <div class="gpcms-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="bi bi-journal-check text-success me-2"></i>Verification Audit Log</h6>
                        <button class="btn btn-sm btn-outline-dark" onclick="printElement('verificationLogBody', 'Verified Completed Work Log')">
                            <i class="bi bi-printer me-1"></i> Print Log
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 gpcms-table">
                                <thead>
                                    <tr>
                                        <th>Complaint ID</th>
                                        <th>Title</th>
                                        <th>Village</th>
                                        <th>Field Officer</th>
                                        <th>Verification Decision</th>
                                        <th>Gram Sevak Remarks</th>
                                        <th>Verified Date</th>
                                    </tr>
                                </thead>
                                <tbody id="verificationLogBody">
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

    <!-- Verification Decision Modal -->
    <div class="modal fade" id="verificationActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalTitle"><i class="bi bi-shield-check text-primary-custom me-2"></i>Work Verification Decision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert mb-3" id="verificationActionAlert">
                        <span id="verificationActionText"></span>
                    </div>

                    <div class="mb-3">
                        <label for="verifyRemarksInput" class="form-label font-weight-bold">Gram Sevak Verification Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="verifyRemarksInput" rows="3" placeholder="Enter reason for approval or rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gpcms-primary btn-sm" id="btnSubmitVerification">Submit Decision</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>

