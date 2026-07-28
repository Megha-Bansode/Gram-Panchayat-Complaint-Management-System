/**
 * GPCMS — Gram Panchayat Complaint Management System
 * Interactive Script (PHP Edition)
 */

document.addEventListener('DOMContentLoaded', () => {
    initPageLoader();
    initAOS();
    initHeaderScroll();
    initScrollToTop();
    initAnnouncementSlider();
    initCounterAnimation();
    initComplaintTracker();
    initModalNoticeFilterListeners();
    initScrollSpy();
    initSubpageNavHighlight();
});

/* --------------------------------------------------------------------------
   1. Page Loader
   -------------------------------------------------------------------------- */
function initPageLoader() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;
    
    // Show logo for 2 seconds before hiding smoothly
    setTimeout(() => {
        loader.classList.add('hidden');
    }, 2000);
}

/* --------------------------------------------------------------------------
   2. Animate On Scroll (AOS) Initialization
   -------------------------------------------------------------------------- */
function initAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 80
        });
    }
}

/* --------------------------------------------------------------------------
   3. Sticky Header Scroll Effect
   -------------------------------------------------------------------------- */
function initHeaderScroll() {
    const header = document.getElementById('main-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

/* --------------------------------------------------------------------------
   4. Scroll To Top Button
   -------------------------------------------------------------------------- */
function initScrollToTop() {
    const scrollBtn = document.getElementById('scroll-top');
    if (!scrollBtn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    });

    scrollBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/* --------------------------------------------------------------------------
   5. Announcement Slider
   -------------------------------------------------------------------------- */
function initAnnouncementSlider() {
    const slides = document.querySelectorAll('.announcement-slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (!slides.length) return;

    let currentIndex = 0;
    let autoSlideTimer;

    function showSlide(index) {
        slides.forEach((s, i) => {
            s.classList.toggle('active', i === index);
        });
        dots.forEach((d, i) => {
            d.classList.toggle('active', i === index);
        });
        currentIndex = index;
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            resetAutoSlide();
        });
    });

    function nextSlide() {
        let next = (currentIndex + 1) % slides.length;
        showSlide(next);
    }

    function startAutoSlide() {
        autoSlideTimer = setInterval(nextSlide, 5000);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideTimer);
        startAutoSlide();
    }

    startAutoSlide();
}

/* --------------------------------------------------------------------------
   6. Animated Counter Statistics
   -------------------------------------------------------------------------- */
function initCounterAnimation() {
    const counters = document.querySelectorAll('.counter-value');
    if (!counters.length) return;

    let animated = false;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                    let count = 0;
                    const speed = Math.ceil(target / 50);

                    const update = () => {
                        count += speed;
                        if (count >= target) {
                            counter.innerText = target.toLocaleString();
                        } else {
                            counter.innerText = count.toLocaleString();
                            requestAnimationFrame(update);
                        }
                    };
                    update();
                });
            }
        });
    }, { threshold: 0.3 });

    const statsSec = document.getElementById('stats');
    if (statsSec) {
        observer.observe(statsSec);
    }
}

/* --------------------------------------------------------------------------
   7. Interactive Complaint Status Tracker
   -------------------------------------------------------------------------- */
function initComplaintTracker() {
    const input = document.getElementById('complaint-id-input');
    const trackingNo = document.getElementById('tracking-no');
    
    if (input && trackingNo) {
        input.addEventListener('input', () => {
            if (input.value.trim() !== '') {
                trackingNo.innerText = input.value.trim();
            } else {
                trackingNo.innerText = 'GPCMS202600123';
            }
        });
    }
}

/* --------------------------------------------------------------------------
   8. Filter Notices in Dedicated Modal
   -------------------------------------------------------------------------- */
window.filterModalNotices = function() {
    const input = document.getElementById('noticeSearchInput');
    const filter = input ? input.value.toLowerCase().trim() : '';
    const categorySelect = document.getElementById('noticeCategoryFilter');
    const selectedCategory = categorySelect ? categorySelect.value : 'all';
    
    const items = document.querySelectorAll('#modalNoticesList .modal-notice-item');
    
    items.forEach(item => {
        const title = item.getAttribute('data-title') || '';
        const textContent = item.innerText || '';
        const itemCategory = item.getAttribute('data-category') || '';
        
        const matchesSearch = filter === '' || title.toLowerCase().includes(filter) || textContent.toLowerCase().includes(filter);
        const matchesCategory = selectedCategory === 'all' || itemCategory === selectedCategory;
        
        if (matchesSearch && matchesCategory) {
            item.style.setProperty('display', 'block', 'important');
        } else {
            item.style.setProperty('display', 'none', 'important');
        }
    });
};

function initModalNoticeFilterListeners() {
    const input = document.getElementById('noticeSearchInput');
    const categorySelect = document.getElementById('noticeCategoryFilter');
    const searchBtn = document.getElementById('noticeSearchBtn');
    
    if (input) {
        input.addEventListener('input', window.filterModalNotices);
        input.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') window.filterModalNotices();
        });
    }
    if (categorySelect) {
        categorySelect.addEventListener('change', window.filterModalNotices);
    }
    if (searchBtn) {
        searchBtn.addEventListener('click', window.filterModalNotices);
    }
}

/* --------------------------------------------------------------------------
   9. Scroll Spy — Highlight active nav link as sections enter the viewport
   -------------------------------------------------------------------------- */
function initScrollSpy() {
    // Section IDs on the landing page mapped to nav anchor fragment
    const sectionMap = {
        'home'          : 'home',
        'quick-services': 'quick-services',
        'notices'       : 'notices',
        'schemes'       : 'schemes',
        'track-status'  : 'track-status',
        'gallery'       : 'gallery',
        'about'         : 'about',
        'contact'       : 'contact'
    };

    // Build navLinks map: sectionId -> <a> element
    // Match by href fragment (#id) regardless of base_path prefix
    const navLinks = {};
    Object.keys(sectionMap).forEach(id => {
        const frag = sectionMap[id];
        const link = document.querySelector(`a.nav-link[href$="#${frag}"]`);
        if (link) navLinks[id] = link;
    });

    // Collect only sections that actually exist on this page
    const sectionsOnPage = Object.keys(sectionMap).filter(id => document.getElementById(id));
    if (sectionsOnPage.length === 0) return; // Sub-pages — PHP handles highlighting

    const headerEl  = document.getElementById('main-header');
    const headerH   = () => headerEl ? headerEl.offsetHeight : 70;

    // --- Active state setter ---
    function setActive(id) {
        Object.values(navLinks).forEach(l => l.classList.remove('active-nav'));
        if (id && navLinks[id]) navLinks[id].classList.add('active-nav');
    }

    // --- IntersectionObserver (primary method) ---
    let currentSection = sectionsOnPage[0];

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                currentSection = entry.target.id;
                setActive(currentSection);
            }
        });
    }, {
        // Fire when a section crosses the top 30% of the viewport
        rootMargin: `-${headerH() + 10}px 0px -60% 0px`,
        threshold: 0
    });

    sectionsOnPage.forEach(id => {
        const el = document.getElementById(id);
        if (el) observer.observe(el);
    });

    // --- Fallback: position-based on scroll (handles fast scrolls & edge) ---
    function getActiveSectionByScroll() {
        const scrollY = window.scrollY + headerH() + 40;
        let active = sectionsOnPage[0];
        sectionsOnPage.forEach(id => {
            const el = document.getElementById(id);
            if (el && el.offsetTop <= scrollY) active = id;
        });
        return active;
    }

    window.addEventListener('scroll', () => {
        const id = getActiveSectionByScroll();
        if (id !== currentSection) {
            currentSection = id;
            setActive(id);
        }
    }, { passive: true });

    // --- Instant highlight on nav click ---
    Object.entries(navLinks).forEach(([id, link]) => {
        link.addEventListener('click', () => {
            currentSection = id;
            setActive(id);
        });
    });

    // --- On page load: honour URL hash, then fallback ---
    const hash = window.location.hash.replace('#', '');
    if (hash && navLinks[hash]) {
        setActive(hash);
        currentSection = hash;
    } else {
        const initial = getActiveSectionByScroll();
        setActive(initial);
        currentSection = initial;
    }
}

/* --------------------------------------------------------------------------
   10. Scheme Expandable Cards — Toggle inline detail panel
   -------------------------------------------------------------------------- */
window.toggleAccordion = function(btn) {
    const card = btn.closest('.scheme-card-wrap');
    const isOpen = card.classList.contains('accordion-open');
    
    // Close all other open scheme cards first
    document.querySelectorAll('.scheme-card-wrap.accordion-open').forEach(c => {
        if (c !== card) c.classList.remove('accordion-open');
    });
    
    // Toggle this card
    card.classList.toggle('accordion-open', !isOpen);
};

/* --------------------------------------------------------------------------
   11. Sub-page Dynamic Active Navigation Highlight Detector
   -------------------------------------------------------------------------- */
function initSubpageNavHighlight() {
    const path = window.location.pathname.toLowerCase();
    
    // Check if on standalone complaint tracking page / route
    const isTrackPage = (path.includes('track_complaint') || 
                        path.includes('complaint_status') || 
                        path.includes('complaint-status')) &&
                        !path.endsWith('index.php') && 
                        !path.endsWith('/');

    if (isTrackPage) {
        // Ensure ONLY ONE navigation item is active at a time
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.classList.remove('active-nav', 'active');
        });

        const trackLink = document.querySelector('.navbar-nav a.nav-link[data-nav-key="track"]') ||
                          document.querySelector('.navbar-nav a.nav-link[href*="track-status"]') ||
                          document.querySelector('.navbar-nav a.nav-link[href*="track_complaint"]');

        if (trackLink) {
            trackLink.classList.add('active-nav', 'active');
        }
    }
}
