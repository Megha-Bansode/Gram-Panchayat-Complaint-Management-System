<?php
/**
 * includes/footer.php
 * GPCMS — Premium Enterprise Redesigned Footer Include
 */
declare(strict_types=1);
?>
<footer class="app-footer" role="contentinfo">
    <div class="footer-grid">
        <!-- Column 1: Brand & About -->
        <div class="footer-column">
            <div class="footer-column-title">GPCMS Portal</div>
            <div class="footer-column-text">
                Gram Panchayat Complaint Management System. A Digital India initiative for smart, transparent, and responsive local governance and issue resolution.
            </div>
        </div>



        <!-- Column 3: Government Resources -->
        <div class="footer-column">
            <div class="footer-column-title">Govt Resources</div>
            <div class="footer-column-links"><a href="https://www.india.gov.in/" target="_blank" rel="noopener noreferrer" class="footer-link"><i class="bi bi-bank"></i> National Portal of India</a><a href="https://www.panchayat.gov.in/" target="_blank" rel="noopener noreferrer" class="footer-link"><i class="bi bi-globe"></i> Ministry of Panchayati Raj</a><a href="https://www.digitalindia.gov.in/" target="_blank" rel="noopener noreferrer" class="footer-link"><i class="bi bi-lightning-charge-fill"></i> Digital India Portal</a><a href="https://www.india.gov.in/topics/rural/panchayati-raj" target="_blank" rel="noopener noreferrer" class="footer-link"><i class="bi bi-link-45deg"></i> State Panchayat Dept</a></div>
        </div>

        <!-- Column 4: Help & Contact -->
        <div class="footer-column">
            <div class="footer-column-title">Help & Support</div>
            <div class="footer-column-links"><a href="tel:1800112345" class="footer-link"><i class="bi bi-telephone-fill"></i> Helpline: 1800-11-2345</a><a href="mailto:support-gpcms@nic.in" class="footer-link"><i class="bi bi-envelope-at-fill"></i> support-gpcms@nic.in</a><a href="https://services.india.gov.in/" target="_blank" rel="noopener noreferrer" class="footer-link"><i class="bi bi-cpu-fill"></i> System Status (Active)</a></div>
        </div>
    </div>

    <!-- Bottom Row: Copyright and Legal -->
    <div class="footer-bottom-row">
        <div class="footer-copyright">
            Developed for Digital India Initiative &copy; <?= date('Y') ?> | v2.0 Enterprise
        </div>
        <div class="footer-bottom-links">
            <a href="https://www.india.gov.in/privacy-policy" target="_blank" rel="noopener noreferrer" class="footer-bottom-link">Privacy Policy</a>
            <a href="https://www.india.gov.in/website-policy" target="_blank" rel="noopener noreferrer" class="footer-bottom-link">Terms & Conditions</a>
            <a href="https://www.cert-in.org.in/" target="_blank" rel="noopener noreferrer" class="footer-bottom-link">Security Guidelines</a>
        </div>
    </div>
</footer>

<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-gp">
            <div class="modal-header modal-header-gp">
                <h5 class="modal-title" id="notificationsModalLabel"><i class="bi bi-bell-fill me-2"></i>System Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-gp">
                <div class="notif-item priority">
                    <div class="notif-item-icon text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">SLA Warning: Approaching Deadline</div>
                        <div class="notif-item-desc">3 complaints in Ward 4 (Health & Sanitation) are approaching the 48-hour resolution limit.</div>
                        <div class="notif-item-time">1 hour ago</div>
                    </div>
                </div>
                <div class="notif-item info">
                    <div class="notif-item-icon text-primary"><i class="bi bi-info-circle-fill"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Database Backup Completed</div>
                        <div class="notif-item-desc">System auto-backup completed successfully. Archive was pushed to secure cloud storage.</div>
                        <div class="notif-item-time">10 hours ago</div>
                    </div>
                </div>
                <div class="notif-item success">
                    <div class="notif-item-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Category Settings Updated</div>
                        <div class="notif-item-desc">Settings for 'Drinking Water Supply' were updated by Chief Panchayat Administrator.</div>
                        <div class="notif-item-time">1 day ago</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-outline-secondary btn-sm ripple-btn" onclick="alert('All notifications marked as read.');">Mark All Read</button>
                <button type="button" class="btn btn-primary btn-sm ripple-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-gp">
            <div class="modal-header modal-header-gp">
                <h5 class="modal-title" id="settingsModalLabel"><i class="bi bi-gear-fill me-2"></i>Dashboard Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-gp" style="text-align: left;">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-arrow-clockwise me-1"></i>Data Auto-Refresh</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modalAutoRefreshToggle" style="cursor:pointer;">
                        <label class="form-check-label text-muted" for="modalAutoRefreshToggle" style="font-size: 13px;">Auto-refresh analytics dashboards every 30 seconds</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-palette-fill me-1"></i>Portal Theme Mode</label>
                    <select class="form-select" id="settingTheme" style="font-size: 13.5px; border-radius: 8px;">
                        <option value="light" selected>Sandstone Gold (Default)</option>
                        <option value="high-contrast">High Contrast Mode</option>
                        <option value="dark">Dark Slate Theme</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-file-earmark-arrow-down-fill"></i> Default Export Preference</label>
                    <select class="form-select" style="font-size: 13.5px; border-radius: 8px;">
                        <option value="csv" selected>CSV Format (.csv)</option>
                        <option value="excel">Excel Document (.xls)</option>
                        <option value="pdf">A4 Report Document (.pdf)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-hdd-network-fill me-1"></i>System Maintenance</label>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearDashboardCache();"><i class="bi bi-trash3-fill me-1"></i>Purge Temporary Cache</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm ripple-btn" onclick="saveDashboardSettings();">Save Settings</button>
            </div>
        </div>
</div>

<!-- Messages Modal -->
<div class="modal fade" id="messagesModal" tabindex="-1" aria-labelledby="messagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-gp">
            <div class="modal-header modal-header-gp">
                <h5 class="modal-title" id="messagesModalLabel"><i class="bi bi-chat-dots-fill me-2"></i>Panchayat Helpdesk Messages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-gp">
                <div class="notif-item priority">
                    <div class="notif-item-icon text-danger"><i class="bi bi-chat-right-text-fill"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Rahul Sharma (Ward 2 Member)</div>
                        <div class="notif-item-desc">A new critical sanitation issue has been reported near the public water tap in Ward 2. Residents are complaining. Please prioritize assignment to the field technician.</div>
                        <div class="notif-item-time">30 minutes ago</div>
                    </div>
                </div>
                <div class="notif-item info">
                    <div class="notif-item-icon text-primary"><i class="bi bi-chat-right-text"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Priya Patel (System Administrator)</div>
                        <div class="notif-item-desc">The server database upgrade is scheduled for tonight at 11:59 PM. There might be a brief 5-minute downtime. Please ensure all critical reports are exported beforehand.</div>
                        <div class="notif-item-time">3 hours ago</div>
                    </div>
                </div>
                <div class="notif-item success">
                    <div class="notif-item-icon text-success"><i class="bi bi-chat-right-text"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Nandini Deshmukh (Gram Sevak)</div>
                        <div class="notif-item-desc">I have verified and resolved the water leakage complaint ID #1043. Status has been updated in the portal. Please review.</div>
                        <div class="notif-item-time">1 day ago</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-outline-secondary btn-sm ripple-btn" onclick="alert('All messages marked as read.');">Mark All Read</button>
                <button type="button" class="btn btn-primary btn-sm ripple-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function clearDashboardCache() {
    alert("System Cache cleared successfully! 12.4 MB of temporary session storage was purged.");
}

function saveDashboardSettings() {
    // Sync settings modal switch with header switch
    const modalSwitch = document.getElementById('modalAutoRefreshToggle');
    const headerSwitch = document.getElementById('autoRefreshToggle');
    if (modalSwitch && headerSwitch) {
        headerSwitch.checked = modalSwitch.checked;
        // Trigger change event to fire dashboard refresh handlers
        headerSwitch.dispatchEvent(new Event('change'));
    }
    
    // Save theme mock setting
    const themeSelect = document.getElementById('settingTheme');
    if (themeSelect && themeSelect.value !== 'light') {
        alert("Settings saved successfully! Theme will apply upon database sync.");
    } else {
        alert("Settings saved successfully!");
    }
    
    // Hide modal
    const settingsModalEl = document.getElementById('settingsModal');
    const modalInstance = bootstrap.Modal.getInstance(settingsModalEl);
    if (modalInstance) {
        modalInstance.hide();
    }
}

// Sync switches on settings modal open
document.addEventListener('DOMContentLoaded', () => {
    const settingsModalEl = document.getElementById('settingsModal');
    if (settingsModalEl) {
        settingsModalEl.addEventListener('show.bs.modal', () => {
            const modalSwitch = document.getElementById('modalAutoRefreshToggle');
            const headerSwitch = document.getElementById('autoRefreshToggle');
            if (modalSwitch && headerSwitch) {
                modalSwitch.checked = headerSwitch.checked;
            }
        });
    }
});
</script>
