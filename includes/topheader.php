<?php
/**
 * FILE: includes/topheader.php
 * PURPOSE: Reusable Top Header Bar Component
 */
$full_name = $_SESSION['full_name'] ?? 'User';
$role_name = $_SESSION['role_name'] ?? 'Citizen';
$user_initial = mb_strtoupper(mb_substr($full_name, 0, 1, 'UTF-8'));
?>
<!-- ═══════════════════════════════════════════════════════
     TOP HEADER BAR
     ═══════════════════════════════════════════════════════ -->
<header class="citizen-topheader">
    <div class="d-flex align-items-center gap-3">
        <button class="citizen-nav-toggler d-lg-none" type="button" id="sidebarToggleBtn" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="citizen-topheader-title d-none d-sm-block">
            Gram Panchayat Complaint Management System
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="citizen-topheader-meta d-none d-md-block">
            <i class="bi bi-clock me-1"></i><?php echo date('D, d M Y, h:i a'); ?>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="citizen-avatar"><?php echo htmlspecialchars($user_initial); ?></span>
                <span class="d-none d-md-inline fw-semibold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($full_name); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 citizen-profile-dropdown">
                <li class="px-3 pt-2 pb-1">
                    <div class="fw-semibold citizen-profile-name"><?php echo htmlspecialchars($full_name); ?></div>
                    <small class="text-muted"><?php echo htmlspecialchars($role_name); ?> Account</small>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="../logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
