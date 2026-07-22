<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Register Complaint Module
 */
include '../includes/header.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-tag"><?php echo __('services_tag'); ?></span>
            <h2 class="section-title"><?php echo __('btn_register'); ?></h2>
            <p class="section-subtitle"><?php echo __('s1_desc'); ?></p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card shadow-lg p-4 p-md-5">
                    <form action="track_complaint.php" method="GET">
                        
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">1. Citizen Contact Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name *</label>
                                <input type="text" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mobile Number *</label>
                                <input type="tel" class="form-control" placeholder="10-digit mobile number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ward / Area Name *</label>
                                <select class="form-select" required>
                                    <option value="" selected disabled>Select Ward...</option>
                                    <option>Ward No. 1 - North Colony</option>
                                    <option>Ward No. 2 - Market Area</option>
                                    <option>Ward No. 3 - East Extension</option>
                                    <option>Ward No. 4 - South Village</option>
                                    <option>Ward No. 5 - School Zone</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address (Optional)</label>
                                <input type="email" class="form-control" placeholder="name@domain.com">
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">2. Grievance Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Complaint Category *</label>
                                <select class="form-select" required>
                                    <option value="" selected disabled>Choose Category...</option>
                                    <option>Water Supply & Drainage</option>
                                    <option>Roads & Street Lights</option>
                                    <option>Sanitation & Garbage Disposal</option>
                                    <option>Electricity & Transformer</option>
                                    <option>Panchayat Building & Property</option>
                                    <option>Other Public Issue</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Priority Level *</label>
                                <select class="form-select" required>
                                    <option value="normal">Normal (Routine Resolution)</option>
                                    <option value="high">High (Urgent Attention Required)</option>
                                    <option value="emergency">Emergency / Health Hazard</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Grievance Subject / Title *</label>
                                <input type="text" class="form-control" placeholder="Brief summary of the issue" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Detailed Description *</label>
                                <textarea class="form-control" rows="4" placeholder="Explain the problem in detail (location landmarks, duration of issue, etc.)..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Attach Supporting Photo / Document (Optional)</label>
                                <input type="file" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted">Max file size: 5MB (JPG, PNG, PDF allowed)</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm"><?php echo __('submit_complaint'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
