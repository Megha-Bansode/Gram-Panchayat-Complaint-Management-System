<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Scheme Details Module
 */
include '../includes/header.php';
$scheme_code = isset($_GET['scheme']) ? htmlspecialchars($_GET['scheme']) : 'pmay';

$schemes = [
    'pmay' => [
        'title' => __('scheme_pmay_title'),
        'category' => __('central_govt'),
        'desc' => __('scheme_pmay_desc'),
        'benefits' => 'Financial assistance of ₹1.20 Lakh in plains and ₹1.30 Lakh in hilly areas for house construction.',
        'eligibility' => 'Kutcha house owners, homeless rural families listed under SECC data.',
        'documents' => 'Aadhaar Card, Bank Account Passbook, MGNREGA Job Card, BPL Certificate.'
    ],
    'mgnrega' => [
        'title' => __('s_mgnrega_title'),
        'category' => __('central_govt'),
        'desc' => __('s_mgnrega_desc'),
        'benefits' => 'Guaranteed daily wage paid directly into bank/post office account within 15 days.',
        'eligibility' => __('s_mgnrega_elig'),
        'documents' => 'Aadhaar Card, Passport photo, Address proof, Bank Account details.'
    ],
    'jjm' => [
        'title' => __('scheme_jjm_title'),
        'category' => __('central_govt'),
        'desc' => __('scheme_jjm_desc'),
        'benefits' => '100% functional tap water connection per family at zero initial installation cost.',
        'eligibility' => 'All rural households lacking tap water connection.',
        'documents' => 'Aadhaar Card, Household tax receipt.'
    ]
];

$info = isset($schemes[$scheme_code]) ? $schemes[$scheme_code] : $schemes['pmay'];
?>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="glass-card shadow-lg p-4 p-md-5">
                    <span class="badge bg-secondary text-dark mb-2"><?php echo $info['category']; ?></span>
                    <h2 class="fw-bold text-primary mb-3"><?php echo $info['title']; ?></h2>
                    
                    <p class="lead text-muted mb-4"><?php echo $info['desc']; ?></p>

                    <div class="card bg-warm border-0 p-3 mb-4">
                        <h5 class="fw-bold text-success"><i class="fa-solid fa-gift me-2"></i>Scheme Benefits</h5>
                        <p class="mb-0 text-muted"><?php echo $info['benefits']; ?></p>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-primary"><i class="fa-solid fa-user-check me-2"></i><?php echo __('eligibility'); ?></h6>
                                <p class="text-muted small mb-0"><?php echo $info['eligibility']; ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-warning"><i class="fa-solid fa-folder-open me-2"></i>Required Documents</h6>
                                <p class="text-muted small mb-0"><?php echo $info['documents']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="https://pmayg.nic.in" target="_blank" class="btn btn-primary px-4 fw-bold">Apply Online <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a>
                        <a href="../index.php#schemes" class="btn btn-outline-secondary px-4">← <?php echo __('nav_schemes'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
