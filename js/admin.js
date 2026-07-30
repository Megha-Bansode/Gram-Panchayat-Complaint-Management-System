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

// Dedicated PDF Export Engine (Triggers Browser Print to PDF with Landscape A4 layout)
function exportPDFReport(targetId, title) {
    const targetEl = document.getElementById(targetId);
    if (!targetEl) return;

    const printWin = window.open('', '_blank', 'width=1000,height=800');
    if (!printWin) return;

    const htmlContent = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>${title || 'Pending vs Resolved Report'}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @page { size: A4 landscape; margin: 12mm; }
                body { font-family: 'Poppins', sans-serif; padding: 15px; color: #3a3224; background: #ffffff; }
                .print-header { text-align: center; border-bottom: 2px solid #8A724C; padding-bottom: 12px; margin-bottom: 20px; }
                .print-header h2 { color: #8A724C; font-weight: 700; margin: 0 0 4px 0; font-size: 1.5rem; }
                .print-header p { margin: 0; color: #555; font-size: 0.85rem; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th { background-color: #8A724C !important; color: #ffffff !important; font-weight: 600; padding: 10px; border: 1px solid #735d3c; font-size: 0.85rem; }
                td { padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 0.85rem; }
                .badge { padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 0.75rem; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>Gram Panchayat Administration Console</h2>
                <p>Official Complaint Operations & Analytical Statement — <strong>${title}</strong></p>
                <small class="text-muted">Exported Date: ${new Date().toLocaleString()}</small>
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
    }, 350);
}

// Dedicated Excel Export Engine (.xls MS-Excel Format)
function exportExcelReport(tableId, filename = 'pending_resolved_report.xls') {
    const table = document.getElementById(tableId);
    if (!table) return;

    let excelContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Report Statement</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
                th { background-color: #8A724C; color: #ffffff; font-weight: bold; border: 1px solid #735d3c; padding: 8px; text-align: left; }
                td { border: 1px solid #d3d3d3; padding: 6px 8px; vertical-align: top; }
            </style>
        </head>
        <body>
            <h2>Gram Panchayat Complaint Management System (GPCMS)</h2>
            <p><strong>Report Document:</strong> ${filename.replace(/\.[^/.]+$/, "")}</p>
            <p><strong>Export Date:</strong> ${new Date().toLocaleString()}</p>
            <br>
            ${table.outerHTML}
        </body>
        </html>
    `;

    const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    const finalFilename = filename.toLowerCase().endsWith('.xls') ? filename : filename.replace(/\.[^/.]+$/, '') + '.xls';
    link.setAttribute('download', finalFilename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// export routines for reports
function exportCSVReport(tableId, filename) {
    exportExcelReport(tableId, filename);
}
