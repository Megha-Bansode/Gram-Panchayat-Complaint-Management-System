# Gram Sevak Module - Internal Structure & DOM Elements

This document outlines the internal HTML structure, CSS classes, and HTML IDs used across the Gram Panchayat Complaint Management System (GPCMS) Gram Sevak module. This adheres to the Component and Integration Contracts defined in the official handbook.

---

## 1. Shared Layout Structure (`includes/header.php` & `includes/footer.php`)

The main layout wraps around all module pages to provide a consistent sidebar and header.

### Wrapper & Sidebar (`header.php`)
```html
<div class="gpcms-wrapper">
    <aside class="gpcms-sidebar" id="gpcmsSidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">...</div>
            <div class="brand-text">
                <span class="main-title">GPCMS</span>
                <span class="sub-title">Gram Sevak Portal</span>
            </div>
        </div>
        <nav class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="..." class="nav-link active">...</a>
                </li>
                <!-- Other Nav Items -->
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger-custom" id="btnLogout">Logout</a>
                </li>
            </ul>
        </nav>
    </aside>
```

### Main Content & Header (`header.php`)
```html
    <div class="gpcms-main-content">
        <header class="gpcms-header">
            <div class="header-left">
                <button class="btn btn-sidebar-toggle" id="sidebarToggle" type="button">...</button>
                <div class="header-app-title">...</div>
            </div>
            <div class="header-right">
                <div class="header-clock" id="liveClock">
                    <span id="clockTime">--:--:--</span>
                </div>
                <div class="dropdown header-user-dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">...</div>
                        <div class="user-info">
                            <span class="user-name">...</span>
                            <span class="user-role badge badge-role">...</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="#" id="dropdownLogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>
```

### Footer & Modals (`footer.php`)
```html
        <footer class="gpcms-footer">...</footer>
    </div> <!-- End .gpcms-main-content -->
</div> <!-- End .gpcms-wrapper -->

<!-- Shared Modals -->
<div class="modal fade" id="notificationModal">
    <div class="list-group list-group-flush" id="notificationList">...</div>
</div>

<div class="modal fade" id="profileModal">...</div>

<div class="modal fade" id="complaintDetailsModal">
    <h5 class="modal-title" id="modalDetailHeaderTitle">...</h5>
    <div class="modal-body" id="modalDetailContent">...</div>
</div>
```

---

## 2. Dashboard Page (`gramsevak/gramsevak_dashboard.php`)

All page-specific content is wrapped inside a `<main>` container.

```html
<main class="gpcms-body container-fluid">
    <!-- Welcome Banner -->
    <div class="dashboard-welcome-card p-4 rounded-3 text-white">...</div>

    <!-- Summary Cards -->
    <div class="summary-card card-total">
        <h3 class="card-number" id="dashTotalComplaints">...</h3>
    </div>
    <div class="summary-card card-pending">
        <h3 class="card-number text-warning-custom" id="dashPendingComplaints">...</h3>
    </div>
    <div class="summary-card card-assigned">
        <h3 class="card-number text-info-custom" id="dashAssignedComplaints">...</h3>
    </div>
    <div class="summary-card card-in-progress">
        <h3 class="card-number text-primary-custom" id="dashInProgressComplaints">...</h3>
    </div>
    <div class="summary-card card-resolved">
        <h3 class="card-number text-success-custom" id="dashResolvedComplaints">...</h3>
    </div>

    <!-- Charts & Activity -->
    <div class="gpcms-card">
        <div class="chart-container">
            <canvas id="dashboardOverviewChart"></canvas>
        </div>
    </div>
    <div class="gpcms-card">
        <ul class="list-group list-group-flush activity-feed-list" id="activityFeedList">...</ul>
    </div>

    <!-- Recent Complaints Table -->
    <div class="gpcms-card">
        <table class="table table-hover align-middle mb-0 gpcms-table" id="dashboardRecentTable">
            <tbody id="dashboardTableBody">...</tbody>
        </table>
    </div>
</main>
```

---

## 3. Assigned Complaints Page (`gramsevak/assigned_complaints.php`)

```html
<main class="gpcms-body container-fluid">
    <!-- Alerts -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">...</div>

    <!-- Officer Assignment Form -->
    <div class="gpcms-card p-4">
        <form action="assigned_complaints.php" method="POST" class="row g-3">
            <select class="form-select" id="complaint_id" name="complaint_id" required>...</select>
            <select class="form-select" id="officer_id" name="officer_id" required>...</select>
            <input type="text" class="form-control" id="remarks" name="remarks">
            <button type="submit" class="btn btn-gpcms-primary">...</button>
        </form>
    </div>

    <!-- Assigned/Pending Complaints Table -->
    <div class="gpcms-card">
        <table class="table table-hover align-middle mb-0 gpcms-table" id="assignedTable">
            <thead>...</thead>
            <tbody>...</tbody>
        </table>
    </div>
</main>
```

---

## 4. Verify Complaint Page (`gramsevak/verify_complaint.php`)

```html
<main class="gpcms-body container-fluid">
    <!-- Complaint Overview -->
    <div class="gpcms-card p-4 h-100">
        <!-- Badge Classes based on status -->
        <span class="badge bg-warning">pending</span>
        <span class="badge bg-info text-white">assigned</span>
        <span class="badge bg-primary">in_progress</span>
        <span class="badge bg-success">resolved</span>
    </div>

    <!-- Verification / Resolution Form -->
    <div class="gpcms-card p-4 h-100 border-primary">
        <form action="verify_complaint.php?id=..." method="POST">
            <select class="form-select" id="status" name="status" required>...</select>
            <textarea class="form-control" id="remarks" name="remarks" rows="4" required></textarea>
            <button type="submit" class="btn btn-success w-100">...</button>
        </form>
    </div>

    <!-- Photos Verification -->
    <div class="gpcms-card p-4">
        <img src="..." alt="Before Work Photo" class="img-fluid rounded shadow-sm max-h-300">
        <img src="..." alt="After Work Photo" class="img-fluid rounded shadow-sm max-h-300">
    </div>

    <!-- Complaint History Timeline -->
    <div class="gpcms-card p-4">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">...</thead>
            <tbody>...</tbody>
        </table>
    </div>
</main>
```

---

## Handbook Component Contract Summary
All elements strictly use the canonical handbook component and theme classes:
- **Buttons**: `.btn-gpcms-primary`, `.btn-gpcms-secondary`, `.btn-success`, `.btn-outline-dark`
- **Cards**: `.gpcms-card`, `.summary-card`
- **Tables**: `.gpcms-table`
- **Sidebars & Layout**: `.gpcms-wrapper`, `.gpcms-sidebar`, `.gpcms-main-content`
- **Text & Badges**: `.text-primary-custom`, `.text-secondary-custom`, `.bg-primary-custom`
