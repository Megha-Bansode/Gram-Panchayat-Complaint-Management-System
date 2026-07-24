<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Citizen Login Module
 */
include '../includes/header.php';
?>

<section class="section-padding bg-warm" style="min-height: 75vh; display:flex; align-items:center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="glass-card shadow-lg p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="stat-icon-wrapper mx-auto mb-3" style="width:64px; height:64px; font-size:1.75rem;">
                            <i class="fa-solid fa-user text-primary"></i>
                        </div>
                        <h3 class="fw-bold"><?php echo __('citizen_login_header'); ?></h3>
                        <p class="text-muted small"><?php echo __('citizen_login_sub'); ?></p>
                    </div>

                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?php echo __('mobile_or_email'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control" placeholder="<?php echo __('mobile_or_email'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?php echo __('password_otp'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                <input type="password" class="form-control" placeholder="<?php echo __('password_otp'); ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label small" for="rememberMe"><?php echo __('remember_me'); ?></label>
                            </div>
                            <a href="#" class="small text-primary"><?php echo __('forgot_password'); ?></a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm"><?php echo __('citizen_login_header'); ?></button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="#" class="small fw-bold text-primary"><?php echo __('register_new'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
