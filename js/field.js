/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Script
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Full frontend interactions, LocalStorage state & activity log persistence, custom floating dropdowns, mock data search/filter, live file upload previews, count-up metric animations, and toast alerts.
 */

// Central Mock Complaints Dataset with LocalStorage State Persistence
const STORAGE_KEY = 'gpcms_field_complaints_data_v2';
const ACTIVITY_STORAGE_KEY = 'gpcms_field_activity_logs_v2';

const DEFAULT_MOCK_COMPLAINTS = [
  { 
    id: 'CMP-0012', 
    citizen: 'Ramesh Kumar', 
    phone: '+91 98765 43210', 
    title: 'Road Potholes Repair near Primary School', 
    category: 'Roads & Infrastructure', 
    priority: 'High', 
    status: 'in_progress', 
    location: 'Shivapur Ward 2, Near Primary School Gate', 
    date: '18 Jul 2026', 
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
    date: '20 Jul 2026', 
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
    date: '21 Jul 2026', 
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
    date: '21 Jul 2026', 
    desc: 'Open drainage overflowing after heavy rain causing unpleasant odor and blockage near local market shops.' 
  }
];

const DEFAULT_ACTIVITIES = [
  {
    id: 1,
    type: 'photo',
    title: 'Progress Photo Uploaded',
    desc: 'Added before photo for <a href="save_progress.php?id=CMP-0012" class="cmp-link">CMP-0012</a>',
    time: '2 hours ago'
  },
  {
    id: 2,
    type: 'status',
    title: 'Status Updated',
    desc: '<a href="save_progress.php?id=CMP-0035" class="cmp-link">CMP-0035</a> changed to <span class="badge-status-subtle">IN PROGRESS</span>',
    time: 'Yesterday at 4:30 PM'
  },
  {
    id: 3,
    type: 'resolved',
    title: 'Complaint Resolved',
    desc: '<a href="complaint_details.php?id=CMP-0008" class="cmp-link">CMP-0008</a> marked as Resolved',
    time: '20 July 2026'
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
              ? '<span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span>'
              : item.status === 'in_progress'
              ? '<span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span>'
              : '<span class="status-badge resolved"><i class="bi bi-check-circle-fill"></i> Resolved</span>'}
          </td>
          <td class="text-end">
            <a href="save_progress.php?id=${item.id}" class="btn btn-sm text-white" style="background-color: var(--primary-color); border-radius: 6px;">Update</a>
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
      let nodeClass = 'node-blue';
      let iconClass = 'bi-camera-fill';

      if (act.type === 'status') {
        nodeClass = 'node-orange';
        iconClass = 'bi-arrow-repeat';
      } else if (act.type === 'resolved') {
        nodeClass = 'node-green';
        iconClass = 'bi-check-circle-fill';
      }

      return `
        <div class="timeline-item animate-fade-in-up delay-${Math.min(index + 1, 4)}">
          <div class="timeline-badge-node ${nodeClass}" title="${escapeHtml(act.title)}">
            <i class="bi ${iconClass}"></i>
          </div>
          <div class="timeline-body">
            <div class="timeline-title">${escapeHtml(act.title)}</div>
            <div class="timeline-desc">${act.desc}</div>
            <div class="timeline-time"><i class="bi bi-clock me-1"></i>${escapeHtml(act.time)}</div>
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
  const statusSelect = document.getElementById('filterStatus');
  const prioritySelect = document.getElementById('filterPriority');
  const statusDropdownTrigger = document.getElementById('statusDropdownTrigger');
  const priorityDropdownTrigger = document.getElementById('priorityDropdownTrigger');
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
      const innerHTMLContent = item.querySelector('.d-flex').innerHTML;

      const parentMenu = item.closest('.custom-dropdown-menu');
      if (parentMenu) {
        parentMenu.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
      }
      item.classList.add('active');

      if (filterType === 'status') {
        if (statusSelect) statusSelect.value = val;
        if (statusDropdownTrigger) {
          const label = statusDropdownTrigger.querySelector('.selected-label');
          if (label) label.innerHTML = innerHTMLContent;
          if (val) {
            statusDropdownTrigger.classList.add('filter-active');
          } else {
            statusDropdownTrigger.classList.remove('filter-active');
          }
        }
        renderAssignedTable();
      } else if (filterType === 'priority') {
        if (prioritySelect) prioritySelect.value = val;
        if (priorityDropdownTrigger) {
          const label = priorityDropdownTrigger.querySelector('.selected-label');
          if (label) label.innerHTML = innerHTMLContent;
          if (val) {
            priorityDropdownTrigger.classList.add('filter-active');
          } else {
            priorityDropdownTrigger.classList.remove('filter-active');
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
    const statusVal = statusSelect ? statusSelect.value : '';
    const priorityVal = prioritySelect ? prioritySelect.value.toLowerCase() : '';

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
        item.title.toLowerCase().includes(searchTerm);

      const matchStatus = !statusVal || item.status === statusVal;
      const matchPriority = !priorityVal || item.priority.toLowerCase() === priorityVal;

      return matchSearch && matchStatus && matchPriority;
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
          <td>${escapeHtml(item.citizen)}</td>
          <td>${escapeHtml(item.category)}</td>
          <td><span class="priority-badge ${item.priority.toLowerCase()}">${item.priority}</span></td>
          <td>
            ${item.status === 'assigned' 
              ? '<span class="status-badge assigned"><i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Assigned</span>'
              : item.status === 'in_progress'
              ? '<span class="status-badge in-progress"><i class="bi bi-clock"></i> In Progress</span>'
              : '<span class="status-badge resolved"><i class="bi bi-check-circle-fill"></i> Resolved</span>'}
          </td>
          <td>${item.date}</td>
          <td class="text-end">
            <a href="complaint_details.php?id=${item.id}" class="btn btn-sm btn-outline-secondary me-1">
              <i class="bi bi-eye"></i> View
            </a>
            <a href="save_progress.php?id=${item.id}" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
              <i class="bi bi-pencil-square"></i> Update Progress
            </a>
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

  if (filterForm) {
    filterForm.addEventListener('reset', () => {
      setTimeout(() => {
        if (statusSelect) statusSelect.value = '';
        if (prioritySelect) prioritySelect.value = '';

        if (statusDropdownTrigger) {
          statusDropdownTrigger.querySelector('.selected-label').innerText = 'All Statuses';
          statusDropdownTrigger.classList.remove('filter-active');
        }
        if (priorityDropdownTrigger) {
          priorityDropdownTrigger.querySelector('.selected-label').innerText = 'All Priorities';
          priorityDropdownTrigger.classList.remove('filter-active');
        }

        document.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.custom-dropdown-item[data-value=""]').forEach(i => i.classList.add('active'));

        renderAssignedTable();
      }, 50);
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
