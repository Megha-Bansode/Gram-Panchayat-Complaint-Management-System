/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module #5 - Complaint Administration & Reporting
 * Main Administrative Logic & Table Handling
 */

document.addEventListener('DOMContentLoaded', () => {
  // Sidebar Toggle Logic
  const sidebar = document.getElementById('app-sidebar');
  const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
  const mobileToggleBtn = document.getElementById('mobile-nav-toggle');

  if (sidebarToggleBtn && sidebar) {
    sidebarToggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      const icon = sidebarToggleBtn.querySelector('i');
      if (icon) {
        icon.className = sidebar.classList.contains('collapsed')
          ? 'bi bi-layout-sidebar'
          : 'bi bi-layout-sidebar-inset';
      }
    });
  }

  if (mobileToggleBtn && sidebar) {
    mobileToggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show-mobile');
    });
  }

  // Close sidebar on clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 991 && sidebar && sidebar.classList.contains('show-mobile')) {
      if (!sidebar.contains(e.target) && !mobileToggleBtn.contains(e.target)) {
        sidebar.classList.remove('show-mobile');
      }
    }
  });

  // Global Sidebar Navigation Handler for ALL Menu Items across the portal
  if (sidebar) {
    sidebar.querySelectorAll('.nav-link').forEach(link => {
      const text = link.textContent.trim().toLowerCase();
      if (text.includes('dashboard')) {
        link.setAttribute('href', 'admin_dashboard.php');
      } else if (text.includes('category') || text.includes('categories')) {
        link.setAttribute('href', 'manage_categories.php');
      } else if (text.includes('report')) {
        link.setAttribute('href', 'reports.php');
      } else if (text.includes('statistic')) {
        link.setAttribute('href', 'statistics_dashboard.php');
      } else if (text.includes('system setting') || text.includes('preferences')) {
        link.setAttribute('href', 'system_settings.php');
      } else if (text.includes('assigned work')) {
        link.setAttribute('href', 'view_complaints.php?status=assigned');
      } else if (text.includes('all complaint') || text.includes('complaint')) {
        link.setAttribute('href', 'view_complaints.php');
      } else if (text.includes('notification')) {
        link.setAttribute('href', 'view_complaints.php?status=pending');
      } else if (text.includes('help')) {
        link.setAttribute('href', 'system_settings.php');
      } else if (text.includes('profile')) {
        link.setAttribute('href', 'system_settings.php');
      } else if (text.includes('logout')) {
        link.setAttribute('href', 'admin_dashboard.php');
        link.addEventListener('click', () => {
          if (window.GPCMS && GPCMS.showToast) {
            GPCMS.showToast('Logged out successfully! Redirecting to login...', 'info');
          }
        });
      }
    });
  }

  // Category Search & Filter Logic (if table exists)
  const categorySearchInput = document.getElementById('category-search');
  const priorityFilter = document.getElementById('priority-filter');
  const departmentFilter = document.getElementById('department-filter');
  const categoryTableBody = document.getElementById('category-table-body');

  function filterCategoryTable() {
    if (!categoryTableBody) return;
    const searchTerm = categorySearchInput ? categorySearchInput.value.toLowerCase() : '';
    const priorityVal = priorityFilter ? priorityFilter.value : '';
    const deptVal = departmentFilter ? departmentFilter.value : '';

    const rows = categoryTableBody.querySelectorAll('tr');
    let visibleCount = 0;

    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      const priority = row.getAttribute('data-priority') || '';
      const dept = row.getAttribute('data-department') || '';

      const matchesSearch = text.includes(searchTerm);
      const matchesPriority = !priorityVal || priority === priorityVal;
      const matchesDept = !deptVal || dept === deptVal;

      if (matchesSearch && matchesPriority && matchesDept) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    const noDataEl = document.getElementById('no-category-data');
    if (noDataEl) {
      noDataEl.style.display = visibleCount === 0 ? '' : 'none';
    }
  }

  if (categorySearchInput) categorySearchInput.addEventListener('input', filterCategoryTable);
  if (priorityFilter) priorityFilter.addEventListener('change', filterCategoryTable);
  if (departmentFilter) departmentFilter.addEventListener('change', filterCategoryTable);

  // Character Counter for Textareas
  const textareasWithCounter = document.querySelectorAll('textarea[data-counter]');
  textareasWithCounter.forEach(textarea => {
    const counterEl = document.getElementById(textarea.getAttribute('data-counter'));
    const maxLen = textarea.getAttribute('maxlength') || 250;
    if (counterEl) {
      const updateCounter = () => {
        counterEl.textContent = `${textarea.value.length} / ${maxLen}`;
      };
      textarea.addEventListener('input', updateCounter);
      updateCounter();
    }
  });

  // Select All Checkbox Handler
  const selectAllChk = document.getElementById('select-all-categories');
  if (selectAllChk && categoryTableBody) {
    selectAllChk.addEventListener('change', () => {
      const chks = categoryTableBody.querySelectorAll('.row-checkbox');
      chks.forEach(chk => { chk.checked = selectAllChk.checked; });
    });
  }

  // Edit Category Modal Populator
  window.prepareEditModal = function (btn) {
    const row = btn.closest('tr');
    if (!row) return;

    document.getElementById('edit_category_id').value = row.cells[1].innerText.trim();
    document.getElementById('edit_category_name').value = row.getAttribute('data-name') || row.cells[2].innerText.trim();
    document.getElementById('edit_category_description').value = row.getAttribute('data-description') || '';
    document.getElementById('edit_priority').value = row.getAttribute('data-priority') || 'Medium';
    document.getElementById('edit_department').value = row.getAttribute('data-department') || 'Water Supply';
    document.getElementById('edit_color_badge').value = row.getAttribute('data-color') || '#8A724C';
    document.getElementById('edit_status').value = row.getAttribute('data-status') || 'Active';
  };

  // Delete Category Confirmation Modal Populator
  window.prepareDeleteModal = function (btn) {
    const row = btn.closest('tr');
    if (!row) return;
    const catId = row.cells[1].innerText.trim();
    const catName = row.cells[2].innerText.trim();

    document.getElementById('delete_target_id').value = catId;
    document.getElementById('delete-category-name-display').textContent = `${catName} (${catId})`;
  };

  // View Category Details Modal Populator
  window.prepareViewModal = function (btn) {
    const row = btn.closest('tr');
    if (!row) return;

    document.getElementById('view_id').textContent = row.cells[1].innerText.trim();
    document.getElementById('view_name').textContent = row.cells[2].innerText.trim();
    document.getElementById('view_desc').textContent = row.getAttribute('data-description') || row.cells[3].innerText.trim();
    document.getElementById('view_priority').textContent = row.getAttribute('data-priority') || 'Medium';
    document.getElementById('view_dept').textContent = row.getAttribute('data-department') || 'Water Supply';
    document.getElementById('view_status').textContent = row.getAttribute('data-status') || 'Active';
    document.getElementById('view_created').textContent = row.cells[7].innerText.trim();
    document.getElementById('view_by').textContent = row.cells[9].innerText.trim();
  };

  // Form Submit Interceptors (For UX feedback before backend integration)
  const addCategoryForm = document.getElementById('addCategoryForm');
  if (addCategoryForm) {
    addCategoryForm.addEventListener('submit', (e) => {
      // Form validation check
      const nameInput = document.getElementById('category_name');
      if (nameInput && nameInput.value.trim().toLowerCase() === 'water supply') {
        e.preventDefault();
        GPCMS.showToast('Category name "Water Supply" already exists!', 'error', 'Duplicate Category');
        return;
      }
      GPCMS.showToast('Submitting Category form...', 'info');
    });
  }

  const settingsForm = document.getElementById('systemSettingsForm');
  if (settingsForm) {
    settingsForm.addEventListener('submit', (e) => {
      e.preventDefault();
      GPCMS.showToast('System Settings updated successfully!', 'success');
    });
  }
});

/* ==========================================================================
   Module #6 - View Complaints Page: Search & Filter Logic
   Follows the same pattern as filterCategoryTable() above.
   Scoped via IIFE — does not affect any other page.
   ========================================================================== */

(function () {
  document.addEventListener('DOMContentLoaded', () => {
    const complaintsTableBody = document.getElementById('complaints-table-body');
    if (!complaintsTableBody) return; // Only runs on view_complaints.html

    const searchInput    = document.getElementById('complaint-search');
    const filterStatus   = document.getElementById('filter-status');
    const filterCategory = document.getElementById('filter-category');
    const filterVillage  = document.getElementById('filter-village');
    const filterOfficer  = document.getElementById('filter-officer');
    const filterDate     = document.getElementById('filter-date');
    const noDataEl       = document.getElementById('no-complaints-data');
    const visibleCountEl = document.getElementById('complaints-visible-count');
    const clearBtn       = document.getElementById('btn-clear-filters');
    const resetBtn       = document.getElementById('btn-reset-filters');

    function filterComplaintTable() {
      const searchTerm   = searchInput    ? searchInput.value.toLowerCase()    : '';
      const statusVal    = filterStatus   ? filterStatus.value                 : '';
      const categoryVal  = filterCategory ? filterCategory.value               : '';
      const villageVal   = filterVillage  ? filterVillage.value                : '';
      const officerVal   = filterOfficer  ? filterOfficer.value                : '';
      const dateVal      = filterDate     ? filterDate.value                   : '';

      const rows = complaintsTableBody.querySelectorAll('tr');
      let visibleCount = 0;

      rows.forEach(row => {
        const text     = row.innerText.toLowerCase();
        const status   = row.getAttribute('data-status')   || '';
        const category = row.getAttribute('data-category') || '';
        const village  = row.getAttribute('data-village')  || '';
        const officer  = row.getAttribute('data-officer')  || '';
        const rowDate  = row.getAttribute('data-date')     || '';

        const matchesSearch   = !searchTerm   || text.includes(searchTerm);
        const matchesStatus   = !statusVal    || status === statusVal;
        const matchesCategory = !categoryVal  || category === categoryVal;
        const matchesVillage  = !villageVal   || village === villageVal;
        const matchesOfficer  = !officerVal   || officer === officerVal;
        const matchesDate     = !dateVal      || rowDate === dateVal;

        const visible = matchesSearch && matchesStatus && matchesCategory &&
                        matchesVillage && matchesOfficer && matchesDate;

        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
      });

      if (noDataEl)       noDataEl.style.display       = visibleCount === 0 ? '' : 'none';
      if (visibleCountEl) visibleCountEl.textContent   = visibleCount;
    }

    function clearAllFilters() {
      if (searchInput)    searchInput.value    = '';
      if (filterStatus)   filterStatus.value   = '';
      if (filterCategory) filterCategory.value = '';
      if (filterVillage)  filterVillage.value  = '';
      if (filterOfficer)  filterOfficer.value  = '';
      if (filterDate)     filterDate.value     = '';
      filterComplaintTable();
      GPCMS.showToast('All filters cleared.', 'info');
    }

    // Event listeners (mirrors filterCategoryTable pattern)
    if (searchInput)    searchInput.addEventListener('input',   filterComplaintTable);
    if (filterStatus)   filterStatus.addEventListener('change', filterComplaintTable);
    if (filterCategory) filterCategory.addEventListener('change', filterComplaintTable);
    if (filterVillage)  filterVillage.addEventListener('change', filterComplaintTable);
    if (filterOfficer)  filterOfficer.addEventListener('change', filterComplaintTable);
    if (filterDate)     filterDate.addEventListener('change',   filterComplaintTable);

    if (clearBtn) clearBtn.addEventListener('click', clearAllFilters);
    if (resetBtn) resetBtn.addEventListener('click', clearAllFilters);

    // Sync header search bar → filter bar search (view_complaints.html only)
    const headerSearch = document.getElementById('global-header-search');
    if (headerSearch && searchInput) {
      headerSearch.addEventListener('input', () => {
        searchInput.value = headerSearch.value;
        filterComplaintTable();
      });
    }

    // Auto-filter based on URL parameter (e.g. view_complaints.html?status=assigned)
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam && filterStatus) {
      filterStatus.value = statusParam;
      filterComplaintTable();
    }
  });
})();
