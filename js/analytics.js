/**
 * =====================================================
 *  GPCMS — Premium Analytics Dashboard JavaScript
 *  js/analytics.js  |  Version 2.0 Enterprise
 *  Chart.js 4.x | ES6+ | GPCMS Color Palette
 * =====================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initDateTimeDisplay();
    initSidebar();
    initAnimatedCounters();
    initAllCharts();
    initTableFeatures();
    initFAB();
    initAutoRefresh();
    initKeyboardShortcuts();
    initRippleButtons();
    initProgressBars();
    console.log('%c GPCMS Analytics v2.0 Loaded ✓', 'color:#8A724C;font-weight:700;font-size:13px;');
});

/* =========================================================
   PALETTE CONSTANTS
   ========================================================= */
const GP = {
    primary:    '#8A724C',
    primaryDk:  '#6E5A3A',
    secondary:  '#B99668',
    accent:     '#DCC9A7',
    surface:    '#EDE2CC',
    bg:         '#F7F3E8',
    text:       '#3A2E22',
    textMuted:  '#7A6A55',

    // Palette array for multi-series charts
    palette: [
        '#8A724C','#B99668','#DCC9A7','#7A6A55',
        '#C4A87A','#9E8560','#6E5A3A','#D4BC96',
        '#A08660','#E8D8BB'
    ],
    // Semitransparent fills for area charts
    paletteAlpha(hex, a = 0.2) {
        const r = parseInt(hex.slice(1,3),16);
        const g = parseInt(hex.slice(3,5),16);
        const b = parseInt(hex.slice(5,7),16);
        return `rgba(${r},${g},${b},${a})`;
    }
};

/* =========================================================
   DATE / TIME DISPLAY
   ========================================================= */
function initDateTimeDisplay() {
    const dateEl = document.getElementById('headerDate');
    const timeEl = document.getElementById('headerTime');
    if (!dateEl && !timeEl) return;

    function update() {
        const now = new Date();
        if (dateEl) dateEl.textContent = now.toLocaleDateString('en-IN', {
            weekday:'short', day:'2-digit', month:'short', year:'numeric'
        });
        if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-IN', {
            hour:'2-digit', minute:'2-digit', hour12: true
        });
    }
    update();
    setInterval(update, 1000);
}

/* =========================================================
   SIDEBAR TOGGLE
   ========================================================= */
function initSidebar() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.querySelector('.app-sidebar');
    const main      = document.querySelector('.app-main');
    const footer    = document.querySelector('.app-footer');
    const overlay   = document.getElementById('sidebarOverlay');

    if (!sidebar) return;

    const isMobile = () => window.innerWidth < 993;

    function openMobile() {
        sidebar.classList.add('mobile-open');
        if (overlay) overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeMobile() {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    function toggleDesktop() {
        sidebar.classList.toggle('collapsed');
        if (main) main.classList.toggle('expanded');
        if (footer) footer.classList.toggle('expanded');
        localStorage.setItem('gpcms_sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
    }

    // Restore sidebar state
    if (!isMobile() && localStorage.getItem('gpcms_sidebar_collapsed') === '1') {
        sidebar.classList.add('collapsed');
        if (main) main.classList.add('expanded');
        if (footer) footer.classList.add('expanded');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', () => {
        isMobile() ? (sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile()) : toggleDesktop();
    });

    if (overlay) overlay.addEventListener('click', closeMobile);
}

/* =========================================================
   ANIMATED COUNTERS
   ========================================================= */
function initAnimatedCounters() {
    const els = document.querySelectorAll('[data-counter]');
    if (!els.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.getAttribute('data-counter'), 10);
            if (isNaN(target)) return;
            animateCounter(el, target);
            observer.unobserve(el);
        });
    }, { threshold: 0.3 });

    els.forEach(el => observer.observe(el));
}

function animateCounter(el, target) {
    let start = 0;
    const duration = 1400;
    const step = target / (duration / 16);
    const suffix = el.getAttribute('data-suffix') || '';

    const tick = () => {
        start += step;
        if (start >= target) {
            el.textContent = target.toLocaleString('en-IN') + suffix;
            return;
        }
        el.textContent = Math.floor(start).toLocaleString('en-IN') + suffix;
        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

/* =========================================================
   PROGRESS BARS
   ========================================================= */
function initProgressBars() {
    const bars = document.querySelectorAll('[data-progress]');
    if (!bars.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const bar = entry.target;
            const pct = parseInt(bar.getAttribute('data-progress'), 10);
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = pct + '%'; }, 100);
            observer.unobserve(bar);
        });
    }, { threshold: 0.2 });

    bars.forEach(b => observer.observe(b));
}

/* =========================================================
   CHART.JS GLOBAL DEFAULTS
   ========================================================= */
function setChartDefaults() {
    if (typeof Chart === 'undefined') return;
    Chart.defaults.font.family = "'Poppins', 'Segoe UI', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = GP.textMuted;
    Chart.defaults.plugins.legend.labels.padding = 16;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(26,20,14,0.9)';
    Chart.defaults.plugins.tooltip.titleColor = '#EDE2CC';
    Chart.defaults.plugins.tooltip.bodyColor  = '#DCC9A7';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(185,150,104,0.4)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.titleFont = { weight: '600' };
}

function gridOpts() {
    return { color: 'rgba(138,114,76,0.1)', drawTicks: false };
}
function tickOpts() {
    return { color: GP.textMuted, padding: 6 };
}

/* =========================================================
   ALL CHARTS INIT
   ========================================================= */
function initAllCharts() {
    if (typeof Chart === 'undefined') return;
    setChartDefaults();

    chartComplaintTrend();
    chartMonthlyArea();
    chartCategoryDoughnut();
    chartVillageBar();
    chartPendingResolved();
    chartWeeklyActivity();
    chartResolutionTimeline();
    chartRadar();
    chartStackedBar();
    chartHorizontalBar();
}

/* ── 1. Complaint Trend (Line) ── */
function chartComplaintTrend() {
    const ctx = document.getElementById('chartComplaintTrend');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const totalAttr = ctx.getAttribute('data-total');
    const resolvedAttr = ctx.getAttribute('data-resolved');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const totalData = totalAttr ? JSON.parse(totalAttr) : [42,58,65,78,90,85,110,95,120,135,140,155];
    const resolvedData = resolvedAttr ? JSON.parse(resolvedAttr) : [30,45,55,60,75,72,95,82,105,118,125,140];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Complaints',
                    data: totalData,
                    borderColor: GP.primary,
                    backgroundColor: GP.paletteAlpha(GP.primary, 0.12),
                    fill: true, tension: 0.45, borderWidth: 2.5,
                    pointBackgroundColor: GP.primary,
                    pointRadius: 4, pointHoverRadius: 7,
                },
                {
                    label: 'Resolved',
                    data: resolvedData,
                    borderColor: GP.secondary,
                    backgroundColor: GP.paletteAlpha(GP.secondary, 0.08),
                    fill: true, tension: 0.45, borderWidth: 2,
                    pointBackgroundColor: GP.secondary,
                    pointRadius: 3, pointHoverRadius: 6,
                    borderDash: [4,3],
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                x: { grid: gridOpts(), ticks: tickOpts() },
                y: { grid: gridOpts(), ticks: tickOpts(), beginAtZero: true }
            }
        }
    });
}

/* ── 2. Monthly Area Chart ── */
function chartMonthlyArea() {
    const ctx = document.getElementById('chartMonthlyArea');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const totalAttr = ctx.getAttribute('data-total');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const totalData = totalAttr ? JSON.parse(totalAttr) : [42,58,65,78,90,85,110,95,120,135,140,155];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Total',
                data: totalData,
                borderColor: GP.primary,
                backgroundColor: {
                    type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                    colorStops: [
                        { offset: 0, color: GP.paletteAlpha(GP.primary, 0.4) },
                        { offset: 1, color: GP.paletteAlpha(GP.primary, 0.02) }
                    ]
                },
                fill: 'start', tension: 0.45,
                borderWidth: 2.5, pointRadius: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: tickOpts() },
                y: { grid: gridOpts(), ticks: tickOpts(), beginAtZero: true }
            }
        }
    });
}

/* ── 3. Category Doughnut ── */
function chartCategoryDoughnut() {
    const ctx = document.getElementById('chartCategoryDoughnut');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const valuesAttr = ctx.getAttribute('data-values');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Water Supply','Roads','Sanitation','Street Lighting','Electricity','Agriculture','Others'];
    const values = valuesAttr ? JSON.parse(valuesAttr) : [35,25,18,10,6,4,2];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: GP.palette.slice(0, labels.length),
                borderWidth: 2, borderColor: GP.bg,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            aspectRatio: 1,
            cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { color: GP.text } }
            }
        }
    });
}

/* ── 4. Village Bar ── */
function chartVillageBar() {
    const ctx = document.getElementById('chartVillageBar');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const valuesAttr = ctx.getAttribute('data-values');
    const resolvedAttr = ctx.getAttribute('data-resolved');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Karanji','Pimpalgaon','Chincholi','Shirdi','Rahit','Savedi','Wadgaon'];
    const totalData = valuesAttr ? JSON.parse(valuesAttr) : [84,62,45,95,38,67,52];
    const resolvedData = resolvedAttr ? JSON.parse(resolvedAttr) : [65,50,35,78,28,55,40];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Complaints',
                    data: totalData,
                    backgroundColor: GP.paletteAlpha(GP.primary, 0.75),
                    borderColor: GP.primary, borderWidth: 1.5,
                    borderRadius: 6, barPercentage: 0.6,
                },
                {
                    label: 'Resolved',
                    data: resolvedData,
                    backgroundColor: GP.paletteAlpha(GP.secondary, 0.6),
                    borderColor: GP.secondary, borderWidth: 1.5,
                    borderRadius: 6, barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index' },
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { grid: { display: false }, ticks: tickOpts() },
                y: { grid: gridOpts(), ticks: tickOpts(), beginAtZero: true }
            }
        }
    });
}

/* ── 5. Pending vs Resolved Pie ── */
function chartPendingResolved() {
    const ctx = document.getElementById('chartPendingResolved');
    if (!ctx) return;
    const pData = ctx.dataset;
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending','Assigned','In Progress','Resolved'],
            datasets: [{
                data: [
                    parseInt(pData.pending  || '22'),
                    parseInt(pData.assigned || '18'),
                    parseInt(pData.progress || '25'),
                    parseInt(pData.resolved || '145'),
                ],
                backgroundColor: ['#8A724C','#B99668','#DCC9A7','#6E5A3A'],
                borderColor: GP.bg, borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            aspectRatio: 1,
            plugins: {
                legend: { position: 'bottom', labels: { color: GP.text, padding: 16 } }
            }
        }
    });
}

/* ── 6. Weekly Activity ── */
function chartWeeklyActivity() {
    const ctx = document.getElementById('chartWeeklyActivity');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const valuesAttr = ctx.getAttribute('data-values');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    const values = valuesAttr ? JSON.parse(valuesAttr) : [18,25,22,30,27,12,8];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Grievances Filed',
                data: values,
                backgroundColor: GP.palette.slice(0, labels.length).map(c => GP.paletteAlpha(c, 0.75)),
                borderRadius: 8, barPercentage: 0.55,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: tickOpts() },
                y: { grid: gridOpts(), ticks: tickOpts(), beginAtZero: true }
            }
        }
    });
}

/* ── 7. Resolution Timeline (Line) ── */
function chartResolutionTimeline() {
    const ctx = document.getElementById('chartResolutionTimeline');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const valuesAttr = ctx.getAttribute('data-values');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Wk 1','Wk 2','Wk 3','Wk 4','Wk 5','Wk 6','Wk 7','Wk 8'];
    const values = valuesAttr ? JSON.parse(valuesAttr) : [7.2,5.8,6.5,4.9,5.2,4.1,3.8,3.2];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Avg. Days to Resolve',
                data: values,
                borderColor: GP.primary,
                backgroundColor: GP.paletteAlpha(GP.primary, 0.1),
                fill: true, tension: 0.4, borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: GP.primary,
                pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: tickOpts() },
                y: { grid: gridOpts(), ticks: tickOpts(), beginAtZero: true }
            }
        }
    });
}

/* ── 8. Radar Chart ── */
function chartRadar() {
    const ctx = document.getElementById('chartRadar');
    if (!ctx) return;

    const thisMonthAttr = ctx.getAttribute('data-this-month');
    const lastMonthAttr = ctx.getAttribute('data-last-month');

    const thisMonth = thisMonthAttr ? JSON.parse(thisMonthAttr) : [88,75,92,68,85,80];
    const lastMonth = lastMonthAttr ? JSON.parse(lastMonthAttr) : [72,65,80,60,78,70];

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Response Speed','Resolution Rate','Citizen Satisfaction','Coverage','Documentation','SLA Compliance'],
            datasets: [
                {
                    label: 'This Month',
                    data: thisMonth,
                    borderColor: GP.primary,
                    backgroundColor: GP.paletteAlpha(GP.primary, 0.2),
                    borderWidth: 2, pointRadius: 4,
                },
                {
                    label: 'Last Month',
                    data: lastMonth,
                    borderColor: GP.accent,
                    backgroundColor: GP.paletteAlpha(GP.accent, 0.2),
                    borderWidth: 2, pointRadius: 4,
                    borderDash: [5,4],
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: {
                r: {
                    grid: { color: 'rgba(138,114,76,0.15)' },
                    pointLabels: { color: GP.text, font: { size: 11 } },
                    ticks: { color: GP.textMuted, backdropColor: 'transparent', stepSize: 20 },
                    min: 0, max: 100,
                }
            }
        }
    });
}

/* ── 9. Stacked Bar ── */
function chartStackedBar() {
    const ctx = document.getElementById('chartStackedBar');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const pendingAttr = ctx.getAttribute('data-pending');
    const assignedAttr = ctx.getAttribute('data-assigned');
    const progressAttr = ctx.getAttribute('data-progress');
    const resolvedAttr = ctx.getAttribute('data-resolved');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Jan','Feb','Mar','Apr','May','Jun'];
    const pendingData = pendingAttr ? JSON.parse(pendingAttr) : [10,12,8,15,9,7];
    const assignedData = assignedAttr ? JSON.parse(assignedAttr) : [8,10,7,12,8,6];
    const progressData = progressAttr ? JSON.parse(progressAttr) : [12,15,10,18,14,9];
    const resolvedData = resolvedAttr ? JSON.parse(resolvedAttr) : [22,28,35,42,38,45];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Pending',     data: pendingData,  backgroundColor: '#8A724C', borderRadius: { topLeft:0, topRight:0, bottomLeft:4, bottomRight:4 } },
                { label: 'Assigned',    data: assignedData,   backgroundColor: '#B99668' },
                { label: 'In Progress', data: progressData, backgroundColor: '#DCC9A7' },
                { label: 'Resolved',    data: resolvedData, backgroundColor: '#6E5A3A', borderRadius: { topLeft:4, topRight:4, bottomLeft:0, bottomRight:0 } },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index' },
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { stacked: true, grid: { display: false }, ticks: tickOpts() },
                y: { stacked: true, grid: gridOpts(), ticks: tickOpts() }
            }
        }
    });
}

/* ── 10. Horizontal Comparison Bar ── */
function chartHorizontalBar() {
    const ctx = document.getElementById('chartHorizontalBar');
    if (!ctx) return;

    const labelsAttr = ctx.getAttribute('data-labels');
    const valuesAttr = ctx.getAttribute('data-values');

    const labels = labelsAttr ? JSON.parse(labelsAttr) : ['Water Supply','Roads','Sanitation','Lighting','Electricity','Agriculture'];
    const values = valuesAttr ? JSON.parse(valuesAttr) : [88,72,65,80,55,70];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Resolution Rate (%)',
                data: values,
                backgroundColor: GP.palette.slice(0, labels.length).map(c => GP.paletteAlpha(c, 0.8)),
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: gridOpts(), ticks: tickOpts(), max: 100, beginAtZero: true },
                y: { grid: { display: false }, ticks: tickOpts() }
            }
        }
    });
}

/* ── Satisfaction Gauge ── */
function chartSatisfactionGauge() {
    const ctx = document.getElementById('chartSatisfactionGauge');
    if (!ctx) return;
    const score = parseInt(ctx.dataset.score || '92', 10);
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [score, 100 - score],
                backgroundColor: [GP.primary, GP.surface],
                borderWidth: 0,
                circumference: 180, rotation: 270,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true, cutout: '75%',
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    });
}
// Call gauge separately so it triggers even if called from report pages
window.addEventListener('load', chartSatisfactionGauge);

/* =========================================================
   TABLE FEATURES: Search / Sort / Pagination / Export
   ========================================================= */
function initTableFeatures() {
    // Live search
    document.querySelectorAll('[data-table-search]').forEach(input => {
        const tableId = input.getAttribute('data-table-search');
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase().trim();
            const tbody = document.querySelector(`#${tableId} tbody`);
            if (!tbody) return;
            tbody.querySelectorAll('tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });

    // Column sorting
    document.querySelectorAll('.data-table th[data-sort]').forEach(th => {
        th.addEventListener('click', () => {
            const table = th.closest('table');
            const col   = [...th.parentElement.children].indexOf(th);
            const asc   = th.dataset.dir !== 'asc';
            th.dataset.dir = asc ? 'asc' : 'desc';
            sortTable(table, col, asc);
            table.querySelectorAll('th').forEach(t => t.removeAttribute('data-dir'));
            th.dataset.dir = asc ? 'asc' : 'desc';
        });
    });

    // Pagination
    document.querySelectorAll('[data-paginate]').forEach(table => {
        paginateTable(table, parseInt(table.dataset.paginate || '10', 10));
    });
}

function sortTable(table, col, asc) {
    const tbody = table.querySelector('tbody');
    const rows  = [...tbody.querySelectorAll('tr')];
    rows.sort((a, b) => {
        const aT = a.cells[col]?.textContent.trim() || '';
        const bT = b.cells[col]?.textContent.trim() || '';
        const aV = parseFloat(aT.replace(/[^0-9.-]/g,''));
        const bV = parseFloat(bT.replace(/[^0-9.-]/g,''));
        if (!isNaN(aV) && !isNaN(bV)) return asc ? aV - bV : bV - aV;
        return asc ? aT.localeCompare(bT) : bT.localeCompare(aT);
    });
    rows.forEach(r => tbody.appendChild(r));
}

function paginateTable(table, perPage = 10) {
    const tbody  = table.querySelector('tbody');
    const wrap   = table.closest('.section-card') || table.parentElement;
    const pager  = wrap.querySelector('.table-pagination');
    if (!tbody || !pager) return;

    let page = 1;
    const allRows = () => [...tbody.querySelectorAll('tr:not(.hidden-row)')];

    function render() {
        const rows = allRows();
        const total = rows.length;
        const pages = Math.ceil(total / perPage);
        rows.forEach((r, i) => r.style.display = (i >= (page-1)*perPage && i < page*perPage) ? '' : 'none');
        // Update pager info
        const info = pager.querySelector('.pager-info');
        if (info) info.textContent = `Showing ${Math.min((page-1)*perPage+1, total)}–${Math.min(page*perPage, total)} of ${total}`;
        // Render page buttons
        const btnWrap = pager.querySelector('.pagination-btns');
        if (btnWrap) {
            btnWrap.innerHTML = '';
            const prev = btn('‹', page > 1, () => { page--; render(); });
            btnWrap.appendChild(prev);
            for (let p = 1; p <= pages; p++) {
                const b = btn(p, true, () => { page = p; render(); });
                if (p === page) b.classList.add('active');
                btnWrap.appendChild(b);
            }
            const next = btn('›', page < pages, () => { page++; render(); });
            btnWrap.appendChild(next);
        }
    }

    function btn(label, enabled, onClick) {
        const b = document.createElement('button');
        b.className = 'page-btn';
        b.textContent = label;
        b.disabled = !enabled;
        b.addEventListener('click', onClick);
        return b;
    }

    render();
}

/* =========================================================
   EXPORT FUNCTIONS
   ========================================================= */
window.exportTableCSV = function(tableId, filename = 'gpcms_report.csv') {
    const table = document.getElementById(tableId);
    if (!table) return alert('Table not found');
    const rows = [...table.querySelectorAll('tr')];
    const csv  = rows.map(r =>
        [...r.querySelectorAll('th,td')]
            .map(c => '"' + c.innerText.replace(/"/g,'""').trim() + '"')
            .join(',')
    ).join('\n');
    downloadFile('\uFEFF' + csv, filename, 'text/csv;charset=utf-8;');
};

window.exportTableExcel = function(tableId, filename = 'gpcms_report.xls') {
    const table = document.getElementById(tableId);
    if (!table) return alert('Table not found');
    const html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"><style>th{background:#EDE2CC;font-weight:bold;}</style></head>
        <body>${table.outerHTML}</body></html>`;
    downloadFile(html, filename, 'application/vnd.ms-excel');
};

function downloadFile(content, filename, type) {
    const blob = new Blob([content], { type });
    const url  = URL.createObjectURL(blob);
    const a    = Object.assign(document.createElement('a'), { href: url, download: filename });
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

window.printPage = function() { window.print(); };

/* =========================================================
   FLOATING ACTION BUTTON
   ========================================================= */
function initFAB() {
    const fab     = document.getElementById('fabBtn');
    const fabMenu = document.getElementById('fabMenu');
    if (!fab) return;
    fab.addEventListener('click', () => {
        fab.classList.toggle('active');
        if (fabMenu) fabMenu.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
        if (!fab.contains(e.target) && fabMenu && !fabMenu.contains(e.target)) {
            fab.classList.remove('active');
            fabMenu.classList.remove('open');
        }
    });
}

/* =========================================================
   AUTO REFRESH
   ========================================================= */
function initAutoRefresh() {
    const toggle = document.getElementById('autoRefreshToggle');
    if (!toggle) return;
    let timer = null;

    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            timer = setInterval(() => { /* AJAX refresh placeholder */ console.log('Auto-refresh tick'); }, 30000);
            showToast('Auto-refresh enabled (every 30s)');
        } else {
            clearInterval(timer);
            showToast('Auto-refresh disabled');
        }
    });
}

/* =========================================================
   KEYBOARD SHORTCUTS
   ========================================================= */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        if (e.altKey) {
            switch (e.key) {
                case 'd': location.href = 'analytics_dashboard.php'; break;
                case 'c': location.href = 'category_report.php'; break;
                case 'v': location.href = 'village_report.php'; break;
                case 'r': location.href = 'pending_resolved_report.php'; break;
                case 'p': window.print(); break;
                case 'f': document.querySelector('.header-search input')?.focus(); break;
            }
        }
    });
}

/* =========================================================
   RIPPLE BUTTONS
   ========================================================= */
function initRippleButtons() {
    document.querySelectorAll('.ripple-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position:absolute;
                width:${Math.max(rect.width,rect.height)*2}px;
                height:${Math.max(rect.width,rect.height)*2}px;
                top:${e.clientY-rect.top-Math.max(rect.width,rect.height)}px;
                left:${e.clientX-rect.left-Math.max(rect.width,rect.height)}px;
                background:rgba(255,255,255,0.35);
                border-radius:50%;
                animation:ripple 0.5s ease forwards;
                pointer-events:none;
            `;
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
        });
    });
}

/* =========================================================
   TOAST NOTIFICATION
   ========================================================= */
function showToast(msg, duration = 3000) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;bottom:80px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = `
        background:#1A140E;color:#EDE2CC;padding:10px 18px;
        border-radius:8px;font-size:0.82rem;font-family:Poppins,sans-serif;
        box-shadow:0 4px 16px rgba(0,0,0,0.3);
        animation:fadeInUp 0.3s ease;
        border-left:3px solid #B99668;
    `;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, duration);
}

window.showToast = showToast;
