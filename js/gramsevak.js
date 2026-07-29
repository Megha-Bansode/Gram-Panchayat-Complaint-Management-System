/**
 * Gram Panchayat Complaint Management System (GPCMS) v3.1
 * Gram Sevak Module Core JavaScript Controller
 * 
 * Features:
 * - Responsive Sidebar Toggle
 * - Database Contract Compliant Dummy Dataset
 * - Dynamic Filtering & Searching
 * - Chart.js Initializations (Pie, Bar, Line)
 * - Lightbox Image Modal Handlers
 * - Status History Timeline Rendering
 * - Standalone Print Engine (printElement)
 * - CSV & PDF Data Export Engine (downloadCSV, exportComplaintsCSV, etc.)
 * - Form Validation & Backend Placeholder Handlers
 */

// ==========================================================================
// 1. DATABASE CONTRACT DUMMY DATASET (Integration Safe)
// ==========================================================================

const gpcmsDataset = window.gpcmsDataset || {
    // Database table: users
    users: [
        { user_id: 101, full_name: "Rajesh Patil (Gram Sevak)", mobile_number: "9876543210", role_id: 2, role_name: "Gram Sevak" },
        { user_id: 201, full_name: "Ramesh Shinde", mobile_number: "9823012345", role_id: 3, role_name: "Field Officer" },
        { user_id: 202, full_name: "Suresh Patil", mobile_number: "9823054321", role_id: 3, role_name: "Field Officer" },
        { user_id: 203, full_name: "Anil Kadam", mobile_number: "9823098765", role_id: 3, role_name: "Field Officer" }
    ],

    // Database table: categories
    categories: [
        { category_id: 1, category_name: "Water Supply", description: "Clean drinking water, pipeline leaks, pump repairs", count: 42, status: "Active" },
        { category_id: 2, category_name: "Sanitation & Waste", description: "Garbage collection, street sweeping, cleanliness", count: 35, status: "Active" },
        { category_id: 3, category_name: "Road Repair", description: "Pothole filling, asphalt repair, road obstruction", count: 28, status: "Active" },
        { category_id: 4, category_name: "Street Lighting", description: "Fused streetlights, pole repairs, wiring issues", count: 22, status: "Active" },
        { category_id: 5, category_name: "Drainage System", description: "Blocked gutters, sewage overflow, storm drain repair", count: 14, status: "Active" },
        { category_id: 6, category_name: "Public Health", description: "Mosquito fogging, sanitization drives, medical aid", count: 7, status: "Active" }
    ],

    // Database table: complaints
    // Allowed Status Values ONLY: 'pending', 'assigned', 'in_progress', 'resolved'
    complaints: [
        {
            complaint_id: "CMP-2024-001",
            category_id: 1,
            category_name: "Water Supply",
            assigned_to: 201,
            officer_name: "Ramesh Shinde",
            status: "pending",
            complaint_title: "Water Pipeline Burst near Ward 3 Community Hall",
            complaint_description: "Main supply pipeline damaged resulting in drinking water wastage and low pressure in Ward 3.",
            complaint_image: "https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?w=500&auto=format&fit=crop&q=60",
            complainant_name: "Sunil Deshmukh",
            mobile_number: "9890112233",
            village_ward: "Shivaji Nagar",
            submission_date: "2026-07-25 09:30 AM",
            before_photo: "https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?w=500&auto=format&fit=crop&q=60",
            after_photo: null,
            officer_remarks: null
        },
        {
            complaint_id: "CMP-2024-002",
            category_id: 2,
            category_name: "Sanitation & Waste",
            assigned_to: 202,
            officer_name: "Suresh Patil",
            status: "in_progress",
            complaint_title: "Uncollected Garbage Dumping near ZP Primary School",
            complaint_description: "Large dump of solid waste accumulated causing unhygienic conditions for school children.",
            complaint_image: "https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=500&auto=format&fit=crop&q=60",
            complainant_name: "Priya Pawar",
            mobile_number: "9890223344",
            village_ward: "Rampur Ward 1",
            submission_date: "2026-07-24 11:15 AM",
            before_photo: "https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=500&auto=format&fit=crop&q=60",
            after_photo: "https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=500&auto=format&fit=crop&q=60",
            officer_remarks: "Waste clearance crew dispatched. 70% cleared."
        },
        {
            complaint_id: "CMP-2024-003",
            category_id: 3,
            category_name: "Road Repair",
            assigned_to: 201,
            officer_name: "Ramesh Shinde",
            status: "assigned",
            complaint_title: "Hazardous Potholes on Main Temple Approach Road",
            complaint_description: "Deep potholes created post monsoon leading to two-wheeler accidents.",
            complaint_image: "https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=500&auto=format&fit=crop&q=60",
            complainant_name: "Ganesh Jadhav",
            mobile_number: "9890334455",
            village_ward: "Ganesh Wadi",
            submission_date: "2026-07-23 02:45 PM",
            before_photo: "https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=500&auto=format&fit=crop&q=60",
            after_photo: null,
            officer_remarks: "Inspection scheduled for gravel filling."
        },
        {
            complaint_id: "CMP-2024-004",
            category_id: 4,
            category_name: "Street Lighting",
            assigned_to: 203,
            officer_name: "Anil Kadam",
            status: "resolved",
            complaint_title: "Fused LED Street Lights on Station Road",
            complaint_description: "4 consecutive poles dark creating safety issues at night.",
            complaint_image: "https://images.unsplash.com/photo-1509114397022-ed747cca3f65?w=500&auto=format&fit=crop&q=60",
            complainant_name: "Meena More",
            mobile_number: "9890445566",
            village_ward: "Hanuman Nagar",
            submission_date: "2026-07-20 06:00 PM",
            before_photo: "https://images.unsplash.com/photo-1509114397022-ed747cca3f65?w=500&auto=format&fit=crop&q=60",
            after_photo: "https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=500&auto=format&fit=crop&q=60",
            officer_remarks: "Replaced 4 defective LED bulbs with 50W units."
        },
        {
            complaint_id: "CMP-2024-005",
            category_id: 5,
            category_name: "Drainage System",
            assigned_to: null,
            officer_name: "Unassigned",
            status: "pending",
            complaint_title: "Blocked Open Drain Overflowing on Market Road",
            complaint_description: "Plastic choke in main storm drain flooding shops during rain.",
            complaint_image: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&auto=format&fit=crop&q=60",
            complainant_name: "Vikas Bhosale",
            mobile_number: "9890556677",
            village_ward: "Rampur Ward 2",
            submission_date: "2026-07-26 10:10 AM",
            before_photo: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&auto=format&fit=crop&q=60",
            after_photo: null,
            officer_remarks: null
        }
    ],

    // Database table: complaint_history
    history: [
        { history_id: 1, complaint_id: "CMP-2024-001", status: "pending", remarks: "Complaint submitted by citizen Sunil Deshmukh", timestamp: "2026-07-25 09:30 AM" },
        { history_id: 2, complaint_id: "CMP-2024-002", status: "pending", remarks: "Complaint registered", timestamp: "2026-07-24 11:15 AM" },
        { history_id: 3, complaint_id: "CMP-2024-002", status: "assigned", remarks: "Assigned to Field Officer Suresh Patil", timestamp: "2026-07-24 01:00 PM" },
        { history_id: 4, complaint_id: "CMP-2024-002", status: "in_progress", remarks: "Cleaning tractor dispatched to site", timestamp: "2026-07-25 08:30 AM" },
        { history_id: 5, complaint_id: "CMP-2024-004", status: "resolved", remarks: "Work verified & approved by Gram Sevak", timestamp: "2026-07-22 04:00 PM" }
    ]
};

// State Variables for Pagination & Filtering
let currentPage = 1;
const rowsPerPage = 10;
let filteredComplaintsList = [...gpcmsDataset.complaints];
let currentSelectedComplaintId = null;

// ==========================================================================
// 2. INITIALIZATION ON DOM READY
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
    initSidebarToggle();
    initLiveClock();
    initGlobalSearch();
    populateNotificationsList();
    
    // Page specific initializations based on DOM elements
    if (document.getElementById('dashboardTableBody')) {
        renderDashboardPage();
    }
    if (document.getElementById('viewComplaintsBody')) {
        renderViewComplaintsPage();
    }
    if (document.getElementById('assignComplaintsTableBody')) {
        renderAssignComplaintsPage();
    }
    if (document.getElementById('selectComplaintForUpdate')) {
        renderUpdateStatusPage();
    }
    if (document.getElementById('verificationCardsContainer')) {
        renderVerifyWorkPage();
    }
    if (document.getElementById('categoryTableBody')) {
        renderCategoryManagementPage();
    }
    if (document.getElementById('reportsMainChart')) {
        renderReportsPage();
    }
    if (document.getElementById('categoryPieChart')) {
        renderStatisticsPage();
    }
    if (document.getElementById('monthlyReportTableBody')) {
        renderMonthlyReportsPage();
    }

    // Attach global click event for logout confirmation
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to log out of Gram Sevak Portal?')) {
                window.location.href = '../includes/logout.php';
            }
        });
    }
    const dropdownLogout = document.getElementById('dropdownLogout');
    if (dropdownLogout) {
        dropdownLogout.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = '../includes/logout.php';
            }
        });
    }
});

// ==========================================================================
// 3. UI LAYOUT HANDLERS (Sidebar, Clock, Modals, Print Engine, Export Engine)
// ==========================================================================

function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('gpcmsSidebar');
    const mainContent = document.querySelector('.gpcms-main-content');

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            sidebar.classList.toggle('mobile-open');
            mainContent.classList.toggle('expanded');
        });
    }
}

function initLiveClock() {
    const clockTime = document.getElementById('clockTime');
    if (clockTime) {
        const updateClock = () => {
            const now = new Date();
            clockTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) + " | " + now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        };
        updateClock();
        setInterval(updateClock, 1000);
    }
}

function initGlobalSearch() {
    const globalSearchInput = document.getElementById('globalSearchInput');
    if (globalSearchInput) {
        globalSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = e.target.value.trim();
                if (query.length > 0) {
                    window.location.href = `view_complaints.php?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }
}

// Utility Status Badge HTML Generator
function getStatusBadgeHTML(status) {
    let iconClass = 'bi-circle';
    switch (status) {
        case 'pending': iconClass = 'bi-hourglass-split'; break;
        case 'assigned': iconClass = 'bi-person-check'; break;
        case 'in_progress': iconClass = 'bi-gear-wide-connected'; break;
        case 'resolved': iconClass = 'bi-check-circle-fill'; break;
    }
    return `<span class="status-badge status-${status}"><i class="bi ${iconClass}"></i> ${status}</span>`;
}

// Universal CSV Download Engine
function downloadCSV(filename, headers, rowsData) {
    let csvContent = "\uFEFF"; // UTF-8 BOM for proper Excel rendering
    
    // Header Row
    csvContent += headers.map(h => `"${String(h).replace(/"/g, '""')}"`).join(",") + "\r\n";
    
    // Data Rows
    rowsData.forEach(row => {
        const line = row.map(val => {
            const strVal = (val === null || val === undefined) ? "" : String(val);
            return `"${strVal.replace(/"/g, '""')}"`;
        }).join(",");
        csvContent += line + "\r\n";
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
}

// View Complaints Export CSV Handler
function exportComplaintsCSV() {
    const listToExport = (filteredComplaintsList && filteredComplaintsList.length > 0) 
        ? filteredComplaintsList 
        : gpcmsDataset.complaints;

    const headers = [
        "Complaint ID",
        "Complainant Name",
        "Mobile Number",
        "Village / Ward",
        "Category",
        "Complaint Title",
        "Description",
        "Status",
        "Assigned Officer",
        "Submission Date"
    ];

    const rows = listToExport.map(c => [
        c.complaint_id,
        c.complainant_name,
        c.mobile_number,
        c.village_ward,
        c.category_name,
        c.complaint_title,
        c.complaint_description,
        c.status,
        c.officer_name,
        c.submission_date
    ]);

    const dateStr = new Date().toISOString().slice(0, 10);
    downloadCSV(`gpcms_complaints_export_${dateStr}.csv`, headers, rows);
}

// Reports Page Export Handlers
function exportReportExcel() {
    const table = document.getElementById('reportDataTable');
    if (!table) return;

    const headers = [];
    const rows = [];
    
    table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText.trim()));
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => row.push(td.innerText.trim()));
        if (row.length > 0) rows.push(row);
    });

    const dateStr = new Date().toISOString().slice(0, 10);
    downloadCSV(`gpcms_analytical_report_${dateStr}.csv`, headers, rows);
}

function exportReportPDF() {
    printElement('reportDataTable', 'Gram Panchayat Analytical Report Statement');
}

function exportMonthlyReportExcel() {
    const table = document.getElementById('monthlyReportTable');
    if (!table) return;

    const headers = [];
    const rows = [];
    
    table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText.trim()));
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => row.push(td.innerText.trim()));
        if (row.length > 0) rows.push(row);
    });

    const m = document.getElementById('mthFilterMonth') ? document.getElementById('mthFilterMonth').value : 'July';
    const y = document.getElementById('mthFilterYear') ? document.getElementById('mthFilterYear').value : '2026';
    
    downloadCSV(`gpcms_monthly_statement_${m}_${y}.csv`, headers, rows);
}

function exportMonthlyReportPDF() {
    printElement('monthlyReportTable', 'Gram Panchayat Monthly Complaint Statement');
}

// Dedicated Print Engine Function (Prints any target table/element cleanly)
function printElement(targetId, title) {
    const targetEl = document.getElementById(targetId) || document.querySelector(targetId);
    if (!targetEl) {
        window.print();
        return;
    }

    const printWin = window.open('', '_blank', 'width=950,height=750');
    if (!printWin) {
        window.print();
        return;
    }

    const htmlContent = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>${title || 'Gram Panchayat Official Document'}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Poppins', sans-serif; padding: 25px; color: #3a3224; background: #ffffff; }
                .print-header { text-align: center; border-bottom: 2px solid #8A724C; padding-bottom: 12px; margin-bottom: 20px; }
                .print-header h3 { color: #8A724C; font-weight: 700; margin: 0; font-size: 1.4rem; }
                .print-header p { margin: 0; color: #5a5246; font-size: 0.85rem; }
                .status-badge { padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; text-transform: lowercase; }
                .status-pending { background: #fff8e6; color: #b45309; border: 1px solid #b45309; }
                .status-assigned { background: #e0f2fe; color: #0369a1; border: 1px solid #0369a1; }
                .status-in_progress { background: #fef3c7; color: #92400e; border: 1px solid #92400e; }
                .status-resolved { background: #dcfce7; color: #15803d; border: 1px solid #15803d; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th { background-color: #EDE2CC !important; color: #3a3224 !important; font-weight: 600; text-transform: uppercase; font-size: 0.78rem; padding: 10px; border: 1px solid #ddd; }
                td { padding: 10px; border: 1px solid #ddd; font-size: 0.85rem; }
                img { max-height: 80px; border-radius: 6px; }
                .btn, .no-print, action { display: none !important; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h3>Shivaji Nagar Gram Panchayat</h3>
                <p>Gram Panchayat Complaint Management System (GPCMS v3.1)</p>
                <small class="text-muted">Document: <strong>${title || 'Official Report'}</strong> | Date: ${new Date().toLocaleString()}</small>
            </div>
            <div>
                ${targetEl.tagName === 'TABLE' ? targetEl.outerHTML : targetEl.innerHTML}
            </div>
        </body>
        </html>
    `;

    printWin.document.write(htmlContent);
    printWin.document.close();
    printWin.focus();
    setTimeout(() => {
        printWin.print();
        printWin.close();
    }, 400);
}

// ==========================================================================
// 4. PAGE RENDERING LOGIC
// ==========================================================================

// --- PAGE 1: Dashboard Page ---
function renderDashboardPage() {
    renderRecentDashboardTable();
    populateActivityFeed();
    initDashboardOverviewChart();
}

function renderRecentDashboardTable() {
    const tbody = document.getElementById('dashboardTableBody');
    if (!tbody) return;

    tbody.innerHTML = gpcmsDataset.complaints.slice(0, 5).map(c => `
        <tr>
            <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
            <td>${c.complainant_name}</td>
            <td>${c.village_ward}</td>
            <td><span class="badge bg-surface-custom text-dark">${c.category_name}</span></td>
            <td><span class="text-truncate d-inline-block" style="max-width: 180px;">${c.complaint_title}</span></td>
            <td>${getStatusBadgeHTML(c.status)}</td>
            <td>${c.officer_name}</td>
            <td><small class="text-muted">${c.submission_date.split(' ')[0]}</small></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="openComplaintModal('${c.complaint_id}')">
                    <i class="bi bi-eye me-1"></i> View
                </button>
            </td>
        </tr>
    `).join('');
}

function populateActivityFeed() {
    const feed = document.getElementById('activityFeedList');
    if (!feed) return;

    if (!gpcmsDataset.history || gpcmsDataset.history.length === 0) {
        feed.innerHTML = '<li class="list-group-item p-3 text-center text-muted">No recent activity</li>';
        return;
    }

    feed.innerHTML = gpcmsDataset.history.slice(0, 5).map(h => {
        let statusColorClass = 'text-dark';
        if (h.status === 'assigned') statusColorClass = 'text-info-custom';
        else if (h.status === 'in_progress') statusColorClass = 'text-primary';
        else if (h.status === 'resolved') statusColorClass = 'text-success-custom';

        const statusText = h.status.charAt(0).toUpperCase() + h.status.slice(1);

        return `
            <li class="list-group-item p-3 border-0 border-bottom">
                <div class="d-flex w-100 justify-content-between">
                    <span class="fw-semibold ${statusColorClass}">Complaint #${h.complaint_id} ${statusText}</span>
                    <small class="text-muted">${h.timestamp.split(',')[0]}</small>
                </div>
                <p class="mb-0 small text-muted">${h.remarks || 'Status updated to ' + h.status}</p>
            </li>
        `;
    }).join('');
}

function initDashboardOverviewChart() {
    const ctx = document.getElementById('dashboardOverviewChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Water Supply', 'Sanitation', 'Road Repair', 'Street Lighting', 'Drainage', 'Health'],
            datasets: [{
                label: 'Complaints Registered',
                data: [42, 35, 28, 22, 14, 7],
                backgroundColor: '#8A724C',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

function refreshActivityFeed() {
    populateActivityFeed();
    alert('Activity feed refreshed.');
}

// --- PAGE 2: View Complaints Page ---
function renderViewComplaintsPage() {
    const searchInput = document.getElementById('filterSearch');
    const categorySelect = document.getElementById('filterCategory');
    const villageSelect = document.getElementById('filterVillage');
    const statusSelect = document.getElementById('filterStatus');
    const dateInput = document.getElementById('filterDate');

    const applyFilters = () => {
        const query = searchInput ? searchInput.value.toLowerCase() : '';
        const cat = categorySelect ? categorySelect.value : '';
        const vil = villageSelect ? villageSelect.value : '';
        const stat = statusSelect ? statusSelect.value : '';
        const dt = dateInput ? dateInput.value : '';

        filteredComplaintsList = gpcmsDataset.complaints.filter(c => {
            const matchesQuery = !query || c.complaint_id.toLowerCase().includes(query) || c.complaint_title.toLowerCase().includes(query) || c.complainant_name.toLowerCase().includes(query);
            const matchesCat = !cat || c.category_name === cat;
            const matchesVil = !vil || c.village_ward === vil;
            const matchesStat = !stat || c.status === stat;
            const matchesDt = !dt || c.submission_date.includes(dt);

            return matchesQuery && matchesCat && matchesVil && matchesStat && matchesDt;
        });

        currentPage = 1;
        renderViewComplaintsTable();
    };

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (categorySelect) categorySelect.addEventListener('change', applyFilters);
    if (villageSelect) villageSelect.addEventListener('change', applyFilters);
    if (statusSelect) statusSelect.addEventListener('change', applyFilters);
    if (dateInput) dateInput.addEventListener('change', applyFilters);

    // Initial table load
    renderViewComplaintsTable();
}

function renderViewComplaintsTable() {
    const tbody = document.getElementById('viewComplaintsBody');
    const countBadge = document.getElementById('complaintRecordCount');
    if (!tbody) return;

    if (countBadge) countBadge.textContent = `Showing ${filteredComplaintsList.length} entries`;

    if (filteredComplaintsList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-muted">No matching complaints found.</td></tr>`;
        return;
    }

    tbody.innerHTML = filteredComplaintsList.map(c => `
        <tr>
            <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
            <td>
                <div><strong class="d-block text-dark">${c.complainant_name}</strong></div>
                <small class="text-muted"><i class="bi bi-telephone me-1"></i>${c.mobile_number}</small>
            </td>
            <td>${c.village_ward}</td>
            <td><span class="badge bg-surface-custom text-dark">${c.category_name}</span></td>
            <td><span class="fw-semibold text-dark">${c.complaint_title}</span></td>
            <td>
                <img src="${c.complaint_image}" class="complaint-img-thumb" alt="Complaint Photo" onclick="openLightbox('${c.complaint_image}', '${c.complaint_title}')">
            </td>
            <td>${getStatusBadgeHTML(c.status)}</td>
            <td>${c.officer_name}</td>
            <td><small class="text-muted">${c.submission_date}</small></td>
            <td class="text-end">
                <button class="btn btn-sm btn-gpcms-primary" onclick="openComplaintModal('${c.complaint_id}')">
                    <i class="bi bi-info-circle me-1"></i> Details
                </button>
            </td>
        </tr>
    `).join('');
}

function resetComplaintsFilter() {
    if (document.getElementById('filterSearch')) document.getElementById('filterSearch').value = '';
    if (document.getElementById('filterCategory')) document.getElementById('filterCategory').value = '';
    if (document.getElementById('filterVillage')) document.getElementById('filterVillage').value = '';
    if (document.getElementById('filterStatus')) document.getElementById('filterStatus').value = '';
    if (document.getElementById('filterDate')) document.getElementById('filterDate').value = '';
    filteredComplaintsList = [...gpcmsDataset.complaints];
    renderViewComplaintsTable();
}

// Complaint Details Modal Handler
function openComplaintModal(complaintId) {
    const complaint = gpcmsDataset.complaints.find(c => c.complaint_id === complaintId);
    if (!complaint) {
        alert('Complaint details not found.');
        return;
    }

    const modalBody = document.getElementById('modalDetailContent') || document.getElementById('modalComplaintBody');
    const modalTitle = document.getElementById('modalDetailHeaderTitle') || document.getElementById('modalComplaintTitle');

    if (modalTitle) {
        modalTitle.innerHTML = `<i class="bi bi-file-earmark-text me-2"></i>Details for ${complaint.complaint_id}`;
    }

    const historyItems = gpcmsDataset.history
        .filter(h => h.complaint_id === complaintId)
        .map(h => `
            <li class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Status: ${getStatusBadgeHTML(h.status)}</strong>
                        <small class="text-muted">${h.timestamp}</small>
                    </div>
                    <p class="mb-0 small mt-1">${h.remarks}</p>
                </div>
            </li>
        `).join('') || `<p class="text-muted small">No historical updates recorded.</p>`;

    if (modalBody) {
        modalBody.innerHTML = `
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="text-muted small d-block">Complainant Name</label>
                    <strong class="text-dark">${complaint.complainant_name} (${complaint.mobile_number})</strong>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Village / Ward</label>
                    <strong class="text-dark">${complaint.village_ward}</strong>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Complaint Category</label>
                    <span class="badge bg-secondary-custom">${complaint.category_name}</span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Current Status</label>
                    ${getStatusBadgeHTML(complaint.status)}
                </div>
            </div>

            <div class="mb-3">
                <label class="text-muted small d-block">Complaint Title</label>
                <h6 class="fw-bold">${complaint.complaint_title}</h6>
            </div>

            <div class="mb-3">
                <label class="text-muted small d-block">Description</label>
                <p class="p-3 bg-light rounded text-dark">${complaint.complaint_description}</p>
            </div>

            <div class="mb-3">
                <label class="text-muted small d-block mb-1">Uploaded Evidence Photo</label>
                <img src="${complaint.complaint_image}" class="img-fluid rounded border" style="max-height: 250px;" alt="Evidence Image">
            </div>

            <hr>

            <h6 class="fw-bold text-secondary-custom mb-3"><i class="bi bi-clock-history me-1"></i>Complaint Timeline History</h6>
            <ul class="timeline-list">
                ${historyItems}
            </ul>
        `;
    }

    const modalEl = document.getElementById('complaintDetailsModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openLightbox(imgSrc, captionText) {
    const imgEl = document.getElementById('lightboxImage');
    const capEl = document.getElementById('lightboxCaption');
    if (imgEl) imgEl.src = imgSrc;
    if (capEl) capEl.textContent = captionText || 'Photo Preview';

    const modal = new bootstrap.Modal(document.getElementById('imageLightboxModal'));
    modal.show();
}

// --- PAGE 3: Assign Complaints Page ---
function renderAssignComplaintsPage() {
    renderUnassignedTable();
    renderAssignmentHistoryTable();
}

function renderUnassignedTable() {
    const tbody = document.getElementById('assignComplaintsTableBody');
    if (!tbody) return;

    const pendingComplaints = gpcmsDataset.complaints.filter(c => c.status === 'pending');

    if (pendingComplaints.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">No pending complaints requiring officer assignment.</td></tr>`;
        return;
    }

    tbody.innerHTML = pendingComplaints.map(c => `
        <tr>
            <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
            <td>${c.complainant_name}</td>
            <td>${c.village_ward}</td>
            <td><span class="badge bg-surface-custom text-dark">${c.category_name}</span></td>
            <td>${c.complaint_title}</td>
            <td><small class="text-muted">${c.submission_date}</small></td>
            <td>${getStatusBadgeHTML(c.status)}</td>
            <td class="text-center">
                <div class="input-group input-group-sm" style="max-width: 280px; margin: 0 auto;">
                    <select class="form-select form-select-sm" id="selectOfficer_${c.complaint_id}">
                        <option value="">-- Select Officer --</option>
                        <option value="201">Ramesh Shinde (Field Officer)</option>
                        <option value="202">Suresh Patil (Field Officer)</option>
                        <option value="203">Anil Kadam (Field Officer)</option>
                    </select>
                    <button class="btn btn-gpcms-primary btn-sm" onclick="promptAssignModal('${c.complaint_id}')">
                        Assign
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function promptAssignModal(complaintId) {
    const selectEl = document.getElementById(`selectOfficer_${complaintId}`);
    if (!selectEl || !selectEl.value) {
        alert('Please select a Field Officer from the dropdown first.');
        return;
    }

    currentSelectedComplaintId = complaintId;
    const officerId = selectEl.value;
    const officerObj = gpcmsDataset.users.find(u => u.user_id == officerId);

    document.getElementById('modalAssignComplaintId').textContent = complaintId;
    document.getElementById('modalAssignOfficerName').textContent = officerObj ? officerObj.full_name : 'Selected Officer';

    const btnConfirm = document.getElementById('btnConfirmAssignment');
    if (btnConfirm) {
        btnConfirm.onclick = () => executeOfficerAssignment(complaintId, officerObj);
    }

    const modal = new bootstrap.Modal(document.getElementById('assignConfirmModal'));
    modal.show();
}

function executeOfficerAssignment(complaintId, officerObj) {
    const complaint = gpcmsDataset.complaints.find(c => c.complaint_id === complaintId);
    if (complaint && officerObj) {
        // Enforce DB Contract Status: pending -> assigned
        complaint.status = 'assigned';
        complaint.assigned_to = officerObj.user_id;
        complaint.officer_name = officerObj.full_name;

        gpcmsDataset.history.push({
            history_id: Date.now(),
            complaint_id: complaintId,
            status: 'assigned',
            remarks: `Assigned to ${officerObj.full_name} by Gram Sevak`,
            timestamp: new Date().toLocaleString()
        });

        alert(`Success! Complaint ${complaintId} assigned to ${officerObj.full_name}. Status updated to 'assigned'.`);
        
        // Hide modal
        const modalEl = document.getElementById('assignConfirmModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        // Refresh tables
        renderAssignComplaintsPage();
    }
}

function renderAssignmentHistoryTable() {
    const tbody = document.getElementById('assignmentHistoryBody');
    if (!tbody) return;

    const assignedHistory = gpcmsDataset.complaints.filter(c => c.assigned_to !== null);

    tbody.innerHTML = assignedHistory.map(c => `
        <tr>
            <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
            <td>${c.complaint_title}</td>
            <td>${c.village_ward}</td>
            <td><span class="fw-semibold text-dark">${c.officer_name}</span></td>
            <td>Rajesh Patil (Gram Sevak)</td>
            <td>${getStatusBadgeHTML(c.status)}</td>
            <td><small class="text-muted">${c.submission_date}</small></td>
        </tr>
    `).join('');
}

// --- PAGE 4: Update Complaint Status Page ---
function renderUpdateStatusPage() {
    const selectCmp = document.getElementById('selectComplaintForUpdate');
    const tableBody = document.getElementById('statusTrackingTableBody');

    if (selectCmp) {
        selectCmp.innerHTML = `<option value="">-- Choose Complaint --</option>` + 
            gpcmsDataset.complaints.map(c => `<option value="${c.complaint_id}">${c.complaint_id} - ${c.complaint_title} (${c.status})</option>`).join('');
    }

    if (tableBody) {
        tableBody.innerHTML = gpcmsDataset.complaints.map(c => `
            <tr>
                <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
                <td>${c.complaint_title}</td>
                <td>${c.village_ward}</td>
                <td><span class="badge bg-surface-custom text-dark">${c.category_name}</span></td>
                <td>${getStatusBadgeHTML(c.status)}</td>
                <td>${c.officer_name}</td>
                <td><small class="text-muted">${c.submission_date}</small></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-gpcms-secondary" onclick="quickSelectForUpdate('${c.complaint_id}')">
                        <i class="bi bi-pencil me-1"></i> Change Status
                    </button>
                </td>
            </tr>
        `).join('');
    }
}

function onComplaintSelectChange(complaintId) {
    if (!complaintId) {
        document.getElementById('selectedComplaintCard').classList.add('d-none');
        document.getElementById('timelinePlaceholder').classList.remove('d-none');
        document.getElementById('timelineContainer').classList.add('d-none');
        return;
    }

    const complaint = gpcmsDataset.complaints.find(c => c.complaint_id === complaintId);
    if (!complaint) return;

    document.getElementById('selectedComplaintCard').classList.remove('d-none');
    document.getElementById('scId').textContent = complaint.complaint_id;
    document.getElementById('scTitle').textContent = complaint.complaint_title;
    document.getElementById('scDescription').textContent = complaint.complaint_description;
    document.getElementById('scComplainant').textContent = complaint.complainant_name;
    document.getElementById('scOfficer').textContent = complaint.officer_name;
    document.getElementById('scCurrentStatusBadge').innerHTML = getStatusBadgeHTML(complaint.status);

    // Set dropdown to current status
    document.getElementById('newStatusSelect').value = complaint.status;

    // Render Timeline
    renderStatusTimeline(complaintId);
}

function renderStatusTimeline(complaintId) {
    document.getElementById('timelinePlaceholder').classList.add('d-none');
    document.getElementById('timelineContainer').classList.remove('d-none');
    document.getElementById('timelineComplaintId').textContent = complaintId;

    const list = document.getElementById('timelineList');
    const items = gpcmsDataset.history.filter(h => h.complaint_id === complaintId);

    if (items.length === 0) {
        list.innerHTML = `<li class="text-muted small">No audit log history available.</li>`;
        return;
    }

    list.innerHTML = items.map(h => `
        <li class="timeline-item">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <div class="d-flex justify-content-between">
                    <strong>Status: ${getStatusBadgeHTML(h.status)}</strong>
                    <small class="text-muted">${h.timestamp}</small>
                </div>
                <p class="mb-0 small mt-1">${h.remarks}</p>
            </div>
        </li>
    `).join('');
}

function quickSelectForUpdate(complaintId) {
    const select = document.getElementById('selectComplaintForUpdate');
    if (select) {
        select.value = complaintId;
        onComplaintSelectChange(complaintId);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function submitStatusUpdate() {
    const complaintId = document.getElementById('selectComplaintForUpdate').value;
    const newStatus = document.getElementById('newStatusSelect').value;
    const remarks = document.getElementById('statusRemarks').value.trim();

    if (!complaintId || !newStatus || !remarks) {
        alert('Please complete all required fields.');
        return;
    }

    document.getElementById('modalStatusCmpId').textContent = complaintId;
    document.getElementById('modalTargetStatus').textContent = newStatus;

    const btnExec = document.getElementById('btnExecuteStatusUpdate');
    if (btnExec) {
        btnExec.onclick = () => {
            const form = document.getElementById('formUpdateStatus');
            if (form) {
                form.submit();
            }
        };
    }

    const modal = new bootstrap.Modal(document.getElementById('statusConfirmModal'));
    modal.show();
}

function resetStatusForm() {
    document.getElementById('selectedComplaintCard').classList.add('d-none');
    document.getElementById('timelinePlaceholder').classList.remove('d-none');
    document.getElementById('timelineContainer').classList.add('d-none');
}

// --- PAGE 5: Verify Completed Work Page ---
function renderVerifyWorkPage() {
    const container = document.getElementById('verificationCardsContainer');
    const logBody = document.getElementById('verificationLogBody');
    if (!container) return;

    const workItems = gpcmsDataset.complaints.filter(c => c.before_photo && c.after_photo);

    if (workItems.length === 0) {
        container.innerHTML = `<div class="col-12 text-center py-5 text-muted">No completed work photo submissions pending verification.</div>`;
    } else {
        container.innerHTML = workItems.map(c => `
            <div class="col-lg-6">
                <div class="gpcms-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary-custom">${c.complaint_id}</span>
                        ${getStatusBadgeHTML(c.status)}
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">${c.complaint_title}</h6>
                        <p class="small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>${c.village_ward} | Officer: <strong>${c.officer_name}</strong></p>

                        <div class="row g-2 my-3">
                            <div class="col-6 text-center">
                                <span class="badge bg-danger mb-1 d-block">Before Work Photo</span>
                                <img src="${c.before_photo}" class="img-fluid rounded border complaint-img-thumb w-100" style="height: 160px;" alt="Before Photo" onclick="openLightbox('${c.before_photo}', 'Before Photo Evidence')">
                            </div>
                            <div class="col-6 text-center">
                                <span class="badge bg-success mb-1 d-block">After Completion Photo</span>
                                <img src="${c.after_photo}" class="img-fluid rounded border complaint-img-thumb w-100" style="height: 160px;" alt="After Photo" onclick="openLightbox('${c.after_photo}', 'After Work Evidence')">
                            </div>
                        </div>

                        <div class="bg-light p-2 rounded mb-3 small">
                            <strong>Officer Remarks:</strong> ${c.officer_remarks || 'Work completed as per quality specifications.'}
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-fill" onclick="promptVerificationAction('${c.complaint_id}', 'approve')">
                                <i class="bi bi-check-lg me-1"></i> Approve Completion
                            </button>
                            <button class="btn btn-outline-danger btn-sm flex-fill" onclick="promptVerificationAction('${c.complaint_id}', 'reject')">
                                <i class="bi bi-x-lg me-1"></i> Reject & Re-assign
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    if (logBody) {
        logBody.innerHTML = `
            <tr>
                <td><strong class="text-primary-custom">CMP-2024-004</strong></td>
                <td>Fused LED Street Lights on Station Road</td>
                <td>Hanuman Nagar</td>
                <td>Anil Kadam</td>
                <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span></td>
                <td>Replaced bulbs verified on site. Satisfactory work.</td>
                <td><small class="text-muted">2026-07-22 04:00 PM</small></td>
            </tr>
        `;
    }
}

function promptVerificationAction(complaintId, actionType) {
    const alertEl = document.getElementById('verificationActionAlert');
    const textEl = document.getElementById('verificationActionText');
    const btnSubmit = document.getElementById('btnSubmitVerification');

    if (actionType === 'approve') {
        alertEl.className = 'alert alert-success mb-3';
        textEl.textContent = `Approving work completion for ${complaintId}. Status will set to 'resolved'.`;
    } else {
        alertEl.className = 'alert alert-danger mb-3';
        textEl.textContent = `Rejecting work completion for ${complaintId}. Status will revert to 'in_progress' for rework.`;
    }

    btnSubmit.onclick = () => {
        const remarks = document.getElementById('verifyRemarksInput').value.trim();
        if (!remarks) {
            alert('Verification remarks are required.');
            return;
        }

        const complaint = gpcmsDataset.complaints.find(c => c.complaint_id === complaintId);
        if (complaint) {
            complaint.status = actionType === 'approve' ? 'resolved' : 'in_progress';
            alert(`Verification recorded! Complaint ${complaintId} status set to '${complaint.status}'.`);
            const modalEl = document.getElementById('verificationActionModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            renderVerifyWorkPage();
        }
    };

    const modal = new bootstrap.Modal(document.getElementById('verificationActionModal'));
    modal.show();
}

// --- PAGE 6: Category Management Page ---
function renderCategoryManagementPage() {
    renderCategoryTable();
}

function renderCategoryTable() {
    const tbody = document.getElementById('categoryTableBody');
    const countBadge = document.getElementById('categoryCountBadge');
    if (!tbody) return;

    if (countBadge) countBadge.textContent = `Total: ${gpcmsDataset.categories.length}`;

    tbody.innerHTML = gpcmsDataset.categories.map(cat => `
        <tr>
            <td><strong class="text-primary-custom">#CAT-00${cat.category_id}</strong></td>
            <td><strong class="text-dark">${cat.category_name}</strong></td>
            <td>${cat.description}</td>
            <td><span class="badge bg-light text-dark border">${cat.count} complaints</span></td>
            <td>
                <span class="badge ${cat.status === 'Active' ? 'bg-success' : 'bg-secondary'}">${cat.status}</span>
            </td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditCategoryModal(${cat.category_id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="promptDeleteCategoryModal(${cat.category_id})">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

function handleAddCategory() {
    const name = document.getElementById('newCategoryName').value.trim();
    const desc = document.getElementById('newCategoryDesc').value.trim();
    const status = document.getElementById('newCategoryStatus').value;

    if (!name) return;

    const newId = gpcmsDataset.categories.length + 1;
    gpcmsDataset.categories.push({
        category_id: newId,
        category_name: name,
        description: desc,
        count: 0,
        status: status
    });

    alert(`Category '${name}' added successfully!`);
    const modalEl = document.getElementById('addCategoryModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    document.getElementById('formAddCategory').reset();
    renderCategoryTable();
}

function openEditCategoryModal(catId) {
    const cat = gpcmsDataset.categories.find(c => c.category_id === catId);
    if (!cat) return;

    const idEl = document.getElementById('editCategoryId');
    const nameEl = document.getElementById('editCategoryName');
    const descEl = document.getElementById('editCategoryDesc');
    const statusEl = document.getElementById('editCategoryStatus');

    if (idEl) idEl.value = cat.category_id;
    if (nameEl) nameEl.value = cat.category_name;
    if (descEl) descEl.value = cat.description || '';
    if (statusEl) statusEl.value = cat.status || 'Active';

    const modalEl = document.getElementById('editCategoryModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function handleEditCategory() {
    const catId = parseInt(document.getElementById('editCategoryId').value);
    const name = document.getElementById('editCategoryName').value.trim();
    const desc = document.getElementById('editCategoryDesc').value.trim();
    const status = document.getElementById('editCategoryStatus').value;

    const cat = gpcmsDataset.categories.find(c => c.category_id === catId);
    if (cat) {
        cat.category_name = name;
        cat.description = desc;
        cat.status = status;

        alert(`Category #CAT-00${catId} updated successfully!`);
        const modalEl = document.getElementById('editCategoryModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        renderCategoryTable();
    }
}

function promptDeleteCategoryModal(catId) {
    const cat = gpcmsDataset.categories.find(c => c.category_id === catId);
    if (!cat) return;

    const nameText = document.getElementById('deleteCategoryNameText');
    if (nameText) nameText.textContent = cat.category_name;
    const btnDel = document.getElementById('btnConfirmDeleteCat');

    if (btnDel) {
        btnDel.onclick = () => {
            gpcmsDataset.categories = gpcmsDataset.categories.filter(c => c.category_id !== catId);
            alert(`Category '${cat.category_name}' removed.`);
            const modalEl = document.getElementById('deleteCategoryModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            renderCategoryTable();
        };
    }

    const modalEl = document.getElementById('deleteCategoryModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        if (confirm(`Are you sure you want to delete category '${cat.category_name}'?`)) {
            gpcmsDataset.categories = gpcmsDataset.categories.filter(c => c.category_id !== catId);
            alert(`Category '${cat.category_name}' removed.`);
            renderCategoryTable();
        }
    }
}

function filterCategories() {
    const query = document.getElementById('searchCategoryInput').value.toLowerCase();
    const status = document.getElementById('filterCategoryStatus').value;

    const rows = document.querySelectorAll('#categoryTableBody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const matchesQuery = !query || text.includes(query);
        const matchesStatus = !status || text.includes(status.toLowerCase());

        row.style.display = (matchesQuery && matchesStatus) ? '' : 'none';
    });
}

// --- PAGE 7: System Settings Page Handlers ---
function saveSystemSettings() {
    alert('System settings saved successfully. Database contract table `settings` updated.');
}

function resetSettingsForm() {
    if (confirm('Reset system settings to defaults?')) {
        document.getElementById('formSystemSettings').reset();
    }
}

// --- PAGE 8: Reports Page ---
function renderReportsPage() {
    generateCustomReport();
}

function generateCustomReport() {
    const rptType = document.getElementById('reportType').value;
    const headEl = document.getElementById('reportTableHead');
    const bodyEl = document.getElementById('reportTableBody');
    const titleEl = document.getElementById('reportTableHeading');
    const chartCtx = document.getElementById('reportsMainChart');

    if (!bodyEl) return;

    if (titleEl) titleEl.innerHTML = `<i class="bi bi-table text-primary-custom me-2"></i>Generated ${rptType.toUpperCase()} Report`;

    if (rptType === 'category') {
        headEl.innerHTML = `<tr><th>Category Name</th><th>Total Complaints</th><th>Pending</th><th>Assigned</th><th>In Progress</th><th>Resolved</th></tr>`;
        bodyEl.innerHTML = `
            <tr><td>Water Supply</td><td>42</td><td>8</td><td>10</td><td>10</td><td>14</td></tr>
            <tr><td>Sanitation & Waste</td><td>35</td><td>5</td><td>8</td><td>8</td><td>14</td></tr>
            <tr><td>Road Repair</td><td>28</td><td>4</td><td>6</td><td>6</td><td>12</td></tr>
            <tr><td>Street Lighting</td><td>22</td><td>3</td><td>6</td><td>4</td><td>9</td></tr>
            <tr><td>Drainage System</td><td>14</td><td>3</td><td>5</td><td>2</td><td>4</td></tr>
        `;
    } else if (rptType === 'village') {
        headEl.innerHTML = `<tr><th>Village / Ward</th><th>Total Received</th><th>Pending</th><th>Assigned</th><th>In Progress</th><th>Resolved</th></tr>`;
        bodyEl.innerHTML = `
            <tr><td>Shivaji Nagar</td><td>52</td><td>10</td><td>12</td><td>10</td><td>20</td></tr>
            <tr><td>Rampur Ward 1</td><td>38</td><td>6</td><td>10</td><td>8</td><td>14</td></tr>
            <tr><td>Rampur Ward 2</td><td>26</td><td>4</td><td>6</td><td>6</td><td>10</td></tr>
            <tr><td>Ganesh Wadi</td><td>32</td><td>4</td><td>10</td><td>7</td><td>11</td></tr>
        `;
    } else {
        headEl.innerHTML = `<tr><th>Allowed Status</th><th>Count</th><th>Percentage</th><th>Resolution SLA Met</th></tr>`;
        bodyEl.innerHTML = `
            <tr><td>pending</td><td>24</td><td>16.2%</td><td>Pending Action</td></tr>
            <tr><td>assigned</td><td>38</td><td>25.6%</td><td>Assigned to Officer</td></tr>
            <tr><td>in_progress</td><td>31</td><td>20.9%</td><td>Work Underway</td></tr>
            <tr><td>resolved</td><td>55</td><td>37.3%</td><td>100% SLA Met</td></tr>
        `;
    }

    if (chartCtx) {
        if (window.reportsChartInstance) window.reportsChartInstance.destroy();
        window.reportsChartInstance = new Chart(chartCtx, {
            type: 'pie',
            data: {
                labels: ['Water Supply', 'Sanitation', 'Road Repair', 'Street Lighting', 'Drainage'],
                datasets: [{
                    data: [42, 35, 28, 22, 14],
                    backgroundColor: ['#8A724C', '#B99668', '#DCC9A7', '#EDE2CC', '#5a5246']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
}

function onReportTypeChange() {
    generateCustomReport();
}

// --- PAGE 9: Complaint Statistics Dashboard ---
function renderStatisticsPage() {
    renderOfficerPerformanceTable();
    initStatisticsCharts();
}

function renderOfficerPerformanceTable() {
    const tbody = document.getElementById('officerPerformanceTableBody');
    if (!tbody) return;

    tbody.innerHTML = `
        <tr><td><strong>Ramesh Shinde</strong></td><td>18</td><td>10</td><td>25</td><td><span class="badge bg-success">89% Efficiency</span></td></tr>
        <tr><td><strong>Suresh Patil</strong></td><td>12</td><td>14</td><td>18</td><td><span class="badge bg-success">85% Efficiency</span></td></tr>
        <tr><td><strong>Anil Kadam</strong></td><td>8</td><td>7</td><td>12</td><td><span class="badge bg-success">92% Efficiency</span></td></tr>
    `;
}

function initStatisticsCharts() {
    const pieCtx = document.getElementById('categoryPieChart');
    const lineCtx = document.getElementById('monthlyTrendLineChart');
    const barCtx = document.getElementById('villageBarChart');

    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Water', 'Sanitation', 'Roads', 'Lighting', 'Drainage'],
                datasets: [{
                    data: [42, 35, 28, 22, 14],
                    backgroundColor: ['#8A724C', '#B99668', '#DCC9A7', '#EDE2CC', '#3a3224']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [
                    { label: 'Registered', data: [15, 22, 30, 28, 35, 40, 42], borderColor: '#8A724C', fill: false },
                    { label: 'Resolved', data: [12, 18, 25, 24, 30, 35, 38], borderColor: '#16a34a', fill: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Shivaji Nagar', 'Rampur W1', 'Rampur W2', 'Ganesh Wadi', 'Hanuman Nagar'],
                datasets: [{
                    label: 'Total Complaints',
                    data: [52, 38, 26, 32, 18],
                    backgroundColor: '#B99668'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
}

function updateStatisticsCharts() {
    alert('Updating statistics charts for selected timeframe.');
}

// --- PAGE 10: Monthly Reports Page ---
function renderMonthlyReportsPage() {
    generateMonthlyReport();
}

function generateMonthlyReport() {
    const month = document.getElementById('mthFilterMonth').value;
    const year = document.getElementById('mthFilterYear').value;
    const tbody = document.getElementById('monthlyReportTableBody');
    const chartCtx = document.getElementById('monthlyReportChart');

    if (!tbody) return;

    document.getElementById('monthlyTableTitle').innerHTML = `<i class="bi bi-table text-primary-custom me-2"></i>Monthly Complaints Log (${month}/${year})`;

    tbody.innerHTML = gpcmsDataset.complaints.map(c => `
        <tr>
            <td><strong class="text-primary-custom">${c.complaint_id}</strong></td>
            <td>${c.complainant_name}</td>
            <td>${c.village_ward}</td>
            <td><span class="badge bg-surface-custom text-dark">${c.category_name}</span></td>
            <td>${c.complaint_title}</td>
            <td>${getStatusBadgeHTML(c.status)}</td>
            <td>${c.officer_name}</td>
            <td><small class="text-muted">${c.submission_date}</small></td>
        </tr>
    `).join('');

    if (chartCtx) {
        if (window.monthlyChartInstance) window.monthlyChartInstance.destroy();
        window.monthlyChartInstance = new Chart(chartCtx, {
            type: 'bar',
            data: {
                labels: ['pending', 'assigned', 'in_progress', 'resolved'],
                datasets: [{
                    label: 'Status Breakdown',
                    data: [6, 10, 12, 24],
                    backgroundColor: ['#d97706', '#0284c7', '#8A724C', '#16a34a']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
}

function populateNotificationsList() {
    const list = document.getElementById('notificationList');
    if (!list) return;

    if (!gpcmsDataset.notifications || gpcmsDataset.notifications.length === 0) {
        list.innerHTML = '<div class="list-group-item p-3 text-center text-muted">No notifications found</div>';
        return;
    }

    list.innerHTML = gpcmsDataset.notifications.map(n => {
        const readBadge = n.is_read ? '' : '<span class="badge bg-danger ms-2">New</span>';
        return `
            <div class="list-group-item p-3">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <h6 class="mb-1 text-primary-custom">Complaint ID: ${n.complaint_id} ${readBadge}</h6>
                    <small class="text-muted">${n.timestamp}</small>
                </div>
                <p class="mb-1 small text-dark">${n.message}</p>
            </div>
        `;
    }).join('');
}
