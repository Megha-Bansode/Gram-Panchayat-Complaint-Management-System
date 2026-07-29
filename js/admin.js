/**
 * Gram Panchayat Complaint Management System (GPCMS) v3.1
 * Super Admin Module Core JavaScript Controller
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebarToggle();
    initLiveClock();
    
    // Page specific initializations
    if (document.getElementById('adminOverviewChart')) {
        initAdminOverviewChart();
    }
    if (document.getElementById('pendingResolvedChart')) {
        initPendingResolvedChart();
    }
    if (document.getElementById('villageReportsChart')) {
        initVillageReportsChart();
    }

    // Attach global click event for logout confirmation
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to log out of Admin Portal?')) {
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

// Universal CSV Download Engine
function downloadCSV(filename, headers, rowsData) {
    let csvContent = "\uFEFF"; // UTF-8 BOM
    csvContent += headers.map(h => `"${String(h).replace(/"/g, '""')}"`).join(",") + "\r\n";
    rowsData.forEach(row => {
        const line = row.map(val => {
            const strVal = (val === null || val === undefined) ? "" : String(val);
            return `"${strVal.replace(/"/g, '""')}"`;
        }).join(",");
        csvContent += line + "\r\n";
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Dedicated Print Engine Function
function printElement(targetId, title) {
    const targetEl = document.getElementById(targetId);
    if (!targetEl) return;

    const printWin = window.open('', '_blank', 'width=950,height=750');
    if (!printWin) return;

    const htmlContent = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>${title || 'Admin Official Report'}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-family: 'Poppins', sans-serif; padding: 25px; color: #3a3224; background: #ffffff; }
                .print-header { text-align: center; border-bottom: 2px solid #8A724C; padding-bottom: 12px; margin-bottom: 20px; }
                .print-header h3 { color: #8A724C; font-weight: 700; margin: 0; }
                th { background-color: #EDE2CC !important; color: #3a3224 !important; font-weight: 600; padding: 10px; border: 1px solid #ddd; }
                td { padding: 10px; border: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h3>Gram Panchayat Admin Office</h3>
                <p>Gram Panchayat Complaint Management System (GPCMS v3.1)</p>
                <small class="text-muted">Document: <strong>${title}</strong> | Date: ${new Date().toLocaleString()}</small>
            </div>
            <div>
                ${targetEl.outerHTML}
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

// export routines for reports
function exportCSVReport(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const headers = [];
    const rows = [];
    table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText.trim()));
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => row.push(td.innerText.trim()));
        if (row.length > 0) rows.push(row);
    });

    downloadCSV(filename, headers, rows);
}
