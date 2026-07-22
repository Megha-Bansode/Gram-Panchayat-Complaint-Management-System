/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module #5 - Complaint Administration & Reporting
 * Chart.js Integration & Analytics Visualizations
 */

document.addEventListener('DOMContentLoaded', () => {
  // Theme Color Tokens
  const primaryColor = '#8A724C';
  const secondaryColor = '#B99668';
  const accentColor = '#DCC9A7';
  const surfaceColor = '#EDE2CC';

  const pendingColor = '#E67E22';
  const assignedColor = '#2980B9';
  const inProgressColor = '#8E44AD';
  const resolvedColor = '#27AE60';
  const rejectedColor = '#C0392B';

  // Global Chart Defaults
  if (window.Chart) {
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#706253';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
  } else {
    console.warn('Chart.js library not loaded');
    return;
  }

  // --- 1. Category Distribution Bar Chart ---
  const catCanvas = document.getElementById('chart-category-distribution');
  if (catCanvas) {
    new Chart(catCanvas, {
      type: 'bar',
      data: {
        labels: ['Water Supply', 'Sanitation', 'Street Lights', 'Road Repair', 'Drainage', 'Health & Hygiene', 'Taxation'],
        datasets: [{
          label: 'Total Complaints',
          data: [142, 98, 85, 120, 64, 45, 30],
          backgroundColor: [primaryColor, secondaryColor, accentColor, '#A0825B', '#CBB28A', '#7A623C', '#5C482A'],
          borderRadius: 8,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(229, 217, 197, 0.4)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // --- 2. Complaint Status Pie / Donut Chart ---
  const statusCanvas = document.getElementById('chart-complaint-status');
  if (statusCanvas) {
    new Chart(statusCanvas, {
      type: 'doughnut',
      data: {
        labels: ['Pending', 'Assigned', 'In Progress', 'Resolved', 'Rejected'],
        datasets: [{
          data: [85, 110, 145, 220, 24],
          backgroundColor: [pendingColor, assignedColor, inProgressColor, resolvedColor, rejectedColor],
          hoverOffset: 6,
          borderWidth: 2,
          borderColor: '#FFFFFF'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        },
        cutout: '68%'
      }
    });
  }

  // --- 3. Monthly Complaints Line Chart ---
  const monthlyCanvas = document.getElementById('chart-monthly-trend');
  if (monthlyCanvas) {
    const ctx = monthlyCanvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(138, 114, 76, 0.35)');
    gradient.addColorStop(1, 'rgba(138, 114, 76, 0.0)');

    new Chart(monthlyCanvas, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Complaints Registered',
          data: [42, 58, 65, 80, 95, 110, 134, 125, 140, 155, 130, 168],
          borderColor: primaryColor,
          backgroundColor: gradient,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: primaryColor,
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(229, 217, 197, 0.4)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // --- 4. Weekly Trend Area Chart ---
  const weeklyCanvas = document.getElementById('chart-weekly-trend');
  if (weeklyCanvas) {
    new Chart(weeklyCanvas, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'This Week',
          data: [28, 35, 42, 38, 48, 22, 18],
          borderColor: secondaryColor,
          backgroundColor: 'rgba(185, 150, 104, 0.25)',
          fill: true,
          tension: 0.35
        }, {
          label: 'Last Week',
          data: [20, 28, 30, 32, 40, 18, 12],
          borderColor: '#9E9E9E',
          borderDash: [5, 5],
          tension: 0.35
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(229, 217, 197, 0.4)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // --- 5. Department Performance Donut Chart ---
  const deptCanvas = document.getElementById('chart-department-performance');
  if (deptCanvas) {
    new Chart(deptCanvas, {
      type: 'pie',
      data: {
        labels: ['Public Works (PWD)', 'Water & Sanitation', 'Electrical / Streetlight', 'Health & Family Welfare', 'Revenue & Finance'],
        datasets: [{
          data: [92, 88, 76, 94, 82],
          backgroundColor: [primaryColor, secondaryColor, accentColor, resolvedColor, assignedColor]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  }

  // --- 6. Officer Performance Radar Chart ---
  const radarCanvas = document.getElementById('chart-officer-radar');
  if (radarCanvas) {
    new Chart(radarCanvas, {
      type: 'radar',
      data: {
        labels: ['Resolution Speed', 'Citizen Feedback', 'SLA Adherence', 'Accuracy', 'Verification Rate'],
        datasets: [{
          label: 'Gram Sevak A. Patil',
          data: [90, 85, 95, 88, 92],
          borderColor: primaryColor,
          backgroundColor: 'rgba(138, 114, 76, 0.25)'
        }, {
          label: 'Officer R. Deshmukh',
          data: [75, 80, 82, 90, 85],
          borderColor: assignedColor,
          backgroundColor: 'rgba(41, 128, 185, 0.25)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          r: { angleLines: { color: 'rgba(229, 217, 197, 0.5)' }, grid: { color: 'rgba(229, 217, 197, 0.5)' } }
        }
      }
    });
  }

  // Trigger Live Number Counters
  if (window.GPCMS && GPCMS.animateCounters) {
    GPCMS.animateCounters();
  }
});
