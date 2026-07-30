<?php
require_once __DIR__ . '/../config/db_connect.php';
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

$success_msg = "";
$error_msg = "";

$setting_keys = [
    'app_name',
    'panchayat_name',
    'admin_email',
    'helpline_number',
    'ui_theme',
    'notify_email',
    'notify_sms',
    'notify_citizen',
    'policy_min_length',
    'policy_special_char',
    'policy_numbers'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    if (isset($conn) && $conn !== null) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            foreach ($setting_keys as $key) {
                // Checkbox controls are not in $_POST if unchecked, so we default to '0'
                if (in_array($key, ['notify_email', 'notify_sms', 'notify_citizen', 'policy_special_char', 'policy_numbers'], true)) {
                    $value = isset($_POST[$key]) ? '1' : '0';
                } else {
                    $value = trim($_POST[$key] ?? '');
                }
                
                $stmt->bind_param("sss", $key, $value, $value);
                $stmt->execute();
            }
            $stmt->close();
            $conn->commit();
            $success_msg = "System settings updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Failed to save settings: " . $e->getMessage();
        }
    } else {
        $error_msg = "Database connection not available.";
    }
}

// Fetch current settings
$settings = [];
if (isset($conn) && $conn !== null) {
    $res = $conn->query("SELECT setting_key, setting_value FROM settings");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
}

// Default values fallback
$defaults = [
    'app_name' => 'Gram Panchayat Complaint Management System',
    'panchayat_name' => 'Shivaji Nagar Gram Panchayat',
    'admin_email' => 'gramsevak.shivajinagar@gov.in',
    'helpline_number' => '+91 98765 43210',
    'ui_theme' => 'earthy_gold',
    'notify_email' => '1',
    'notify_sms' => '1',
    'notify_citizen' => '1',
    'policy_min_length' => '8',
    'policy_special_char' => '1',
    'policy_numbers' => '1'
];

foreach ($defaults as $key => $default_val) {
    if (!isset($settings[$key])) {
        $settings[$key] = $default_val;
    }
}

$page_title = "System Settings";
$active_page = "settings";
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

                <div id="settingsAlertArea">
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($success_msg); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Settings Form -->
                <form id="formSystemSettings" method="POST" action="system_settings.php">
                    <input type="hidden" name="action" value="save_settings">
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
                                            <input type="text" class="form-control" id="settingAppName" name="app_name" value="<?php echo htmlspecialchars($settings['app_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingGpName" class="form-label font-weight-bold">Gram Panchayat Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="settingGpName" name="panchayat_name" value="<?php echo htmlspecialchars($settings['panchayat_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingEmail" class="form-label font-weight-bold">Official Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="settingEmail" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="settingContact" class="form-label font-weight-bold">Official Contact Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="settingContact" name="helpline_number" value="<?php echo htmlspecialchars($settings['helpline_number']); ?>" required>
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
                                            <select class="form-select" id="settingTheme" name="ui_theme">
                                                <option value="earthy_gold" <?php echo $settings['ui_theme'] === 'earthy_gold' ? 'selected' : ''; ?>>Professional Earthy Gold (#8A724C)</option>
                                            </select>
                                            <small class="text-muted">High-contrast official government theme palette.</small>
                                        </div>

                                        <hr class="my-3">

                                        <h6 class="fw-semibold text-secondary-custom mb-3">Notification Options</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="settingNotifyEmail" name="notify_email" value="1" <?php echo $settings['notify_email'] === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="settingNotifyEmail">Enable Email Alerts for New Complaints</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="settingNotifySms" name="notify_sms" value="1" <?php echo $settings['notify_sms'] === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="settingNotifySms">Send SMS Notifications to Field Officers on Assignment</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="settingNotifyCitizen" name="notify_citizen" value="1" <?php echo $settings['notify_citizen'] === '1' ? 'checked' : ''; ?>>
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
                                            <input type="number" class="form-control" id="policyMinLength" name="policy_min_length" value="<?php echo htmlspecialchars($settings['policy_min_length']); ?>" min="6" max="20">
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="policySpecialChar" name="policy_special_char" value="1" <?php echo $settings['policy_special_char'] === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="policySpecialChar">Require Special Characters (@, #, $, %)</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="policyNumbers" name="policy_numbers" value="1" <?php echo $settings['policy_numbers'] === '1' ? 'checked' : ''; ?>>
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

