<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Official / Admin Login Module
 */
include '../includes/header.php';
?>

<section class="section-padding bg-warm" style="min-height: 75vh; display:flex; align-items:center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="glass-card shadow-lg p-4 p-md-5" style="border-top: 4px solid var(--secondary);">
                    <div class="text-center mb-4">
                        <div class="stat-icon-wrapper mx-auto mb-3" style="width:64px; height:64px; font-size:1.75rem; background:var(--secondary-soft);">
                            <i class="fa-solid fa-building-columns text-secondary"></i>
                        </div>
                        <h3 class="fw-bold"><?php echo __('official_login_header'); ?></h3>
                        <p class="text-muted small"><?php echo __('official_login_sub'); ?></p>
                    </div>

                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?php echo __('role_label'); ?> *</label>
                            <select class="form-select" required>
                                <option value="" selected disabled>Select Role / Designation...</option>
                                <option value="gram_sevak">Gram Sevak / Secretary</option>
                                <option value="sarpanch">Sarpanch / Deputy Sarpanch</option>
                                <option value="ward_officer">Ward Officer</option>
                                <option value="system_admin">System Administrator</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?php echo __('employee_id'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                                <input type="text" class="form-control" placeholder="e.g. EMP-GP-4091" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?php echo __('password_otp'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" placeholder="<?php echo __('password_otp'); ?>" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-secondary text-white w-100 py-2 fw-bold shadow-sm mt-2"><?php echo __('official_login_header'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
