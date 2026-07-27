/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Script
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Full frontend interactions, LocalStorage state & activity log persistence, custom floating dropdowns, mock data search/filter, live file upload previews, count-up metric animations, and toast alerts.
 */

// Central Mock Complaints Dataset with LocalStorage State Persistence
const STORAGE_KEY = 'gpcms_field_complaints_data_v4';
const ACTIVITY_STORAGE_KEY = 'gpcms_field_activity_logs_v4';

const DEFAULT_MOCK_COMPLAINTS = [
  { 
    id: 'CMP-0012', 
    citizen: 'Ramesh Kumar', 
    phone: '+91 98765 43210', 
    title: 'Road Potholes Repair near Primary School', 
    category: 'Roads & Infrastructure', 
    priority: 'High', 
    status: 'resolved', 
    location: 'Shivapur Ward 2, Near Primary School Gate', 
    date: '23 May 2026', 
    desc: 'The main road leading to the Gram Panchayat Primary School has developed multiple deep potholes following recent rainfall.' 
  },
  { 
    id: 'CMP-0024', 
    citizen: 'Suresh Patil', 
    phone: '+91 98123 45678', 
    title: 'Street Light Outage on Main Bazaar', 
    category: 'Electricity Supply', 
    priority: 'Medium', 
    status: 'assigned', 
    location: 'Shivapur Ward 1, Main Bazaar', 
    date: '22 May 2026', 
    desc: 'Street light out for 3 consecutive days in front of the Gram Panchayat office causing evening visibility issues.' 
  },
  { 
    id: 'CMP-0035', 
    citizen: 'Sunita Deshmukh', 
    phone: '+91 97654 32109', 
    title: 'Water Pipeline Leakage at Temple Road', 
    category: 'Water & Sanitation', 
    priority: 'High', 
    status: 'in_progress', 
    location: 'Shivapur Ward 3, Temple Road', 
    date: '21 May 2026', 
    desc: 'Main water distribution pipe leaking heavily near temple crossroad, leading to water wastage and road waterlogging.' 
  },
  { 
    id: 'CMP-0041', 
    citizen: 'Anil Gawande', 
    phone: '+91 95432 10987', 
    title: 'Drainage Overflow at Naka No 2', 
    category: 'Sanitation & Drainage', 
    priority: 'Low', 
    status: 'assigned', 
    location: 'Shivapur Ward 1, Naka No 2', 
    date: '20 May 2026', 
    desc: 'Open drainage overflowing after heavy rain causing unpleasant odor and blockage near local market shops.' 
  },
  { 
    id: 'CMP-0045', 
    citizen: 'Prakash Shinde', 
    phone: '+91 94321 09876', 
    title: 'Garbage Collection Delayed', 
    category: 'Sanitation & Drainage', 
    priority: 'Medium', 
    status: 'in_progress', 
    location: 'Shivapur Ward 4, Market Area', 
    date: '19 May 2026', 
    desc: 'Garbage collection truck did not arrive for two days causing waste accumulation near local market shops.' 
  }
];

const DEFAULT_ACTIVITIES = [
  {
    id: 1,
    type: 'photo',
    title: 'Progress Photo Uploaded',
    desc: 'Uploaded work evidence photo for <a href="save_progress.php?id=CMP-0012" class="cmp-link">CMP-0012</a>',
    time: 'Just now'
  },
  {
    id: 2,
    type: 'resolved',
    title: 'Complaint Resolved',
    desc: '<a href="complaint_details.php?id=CMP-0012" class="cmp-link">CMP-0012</a> marked as Resolved',
    time: 'Just now'
  },
  {
    id: 3,
    type: 'status',
    title: 'Status Updated',
    desc: 'CMP-0012 status changed to <span class="badge-status-subtle">IN PROGRESS</span>',
    time: 'Just now'
  }
];

function getComplaintsData() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) {
      return JSON.parse(raw);
    }
  } catch (e) {
    console.warn('LocalStorage error:', e);
  }
  localStorage.setItem(STORAGE_KEY, JSON.stringify(DEFAULT_MOCK_COMPLAINTS));
  return DEFAULT_MOCK_COMPLAINTS;
}

function saveComplaintsData(data) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  } catch (e) {
    console.error('LocalStorage write error:', e);
  }
}

function getActivityLogs() {
  try {
    const raw = localStorage.getItem(ACTIVITY_STORAGE_KEY);
    if (raw) {
      return JSON.parse(raw);
    }
  } catch (e) {
    console.warn('Activity LocalStorage error:', e);
  }
  localStorage.setItem(ACTIVITY_STORAGE_KEY, JSON.stringify(DEFAULT_ACTIVITIES));
  return DEFAULT_ACTIVITIES;
}

function addActivityLog(type, title, desc) {
  const activities = getActivityLogs();
  const newEntry = {
    id: Date.now(),
    type: type,
    title: title,
    desc: desc,
    time: 'Just now'
  };
  activities.unshift(newEntry);
  if (activities.length > 10) activities.pop();
  try {
    localStorage.setItem(ACTIVITY_STORAGE_KEY, JSON.stringify(activities));
  } catch (e) {
    console.error('Save activity error:', e);
  }
}

let MOCK_COMPLAINTS = getComplaintsData();

class FieldToast {
  static show(message, icon = 'bi-check-circle-fill') {
    let container = document.querySelector('.toast-container-officer');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container-officer';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast-officer';
    toast.innerHTML = `<i class="bi ${icon} text-warning fs-5"></i> <span>${escapeHtml(message)}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('hiding');
      setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 350);
    }, 3000);
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}

document.addEventListener('DOMContentLoaded', () => {
  MOCK_COMPLAINTS = getComplaintsData();

  // Live Date & Time Clock Updater
  function updateLiveClock() {
    const dateEl = document.getElementById('liveDateStr');
    const timeEl = document.getElementById('liveTimeStr');
    if (!dateEl && !timeEl) return;

    const now = new Date();
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const dayName = days[now.getDay()];
    const dateNum = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();

    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'pm' : 'am';

    hours = hours % 12;
    hours = hours ? hours : 12;
    const hoursStr = String(hours).padStart(2, '0');

    if (dateEl) dateEl.textContent = `${dayName}, ${dateNum} ${monthName}, ${year}`;
    if (timeEl) timeEl.textContent = `${hoursStr}:${minutes}:${seconds} ${ampm}`;
  }

  updateLiveClock();
  setInterval(updateLiveClock, 1000);

  // 1. Responsive Sidebar Navigation Drawer Toggler
  const sidebar = document.querySelector('.app-sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');

  let overlay = document.querySelector('.sidebar-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
  }

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }

  // 2. Notification Bell Dropdown Toggle Listener
  const bellBtn = document.getElementById('notificationBellBtn');
  const dropdown = document.getElementById('notificationDropdown');

  if (bellBtn && dropdown) {
    bellBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
        dropdown.classList.remove('show');
      }
    });
  }

  // 3. Dynamic Dashboard Metrics & Recent Table Sync
  const dashboardTableBody = document.getElementById('dashboardTableBody');
  if (dashboardTableBody || document.querySelector('.counter-metric')) {
    const liveData = getComplaintsData();

    const assignedCount = liveData.filter(c => c.status === 'assigned').length;
    const inProgressCount = liveData.filter(c => c.status === 'in_progress').length;
    const resolvedCount = liveData.filter(c => c.status === 'resolved').length;

    const counters = document.querySelectorAll('.counter-metric');
    counters.forEach(c => {
      const parentLink = c.closest('a');
      if (parentLink) {
        const href = parentLink.getAttribute('href');
        if (href.includes('status=assigned')) c.setAttribute('data-target', assignedCount);
        else if (href.includes('status=in_progress')) c.setAttribute('data-target', inProgressCount);
        else if (href.includes('status=resolved')) c.setAttribute('data-target', resolvedCount + 12);
      }
    });

    if (dashboardTableBody) {
      dashboardTableBody.innerHTML = liveData.map(item => `
        <tr>
          <td class="fw-bold text-dark">${item.id}</td>
          <td>${escapeHtml(item.title)}</td>
          <td>${escapeHtml(item.category)}</td>
          <td>${escapeHtml(item.location)}</td>
          <td>
            ${item.status === 'assigned' 
              ? '<span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-blue" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #1565C0 !important; display: inline-block !important;"></span> Assigned</span>'
              : item.status === 'in_progress'
              ? '<span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-orange" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #D35400 !important; display: inline-block !important;"></span> In Progress</span>'
              : '<span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 5px 13px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.45rem !important;"><span class="status-badge-dot-green" style="width: 7px !important; height: 7px !important; border-radius: 50% !important; background-color: #2E7D32 !important; display: inline-block !important;"></span> Resolved</span>'}
          </td>
          <td class="text-end">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="save_progress.php?id=${item.id}" class="btn-table-update" style="background-color: #6E5A3B !important; color: #FFFFFF !important; font-weight: 700 !important; padding: 0.38rem 1rem !important; border-radius: 10px !important; font-size: 0.82rem !important; text-decoration: none !important;">Update</a>
              <i class="bi bi-three-dots-vertical table-three-dots" style="color: #8C7B6B; cursor: pointer;"></i>
            </div>
          </td>
        </tr>
      `).join('');
    }
  }

  // 4. Dynamic Recent Activity Vertical Timeline Renderer
  function renderActivityTimeline() {
    const container = document.getElementById('activityTimelineContainer');
    if (!container) return;

    const activities = getActivityLogs();
    if (activities.length === 0) {
      container.innerHTML = '<div class="text-muted small py-3 text-center">No recent activity recorded.</div>';
      return;
    }

    container.innerHTML = activities.slice(0, 5).map((act, index) => {
      let nodeStyle = 'width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #1E88E5 !important; color: #FFFFFF !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 0.95rem !important; flex-shrink: 0 !important; box-shadow: 0 3px 10px rgba(30, 136, 229, 0.25) !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;';
      let iconClass = 'bi-camera-fill';
      let iconStyle = 'color: #FFFFFF !important;';

      if (act.type === 'status') {
        nodeStyle = 'width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #FFF3E0 !important; border: 1.5px solid #FFCC80 !important; color: #E65100 !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 0.95rem !important; flex-shrink: 0 !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;';
        iconClass = 'bi-pencil-square';
        iconStyle = 'color: #E65100 !important;';
      } else if (act.type === 'resolved') {
        nodeStyle = 'width: 36px !important; height: 36px !important; border-radius: 50% !important; background-color: #E8F5E9 !important; border: 1.5px solid #A5D6A7 !important; color: #2E7D32 !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 1rem !important; flex-shrink: 0 !important; position: absolute !important; left: 0 !important; top: 2px !important; z-index: 2 !important;';
        iconClass = 'bi-check-lg';
        iconStyle = 'color: #2E7D32 !important;';
      }

      return `
        <div class="timeline-item animate-fade-in-up delay-${Math.min(index + 1, 4)}" style="position: relative !important; margin-bottom: 1.25rem !important;">
          <div class="timeline-badge-node" style="${nodeStyle}" title="${escapeHtml(act.title)}">
            <i class="bi ${iconClass}" style="${iconStyle}"></i>
          </div>
          <div class="timeline-body" style="margin-left: 50px !important;">
            <div class="timeline-title" style="font-weight: 800 !important; color: #241D15 !important; font-size: 0.88rem !important; line-height: 1.2 !important;">${escapeHtml(act.title)}</div>
            <div class="timeline-desc" style="color: #6E6255 !important; font-size: 0.82rem !important; margin-top: 3px !important; line-height: 1.4 !important;">${act.desc}</div>
            <div class="timeline-time" style="font-size: 0.75rem !important; color: #8C7B6B !important; margin-top: 3px !important; display: flex !important; align-items: center !important; gap: 0.25rem !important;"><i class="bi bi-clock me-1"></i>${escapeHtml(act.time)}</div>
          </div>
        </div>
      `;
    }).join('');
  }

  renderActivityTimeline();

  // 5. Animated Count-Up Hero Metric Effect
  function animateMetricCounters() {
    const counters = document.querySelectorAll('.counter-metric');
    counters.forEach(counter => {
      const target = parseInt(counter.getAttribute('data-target') || '0', 10);
      if (target === 0) {
        counter.textContent = '0';
        return;
      }

      const duration = 800;
      const startTime = performance.now();

      function updateCount(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        const easeOut = 1 - Math.pow(1 - progress, 3);
        const currentCount = Math.floor(easeOut * target);

        counter.textContent = currentCount;

        if (progress < 1) {
          requestAnimationFrame(updateCount);
        } else {
          counter.textContent = target;
        }
      }

      requestAnimationFrame(updateCount);
    });
  }

  animateMetricCounters();

  // 6. Assigned Complaints Search & Custom Dropdown Controller
  const searchInput = document.getElementById('searchComplaint');
  const btnClearSearch = document.getElementById('btnClearSearch');
  const categorySelect = document.getElementById('filterCategory');
  const statusSelect = document.getElementById('filterStatus');
  const dateSelect = document.getElementById('filterDate');

  const categoryDropdownTrigger = document.getElementById('categoryDropdownTrigger');
  const statusDropdownTrigger = document.getElementById('statusDropdownTrigger');
  const dateDropdownTrigger = document.getElementById('dateDropdownTrigger');
  const btnResetFilter = document.getElementById('btnResetFilter');
  const filterForm = document.getElementById('filterForm');
  const tableBody = document.getElementById('assignedTableBody');
  const emptyState = document.getElementById('emptyState');
  const tableContainer = document.getElementById('tableContainer');

  const customItems = document.querySelectorAll('.custom-dropdown-item');
  customItems.forEach(item => {
    item.addEventListener('click', (e) => {
      const filterType = item.getAttribute('data-filter');
      const targetInput = item.getAttribute('data-target-input');
      const val = item.getAttribute('data-value');
      const flexChild = item.querySelector('.d-flex');
      const innerHTMLContent = flexChild ? flexChild.innerHTML : item.innerHTML;

      const parentMenu = item.closest('.custom-dropdown-menu');
      if (parentMenu) {
        parentMenu.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
      }
      item.classList.add('active');

      if (filterType === 'category') {
        if (categorySelect) categorySelect.value = val;
        if (categoryDropdownTrigger) {
          const label = categoryDropdownTrigger.querySelector('.selected-label');
          if (label) label.textContent = val || 'All Categories';
        }
        renderAssignedTable();
      } else if (filterType === 'status') {
        if (statusSelect) statusSelect.value = val;
        if (statusDropdownTrigger) {
          const label = statusDropdownTrigger.querySelector('.selected-label');
          if (label) label.innerHTML = val ? innerHTMLContent : 'All Status';
        }
        renderAssignedTable();
      } else if (filterType === 'date') {
        if (dateSelect) dateSelect.value = val;
        if (dateDropdownTrigger) {
          const label = dateDropdownTrigger.querySelector('.selected-label');
          if (label) {
            const displayNames = { '': 'Select Date Range', 'today': 'Today', 'this_week': 'This Week', 'this_month': 'This Month' };
            label.textContent = displayNames[val] || 'Select Date Range';
          }
        }
        renderAssignedTable();
      } else if (targetInput === 'status') {
        const hiddenStatus = document.getElementById('status');
        const trigger = document.getElementById('statusSaveDropdownTrigger');
        if (hiddenStatus) hiddenStatus.value = val;
        if (trigger) {
          const label = trigger.querySelector('.selected-label');
          if (label) label.innerHTML = innerHTMLContent;
        }
      }
    });
  });

  const urlParams = new URLSearchParams(window.location.search);
  const initialStatusParam = urlParams.get('status');
  if (initialStatusParam && statusSelect) {
    statusSelect.value = initialStatusParam;
    const matchingItem = document.querySelector(`.custom-dropdown-item[data-value="${initialStatusParam}"]`);
    if (matchingItem) {
      matchingItem.click();
    }
  }

  function renderAssignedTable() {
    if (!tableBody) return;

    const liveComplaints = getComplaintsData();
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const categoryVal = categorySelect ? categorySelect.value : '';
    const statusVal = statusSelect ? statusSelect.value : '';
    const dateVal = dateSelect ? dateSelect.value : '';

    if (btnClearSearch) {
      if (searchTerm.length > 0) {
        btnClearSearch.classList.remove('d-none');
      } else {
        btnClearSearch.classList.add('d-none');
      }
    }

    const filtered = liveComplaints.filter(item => {
      const matchSearch = !searchTerm || 
        item.id.toLowerCase().includes(searchTerm) || 
        item.citizen.toLowerCase().includes(searchTerm) || 
        item.location.toLowerCase().includes(searchTerm) ||
        item.title.toLowerCase().includes(searchTerm);

      const matchCategory = !categoryVal || item.category.toLowerCase() === categoryVal.toLowerCase();
      const matchStatus = !statusVal || item.status === statusVal;

      let matchDate = true;
      if (dateVal === 'today') {
        matchDate = item.date.includes('23 May');
      } else if (dateVal === 'this_week') {
        matchDate = item.date.includes('May 2026');
      } else if (dateVal === 'this_month') {
        matchDate = item.date.includes('May 2026');
      }

      return matchSearch && matchCategory && matchStatus && matchDate;
    });

    if (filtered.length === 0) {
      tableBody.innerHTML = '';
      if (emptyState) emptyState.classList.remove('d-none');
      if (tableContainer) tableContainer.classList.add('d-none');
    } else {
      if (emptyState) emptyState.classList.add('d-none');
      if (tableContainer) tableContainer.classList.remove('d-none');

      tableBody.innerHTML = filtered.map(item => `
        <tr>
          <td class="fw-bold text-dark">${item.id}</td>
          <td class="fw-semibold text-dark" style="max-width: 220px;">${escapeHtml(item.title)}</td>
          <td>${escapeHtml(item.category)}</td>
          <td><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-geo-alt"></i> ${escapeHtml(item.location)}</span></td>
          <td><span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size: 0.84rem;"><i class="bi bi-calendar-event"></i> ${item.date}</span></td>
          <td>
            ${item.status === 'assigned' 
              ? '<span class="status-badge assigned" style="background-color: #E3F2FD !important; color: #1565C0 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #1565C0; display: inline-block;"></span> Assigned</span>'
              : item.status === 'in_progress'
              ? '<span class="status-badge in-progress" style="background-color: #FFF8E7 !important; color: #D35400 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #D35400; display: inline-block;"></span> In Progress</span>'
              : '<span class="status-badge resolved" style="background-color: #E8F5E9 !important; color: #2E7D32 !important; font-size: 0.78rem !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 50px !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important;"><span style="width: 7px; height: 7px; border-radius: 50%; background-color: #2E7D32; display: inline-block;"></span> Resolved</span>'}
          </td>
          <td class="text-end">
            <div class="d-inline-flex align-items-center gap-2">
              <a href="complaint_details.php?id=${item.id}" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-eye me-1"></i> View
              </a>
              <a href="save_progress.php?id=${item.id}" class="btn btn-sm text-white" style="background-color: #5E4D34; border-radius: 8px; font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Update Progress
              </a>
              <i class="bi bi-three-dots-vertical text-muted cursor-pointer fs-6 ms-1"></i>
            </div>
          </td>
        </tr>
      `).join('');
    }
  }

  if (searchInput) searchInput.addEventListener('input', renderAssignedTable);

  if (btnClearSearch) {
    btnClearSearch.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      renderAssignedTable();
      if (searchInput) searchInput.focus();
    });
  }

  function resetAllFilters() {
    if (searchInput) searchInput.value = '';
    if (categorySelect) categorySelect.value = '';
    if (statusSelect) statusSelect.value = '';
    if (dateSelect) dateSelect.value = '';

    if (categoryDropdownTrigger) {
      const label = categoryDropdownTrigger.querySelector('.selected-label');
      if (label) label.textContent = 'All Categories';
    }
    if (statusDropdownTrigger) {
      const label = statusDropdownTrigger.querySelector('.selected-label');
      if (label) label.textContent = 'All Status';
    }
    if (dateDropdownTrigger) {
      const label = dateDropdownTrigger.querySelector('.selected-label');
      if (label) label.textContent = 'Select Date Range';
    }

    document.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
    document.querySelectorAll('.custom-dropdown-item[data-value=""]').forEach(i => i.classList.add('active'));

    renderAssignedTable();
  }

  if (btnResetFilter) {
    btnResetFilter.addEventListener('click', (e) => {
      e.preventDefault();
      resetAllFilters();
    });
  }

  if (filterForm) {
    filterForm.addEventListener('reset', () => {
      setTimeout(resetAllFilters, 50);
    });
  }

  // Initial Table Render
  renderAssignedTable();

  // 7. Dynamic Data Binding for Details / Save Progress View
  const currentComplaintId = urlParams.get('id') || 'CMP-0012';
  const liveComplaintsList = getComplaintsData();
  const activeComplaint = liveComplaintsList.find(c => c.id === currentComplaintId) || liveComplaintsList[0];

  const detailTitle = document.getElementById('detailTitle');
  const detailId = document.getElementById('detailId');
  const detailCategory = document.getElementById('detailCategory');
  const detailCitizen = document.getElementById('detailCitizen');
  const detailPhone = document.getElementById('detailPhone');
  const detailLocation = document.getElementById('detailLocation');
  const detailDesc = document.getElementById('detailDesc');
  const detailStatusBadge = document.getElementById('detailStatusBadge');

  if (detailTitle) detailTitle.textContent = activeComplaint.title;
  if (detailId) detailId.textContent = activeComplaint.id;
  if (detailCategory) detailCategory.textContent = activeComplaint.category;
  if (detailCitizen) detailCitizen.textContent = activeComplaint.citizen;
  if (detailPhone) detailPhone.textContent = activeComplaint.phone;
  if (detailLocation) detailLocation.textContent = activeComplaint.location;
  if (detailDesc) detailDesc.textContent = activeComplaint.desc;

  if (detailStatusBadge && activeComplaint) {
    if (activeComplaint.status === 'assigned') {
      detailStatusBadge.className = 'status-badge assigned';
      detailStatusBadge.innerHTML = '<i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned';
    } else if (activeComplaint.status === 'in_progress') {
      detailStatusBadge.className = 'status-badge in-progress';
      detailStatusBadge.innerHTML = '<i class="bi bi-clock"></i> In Progress';
    } else if (activeComplaint.status === 'resolved') {
      detailStatusBadge.className = 'status-badge resolved';
      detailStatusBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Resolved';
    }
  }

  const saveId = document.getElementById('summaryComplaintId');
  const saveTitle = document.getElementById('summaryComplaintTitle');
  const saveCategory = document.getElementById('summaryCategory');
  const saveLocation = document.getElementById('summaryLocation');
  const saveDesc = document.getElementById('summaryDesc');
  const hiddenIdInput = document.getElementById('hiddenComplaintId');

  if (saveId) saveId.textContent = activeComplaint.id;
  if (saveTitle) saveTitle.textContent = activeComplaint.title;
  if (saveCategory) saveCategory.textContent = activeComplaint.category;
  if (saveLocation) saveLocation.textContent = activeComplaint.location;
  if (saveDesc) saveDesc.textContent = activeComplaint.desc;
  if (hiddenIdInput) hiddenIdInput.value = activeComplaint.id;

  const noteInputElem = document.getElementById('note');
  const charCountElem = document.getElementById('charCount');
  if (noteInputElem && charCountElem) {
    charCountElem.textContent = noteInputElem.value.length;
    noteInputElem.addEventListener('input', () => {
      charCountElem.textContent = noteInputElem.value.length;
    });
  }

  if (document.getElementById('statusSaveDropdownTrigger') && activeComplaint) {
    const hiddenStatus = document.getElementById('status');
    if (hiddenStatus) hiddenStatus.value = activeComplaint.status;

    const matchingSaveItem = document.querySelector(`.custom-dropdown-item[data-value="${activeComplaint.status}"][data-target-input="status"]`);
    if (matchingSaveItem) {
      matchingSaveItem.click();
    }
  }

  // 8. File Upload Live Image Previews & Dropzone Click Trigger
  function bindFilePreview(inputId, previewZoneId) {
    const input = document.getElementById(inputId);
    const zone = document.getElementById(previewZoneId);
    if (!input || !zone) return;

    zone.addEventListener('click', (e) => {
      if (e.target !== input) {
        input.click();
      }
    });

    input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        if (!file.type.startsWith('image/')) {
          FieldToast.show('Please select a valid image file (JPG/PNG)', 'bi-exclamation-triangle-fill');
          input.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = (evt) => {
          let existingImg = zone.querySelector('.upload-preview-img');
          if (!existingImg) {
            existingImg = document.createElement('img');
            existingImg.className = 'upload-preview-img';
            zone.appendChild(existingImg);
          }
          existingImg.src = evt.target.result;
          FieldToast.show(`${file.name} selected for upload`, 'bi-image');
        };
        reader.readAsDataURL(file);
      }
    });
  }

  bindFilePreview('before_photo', 'beforeUploadZone');
  bindFilePreview('after_photo', 'afterUploadZone');

  // 9. Save Progress Form Submission & Dynamic Activity Log Logging
  const saveForm = document.getElementById('saveProgressForm');
  const btnSaveSubmit = document.getElementById('btnSaveSubmit');

  if (saveForm) {
    saveForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const hiddenIdInput = document.getElementById('hiddenComplaintId');
      const hiddenStatus = document.getElementById('status');
      const noteInput = document.getElementById('note');
      const beforePhotoInput = document.getElementById('before_photo');
      const afterPhotoInput = document.getElementById('after_photo');

      if (!saveForm.checkValidity() || !noteInput.value.trim()) {
        e.stopPropagation();
        saveForm.classList.add('was-validated');
        FieldToast.show('Please complete all required form fields.', 'bi-exclamation-circle-fill');
        return;
      }

      const complaintIdToUpdate = hiddenIdInput ? hiddenIdInput.value : 'CMP-0012';
      const newStatus = hiddenStatus ? hiddenStatus.value : 'in_progress';

      // Update complaint status in LocalStorage
      const allComplaints = getComplaintsData();
      const targetIndex = allComplaints.findIndex(c => c.id === complaintIdToUpdate);
      if (targetIndex !== -1) {
        allComplaints[targetIndex].status = newStatus;
        if (noteInput && noteInput.value.trim()) {
          allComplaints[targetIndex].lastNote = noteInput.value.trim();
        }
        saveComplaintsData(allComplaints);
        MOCK_COMPLAINTS = allComplaints;
      }

      // Automatically Log Activity Entry into Recent Activity Timeline
      const readableStatus = newStatus.replace('_', ' ').toUpperCase();

      if (newStatus === 'resolved') {
        addActivityLog(
          'resolved',
          'Complaint Resolved',
          `<a href="complaint_details.php?id=${complaintIdToUpdate}" class="cmp-link">${complaintIdToUpdate}</a> marked as Resolved`
        );
      } else {
        addActivityLog(
          'status',
          'Status Updated',
          `<a href="save_progress.php?id=${complaintIdToUpdate}" class="cmp-link">${complaintIdToUpdate}</a> status changed to <span class="badge-status-subtle">${readableStatus}</span>`
        );
      }

      const hasBefore = beforePhotoInput && beforePhotoInput.files && beforePhotoInput.files.length > 0;
      const hasAfter = afterPhotoInput && afterPhotoInput.files && afterPhotoInput.files.length > 0;

      if (hasBefore || hasAfter) {
        addActivityLog(
          'photo',
          'Progress Photo Uploaded',
          `Uploaded work evidence photo for <a href="save_progress.php?id=${complaintIdToUpdate}" class="cmp-link">${complaintIdToUpdate}</a>`
        );
      }

      if (btnSaveSubmit) {
        btnSaveSubmit.disabled = true;
        btnSaveSubmit.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> Saving Progress...`;
      }

      FieldToast.show(`Complaint ${complaintIdToUpdate} status updated to ${readableStatus}!`, 'bi-check-circle-fill');

      setTimeout(() => {
        window.location.href = 'assigned_complaints.php';
      }, 1400);
    });
  }
});
