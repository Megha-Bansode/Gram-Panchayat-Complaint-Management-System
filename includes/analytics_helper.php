<?php
/**
 * includes/analytics_helper.php
 * GPCMS Analytics Module — Shared layout & data helpers
 * Version: 3.1 | Integration-safe, prepared statements only
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';

/* ─────────────────────────────────────────────
   OUTPUT HELPERS
───────────────────────────────────────────── */
function clean(mixed $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function num(mixed $v): string
{
    return number_format((int)$v);
}

/* ─────────────────────────────────────────────
   BADGE HELPER
───────────────────────────────────────────── */
function status_badge(string $status): string
{
    $map = [
        'pending'     => 'badge-pending',
        'assigned'    => 'badge-assigned',
        'in_progress' => 'badge-in_progress',
        'resolved'    => 'badge-resolved',
    ];
    $cls = $map[$status] ?? 'badge-pending';
    $label = str_replace('_', ' ', $status);
    return '<span class="gp-badge ' . $cls . '">' . clean($label) . '</span>';
}

/* ─────────────────────────────────────────────
   FILTER BUILDER  (all filters are optional)
───────────────────────────────────────────── */
function build_filter(
    mysqli $conn,
    string $village,
    ?int $cat_id,
    string $start_date,
    string $end_date,
    string $status
): array {
    $clauses = [];
    $params  = [];
    $types   = '';

    if ($village !== '') {
        $clauses[] = 'c.village_ward = ?';
        $params[]  = $village;
        $types    .= 's';
    }
    if ($cat_id !== null) {
        $clauses[] = 'c.category_id = ?';
        $params[]  = $cat_id;
        $types    .= 'i';
    }
    if ($start_date !== '') {
        $clauses[] = 'c.submitted_at >= ?';
        $params[]  = $start_date . ' 00:00:00';
        $types    .= 's';
    }
    if ($end_date !== '') {
        $clauses[] = 'c.submitted_at <= ?';
        $params[]  = $end_date . ' 23:59:59';
        $types    .= 's';
    }
    if ($status !== '') {
        $clauses[] = 'c.status = ?';
        $params[]  = $status;
        $types    .= 's';
    }

    return [
        'sql'    => $clauses ? ' WHERE ' . implode(' AND ', $clauses) : '',
        'params' => $params,
        'types'  => $types,
    ];
}

/* ─────────────────────────────────────────────
   QUERY RUNNER — executes prepared stmt with optional bindings
───────────────────────────────────────────── */
function run_query(mysqli $conn, string $sql, array $filter, string $suffix = ''): mysqli_result
{
    $full = $sql . $filter['sql'] . ($suffix ? ' ' . $suffix : '');
    $stmt = $conn->prepare($full);
    if ($filter['params']) {
        $stmt->bind_param($filter['types'], ...$filter['params']);
    }
    $stmt->execute();
    return $stmt->get_result();
}

/* ─────────────────────────────────────────────
   SCALAR HELPERS
───────────────────────────────────────────── */
function count_with_filter(mysqli $conn, array $filter, string $extra = ''): int
{
    $sql = 'SELECT COUNT(*) FROM complaints c';
    $f   = $filter;
    if ($extra !== '') {
        $f['sql'] = $f['sql'] === '' ? ' WHERE ' . $extra : $f['sql'] . ' AND ' . $extra;
    }
    $res = run_query($conn, $sql, $f);
    $row = $res->fetch_row();
    return $row ? (int)$row[0] : 0;
}

function get_categories(mysqli $conn): array
{
    $r = $conn->query('SELECT category_id, category_name FROM categories ORDER BY category_name');
    $out = [];
    while ($row = $r->fetch_assoc()) $out[] = $row;
    return $out;
}

function get_villages(mysqli $conn): array
{
    $r = $conn->query(
        "SELECT DISTINCT village_ward FROM complaints
         WHERE village_ward IS NOT NULL AND village_ward != ''
         ORDER BY village_ward"
    );
    $out = [];
    while ($row = $r->fetch_row()) $out[] = $row[0];
    return $out;
}

/* ─────────────────────────────────────────────
   LAYOUT: SIDEBAR
───────────────────────────────────────────── */
function render_sidebar(string $active): void
{
    $items = [
        ['id' => 'dashboard',      'href' => 'analytics_dashboard.php',     'icon' => '📊', 'text' => 'Dashboard'],
        ['id' => 'category_report','href' => 'category_report.php',         'icon' => '📁', 'text' => 'Category Report'],
        ['id' => 'village_report', 'href' => 'village_report.php',          'icon' => '🏘️', 'text' => 'Village Report'],
        ['id' => 'pending_resolved','href'=> 'pending_resolved_report.php', 'icon' => '⚖️', 'text' => 'Resolution Report'],
    ];
    $mgmt = [
        ['id' => '_complaints', 'href' => '#', 'icon' => '📥', 'text' => 'Complaints', 'onclick' => "return false"],
        ['id' => '_notif',      'href' => '#', 'icon' => '🔔', 'text' => 'Notifications', 'onclick' => "return false"],
        ['id' => '_settings',   'href' => '#', 'icon' => '⚙️', 'text' => 'Settings', 'onclick' => "return false"],
    ];
    $full_name = $_SESSION['full_name'] ?? 'Admin User';
    $role      = $_SESSION['role_name'] ?? 'Official';
    $initials  = strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', $full_name), 0, 2))));
    ?>
    <aside id="gpSidebar" class="gp-sidebar">
        <!-- Brand -->
        <div class="sb-brand">
            <div class="sb-logo">GP</div>
            <div class="sb-brand-text">
                <strong>GPCMS</strong>
                <span>Analytics Suite</span>
            </div>
        </div>
        <button id="sbToggle" class="sb-toggle" aria-label="Toggle sidebar">&#x203A;</button>

        <!-- Navigation -->
        <nav class="sb-nav" aria-label="Analytics navigation">
            <div class="sb-label">Analytics</div>
            <?php foreach ($items as $item): ?>
            <div class="sb-item">
                <a href="<?= clean($item['href']) ?>"
                   class="sb-link <?= $item['id'] === $active ? 'active' : '' ?>"
                   aria-current="<?= $item['id'] === $active ? 'page' : 'false' ?>">
                    <span class="sb-icon" role="img" aria-label="<?= clean($item['text']) ?>"><?= $item['icon'] ?></span>
                    <span class="sb-text"><?= clean($item['text']) ?></span>
                </a>
            </div>
            <?php endforeach; ?>

            <div class="sb-label" style="margin-top:10px;">Management</div>
            <?php foreach ($mgmt as $item): ?>
            <div class="sb-item">
                <a href="<?= clean($item['href']) ?>"
                   class="sb-link"
                   onclick="<?= $item['onclick'] ?? '' ?>">
                    <span class="sb-icon"><?= $item['icon'] ?></span>
                    <span class="sb-text"><?= clean($item['text']) ?></span>
                </a>
            </div>
            <?php endforeach; ?>

            <div class="sb-item" style="margin-top:auto; padding-top:20px;">
                <a href="../logout.php" class="sb-link" style="color:#ff8a8a;">
                    <span class="sb-icon">🚪</span>
                    <span class="sb-text">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="sb-foot">
            <div style="font-weight:600; color:var(--gp-accent);"><?= clean($full_name) ?></div>
            <div><?= clean($role) ?></div>
            <div style="margin-top:6px; opacity:.6;">GPCMS v3.1 · Analytics</div>
        </div>
    </aside>
    <?php
}

/* ─────────────────────────────────────────────
   LAYOUT: HEADER
───────────────────────────────────────────── */
function render_header(string $page_title, string $breadcrumb_parent = 'Analytics'): void
{
    $full_name = $_SESSION['full_name'] ?? 'Official User';
    $role      = $_SESSION['role_name'] ?? 'Panchayat Admin';
    $initials  = strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', $full_name), 0, 2))));
    $today     = date('l, d M Y');
    ?>
    <header class="gp-header" role="banner">
        <div class="gh-left">
            <nav class="gh-breadcrumb" aria-label="breadcrumb">
                <span>GPCMS</span>
                <span class="sep">›</span>
                <span><?= clean($breadcrumb_parent) ?></span>
                <span class="sep">›</span>
                <span class="active"><?= clean($page_title) ?></span>
            </nav>
            <h1 class="gh-title"><?= clean($page_title) ?></h1>
        </div>
        <div class="gh-right">
            <div class="gh-search">
                <span class="gh-search-icon">🔍</span>
                <input type="search" id="ghSearch" placeholder="Quick search…" aria-label="Quick search">
            </div>
            <div class="gh-date" aria-label="Today's date">📅 <?= $today ?></div>
            <div class="gh-notif" role="button" aria-label="Notifications" tabindex="0">
                🔔 <span class="gh-badge" aria-label="New notifications"></span>
            </div>
            <div class="gh-profile">
                <div class="gh-avatar" aria-hidden="true"><?= clean($initials) ?></div>
                <div>
                    <div class="gh-user-name"><?= clean($full_name) ?></div>
                    <div class="gh-user-role"><?= clean($role) ?></div>
                </div>
            </div>
        </div>
    </header>
    <?php
}

/* ─────────────────────────────────────────────
   LAYOUT: FOOTER
───────────────────────────────────────────── */
function render_footer(): void
{
    ?>
    <footer class="gp-footer" role="contentinfo">
        <span>Gram Panchayat Complaint Management System (GPCMS) — Analytics Module</span>
        <span>Version 3.1.2 &nbsp;|&nbsp; © 2026 GPCMS &nbsp;|&nbsp; All rights reserved</span>
    </footer>
    <?php
}

/* ─────────────────────────────────────────────
   LAYOUT: FILTER SECTION
───────────────────────────────────────────── */
function render_filters(
    string $action,
    array  $villages,
    array  $categories,
    string $fv  = '',
    ?int   $fci = null,
    string $fs  = '',
    string $fsd = '',
    string $fed = ''
): void {
    $statuses = ['pending', 'assigned', 'in_progress', 'resolved'];
    ?>
    <section class="gp-filter anim-up" aria-label="Report filters">
        <form method="get" action="<?= clean($action) ?>">
            <div class="row g-3 align-items-end">
                <!-- Village -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="f_village">Village / Ward</label>
                    <select name="village" id="f_village">
                        <option value="">All Villages</option>
                        <?php foreach ($villages as $v): ?>
                            <option value="<?= clean($v) ?>" <?= $fv === $v ? 'selected' : '' ?>>
                                <?= clean($v) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Category -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="f_category">Category</label>
                    <select name="category_id" id="f_category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['category_id'] ?>" <?= $fci === (int)$c['category_id'] ? 'selected' : '' ?>>
                                <?= clean($c['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Status -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="f_status">Status</label>
                    <select name="status" id="f_status">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $fs === $s ? 'selected' : '' ?>>
                                <?= ucwords(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Start Date -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="f_start">From Date</label>
                    <input type="date" name="start_date" id="f_start" value="<?= clean($fsd) ?>">
                </div>
                <!-- End Date -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="f_end">To Date</label>
                    <input type="date" name="end_date" id="f_end" value="<?= clean($fed) ?>">
                </div>
                <!-- Actions -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label>&nbsp;</label>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn-gp btn-gp-primary" style="flex:1;">
                            🔍 Search
                        </button>
                        <a href="<?= clean($action) ?>" class="btn-gp btn-gp-outline">↺</a>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <?php
}

/* ─────────────────────────────────────────────
   LAYOUT: OPEN PAGE SHELL (before <body> content)
───────────────────────────────────────────── */
function page_head(string $title, string $active): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="GPCMS Analytics — <?= clean($title) ?>">
<title><?= clean($title) ?> | GPCMS Analytics</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- GPCMS Analytics Design System -->
<link rel="stylesheet" href="../css/analytics.css">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>
<!-- Analytics JS -->
<script src="../js/analytics.js" defer></script>
</head>
<body>
<div class="gp-shell">
<?php render_sidebar($active); ?>
<div class="gp-main">
<?php render_header($title); ?>
<main class="gp-content" id="main-content">
    <?php
}

function page_foot(): void
{
    ?>
</main>
<?php render_footer(); ?>
</div><!-- /.gp-main -->
</div><!-- /.gp-shell -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
