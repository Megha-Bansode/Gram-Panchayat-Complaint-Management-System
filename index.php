<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Main Modular Landing Page (PHP Session i18n Edition)
 */
include 'includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
 1. HERO SECTION
═══════════════════════════════════════════════════════════ -->
<section id="home" class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-7 col-xl-6">
                <div class="hero-welcome-badge">
                    <i class="fa-solid fa-leaf me-1"></i> <?php echo __('hero_badge'); ?>
                </div>
                <h1 class="hero-title mb-2">
                    <span class="accent-word"><?php echo __('hero_title_brand'); ?></span> <?php echo __('hero_title_main'); ?>
                </h1>
                <p class="hero-subtitle-main"><?php echo __('hero_subtitle_main'); ?></p>
                <p class="hero-desc"><?php echo __('hero_desc'); ?></p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="complaint/register_complaint.php" class="btn-hero-primary" id="hero-register-btn">
                        <i class="fa-solid fa-bullhorn me-2"></i>
                        <span><?php echo __('btn_register'); ?></span>
                    </a>
                    <a href="complaint/track_complaint.php" class="btn-hero-secondary" id="hero-track-btn">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>
                        <span><?php echo __('btn_track'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <span><?php echo __('scroll_down'); ?></span>
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 2. QUICK CITIZEN SERVICE CARDS
═══════════════════════════════════════════════════════════ -->
<section id="quick-services" class="section-padding bg-light-warm">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('services_tag'); ?></span>
            <h2 class="section-title"><?php echo __('services_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('services_subtitle'); ?></p>
        </div>
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <a href="complaint/register_complaint.php" class="text-decoration-none">
                    <div class="service-card">
                        <div class="service-icon"><i class="fa-solid fa-file-pen"></i></div>
                        <div class="service-title"><?php echo __('s1_title'); ?></div>
                        <p class="service-desc"><?php echo __('s1_desc'); ?></p>
                        <div class="service-arrow"><?php echo __('get_started'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
            <!-- Card 2 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="60">
                <a href="complaint/track_complaint.php" class="text-decoration-none">
                    <div class="service-card">
                        <div class="service-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                        <div class="service-title"><?php echo __('s2_title'); ?></div>
                        <p class="service-desc"><?php echo __('s2_desc'); ?></p>
                        <div class="service-arrow"><?php echo __('track_now'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
            <!-- Card 3 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="120">
                <a href="#schemes" class="text-decoration-none">
                    <div class="service-card">
                        <div class="service-icon"><i class="fa-solid fa-building-columns"></i></div>
                        <div class="service-title"><?php echo __('s3_title'); ?></div>
                        <p class="service-desc"><?php echo __('s3_desc'); ?></p>
                        <div class="service-arrow"><?php echo __('explore'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
            <!-- Card 4 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="180">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-file-certificate"></i></div>
                    <div class="service-title"><?php echo __('s4_title'); ?></div>
                    <p class="service-desc"><?php echo __('s4_desc'); ?></p>
                    <div class="service-arrow"><?php echo __('apply_now'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                </div>
            </div>
            <!-- Card 5 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="240">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-coins"></i></div>
                    <div class="service-title"><?php echo __('s5_title'); ?></div>
                    <p class="service-desc"><?php echo __('s5_desc'); ?></p>
                    <div class="service-arrow"><?php echo __('pay_now'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                </div>
            </div>
            <!-- Card 6 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-comments"></i></div>
                    <div class="service-title"><?php echo __('s6_title'); ?></div>
                    <p class="service-desc"><?php echo __('s6_desc'); ?></p>
                    <div class="service-arrow"><?php echo __('share_now'); ?> <i class="fa-solid fa-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 3. ANIMATED STATISTICS SECTION
═══════════════════════════════════════════════════════════ -->
<section class="stats-section" id="stats">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-number counter-value" data-target="1245" data-suffix="">0</div>
                    <div class="stat-label"><?php echo __('stat_residents'); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-number counter-value" data-target="428" data-suffix="">0</div>
                    <div class="stat-label"><?php echo __('stat_resolved'); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-landmark"></i></div>
                    <div class="stat-number counter-value" data-target="36" data-suffix="">0</div>
                    <div class="stat-label"><?php echo __('stat_schemes'); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="stat-number counter-value" data-target="12" data-suffix="">0</div>
                    <div class="stat-label"><?php echo __('stat_events'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 4. LATEST NOTICES BOARD
═══════════════════════════════════════════════════════════ -->
<section id="notices" class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4" data-aos="fade-right">
                <span class="section-tag"><?php echo __('notices_tag'); ?></span>
                <h2 class="section-title"><?php echo __('notices_title'); ?></h2>
                <p class="section-subtitle text-start" style="max-width:none;"><?php echo __('notices_subtitle'); ?></p>
                <a href="#" class="btn-primary-custom mt-2">
                    <i class="fa-solid fa-bell me-2"></i> <span><?php echo __('notices_subscribe'); ?></span>
                </a>
            </div>
            <div class="col-lg-8" data-aos="fade-left">
                <div class="notices-board">
                    <div class="notices-header">
                        <div class="notices-header-title">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span><?php echo __('notices_board_title'); ?></span>
                        </div>
                        <a href="javascript:void(0)" class="view-all-link" data-bs-toggle="modal" data-bs-target="#allNoticesModal"><?php echo __('notices_view_all'); ?></a>
                    </div>
                    <!-- Notice 1 Dropdown -->
                    <div class="notice-accordion-item border-bottom">
                        <div class="notice-item d-flex align-items-center justify-content-between p-3" data-bs-toggle="collapse" data-bs-target="#noticeCollapse1" aria-expanded="false" aria-controls="noticeCollapse1" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notice-bell"><i class="fa-solid fa-bell"></i></div>
                                <div class="notice-content">
                                    <div class="notice-title"><?php echo __('notice_1_title'); ?></div>
                                    <div class="notice-date"><i class="fa-regular fa-calendar me-1"></i>21 Jul 2026</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-new"><?php echo __('badge_new'); ?></span>
                                <i class="fa-solid fa-chevron-down notice-arrow-icon text-muted" style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="collapse" id="noticeCollapse1">
                            <div class="px-4 py-3" style="background: #F7F3E8; border-top: 1px solid #EDE2CC;">
                                <p class="small mb-2" style="color: #5A4A35; line-height: 1.6;"><?php echo __('notice_1_desc'); ?></p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: #DCC9A7 !important;">
                                    <span class="small" style="color: #8C7A65;"><i class="fa-solid fa-building-columns me-1" style="color:#8A724C;"></i> Gram Panchayat Administrative Cell</span>
                                    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #8A724C; border-radius: 20px;" onclick="event.stopPropagation(); alert('Notice document downloaded!');">
                                        <i class="fa-solid fa-download me-1"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice 2 Dropdown -->
                    <div class="notice-accordion-item border-bottom">
                        <div class="notice-item d-flex align-items-center justify-content-between p-3" data-bs-toggle="collapse" data-bs-target="#noticeCollapse2" aria-expanded="false" aria-controls="noticeCollapse2" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notice-bell"><i class="fa-solid fa-bell"></i></div>
                                <div class="notice-content">
                                    <div class="notice-title"><?php echo __('notice_2_title'); ?></div>
                                    <div class="notice-date"><i class="fa-regular fa-calendar me-1"></i>19 Jul 2026</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-new"><?php echo __('badge_new'); ?></span>
                                <i class="fa-solid fa-chevron-down notice-arrow-icon text-muted" style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="collapse" id="noticeCollapse2">
                            <div class="px-4 py-3" style="background: #F7F3E8; border-top: 1px solid #EDE2CC;">
                                <p class="small mb-2" style="color: #5A4A35; line-height: 1.6;"><?php echo __('notice_2_desc'); ?></p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: #DCC9A7 !important;">
                                    <span class="small" style="color: #8C7A65;"><i class="fa-solid fa-road me-1" style="color:#8A724C;"></i> Public Works Department</span>
                                    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #8A724C; border-radius: 20px;" onclick="event.stopPropagation(); alert('Notice document downloaded!');">
                                        <i class="fa-solid fa-download me-1"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice 3 Dropdown -->
                    <div class="notice-accordion-item border-bottom">
                        <div class="notice-item d-flex align-items-center justify-content-between p-3" data-bs-toggle="collapse" data-bs-target="#noticeCollapse3" aria-expanded="false" aria-controls="noticeCollapse3" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notice-bell"><i class="fa-solid fa-bell"></i></div>
                                <div class="notice-content">
                                    <div class="notice-title"><?php echo __('notice_3_title'); ?></div>
                                    <div class="notice-date"><i class="fa-regular fa-calendar me-1"></i>17 Jul 2026</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-chevron-down notice-arrow-icon text-muted" style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="collapse" id="noticeCollapse3">
                            <div class="px-4 py-3" style="background: #F7F3E8; border-top: 1px solid #EDE2CC;">
                                <p class="small mb-2" style="color: #5A4A35; line-height: 1.6;"><?php echo __('notice_3_desc'); ?></p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: #DCC9A7 !important;">
                                    <span class="small" style="color: #8C7A65;"><i class="fa-solid fa-faucet-drip me-1" style="color:#8A724C;"></i> Water & Sanitation Board</span>
                                    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #8A724C; border-radius: 20px;" onclick="event.stopPropagation(); alert('Notice document downloaded!');">
                                        <i class="fa-solid fa-download me-1"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice 4 Dropdown -->
                    <div class="notice-accordion-item border-bottom">
                        <div class="notice-item d-flex align-items-center justify-content-between p-3" data-bs-toggle="collapse" data-bs-target="#noticeCollapse4" aria-expanded="false" aria-controls="noticeCollapse4" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notice-bell"><i class="fa-solid fa-bell"></i></div>
                                <div class="notice-content">
                                    <div class="notice-title"><?php echo __('notice_4_title'); ?></div>
                                    <div class="notice-date"><i class="fa-regular fa-calendar me-1"></i>15 Jul 2026</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-chevron-down notice-arrow-icon text-muted" style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="collapse" id="noticeCollapse4">
                            <div class="px-4 py-3" style="background: #F7F3E8; border-top: 1px solid #EDE2CC;">
                                <p class="small mb-2" style="color: #5A4A35; line-height: 1.6;"><?php echo __('notice_4_desc'); ?></p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: #DCC9A7 !important;">
                                    <span class="small" style="color: #8C7A65;"><i class="fa-solid fa-wheat-awn me-1" style="color:#8A724C;"></i> Agricultural Development Wing</span>
                                    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #8A724C; border-radius: 20px;" onclick="event.stopPropagation(); alert('Notice document downloaded!');">
                                        <i class="fa-solid fa-download me-1"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice 5 Dropdown -->
                    <div class="notice-accordion-item">
                        <div class="notice-item d-flex align-items-center justify-content-between p-3" data-bs-toggle="collapse" data-bs-target="#noticeCollapse5" aria-expanded="false" aria-controls="noticeCollapse5" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notice-bell"><i class="fa-solid fa-bell"></i></div>
                                <div class="notice-content">
                                    <div class="notice-title"><?php echo __('notice_5_title'); ?></div>
                                    <div class="notice-date"><i class="fa-regular fa-calendar me-1"></i>10 Jul 2026</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-chevron-down notice-arrow-icon text-muted" style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="collapse" id="noticeCollapse5">
                            <div class="px-4 py-3" style="background: #F7F3E8; border-top: 1px solid #EDE2CC;">
                                <p class="small mb-2" style="color: #5A4A35; line-height: 1.6;"><?php echo __('notice_5_desc'); ?></p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: #DCC9A7 !important;">
                                    <span class="small" style="color: #8C7A65;"><i class="fa-solid fa-check-to-slot me-1" style="color:#8A724C;"></i> State Election Commission Representative</span>
                                    <button type="button" class="btn btn-sm text-white px-3" style="background-color: #8A724C; border-radius: 20px;" onclick="event.stopPropagation(); alert('Notice document downloaded!');">
                                        <i class="fa-solid fa-download me-1"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 DEDICATED ALL NOTICES MODAL
═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="allNoticesModal" tabindex="-1" aria-labelledby="allNoticesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: #F7F3E8;">
            <!-- Modal Header with Golden Sandstone Theme -->
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #3E2C23 0%, #70593B 60%, #8A724C 100%); border-bottom: 3px solid #DCC9A7;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-15 p-2 d-flex align-items-center justify-content-center" style="width:44px; height:44px; border: 1px solid rgba(220,201,167,0.4);">
                        <i class="fa-solid fa-clipboard-list text-warning fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="allNoticesModalLabel"><?php echo __('modal_notices_heading'); ?></h5>
                        <small style="color: #DCC9A7; font-size:0.83rem;"><?php echo __('modal_notices_sub'); ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" style="background: #F7F3E8;">
                <!-- Search & Category Filters -->
                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <div class="input-group shadow-sm rounded">
                            <span class="input-group-text bg-white border-end-0" style="border-color:#DCC9A7;"><i class="fa-solid fa-magnifying-glass" style="color:#8A724C;"></i></span>
                            <input type="text" id="noticeSearchInput" class="form-control border-start-0 border-end-0 ps-0" style="border-color:#DCC9A7;" placeholder="<?php echo __('modal_search_placeholder'); ?>" onkeyup="filterModalNotices()">
                            <button type="button" class="btn text-white fw-medium px-3" id="noticeSearchBtn" style="background-color:#8A724C; border-color:#8A724C; transition: all 0.2s;" onclick="filterModalNotices()">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> <?php echo __('btn_search'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="noticeCategoryFilter" class="form-select shadow-sm" style="border-color:#DCC9A7; color:#3E2C23; font-weight:500;" onchange="filterModalNotices()">
                            <option value="all"><?php echo __('modal_all_categories'); ?></option>
                            <option value="meeting"><?php echo __('cat_meeting'); ?></option>
                            <option value="works"><?php echo __('cat_works'); ?></option>
                            <option value="utility"><?php echo __('cat_utility'); ?></option>
                            <option value="scheme"><?php echo __('cat_scheme'); ?></option>
                            <option value="election"><?php echo __('cat_election'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Notices Items Container -->
                <div class="d-flex flex-column gap-3 mt-3" id="modalNoticesList">
                    
                    <!-- Notice 1 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="meeting" data-title="<?php echo htmlspecialchars(__('notice_1_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_1_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 21 Jul 2026 &nbsp;•&nbsp; <span class="badge" style="background:#8A724C; color:#fff;"><?php echo __('badge_new'); ?></span></div>
                                    <p class="small mb-0" style="color:#5A4A35;">Annual Gram Sabha general body meeting regarding rural infrastructure allocation, water conservation plans, and budget approval for FY 2026-27.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_meeting'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 2 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="works" data-title="<?php echo htmlspecialchars(__('notice_2_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-road"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_2_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 19 Jul 2026 &nbsp;•&nbsp; <span class="badge" style="background:#8A724C; color:#fff;"><?php echo __('badge_new'); ?></span></div>
                                    <p class="small mb-0" style="color:#5A4A35;">Concrete road re-surfacing and drainage laying work commencing on Main Market Road. Commuters requested to take bypass route.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_works'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 3 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="utility" data-title="<?php echo htmlspecialchars(__('notice_3_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-faucet-drip"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_3_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 17 Jul 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Scheduled pipeline maintenance in North & West Wards. Morning water supply timing adjusted to 6:00 AM - 8:00 AM.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_utility'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 4 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="scheme" data-title="<?php echo htmlspecialchars(__('notice_4_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-wheat-awn"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_4_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 15 Jul 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Application open for 50% solar pump subsidy under Chief Minister Rural Agriculture Welfare Scheme. Apply before 10th August.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_scheme'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 5 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="election" data-title="<?php echo htmlspecialchars(__('notice_5_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-check-to-slot"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_5_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 10 Jul 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Provisional voter electoral roll published for Ward No. 1 to 5. Objections/corrections can be submitted at Panchayat office by 5th August.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_election'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 6 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="utility" data-title="<?php echo htmlspecialchars(__('notice_6_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_6_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 05 Jul 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Citizens paying full property tax and water bills before 31st August will receive an early payment discount of 10% on total bill amount.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_utility'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 7 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="meeting" data-title="<?php echo htmlspecialchars(__('notice_7_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_7_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 01 Jul 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Free multi-specialty health checkup camp and routine child immunization drive organised at Sub-Health Centre, Main Road.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_meeting'); ?></span>
                        </div>
                    </div>

                    <!-- Notice 8 -->
                    <div class="modal-notice-item bg-white p-3 rounded-3 shadow-sm" style="border: 1px solid #DCC9A7;" data-category="scheme" data-title="<?php echo htmlspecialchars(__('notice_8_title')); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                <div class="badge-icon rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0; background:#EDE2CC; color:#8A724C;">
                                    <i class="fa-solid fa-house-chimney"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color:#3E2C23;"><?php echo __('notice_8_title'); ?></h6>
                                    <div class="small mb-2" style="color:#8C7A65;"><i class="fa-regular fa-calendar me-1"></i> 25 Jun 2026</div>
                                    <p class="small mb-0" style="color:#5A4A35;">Final list of sanctioned applicants under Pradhan Mantri Awas Yojana (Gramin) Phase III displayed on notice board & portal.</p>
                                </div>
                            </div>
                            <span class="badge px-3 py-2 small" style="background:#EDE2CC; color:#755f3c; border: 1px solid #DCC9A7; font-weight:600;"><?php echo __('cat_scheme'); ?></span>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer bg-white py-3 px-4 justify-content-between" style="border-top: 1px solid #DCC9A7;">
                <span class="small" style="color:#8C7A65;"><i class="fa-solid fa-shield-halved me-1" style="color:#8A724C;"></i> Gram Panchayat Official Notice Portal</span>
                <button type="button" class="btn text-white px-4 rounded-pill fw-medium" style="background-color:#8A724C; border-color:#8A724C;" data-bs-dismiss="modal"><?php echo __('btn_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
 5. GOVERNMENT SCHEMES
═══════════════════════════════════════════════════════════ -->
<section id="schemes" class="section-padding bg-light-warm">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('schemes_tag'); ?></span>
            <h2 class="section-title"><?php echo __('schemes_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('schemes_subtitle'); ?></p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="scheme-featured-card">
                    <div class="scheme-img-wrap">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&auto=format&fit=crop&q=80" alt="PM Awas Yojana">
                        <div class="scheme-img-overlay"></div>
                        <span class="scheme-badge-overlay"><?php echo __('central_govt'); ?></span>
                    </div>
                    <div class="scheme-body">
                        <h5 class="scheme-title"><?php echo __('scheme_pmay_title'); ?></h5>
                        <p class="scheme-desc"><?php echo __('scheme_pmay_desc'); ?></p>
                        <a href="schemes/scheme_details.php?scheme=pmay" class="btn-learn-more"><i class="fa-solid fa-arrow-right"></i> <span><?php echo __('learn_more'); ?></span></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80">
                <div class="scheme-featured-card">
                    <div class="scheme-img-wrap">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&auto=format&fit=crop&q=80" alt="Jal Jeevan Mission">
                        <div class="scheme-img-overlay"></div>
                        <span class="scheme-badge-overlay"><?php echo __('central_govt'); ?></span>
                    </div>
                    <div class="scheme-body">
                        <h5 class="scheme-title"><?php echo __('scheme_jjm_title'); ?></h5>
                        <p class="scheme-desc"><?php echo __('scheme_jjm_desc'); ?></p>
                        <a href="schemes/scheme_details.php?scheme=jjm" class="btn-learn-more"><i class="fa-solid fa-arrow-right"></i> <span><?php echo __('learn_more'); ?></span></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160">
                <div class="scheme-featured-card">
                    <div class="scheme-img-wrap">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=600&auto=format&fit=crop&q=80" alt="PM Kisan Samman Nidhi">
                        <div class="scheme-img-overlay"></div>
                        <span class="scheme-badge-overlay"><?php echo __('central_govt'); ?></span>
                    </div>
                    <div class="scheme-body">
                        <h5 class="scheme-title"><?php echo __('scheme_pmkisan_title'); ?></h5>
                        <p class="scheme-desc"><?php echo __('scheme_pmkisan_desc'); ?></p>
                        <a href="schemes/scheme_details.php?scheme=pmkisan" class="btn-learn-more"><i class="fa-solid fa-arrow-right"></i> <span><?php echo __('learn_more'); ?></span></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="glass-card scheme-card">
                    <div class="feature-icon-box bg-primary-soft"><i class="fa-solid fa-heart-pulse"></i></div>
                    <h6 class="scheme-title"><?php echo __('s_ayushman_title'); ?></h6>
                    <p class="scheme-desc"><?php echo __('s_ayushman_desc'); ?></p>
                    <div class="scheme-eligibility"><strong><?php echo __('eligibility'); ?>:</strong> <?php echo __('s_ayushman_elig'); ?> <a href="schemes/scheme_details.php?scheme=pmjay" class="btn btn-secondary-custom w-100 btn-sm mt-2"><?php echo __('learn_more'); ?></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="60">
                <div class="glass-card scheme-card">
                    <div class="feature-icon-box bg-secondary-soft"><i class="fa-solid fa-hands-clean"></i></div>
                    <h6 class="scheme-title"><?php echo __('s_sbm_title'); ?></h6>
                    <p class="scheme-desc"><?php echo __('s_sbm_desc'); ?></p>
                    <div class="scheme-eligibility"><strong><?php echo __('eligibility'); ?>:</strong> <?php echo __('s_sbm_elig'); ?> <a href="schemes/scheme_details.php?scheme=sbm" class="btn btn-secondary-custom w-100 btn-sm mt-2"><?php echo __('learn_more'); ?></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="120">
                <div class="glass-card scheme-card">
                    <div class="feature-icon-box bg-accent-soft"><i class="fa-solid fa-person-digging"></i></div>
                    <h6 class="scheme-title"><?php echo __('s_mgnrega_title'); ?></h6>
                    <p class="scheme-desc"><?php echo __('s_mgnrega_desc'); ?></p>
                    <div class="scheme-eligibility"><strong><?php echo __('eligibility'); ?>:</strong> <?php echo __('s_mgnrega_elig'); ?> <a href="schemes/scheme_details.php?scheme=mgnrega" class="btn btn-secondary-custom w-100 btn-sm mt-2"><?php echo __('learn_more'); ?></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="180">
                <div class="glass-card scheme-card">
                    <div class="feature-icon-box bg-primary-soft"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h6 class="scheme-title"><?php echo __('s_bbbp_title'); ?></h6>
                    <p class="scheme-desc"><?php echo __('s_bbbp_desc'); ?></p>
                    <div class="scheme-eligibility"><strong><?php echo __('eligibility'); ?>:</strong> <?php echo __('s_bbbp_elig'); ?> <a href="schemes/scheme_details.php?scheme=bbbp" class="btn btn-secondary-custom w-100 btn-sm mt-2"><?php echo __('learn_more'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 6. COMPLAINT STATUS CHECKER
═══════════════════════════════════════════════════════════ -->
<section id="tracker" class="tracker-section">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-tag"><?php echo __('tracker_tag'); ?></span>
            <h2 class="section-title"><?php echo __('tracker_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('tracker_subtitle'); ?></p>
        </div>
        <div class="tracker-card" data-aos="zoom-in">
            <form id="tracker-form" action="complaint/track_complaint.php" method="GET">
                <div class="tracker-input-wrap">
                    <i class="fa-solid fa-hashtag" style="color:var(--text-muted);"></i>
                    <input type="text" name="complaint_id" id="complaint-id-input" class="tracker-input"
                        placeholder="<?php echo __('tracker_placeholder'); ?>" autocomplete="off" required>
                    <button type="submit" id="btn-track" class="btn-track">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        <span><?php echo __('btn_track'); ?></span>
                    </button>
                </div>
                <p class="text-center mt-3" style="font-size:0.78rem;color:var(--text-muted);">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    <span><?php echo __('tracker_hint'); ?></span>
                </p>
            </form>

            <div id="timeline-card" class="timeline-result mt-4" style="display:block;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span style="font-size:0.78rem;color:var(--text-muted);"><?php echo __('tracker_id_label'); ?></span>
                        <strong id="tracking-no" style="color:var(--primary);margin-left:5px;">GPCMS202600123</strong>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-2" style="font-size:0.72rem;"><?php echo __('live_tracking'); ?></span>
                </div>
                <div class="timeline-steps">
                    <div class="timeline-bar" id="timeline-bar"></div>
                    <div class="step-node active completed" id="step-1">
                        <div class="step-circle"><i class="fa-solid fa-file-pen"></i></div>
                        <div class="step-label"><?php echo __('step_registered'); ?></div>
                        <div class="step-date" id="date-1">21 Jul 2026</div>
                    </div>
                    <div class="step-node active completed" id="step-2">
                        <div class="step-circle"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                        <div class="step-label"><?php echo __('step_review'); ?></div>
                        <div class="step-date" id="date-2">21 Jul 2026</div>
                    </div>
                    <div class="step-node active completed" id="step-3">
                        <div class="step-circle"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="step-label"><?php echo __('step_assigned'); ?></div>
                        <div class="step-date" id="date-3">22 Jul 2026</div>
                    </div>
                    <div class="step-node active" id="step-4">
                        <div class="step-circle"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                        <div class="step-label"><?php echo __('step_progress'); ?></div>
                        <div class="step-date" id="date-4">Active</div>
                    </div>
                    <div class="step-node" id="step-5">
                        <div class="step-circle"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="step-label"><?php echo __('step_resolved'); ?></div>
                        <div class="step-date" id="date-5">Pending</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 7. GALLERY SECTION
═══════════════════════════════════════════════════════════ -->
<section id="gallery" class="section-padding bg-light-warm">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('gallery_tag'); ?></span>
            <h2 class="section-title"><?php echo __('gallery_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('gallery_subtitle'); ?></p>
        </div>
        <div class="gallery-grid" data-aos="fade-up">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1577495508048-b635879837f1?w=600&auto=format&fit=crop&q=80" alt="Gram Panchayat Office" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-building me-2"></i><?php echo __('g_office'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&auto=format&fit=crop&q=80" alt="Village Lake" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-water me-2"></i><?php echo __('g_lake'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&auto=format&fit=crop&q=80" alt="Village Roads" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-road me-2"></i><?php echo __('g_roads'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&auto=format&fit=crop&q=80" alt="Village School" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-school me-2"></i><?php echo __('g_school'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&auto=format&fit=crop&q=80" alt="Plantation Drive" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-tree me-2"></i><?php echo __('g_plantation'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&auto=format&fit=crop&q=80" alt="Community Hall" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-people-roof me-2"></i><?php echo __('g_community'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1532094349884-543559c5bc5b?w=600&auto=format&fit=crop&q=80" alt="Clean Village Campaign" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-broom me-2"></i><?php echo __('g_clean'); ?></div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?w=600&auto=format&fit=crop&q=80" alt="Gram Sabha Meeting" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title"><i class="fa-solid fa-people-group me-2"></i><?php echo __('g_gramsabha'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 8. ANNOUNCEMENT SLIDER
═══════════════════════════════════════════════════════════ -->
<section class="announcement-slider-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('ann_tag'); ?></span>
            <h2 class="section-title"><?php echo __('ann_title'); ?></h2>
            <p class="section-subtitle" style="color:rgba(255,255,255,0.6);"><?php echo __('ann_subtitle'); ?></p>
        </div>
        <div class="announcement-slider-wrap" id="announcement-slider">
            <div class="announcement-slide active">
                <div class="announcement-card-slide">
                    <div class="announce-icon-circle"><i class="fa-solid fa-syringe"></i></div>
                    <div>
                        <div class="announce-title"><?php echo __('ann_1_title'); ?></div>
                        <div class="announce-desc"><?php echo __('ann_1_desc'); ?></div>
                        <div class="announce-date"><i class="fa-regular fa-clock me-1"></i>21 Jul 2026</div>
                    </div>
                </div>
            </div>
            <div class="announcement-slide">
                <div class="announcement-card-slide">
                    <div class="announce-icon-circle"><i class="fa-solid fa-people-group"></i></div>
                    <div>
                        <div class="announce-title"><?php echo __('notice_1_title'); ?></div>
                        <div class="announce-desc"><?php echo __('notices_subtitle'); ?></div>
                        <div class="announce-date"><i class="fa-regular fa-clock me-1"></i>25 Jul 2026</div>
                    </div>
                </div>
            </div>
            <div class="announcement-slide">
                <div class="announcement-card-slide">
                    <div class="announce-icon-circle"><i class="fa-solid fa-broom"></i></div>
                    <div>
                        <div class="announce-title"><?php echo __('g_clean'); ?></div>
                        <div class="announce-desc"><?php echo __('s_sbm_desc'); ?></div>
                        <div class="announce-date"><i class="fa-regular fa-clock me-1"></i>27 Jul 2026</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-dots" id="slider-dots">
            <button class="slider-dot active" data-slide="0"></button>
            <button class="slider-dot" data-slide="1"></button>
            <button class="slider-dot" data-slide="2"></button>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 9. ABOUT GRAM PANCHAYAT — TWO COLUMN
═══════════════════════════════════════════════════════════ -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="about-image-wrap">
                    <img src="https://images.unsplash.com/photo-1605462863863-10d9e47e15ee?w=800&auto=format&fit=crop&q=80"
                        alt="Gram Panchayat Village" style="width:100%;height:420px;object-fit:cover;">
                    <div class="about-image-badge">
                        <div class="about-badge-icon"><i class="fa-solid fa-leaf"></i></div>
                        <div>
                            <div class="about-badge-text"><?php echo __('about_badge_text'); ?></div>
                            <div class="about-badge-sub"><?php echo __('about_badge_sub'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="section-tag"><?php echo __('about_tag'); ?></span>
                <h2 class="section-title"><?php echo __('about_title'); ?></h2>
                <p style="font-size:0.95rem;color:var(--text-muted);line-height:1.8;margin-bottom:1rem;"><?php echo __('about_desc'); ?></p>
                <ul class="about-content-list">
                    <li><i class="fa-solid fa-check-circle"></i> <span><?php echo __('about_f1'); ?></span></li>
                    <li><i class="fa-solid fa-check-circle"></i> <span><?php echo __('about_f2'); ?></span></li>
                    <li><i class="fa-solid fa-check-circle"></i> <span><?php echo __('about_f3'); ?></span></li>
                    <li><i class="fa-solid fa-check-circle"></i> <span><?php echo __('about_f4'); ?></span></li>
                    <li><i class="fa-solid fa-check-circle"></i> <span><?php echo __('about_f5'); ?></span></li>
                </ul>
                <div class="d-flex gap-3 mt-3">
                    <a href="complaint/register_complaint.php" class="btn-primary-custom"><?php echo __('about_cta_1'); ?></a>
                    <a href="#schemes" class="btn-secondary-custom"><?php echo __('about_cta_2'); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 10. CITIZEN TESTIMONIALS
═══════════════════════════════════════════════════════════ -->
<section id="testimonials" class="section-padding bg-light-warm">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('testimonials_tag'); ?></span>
            <h2 class="section-title"><?php echo __('testimonials_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('testimonials_subtitle'); ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <div class="glass-card testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <div class="testimonial-quote">"Complaint was resolved within two days. The street lighting in our ward was fixed promptly."</div>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&q=80" alt="Mukund Thorat" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-name">Mukund Thorat</div>
                            <div class="testimonial-loc">Ward No. 3, Village Nandur</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="80">
                <div class="glass-card testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <div class="testimonial-quote">"Tracking my PM Awas status online meant zero trips to the block office. Real transparency!"</div>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&q=80" alt="Vedika Gavane" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-name">Vedika Gavane</div>
                            <div class="testimonial-loc">Ward No. 7, Village Palus</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="160">
                <div class="glass-card testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <div class="testimonial-quote">"Water quality issue was inspected and resolved by the sanitary officer within days. Brilliant system!"</div>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&q=80" alt="Smit Ahirrao" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-name">Smit Ahirrao</div>
                            <div class="testimonial-loc">Ward No. 1, Village Shirpur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 11. FAQ SECTION
═══════════════════════════════════════════════════════════ -->
<section id="faq" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('faq_tag'); ?></span>
            <h2 class="section-title"><?php echo __('faq_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('faq_subtitle'); ?></p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                <i class="fa-regular fa-circle-question me-2" style="color:var(--primary);"></i>
                                <span><?php echo __('faq_q1'); ?></span>
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo __('faq_a1'); ?></div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="fa-regular fa-circle-question me-2" style="color:var(--primary);"></i>
                                <span><?php echo __('faq_q2'); ?></span>
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo __('faq_a2'); ?></div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="fa-regular fa-circle-question me-2" style="color:var(--primary);"></i>
                                <span><?php echo __('faq_q3'); ?></span>
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo __('faq_a3'); ?></div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="fa-regular fa-circle-question me-2" style="color:var(--primary);"></i>
                                <span><?php echo __('faq_q4'); ?></span>
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo __('faq_a4'); ?></div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <i class="fa-regular fa-circle-question me-2" style="color:var(--primary);"></i>
                                <span><?php echo __('faq_q5'); ?></span>
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo __('faq_a5'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
 12. CONTACT SECTION
═══════════════════════════════════════════════════════════ -->
<section id="contact" class="section-padding bg-light-warm">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo __('contact_tag'); ?></span>
            <h2 class="section-title"><?php echo __('contact_title'); ?></h2>
            <p class="section-subtitle"><?php echo __('contact_subtitle'); ?></p>
        </div>
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="contact-map-wrap">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d121085.79013736!2d73.75440459!3d18.52042135!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bf2e67461101%3A0x828d43bf9d9ee343!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1690000000000"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        title="Gram Panchayat Office Location"></iframe>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="contact-info-card">
                    <h4 class="section-title mb-0" style="font-size:1.3rem;"><?php echo __('contact_office_title'); ?></h4>
                    <p style="font-size:0.82rem;color:var(--text-muted);margin:0.3rem 0 1rem;"><?php echo __('contact_office_sub'); ?></p>

                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                        <div>
                            <div class="contact-info-label"><?php echo __('address_label'); ?></div>
                            <div class="contact-info-value"><?php echo __('address_val'); ?></div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <div class="contact-info-label"><?php echo __('email_label'); ?></div>
                            <div class="contact-info-value">office@grampanchayat.gov.in</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <div class="contact-info-label"><?php echo __('phone_label'); ?></div>
                            <div class="contact-info-value">+91 98765 43210</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fa-regular fa-clock"></i></div>
                        <div>
                            <div class="contact-info-label"><?php echo __('hours_label'); ?></div>
                            <div class="contact-info-value"><?php echo __('hours_val'); ?></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="mailto:office@grampanchayat.gov.in" class="btn-primary-custom btn-sm" style="font-size:0.82rem;padding:0.5rem 1.2rem;">
                            <i class="fa-solid fa-envelope me-1"></i> <?php echo __('send_email'); ?>
                        </a>
                        <a href="tel:+919876543210" class="btn-secondary-custom btn-sm" style="font-size:0.82rem;padding:0.5rem 1.2rem;">
                            <i class="fa-solid fa-phone me-1"></i> <?php echo __('call_us'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
