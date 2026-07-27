/* ============================================================
   citizen.js – Citizen Module Interactive Behaviours
   GPCMS Engineering Handbook V3.1
   No inline JS anywhere – all behaviour lives here.
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════════════════
    // 1. AUTO-DISMISS BANNERS
    //    Data attribute: data-auto-dismiss="<ms>"
    // ══════════════════════════════════════════════════════
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        var delay = parseInt(el.getAttribute('data-auto-dismiss'), 10) || 8000;
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity    = '0';
            setTimeout(function () { el.remove(); }, 520);
        }, delay);
    });

    // ══════════════════════════════════════════════════════
    // 2. BANNER CLOSE BUTTONS
    // ══════════════════════════════════════════════════════
    document.querySelectorAll('.citizen-alert-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var banner = btn.closest('.citizen-alert');
            if (!banner) return;
            banner.style.transition = 'opacity 0.3s ease';
            banner.style.opacity    = '0';
            setTimeout(function () { banner.remove(); }, 320);
        });
    });

    // ══════════════════════════════════════════════════════
    // 3. DESCRIPTION CHARACTER COUNTER
    //    Targets: #complaint_description + #descCharCount
    // ══════════════════════════════════════════════════════
    var descTextarea = document.getElementById('complaint_description');
    var descCounter  = document.getElementById('descCharCount');

    if (descTextarea && descCounter) {
        var maxLen = parseInt(descTextarea.getAttribute('maxlength'), 10) || 1000;

        function updateCounter() {
            var len = descTextarea.value.length;
            descCounter.textContent = len;
            if (len > maxLen * 0.9) {
                descCounter.style.color = '#dc2626';
            } else if (len > maxLen * 0.7) {
                descCounter.style.color = '#d97706';
            } else {
                descCounter.style.color = '';
            }
        }

        descTextarea.addEventListener('input', updateCounter);
        updateCounter(); // initialise on page load
    }

    // ══════════════════════════════════════════════════════
    // 4. IMAGE UPLOAD PREVIEW + REMOVE
    //    Targets: #complaint_image, #imagePreview,
    //             #imagePreviewWrap, #removeImageBtn,
    //             #fileDropZone
    // ══════════════════════════════════════════════════════
    var fileInput     = document.getElementById('complaint_image');
    var preview       = document.getElementById('imagePreview');
    var previewWrap   = document.getElementById('imagePreviewWrap');
    var removeBtn     = document.getElementById('removeImageBtn');
    var dropZone      = document.getElementById('fileDropZone');
    var fileZoneContent = document.getElementById('fileZoneContent');
    var imageErrEl    = document.getElementById('complaint_image-error');

    var MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    var ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    function showPreview(file) {
        if (!file || !preview || !previewWrap) return;

        // Validate type
        if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            showImageError('Only JPG, JPEG, PNG, and WEBP images are allowed.');
            clearFileInput();
            return;
        }

        // Validate size
        if (file.size > MAX_FILE_SIZE) {
            showImageError('File is too large. Maximum allowed size is 5 MB.');
            clearFileInput();
            return;
        }

        clearImageError();

        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            previewWrap.classList.add('visible');
            if (fileZoneContent) {
                fileZoneContent.innerHTML =
                    '<i class="bi bi-check-circle-fill" style="font-size:2rem;color:#16a34a;display:block;margin-bottom:.4rem;"></i>' +
                    '<p class="mb-0" style="font-size:.83rem;color:#16a34a;font-weight:600;">' +
                    escapeHtml(file.name) + '</p>';
            }
        };
        reader.readAsDataURL(file);
    }

    function clearFileInput() {
        if (fileInput) { fileInput.value = ''; }
        if (preview)    { preview.src = '#'; }
        if (previewWrap) { previewWrap.classList.remove('visible'); }
        if (fileZoneContent) {
            fileZoneContent.innerHTML =
                '<i class="bi bi-cloud-upload citizen-file-zone-icon"></i>' +
                '<p class="citizen-file-zone-text mb-0">Drag &amp; drop or <span class="citizen-file-zone-link">browse</span></p>';
        }
    }

    function showImageError(msg) {
        if (imageErrEl) {
            imageErrEl.textContent = msg;
            imageErrEl.style.display = 'block';
        }
    }

    function clearImageError() {
        if (imageErrEl) {
            imageErrEl.textContent  = '';
            imageErrEl.style.display = 'none';
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files[0]) {
                showPreview(fileInput.files[0]);
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            clearFileInput();
            clearImageError();
        });
    }

    // Drag-and-drop
    if (dropZone) {
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            var file = e.dataTransfer.files[0];
            if (file) {
                // Assign to the real file input so the form submits it
                var dt   = new DataTransfer();
                dt.items.add(file);
                if (fileInput) { fileInput.files = dt.files; }
                showPreview(file);
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // 5. COMPLAINT FORM CLIENT-SIDE VALIDATION
    //    Target: #registerComplaintForm
    // ══════════════════════════════════════════════════════
    var complaintForm = document.getElementById('registerComplaintForm');
    if (complaintForm) {
        complaintForm.addEventListener('submit', function (e) {
            var valid = true;

            // Helper: show/hide field error
            function setError(fieldId, msg) {
                var el = document.getElementById(fieldId);
                var errEl = document.getElementById(fieldId + '-error');
                if (el)    el.classList.add('is-invalid');
                if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; }
                valid = false;
            }

            function clearError(fieldId) {
                var el = document.getElementById(fieldId);
                var errEl = document.getElementById(fieldId + '-error');
                if (el)    el.classList.remove('is-invalid');
                if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
            }

            // Validate each field
            var fields = [
                'complainant_name',
                'mobile_number',
                'village_ward',
                'category_id',
                'address',
                'complaint_title',
                'complaint_description'
            ];

            fields.forEach(function (id) { clearError(id); });

            // Complainant Name
            var name = document.getElementById('complainant_name');
            if (!name || name.value.trim().length < 2) {
                setError('complainant_name', 'Please enter a valid full name (at least 2 characters).');
            }

            // Mobile Number: 10 digits, starts with 6–9
            var mobile = document.getElementById('mobile_number');
            if (!mobile || !/^[6-9][0-9]{9}$/.test(mobile.value.trim())) {
                setError('mobile_number', 'Enter a valid 10-digit Indian mobile number (starting with 6–9).');
            }

            // Village / Ward
            var ward = document.getElementById('village_ward');
            if (!ward || ward.value.trim().length < 2) {
                setError('village_ward', 'Please enter your village or ward name.');
            }

            // Category
            var cat = document.getElementById('category_id');
            if (!cat || cat.value === '') {
                setError('category_id', 'Please select a complaint category.');
            }

            // Address
            var addr = document.getElementById('address');
            if (!addr || addr.value.trim().length < 5) {
                setError('address', 'Please provide a complete address or landmark.');
            }

            // Complaint Title
            var title = document.getElementById('complaint_title');
            if (!title || title.value.trim().length < 5) {
                setError('complaint_title', 'Title must be at least 5 characters.');
            }

            // Description
            var desc = document.getElementById('complaint_description');
            if (!desc || desc.value.trim().length < 10) {
                setError('complaint_description', 'Description must be at least 10 characters.');
            }

            if (!valid) {
                e.preventDefault();
                // Scroll to first error
                var firstErr = complaintForm.querySelector('.is-invalid');
                if (firstErr) {
                    firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErr.focus();
                }
                return;
            }

            // Show loading state on submit button
            var btn = document.getElementById('btn-submit-complaint');
            if (btn) {
                var submitText  = btn.querySelector('.btn-submit-text');
                var loadingText = btn.querySelector('.btn-loading-text');
                if (submitText)  submitText.classList.add('d-none');
                if (loadingText) loadingText.classList.remove('d-none');
                btn.disabled = true;
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // 6. MY COMPLAINTS – SEARCH + STATUS FILTER
    //    Target: #complaintSearchInput, .citizen-filter-btn,
    //            .complaint-row, #visibleCount,
    //            #noFilterResults, #noFilterResultsMobile
    // ══════════════════════════════════════════════════════
    var searchInput    = document.getElementById('complaintSearchInput');
    var filterBtns     = document.querySelectorAll('.citizen-filter-btn');
    var rows           = document.querySelectorAll('.complaint-row');
    var visibleCount   = document.getElementById('visibleCount');
    var noResultsDesk  = document.getElementById('noFilterResults');
    var noResultsMob   = document.getElementById('noFilterResultsMobile');
    var resetBtn       = document.getElementById('resetFiltersBtn');
    var resetBtnMob    = document.getElementById('noResultsResetBtn');
    var resetBtnMobAlt = document.getElementById('noResultsResetBtnMobile');

    var currentFilter = 'all';
    var currentSearch = '';

    function applyFilter() {
        var count = 0;

        rows.forEach(function (row) {
            var status = row.getAttribute('data-status') || '';
            var text   = row.getAttribute('data-search') || '';

            var matchFilter = (currentFilter === 'all' || status === currentFilter);
            var matchSearch = (currentSearch === '' || text.indexOf(currentSearch) !== -1);

            if (matchFilter && matchSearch) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update visible count
        if (visibleCount) visibleCount.textContent = count;

        // Show/hide no-results state
        var noResult = (count === 0 && rows.length > 0);
        if (noResultsDesk) noResultsDesk.classList.toggle('d-none', !noResult);
        if (noResultsMob)  noResultsMob.classList.toggle('d-none', !noResult);

        // Show/hide reset button
        var hasFilter = (currentFilter !== 'all' || currentSearch !== '');
        if (resetBtn) resetBtn.classList.toggle('d-none', !hasFilter);
    }

    // Filter buttons
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
            currentFilter = btn.getAttribute('data-filter') || 'all';
            applyFilter();
        });
    });

    // Search input
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = searchInput.value.toLowerCase().trim();
            applyFilter();
        });
    }

    // Reset buttons
    function resetAll() {
        currentFilter = 'all';
        currentSearch = '';
        if (searchInput) searchInput.value = '';
        filterBtns.forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-filter') === 'all');
            b.setAttribute('aria-pressed', b.getAttribute('data-filter') === 'all' ? 'true' : 'false');
        });
        applyFilter();
    }

    [resetBtn, resetBtnMob, resetBtnMobAlt].forEach(function (btn) {
        if (btn) btn.addEventListener('click', resetAll);
    });

    // ══════════════════════════════════════════════════════
    // 7. SIDEBAR MOBILE TOGGLE
    // ══════════════════════════════════════════════════════
    var sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    var sidebarCloseBtn  = document.getElementById('sidebarCloseBtn');
    var sidebar          = document.getElementById('citizenSidebar');

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    if (sidebarCloseBtn && sidebar) {
        sidebarCloseBtn.addEventListener('click', function () {
            sidebar.classList.remove('show');
        });
    }

    // ══════════════════════════════════════════════════════
    // 8. UTIL: ESCAPE HTML (used in JS template strings)
    // ══════════════════════════════════════════════════════
    function escapeHtml(str) {
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

}); // end DOMContentLoaded

