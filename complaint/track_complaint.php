<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Track Complaint Module
 */
include '../includes/header.php';
$complaint_id = isset($_GET['complaint_id']) ? htmlspecialchars($_GET['complaint_id']) : 'GPCMS202600123';
?>

<section class="section-padding">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-tag"><?php echo __('tracker_tag'); ?></span>
            <h2 class="section-title"><?php echo __('tracker_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('tracker_subtitle'); ?> <strong><?php echo $complaint_id; ?></strong></p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="glass-card mb-4 p-3">
                    <form action="track_complaint.php" method="GET" class="row g-2">
                        <div class="col-md-9">
                            <input type="text" name="complaint_id" class="form-control" value="<?php echo $complaint_id; ?>" placeholder="<?php echo __('tracker_placeholder'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 fw-bold"><?php echo __('track_now'); ?></button>
                        </div>
                    </form>
                </div>

                <!-- Complaint Details Card -->
                <div class="glass-card shadow-lg p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div>
                            <span class="badge bg-primary fs-6 me-2"><?php echo $complaint_id; ?></span>
                            <span class="badge bg-warning text-dark"><?php echo __('step_progress'); ?></span>
                        </div>
                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>21 Jul 2026, 10:30 AM</small>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Category:</small>
                            <span class="fw-bold">Water Supply & Pipeline Leakage</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Ward Location:</small>
                            <span class="fw-bold">Ward No. 2 - Market Area</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Assigned Official:</small>
                            <span class="fw-bold text-primary">Ramesh Patil (Gram Sevak)</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Estimated Resolution:</small>
                            <span class="fw-bold text-success">23 Jul 2026</span>
                        </div>
                    </div>

                    <!-- Visual Timeline -->
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Resolution Progress Timeline</h5>
                    <div class="tracking-timeline mb-4">
                        <div class="timeline-step completed">
                            <div class="timeline-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="timeline-label"><?php echo __('step_registered'); ?></div>
                            <small class="text-muted d-block" style="font-size:0.75rem;">21 Jul, 10:30 AM</small>
                        </div>
                        <div class="timeline-step completed">
                            <div class="timeline-icon"><i class="fa-solid fa-user-check"></i></div>
                            <div class="timeline-label"><?php echo __('step_assigned'); ?></div>
                            <small class="text-muted d-block" style="font-size:0.75rem;">21 Jul, 02:15 PM</small>
                        </div>
                        <div class="timeline-step active">
                            <div class="timeline-icon"><i class="fa-solid fa-wrench"></i></div>
                            <div class="timeline-label"><?php echo __('step_progress'); ?></div>
                            <small class="text-primary fw-bold d-block" style="font-size:0.75rem;">Under Inspection</small>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-icon"><i class="fa-solid fa-flag-checkered"></i></div>
                            <div class="timeline-label"><?php echo __('step_resolved'); ?></div>
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pending</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="../index.php" class="btn btn-outline-secondary w-50">← <?php echo __('nav_home'); ?></a>
                        <button onclick="window.print()" class="btn btn-outline-primary w-50"><i class="fa-solid fa-print me-2"></i>Print Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
