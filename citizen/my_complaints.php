<?php
/**
 * FILE: citizen/my_complaints.php
 * MODULE: Citizen Module
 * PURPOSE: Full complaints listing for the authenticated citizen.
 *          Shows all complaints with status filter, search,
 *          and track action. Citizen sees ONLY their own data.
 * CONTRACT: Citizen_Database_Contract.txt
 */

// ── CONTRACT INCLUDES (required on every protected page) ─
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';


$user_id = intval($_SESSION['user_id']);


// ── READ ?created= PARAM FOR SUCCESS BANNER ───────────────────────────────────
// save_complaint.php redirects here with ?created=<id> after success
$created_id = null;
if (isset($_GET['created']) && ctype_digit((string) $_GET['created'])) {
    $created_id = (int) $_GET['created'];
}

// ── COMPLAINT STATISTICS ──────────────────────────────────────────────────────
$stats = [
    'total'       => 0,
    'pending'     => 0,
    'assigned'    => 0,
    'in_progress' => 0,
    'resolved'    => 0,
];

$stats_stmt = $conn->prepare(
    "SELECT status, COUNT(*) AS cnt
     FROM complaints
     WHERE user_id = ?
     GROUP BY status"
);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();

while ($row = $stats_result->fetch_assoc()) {
    $s = $row['status'];
    if (array_key_exists($s, $stats)) {
        $stats[$s] = (int) $row['cnt'];
    }
    $stats['total'] += (int) $row['cnt'];
}
$stats_stmt->close();

// ── FETCH ALL CITIZEN COMPLAINTS ──────────────────────────────────────────────
// Sorted newest first; citizen can ONLY see their own rows (WHERE user_id = ?)
$complaints = [];
$comp_stmt = $conn->prepare(
    "SELECT
        c.complaint_id,
        c.complaint_title,
        c.complaint_description,
        c.village_ward,
        c.status,
        c.submitted_at,
        c.updated_at,
        cat.category_name
     FROM complaints c
     LEFT JOIN categories cat ON c.category_id = cat.category_id
     WHERE c.user_id = ?
     ORDER BY c.submitted_at DESC, c.complaint_id DESC"
);
$comp_stmt->bind_param("i", $user_id);
$comp_stmt->execute();
$comp_result = $comp_stmt->get_result();

while ($row = $comp_result->fetch_assoc()) {
    $complaints[] = $row;
}
$comp_stmt->close();
$conn->close();

$total_count = count($complaints);

// ── HELPERS ───────────────────────────────────────────────────────────────────

/**
 * Returns a human-readable label for a complaint status.
 */
function mc_status_label(string $status): string
{
    $map = [
        'pending'     => 'Pending',
        'assigned'    => 'Assigned',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
    ];
    return $map[$status] ?? htmlspecialchars(ucfirst(str_replace('_', ' ', $status)));
}

/**
 * Returns the CSS class suffix for a complaint status badge.
 */
function mc_status_badge(string $status): string
{
    $map = [
        'pending'     => 'badge-status-pending',
        'assigned'    => 'badge-status-assigned',
        'in_progress' => 'badge-status-inprogress',
        'resolved'    => 'badge-status-resolved',
    ];
    return $map[$status] ?? 'badge-status-default';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My Complaints – GPCMS Citizen Portal">
    <title>My Complaints | GPCMS Citizen Portal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons 1.11 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts – Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Citizen Module Stylesheet -->
    <link rel="stylesheet" href="../css/citizen.css">
</head>
<body class="citizen-body">

<div class="citizen-layout">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="citizen-main-content">
        <?php require_once '../includes/topheader.php'; ?>

        <!-- ═══════════════════════════════════════════════════════
             PAGE WRAPPER
             ═══════════════════════════════════════════════════════ -->
        <main class="citizen-page-wrapper" id="mainContent">
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- ── Breadcrumb ─────────────────────────────────── -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb citizen-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="citizen_dashboard.php">
                        <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">My Complaints</li>
            </ol>
        </nav>

        <!-- ── Page Header ────────────────────────────────── -->
        <div class="citizen-page-header mb-4">
            <div>
                <h1 class="citizen-page-title">
                    <i class="bi bi-list-ul me-2" aria-hidden="true"></i>My Complaints
                </h1>
                <p class="citizen-page-subtitle">
                    Showing all <?php echo $total_count; ?> complaint<?php echo $total_count !== 1 ? 's' : ''; ?>
                    registered under your account.
                </p>
            </div>
            <div class="citizen-header-cta">
                <a href="register_complaint.php"
                   class="btn citizen-btn-primary"
                   id="btn-register-new">
                    <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>Register New
                </a>
            </div>
        </div>

        <!-- ── Success Banner (after save_complaint.php redirect) ── -->
        <?php if ($created_id !== null): ?>
        <div class="citizen-alert citizen-alert-success mb-4"
             role="alert"
             id="successBanner"
             data-auto-dismiss="10000">
            <span class="citizen-alert-icon" aria-hidden="true">
                <i class="bi bi-check-circle-fill"></i>
            </span>
            <div class="citizen-alert-body">
                <strong>Complaint Registered Successfully!</strong>
                <div class="mt-1">
                    Your complaint
                    <strong>#<?php echo $created_id; ?></strong>
                    has been submitted and is now visible below with status
                    <span class="citizen-status-badge badge-status-pending">Pending</span>.
                    Use
                    <a href="track_complaint.php?complaint_id=<?php echo $created_id; ?>"
                       class="citizen-alert-link">Track Complaint</a>
                    to follow its progress.
                </div>
            </div>
            <button type="button"
                    class="citizen-alert-close"
                    aria-label="Dismiss success banner">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- ── Statistics Strip ───────────────────────────── -->
        <div class="row g-3 mb-4" role="region" aria-label="Complaint Statistics">
            <div class="col-6 col-sm-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-total" id="stat-card-total">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number"><?php echo $stats['total']; ?></div>
                        <div class="citizen-stat-label">Total</div>
                    </div>
                </article>
            </div>
            <div class="col-6 col-sm-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-pending" id="stat-card-pending">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number"><?php echo $stats['pending']; ?></div>
                        <div class="citizen-stat-label">Pending</div>
                    </div>
                </article>
            </div>
            <div class="col-6 col-sm-4 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-assigned" id="stat-card-assigned">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number"><?php echo $stats['assigned']; ?></div>
                        <div class="citizen-stat-label">Assigned</div>
                    </div>
                </article>
            </div>
            <div class="col-6 col-sm-6 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-inprogress" id="stat-card-inprogress">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number"><?php echo $stats['in_progress']; ?></div>
                        <div class="citizen-stat-label">In Progress</div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-stat5">
                <article class="citizen-stat-card citizen-stat-resolved" id="stat-card-resolved">
                    <div class="citizen-stat-icon" aria-hidden="true">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="citizen-stat-body">
                        <div class="citizen-stat-number"><?php echo $stats['resolved']; ?></div>
                        <div class="citizen-stat-label">Resolved</div>
                    </div>
                </article>
            </div>
        </div>

        <?php if ($total_count === 0): ?>
        <!-- ── Complete Empty State ───────────────────────── -->
        <div class="citizen-card">
            <div class="citizen-card-body">
                <div class="citizen-empty-state py-5" id="completeEmptyState">
                    <i class="bi bi-inbox citizen-empty-icon" aria-hidden="true"></i>
                    <h2 class="citizen-empty-title">No Complaints Yet</h2>
                    <p class="citizen-empty-text">
                        You have not registered any complaints. Submit your first complaint below.
                    </p>
                    <a href="register_complaint.php"
                       class="btn citizen-btn-primary mt-2"
                       id="btn-first-complaint">
                        <i class="bi bi-plus me-1" aria-hidden="true"></i>
                        Register Your First Complaint
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ── Filter & Search Bar ────────────────────────── -->
        <div class="citizen-filter-bar mb-3" id="complaintsFilterBar">

            <!-- Search Input -->
            <div class="citizen-search-wrap">
                <i class="bi bi-search citizen-search-icon" aria-hidden="true"></i>
                <input type="search"
                       class="citizen-search-input"
                       id="complaintSearchInput"
                       placeholder="Search by ID or title…"
                       aria-label="Search complaints">
            </div>

            <!-- Status Filter Buttons -->
            <div class="citizen-status-filters" role="group" aria-label="Filter by status">
                <button class="citizen-filter-btn active"
                        data-filter="all"
                        type="button"
                        id="filterBtnAll"
                        aria-pressed="true">
                    All
                    <span class="citizen-filter-count"><?php echo $total_count; ?></span>
                </button>
                <button class="citizen-filter-btn"
                        data-filter="pending"
                        type="button"
                        id="filterBtnPending"
                        aria-pressed="false">
                    Pending
                    <span class="citizen-filter-count"><?php echo $stats['pending']; ?></span>
                </button>
                <button class="citizen-filter-btn"
                        data-filter="assigned"
                        type="button"
                        id="filterBtnAssigned"
                        aria-pressed="false">
                    Assigned
                    <span class="citizen-filter-count"><?php echo $stats['assigned']; ?></span>
                </button>
                <button class="citizen-filter-btn"
                        data-filter="in_progress"
                        type="button"
                        id="filterBtnInProgress"
                        aria-pressed="false">
                    In Progress
                    <span class="citizen-filter-count"><?php echo $stats['in_progress']; ?></span>
                </button>
                <button class="citizen-filter-btn"
                        data-filter="resolved"
                        type="button"
                        id="filterBtnResolved"
                        aria-pressed="false">
                    Resolved
                    <span class="citizen-filter-count"><?php echo $stats['resolved']; ?></span>
                </button>
            </div>

            <!-- Results Count + Reset -->
            <div class="citizen-filter-meta" id="filterMeta">
                <span id="visibleCount"><?php echo $total_count; ?></span>
                complaint<?php echo $total_count !== 1 ? 's' : ''; ?> shown
                <button type="button"
                        class="citizen-filter-reset d-none"
                        id="resetFiltersBtn"
                        aria-label="Reset all filters">
                    <i class="bi bi-x me-1" aria-hidden="true"></i>Reset
                </button>
            </div>
        </div>

        <!-- ── Complaints Table (Desktop ≥ 768px) ─────────── -->
        <div class="citizen-card mb-3" id="complaintsTableCard">
            <div class="citizen-card-body p-0">
                <div class="table-responsive">
                    <table class="citizen-table w-100"
                           id="complaintsTable"
                           aria-label="All your complaints">
                        <thead>
                            <tr>
                                <th scope="col">#&nbsp;ID</th>
                                <th scope="col">Title</th>
                                <th scope="col">Category</th>
                                <th scope="col">Village / Ward</th>
                                <th scope="col">Submitted</th>
                                <th scope="col">Last Updated</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="complaintsTableBody">

                        <?php foreach ($complaints as $cmp): ?>
                        <?php
                            $cid         = intval($cmp['complaint_id']);
                            $status      = $cmp['status'];
                            $isNew       = ($created_id !== null && $cid === $created_id);
                            $searchText  = strtolower('#' . $cid . ' ' . $cmp['complaint_title']);
                        ?>
                        <tr class="complaint-row <?php echo $isNew ? 'row-new-highlight' : ''; ?>"
                            data-status="<?php echo htmlspecialchars($status); ?>"
                            data-search="<?php echo htmlspecialchars($searchText); ?>"
                            id="row-<?php echo $cid; ?>">

                            <td>
                                <span class="citizen-complaint-id">
                                    #<?php echo $cid; ?>
                                </span>
                                <?php if ($isNew): ?>
                                <span class="citizen-new-badge" aria-label="Newly registered">NEW</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="citizen-complaint-title">
                                    <?php echo htmlspecialchars($cmp['complaint_title']); ?>
                                </span>
                                <div class="citizen-complaint-preview citizen-small text-muted">
                                    <?php
                                    $preview = htmlspecialchars($cmp['complaint_description']);
                                    echo mb_strlen($preview) > 80
                                        ? mb_substr($preview, 0, 80) . '…'
                                        : $preview;
                                    ?>
                                </div>
                            </td>

                            <td>
                                <span class="citizen-small text-muted">
                                    <?php echo htmlspecialchars($cmp['category_name'] ?? '—'); ?>
                                </span>
                            </td>

                            <td>
                                <span class="citizen-small text-muted">
                                    <?php echo htmlspecialchars($cmp['village_ward']); ?>
                                </span>
                            </td>

                            <td>
                                <span class="citizen-small text-muted">
                                    <?php echo htmlspecialchars(
                                        date('d M Y', strtotime($cmp['submitted_at']))
                                    ); ?>
                                </span>
                            </td>

                            <td>
                                <span class="citizen-small text-muted">
                                    <?php echo htmlspecialchars(
                                        date('d M Y', strtotime($cmp['updated_at']))
                                    ); ?>
                                </span>
                            </td>

                            <td>
                                <span class="citizen-status-badge <?php echo mc_status_badge($status); ?>">
                                    <?php echo mc_status_label($status); ?>
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="track_complaint.php?complaint_id=<?php echo $cid; ?>"
                                       class="btn citizen-btn-action-sm"
                                       aria-label="Track complaint #<?php echo $cid; ?>">
                                        <i class="bi bi-search me-1" aria-hidden="true"></i>Track
                                    </a>
                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- No results after filter (hidden by default, shown by JS) -->
                <div class="citizen-empty-state py-4 d-none" id="noFilterResults">
                    <i class="bi bi-funnel citizen-empty-icon"
                       style="font-size:2.5rem;" aria-hidden="true"></i>
                    <h3 class="citizen-empty-title" style="font-size:1.1rem;">No Complaints Match</h3>
                    <p class="citizen-empty-text">
                        Try a different status filter or clear your search.
                    </p>
                    <button type="button"
                            class="btn citizen-btn-secondary btn-sm"
                            id="noResultsResetBtn">
                        <i class="bi bi-x me-1" aria-hidden="true"></i>Clear Filters
                    </button>
                </div>

            </div>
        </div>

        <!-- ── Mobile Card View (< 768px) ─────────────────── -->
        <div class="citizen-mobile-cards" id="complaintsMobileCards" aria-label="Complaints list">

            <?php foreach ($complaints as $cmp): ?>
            <?php
                $cid        = intval($cmp['complaint_id']);
                $status     = $cmp['status'];
                $isNew      = ($created_id !== null && $cid === $created_id);
                $searchText = strtolower('#' . $cid . ' ' . $cmp['complaint_title']);
            ?>
            <div class="citizen-mobile-card complaint-row <?php echo $isNew ? 'row-new-highlight' : ''; ?>"
                 data-status="<?php echo htmlspecialchars($status); ?>"
                 data-search="<?php echo htmlspecialchars($searchText); ?>"
                 id="card-<?php echo $cid; ?>">

                <!-- Card Top Row: ID + NEW badge + Status -->
                <div class="citizen-mc-top">
                    <div class="d-flex align-items-center gap-2">
                        <span class="citizen-complaint-id">#<?php echo $cid; ?></span>
                        <?php if ($isNew): ?>
                        <span class="citizen-new-badge" aria-label="Newly registered">NEW</span>
                        <?php endif; ?>
                    </div>
                    <span class="citizen-status-badge <?php echo mc_status_badge($status); ?>">
                        <?php echo mc_status_label($status); ?>
                    </span>
                </div>

                <!-- Title -->
                <div class="citizen-mc-title">
                    <?php echo htmlspecialchars($cmp['complaint_title']); ?>
                </div>

                <!-- Meta row -->
                <div class="citizen-mc-meta">
                    <span>
                        <i class="bi bi-tag me-1" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($cmp['category_name'] ?? '—'); ?>
                    </span>
                    <span>
                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($cmp['village_ward']); ?>
                    </span>
                </div>

                <!-- Dates -->
                <div class="citizen-mc-dates citizen-small text-muted">
                    <span>
                        <i class="bi bi-calendar me-1" aria-hidden="true"></i>
                        Submitted:
                        <?php echo htmlspecialchars(date('d M Y', strtotime($cmp['submitted_at']))); ?>
                    </span>
                    <span>
                        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                        Updated:
                        <?php echo htmlspecialchars(date('d M Y', strtotime($cmp['updated_at']))); ?>
                    </span>
                </div>

                <!-- Action -->
                <div class="citizen-mc-actions">
                    <a href="track_complaint.php?complaint_id=<?php echo $cid; ?>"
                       class="btn citizen-btn-action-sm w-100"
                       aria-label="Track complaint #<?php echo $cid; ?>">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>Track Complaint
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- No results (mobile) -->
            <div class="citizen-empty-state py-4 d-none" id="noFilterResultsMobile">
                <i class="bi bi-funnel citizen-empty-icon"
                   style="font-size:2.5rem;" aria-hidden="true"></i>
                <h3 class="citizen-empty-title" style="font-size:1.1rem;">No Complaints Match</h3>
                <p class="citizen-empty-text">Try a different filter or clear your search.</p>
                <button type="button"
                        class="btn citizen-btn-secondary btn-sm"
                        id="noResultsResetBtnMobile">
                    <i class="bi bi-x me-1" aria-hidden="true"></i>Clear Filters
                </button>
            </div>

        </div><!-- /.citizen-mobile-cards -->

        <?php endif; // end $total_count > 0 ?>

    </div><!-- /.container-fluid -->
</main>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════ -->
<footer class="citizen-footer" id="citizenFooter">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-center gap-2">
            <div class="citizen-footer-brand">
                <i class="bi bi-building me-1" aria-hidden="true"></i>
                <strong>Gram Panchayat Complaint Management System</strong>
            </div>
            <div class="citizen-footer-meta">
                <span><i class="bi bi-telephone me-1" aria-hidden="true"></i>1800-123-456</span>
                <span class="citizen-footer-sep" aria-hidden="true">·</span>
                <span><i class="bi bi-envelope me-1" aria-hidden="true"></i>support@gpcms.gov.in</span>
                <span class="citizen-footer-sep" aria-hidden="true">·</span>
                <span>v3.1</span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Citizen Module JavaScript -->
<script src="../js/citizen.js"></script>

    </div><!-- /.citizen-main-content -->
</div><!-- /.citizen-layout -->

</body>
</html>
