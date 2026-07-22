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
