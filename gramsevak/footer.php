<?php
/**
 * Shared Footer Component - Gram Panchayat Complaint Management System
 */
?>
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

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell-fill text-warning me-2"></i>System Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush" id="notificationList">
                        <div class="list-group-item p-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-primary-custom">New Complaint Submitted</h6>
                                <small class="text-muted">10 mins ago</small>
                            </div>
                            <p class="mb-1 small">Water pipeline leakage reported near Ward 3 Community Center.</p>
                            <small class="text-warning-custom"><i class="bi bi-exclamation-triangle me-1"></i>Status: pending</small>
                        </div>
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
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Gram Sevak'); ?></h5>
                        <span class="badge bg-primary-custom"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Gram Sevak'); ?></span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">User ID:</span>
                            <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['user_id'] ?? '101'); ?></span>
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Gram Sevak JS -->
    <script src="../js/gramsevak.js?v=<?php echo time(); ?>"></script>
</body>
</html>
