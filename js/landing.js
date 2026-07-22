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
