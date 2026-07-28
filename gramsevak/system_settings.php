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
 * - settings (setting_id, application_name, gram_panchayat_name)
 * - users (user_id, full_name, mobile_number, role_id)
 */
$page_title = "System Settings";
$active_page = "system_settings";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">System Configuration</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">System Settings</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-outline-dark btn-sm" onclick="printElement('settingsFormContainer', 'System Configuration Settings')">
                        <i class="bi bi-printer me-1"></i> Print Settings Summary
                    </button>
                </div>

                <div id="settingsAlertArea"></div>

                <!-- Settings Form -->
                <form id="formSystemSettings" onsubmit="event.preventDefault(); saveSystemSettings();">
                    <div id="settingsFormContainer">
                        <div class="row g-4 mb-4">
                            <!-- Gram Panchayat General Info Card -->
                            <div class="col-lg-6">
                                <div class="gpcms-card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-building me-2 text-primary-custom"></i>General Application Info</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="settingAppName" class="form-label font-weight-bold">Application Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="settingAppName" value="Gram Panchayat Complaint Management System" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingGpName" class="form-label font-weight-bold">Gram Panchayat Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="settingGpName" value="Shivaji Nagar Gram Panchayat" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingEmail" class="form-label font-weight-bold">Official Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="settingEmail" value="gramsevak.shivajinagar@gov.in" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingContact" class="form-label font-weight-bold">Official Contact Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="settingContact" value="+91 98765 43210" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Theme & Notification Settings -->
                            <div class="col-lg-6">
                                <div class="gpcms-card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-palette me-2 text-primary-custom"></i>Theme & Notification Preferences</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="settingTheme" class="form-label font-weight-bold">UI Theme System</label>
                                            <select class="form-select" id="settingTheme">
                                                <option value="earthy_gold" selected>Professional Earthy Gold (#8A724C)</option>
                                            </select>
                                            <small class="text-muted">High-contrast official government theme palette.</small>
                                        </div>

                                        <hr class="my-3">

                                        <h6 class="fw-semibold text-secondary-custom mb-3">Notification Options</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="settingNotifyEmail" checked>
                                            <label class="form-check-label" for="settingNotifyEmail">Enable Email Alerts for New Complaints</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="settingNotifySms" checked>
                                            <label class="form-check-label" for="settingNotifySms">Send SMS Notifications to Field Officers on Assignment</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="settingNotifyCitizen" checked>
                                            <label class="form-check-label" for="settingNotifyCitizen">Notify Citizens on Status Update</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- Security & Password Policy -->
                            <div class="col-lg-6">
                                <div class="gpcms-card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-shield-lock me-2 text-primary-custom"></i>Password Policy Configuration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="policyMinLength" class="form-label font-weight-bold">Minimum Password Length</label>
                                            <input type="number" class="form-control" id="policyMinLength" value="8" min="6" max="20">
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="policySpecialChar" checked>
                                            <label class="form-check-label" for="policySpecialChar">Require Special Characters (@, #, $, %)</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="policyNumbers" checked>
                                            <label class="form-check-label" for="policyNumbers">Require Numbers in Password</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Information Card -->
                            <div class="col-lg-6">
                                <div class="gpcms-card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-info-square me-2 text-primary-custom"></i>System Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Portal Version:</span>
                                                <span class="fw-bold">v3.1.0 (Gram Sevak Edition)</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Environment:</span>
                                                <span class="badge bg-success">Production XAMPP</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Database Contract Tables:</span>
                                                <span class="small font-monospace">users, complaints, categories, settings</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons Bar -->
                    <div class="gpcms-card p-3 mb-4 text-end">
                        <button type="button" class="btn btn-outline-dark me-2" onclick="printElement('settingsFormContainer', 'System Settings Summary')">
                            <i class="bi bi-printer me-1"></i> Print Summary
                        </button>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetSettingsForm()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Settings
                        </button>
                        <button type="submit" class="btn btn-gpcms-primary">
                            <i class="bi bi-floppy-fill me-1"></i> Save System Settings
                        </button>
                    </div>
                </form>
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

<?php require_once __DIR__ . '/footer.php'; ?>

