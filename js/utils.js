/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module #5 - Complaint Administration & Reporting
 * Utilities & Helper Scripts (Vanilla JS)
 */

window.GPCMS = window.GPCMS || {};

/**
 * Toast Notification System
 */
GPCMS.showToast = function (message, type = 'info', title = '') {
  let toastContainer = document.getElementById('gpcms-toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'gpcms-toast-container';
    toastContainer.className = 'toast-container-custom';
    document.body.appendChild(toastContainer);
  }

  const iconMap = {
    success: 'bi-check-circle-fill text-success',
    error: 'bi-x-circle-fill text-danger',
    warning: 'bi-exclamation-triangle-fill text-warning',
    info: 'bi-info-circle-fill text-primary'
  };

  const toast = document.createElement('div');
  toast.className = `toast-custom toast-${type}`;
  toast.innerHTML = `
    <i class="bi ${iconMap[type] || iconMap.info} fs-4"></i>
    <div class="flex-grow-1">
      ${title ? `<div class="fw-bold fs-6">${title}</div>` : ''}
      <div class="small">${message}</div>
    </div>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
  `;

  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'slideInRight 0.3s reverse forwards';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
};

/**
 * Real-Time Clock & Date Header Update
 */
GPCMS.initHeaderClock = function () {
  const timeEl = document.getElementById('live-time');
  const dateEl = document.getElementById('live-date');

  function update() {
    const now = new Date();
    if (timeEl) {
      timeEl.textContent = now.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      });
    }
    if (dateEl) {
      dateEl.textContent = now.toLocaleDateString('en-IN', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
    }
  }

  update();
  setInterval(update, 1000);
};

/**
 * Theme Toggler (Dark / Light)
 */
GPCMS.initTheme = function () {
  const currentTheme = localStorage.getItem('gpcms-theme') || 'light';
  document.documentElement.setAttribute('data-bs-theme', currentTheme);

  const themeBtn = document.getElementById('dark-mode-toggle');
  if (themeBtn) {
    const updateIcon = (theme) => {
      const icon = themeBtn.querySelector('i');
      if (icon) {
        icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
      }
    };
    updateIcon(currentTheme);

    themeBtn.addEventListener('click', () => {
      const active = document.documentElement.getAttribute('data-bs-theme');
      const next = active === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', next);
      localStorage.setItem('gpcms-theme', next);
      updateIcon(next);
      GPCMS.showToast(`Switched to ${next} mode`, 'info');
    });
  }
};

/**
 * Animated Live Counters
 */
GPCMS.animateCounters = function () {
  const counters = document.querySelectorAll('.animate-num');
  counters.forEach(counter => {
    const target = +counter.getAttribute('data-target') || parseInt(counter.textContent) || 0;
    let count = 0;
    const speed = target / 30;

    const updateCount = () => {
      count += speed;
      if (count < target) {
        counter.textContent = Math.ceil(count).toLocaleString('en-IN');
        setTimeout(updateCount, 25);
      } else {
        counter.textContent = target.toLocaleString('en-IN');
      }
    };
    updateCount();
  });
};

/**
 * Export Table to CSV
 */
GPCMS.exportTableToCSV = function (tableId, filename = 'export.csv') {
  const table = document.getElementById(tableId);
  if (!table) {
    GPCMS.showToast('Table element not found', 'error');
    return;
  }

  let csv = [];
  const rows = table.querySelectorAll('tr');

  for (let i = 0; i < rows.length; i++) {
    const rowEl = rows[i];
    
    // Skip empty state placeholder row and hidden rows (filtered out)
    if (rowEl.style.display === 'none' || rowEl.closest('#no-complaints-data') || rowEl.offsetParent === null) {
      continue;
    }

    const row = [], cols = rowEl.querySelectorAll('td, th');
    if (cols.length === 0) continue;

    // Export row cols except last action column
    for (let j = 0; j < cols.length - 1; j++) {
      let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
      row.push('"' + data + '"');
    }
    csv.push(row.join(','));
  }

  // Prepend UTF-8 BOM so Excel opens regional text correctly
  const csvContent = '\uFEFF' + csv.join('\n');
  const csvBlob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const downloadLink = document.createElement('a');
  downloadLink.download = filename;
  downloadLink.href = window.URL.createObjectURL(csvBlob);
  downloadLink.style.display = 'none';
  document.body.appendChild(downloadLink);
  downloadLink.click();
  downloadLink.remove();

  GPCMS.showToast(`Exported table to ${filename}`, 'success');
};

/**
 * Print View Helper
 */
GPCMS.printCurrentPage = function () {
  window.print();
};

document.addEventListener('DOMContentLoaded', () => {
  GPCMS.initHeaderClock();
  GPCMS.initTheme();
});
