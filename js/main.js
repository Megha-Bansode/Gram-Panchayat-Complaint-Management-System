/* ============================================================
   GPCMS — Gram Panchayat Complaint Management System
   main.js — Premium Interactive Features
   ============================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initPageLoader();
    initAOS();
    initStickyHeader();
    initActiveNavOnScroll();
    initLanguageSwitcher();
    initComplaintTracker();
    initStatsCounter();
    initScrollReveal();
    initButtonRipple();
    initAnnouncementSlider();
    initScrollToTop();
});

/* ============================================================
   PAGE LOADER
   ============================================================ */
function initPageLoader() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;

    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 600);
    });

    // Fallback: hide after 2.5s regardless
    setTimeout(() => {
        if (loader) loader.classList.add('hidden');
    }, 2500);
}

/* ============================================================
   AOS INIT
   ============================================================ */
function initAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 650,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    }
}

/* ============================================================
   STICKY HEADER SCROLL EFFECT
   ============================================================ */
function initStickyHeader() {
    const header = document.getElementById('main-header');
    if (!header) return;

    const onScroll = () => {
        if (window.scrollY > 60) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
}

/* ============================================================
   ACTIVE NAV ON SCROLL
   ============================================================ */
function initActiveNavOnScroll() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active-nav');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active-nav');
                    }
                });
            }
        });
    }, { threshold: 0.35, rootMargin: '-80px 0px -30% 0px' });

    sections.forEach(sec => observer.observe(sec));
}

/* ============================================================
   LANGUAGE TRANSLATIONS
   ============================================================ */
const translations = {
    en: {
        /* Nav */
        'nav-home': 'Home', 'nav-about': 'About Us', 'nav-services': 'Services',
        'nav-schemes': 'Government Schemes', 'nav-notices': 'Notices',
        'nav-gallery': 'Gallery', 'nav-contact': 'Contact Us', 'nav-login': 'Login',
        /* Login */
        'citizen-login-title': '👨 Citizen Login',
        'citizen-login-desc': 'Register complaints, track status, view schemes & certificates.',
        'citizen-login-btn': 'Login as Citizen →',
        'official-login-title': '🏛 Official Login',
        'official-login-desc': 'Manage complaints, publish notices & access admin dashboard.',
        'official-login-btn': 'Login as Official →',
        /* Hero */
        'hero-badge': '🌿 Welcome to',
        'hero-title': 'Gram Panchayat',
        'hero-subtitle-main': 'Building a Stronger and Better Village',
        'hero-desc': 'Our mission is to bring transparency, accountability, and efficient governance while providing better public services to every citizen.',
        'btn-register': '📢 Register Complaint',
        'btn-track-complaint': '🔍 Track Complaint',
        /* Services */
        'services-tag': 'Our Services', 'services-title': 'Quick Citizen Services',
        'services-subtitle': 'Access all panchayat services in one click — fast, transparent and fully digital.',
        's1-title': '📄 Register Complaint', 's1-desc': 'Raise issues related to roads, water supply, sanitation, electricity and public services.',
        's2-title': '🔍 Track Complaint', 's2-desc': 'Track the real-time status of your complaint using your unique Complaint ID.',
        's3-title': '🏛 Government Schemes', 's3-desc': 'Explore central and state government schemes available for villagers and rural families.',
        's4-title': '📑 Certificates', 's4-desc': 'Apply online for Birth, Death, Income, Residence and other official certificates.',
        's5-title': '💰 Tax & Payments', 's5-desc': 'Pay House Tax, Water Tax and other Panchayat charges securely online.',
        's6-title': '💬 Feedback', 's6-desc': 'Submit your suggestions and feedback to help improve village administration and services.',
        /* Stats */
        'stat-residents': '👥 Total Residents', 'stat-resolved': '📋 Complaints Resolved',
        'stat-schemes': '🏛 Government Schemes', 'stat-events': '📅 Upcoming Events',
        /* Notices */
        'notices-tag': 'Official Board', 'notices-title': 'Latest Notices',
        'notices-subtitle': 'Stay informed with the most recent official announcements, meetings, and updates from your Gram Panchayat.',
        'notices-subscribe': 'Subscribe Alerts', 'notices-board-title': 'Notice Board', 'notices-view-all': 'View All →',
        'notice-1-title': 'Gram Sabha Meeting — July 2026', 'notice-2-title': 'Road Repair Work — Main Bazaar Lane',
        'notice-3-title': 'Water Supply Schedule Change — 22–24 July', 'notice-4-title': 'Agriculture Subsidy Scheme — Apply Now',
        'notice-5-title': 'Panchayat Election Notice — August 2026',
        /* Schemes */
        'schemes-tag': 'Benefits', 'schemes-title': 'Government Schemes',
        'schemes-subtitle': 'Discover welfare schemes by Central and State Governments. Apply online or view eligibility.',
        'scheme-pmay-title': 'PM Awas Yojana (PMAY)', 'scheme-pmay-desc': 'Providing affordable housing for all rural families with subsidized home loan options and direct financial assistance.',
        'scheme-jjm-title': 'Jal Jeevan Mission', 'scheme-jjm-desc': 'Delivering clean drinking water through functional household tap connections to every rural household.',
        'scheme-pmkisan-title': 'PM Kisan Samman Nidhi', 'scheme-pmkisan-desc': 'Direct financial benefit of ₹6,000/year in three installments to eligible small and marginal farmers.',
        'scheme-learn-more': 'Learn More',
        /* Emergency */
        'emergency-tag': '24×7 Helplines', 'emergency-title': 'Emergency Contacts',
        'emergency-subtitle': 'Reach out immediately for critical local assistance, medical aids, and administrative queries.',
        /* Tracker */
        'tracker-tag': 'Real-Time Tracking', 'tracker-title': 'Complaint Status Checker',
        'tracker-subtitle': 'Enter your Complaint ID to view the live status timeline of your grievance.',
        'tracker-placeholder': 'e.g., GPCMS202600123', 'btn-track': 'Track Complaint',
        'tracker-hint': 'Try: GPCMS202600123 or GPCMS202600999', 'tracker-id-label': 'Complaint ID:',
        'step-registered': 'Complaint Submitted', 'step-review': 'Under Review',
        'step-assigned': 'Assigned to Officer', 'step-progress': 'In Progress', 'step-resolved': 'Resolved',
        /* Gallery */
        'gallery-tag': 'Visual Stories', 'gallery-title': 'Gallery',
        'gallery-subtitle': 'A glimpse into village life, governance, and community development through our lens.',
        /* Announcements */
        'announcements-title': 'Public Announcements',
        'announcements-subtitle': 'Stay updated with official statements and local alerts from your Panchayat body.',
        'ann-1-title': 'Vaccination Drive — July 2026',
        'ann-1-desc': 'Free vaccination camp at Panchayat premises for children aged 0–5. Bring your health card.',
        /* About */
        'about-tag': 'About Us', 'about-title': 'About Gram Panchayat',
        'about-desc': 'The Gram Panchayat is committed to transparent governance, public participation, digital services, and rural development. Citizens can register complaints, track progress, access schemes, and stay informed through this portal.',
        'about-f1': 'Transparent and accountable local governance for every citizen',
        'about-f2': 'Digital complaint registration with real-time status tracking',
        'about-f3': 'Access to all Central & State government welfare schemes',
        'about-f4': 'Online certificates, tax payments and public notices',
        'about-f5': '24×7 citizen support with emergency helpline numbers',
        'about-cta-1': 'Register Complaint', 'about-cta-2': 'View Schemes',
        /* Testimonials */
        'testimonials-title': 'Citizen Testimonials',
        'testimonials-subtitle': 'Read how the GPCMS portal helped actual residents get grievances resolved in record time.',
        /* FAQ */
        'faq-title': 'Frequently Asked Questions',
        'faq-subtitle': 'Quick answers to common questions about registering, tracking, and resolving complaints.',
        'faq-q1': 'How do I register a complaint?',
        'faq-a1': 'Click "Register Complaint" on the hero section or the Services card. Log in with Citizen credentials, choose the department, fill in complaint details, attach photos if needed, and submit. You\'ll receive a unique Complaint ID instantly via SMS.',
        'faq-q2': 'How long does resolution take?',
        'faq-a2': 'Resolution time varies by category. Critical issues like streetlights, sewage, or water supply are usually resolved within 3–5 working days. Complex structural issues may take up to 15 working days. The system updates status at each step.',
        'faq-q3': 'How can I track complaint status?',
        'faq-a3': 'Enter your Complaint ID (e.g., GPCMS202600123) in the "Complaint Status Checker" section on this page and click Track. A live timeline will show all stages from submission to resolution.',
        'faq-q4': 'Which schemes are available?',
        'faq-a4': 'We list 36+ schemes including PM Awas Yojana, Jal Jeevan Mission, PM Kisan Samman Nidhi, Ayushman Bharat, MGNREGA, Ujjwala Yojana, Swachh Bharat Mission, and many more. Visit the Government Schemes section to explore all.',
        'faq-q5': 'Is Aadhaar required to register?',
        'faq-a5': 'Yes, Aadhaar-based OTP verification is required for first-time registration to prevent spam and ensure authenticity. However, tracking an existing Complaint ID is open to all without login.',
        /* Contact */
        'contact-title': 'Contact Us', 'contact-subtitle': 'Visit the Gram Panchayat office or reach us through the details below.',
        'contact-office-title': 'Office Information', 'contact-office-sub': 'Gram Panchayat Office — Open Monday to Saturday',
        /* Footer */
        'footer-links': 'Quick Links', 'footer-help': 'Help',
    },

    mr: {
        /* Nav */
        'nav-home': 'मुख्य पृष्ठ', 'nav-about': 'आमच्याबद्दल', 'nav-services': 'सेवा',
        'nav-schemes': 'शासकीय योजना', 'nav-notices': 'सूचना',
        'nav-gallery': 'दालन', 'nav-contact': 'संपर्क साधा', 'nav-login': 'लॉगिन',
        /* Login */
        'citizen-login-title': '👨 नागरिक लॉगिन',
        'citizen-login-desc': 'तक्रारी नोंदवा, स्थिती ट्रॅक करा, योजना पाहा व प्रमाणपत्रे मिळवा.',
        'citizen-login-btn': 'नागरिक म्हणून लॉगिन →',
        'official-login-title': '🏛 अधिकारी लॉगिन',
        'official-login-desc': 'तक्रारी व्यवस्थापित करा, सूचना प्रकाशित करा व प्रशासकीय डॅशबोर्ड वापरा.',
        'official-login-btn': 'अधिकारी म्हणून लॉगिन →',
        /* Hero */
        'hero-badge': '🌿 स्वागत आहे',
        'hero-title': 'ग्रामपंचायत',
        'hero-subtitle-main': 'एक बळकट आणि चांगले गाव घडवूया',
        'hero-desc': 'आमचे ध्येय म्हणजे पारदर्शकता, जबाबदारी आणि कार्यक्षम प्रशासन आणत प्रत्येक नागरिकाला उत्तम सेवा पुरविणे.',
        'btn-register': '📢 तक्रार नोंदवा', 'btn-track-complaint': '🔍 तक्रार ट्रॅक करा',
        /* Services */
        'services-tag': 'आमच्या सेवा', 'services-title': 'त्वरित नागरिक सेवा',
        'services-subtitle': 'सर्व पंचायत सेवा एका क्लिकमध्ये — जलद, पारदर्शक आणि पूर्णतः डिजिटल.',
        's1-title': '📄 तक्रार नोंदवा', 's1-desc': 'रस्ते, पाणीपुरवठा, स्वच्छता, वीज आणि सार्वजनिक सेवांशी संबंधित समस्या नोंदवा.',
        's2-title': '🔍 तक्रार ट्रॅक करा', 's2-desc': 'आपल्या अनोख्या तक्रार आयडीचा वापर करून तक्रारीची स्थिती तपासा.',
        's3-title': '🏛 शासकीय योजना', 's3-desc': 'ग्रामस्थांसाठी उपलब्ध केंद्र आणि राज्य सरकारच्या योजना पाहा.',
        's4-title': '📑 प्रमाणपत्रे', 's4-desc': 'जन्म, मृत्यू, उत्पन्न, रहिवास व इतर अधिकृत प्रमाणपत्रांसाठी ऑनलाइन अर्ज करा.',
        's5-title': '💰 कर व देयके', 's5-desc': 'घरपट्टी, पाणीपट्टी आणि इतर पंचायत शुल्क ऑनलाइन भरा.',
        's6-title': '💬 अभिप्राय', 's6-desc': 'ग्राम प्रशासन सुधारण्यासाठी आपले सुझाव व अभिप्राय सादर करा.',
        /* Stats */
        'stat-residents': '👥 एकूण रहिवासी', 'stat-resolved': '📋 तक्रारी निकाली',
        'stat-schemes': '🏛 शासकीय योजना', 'stat-events': '📅 आगामी कार्यक्रम',
        /* Notices */
        'notices-tag': 'अधिकृत फलक', 'notices-title': 'ताज्या सूचना',
        'notices-subtitle': 'आपल्या ग्रामपंचायतीच्या अलीकडील अधिकृत घोषणा, बैठका आणि अपडेट्स जाणून घ्या.',
        'notices-subscribe': 'सूचना मिळवा', 'notices-board-title': 'सूचना फलक', 'notices-view-all': 'सर्व पाहा →',
        'notice-1-title': 'ग्रामसभा बैठक — जुलै २०२६', 'notice-2-title': 'रस्ता दुरुस्ती कार्य — मुख्य बाजार गल्ली',
        'notice-3-title': 'पाणीपुरवठा वेळापत्रक बदल — २२-२४ जुलै', 'notice-4-title': 'कृषी अनुदान योजना — अर्ज करा',
        'notice-5-title': 'पंचायत निवडणूक सूचना — ऑगस्ट २०२६',
        /* Schemes */
        'schemes-tag': 'लाभ', 'schemes-title': 'शासकीय योजना',
        'schemes-subtitle': 'केंद्र आणि राज्य सरकारांनी सुरू केलेल्या कल्याण योजना शोधा. ऑनलाइन अर्ज करा किंवा पात्रता पाहा.',
        'scheme-pmay-title': 'पीएम आवास योजना (PMAY)', 'scheme-pmay-desc': 'सर्व ग्रामीण कुटुंबांना परवडणारे घर देण्यासाठी अनुदानित गृहकर्ज व थेट आर्थिक सहाय्य.',
        'scheme-jjm-title': 'जल जीवन मिशन', 'scheme-jjm-desc': 'प्रत्येक ग्रामीण घरात नळाने स्वच्छ पाणी पुरवण्याचे उद्दिष्ट.',
        'scheme-pmkisan-title': 'पीएम किसान सन्मान निधी', 'scheme-pmkisan-desc': 'पात्र शेतकऱ्यांच्या बँक खात्यात वार्षिक ₹६,०००  थेट तीन हप्त्यांत जमा.',
        'scheme-learn-more': 'अधिक माहिती',
        /* Emergency */
        'emergency-tag': '२४×७ हेल्पलाइन', 'emergency-title': 'आपत्कालीन संपर्क',
        'emergency-subtitle': 'गंभीर स्थानिक सहाय्य, वैद्यकीय मदत आणि प्रशासकीय प्रश्नांसाठी लगेच संपर्क साधा.',
        /* Tracker */
        'tracker-tag': 'रिअल-टाइम ट्रॅकिंग', 'tracker-title': 'तक्रार स्थिती तपासा',
        'tracker-subtitle': 'आपल्या तक्रारीची थेट स्थिती टाइमलाइन पाहण्यासाठी तक्रार आयडी प्रविष्ट करा.',
        'tracker-placeholder': 'उदा., GPCMS202600123', 'btn-track': 'तक्रार ट्रॅक करा',
        'tracker-hint': 'वापरून पाहा: GPCMS202600123 किंवा GPCMS202600999', 'tracker-id-label': 'तक्रार आयडी:',
        'step-registered': 'तक्रार नोंदली', 'step-review': 'आढावा सुरू',
        'step-assigned': 'अधिकाऱ्यास नियुक्त', 'step-progress': 'कामकाज सुरू', 'step-resolved': 'निकाली',
        /* Gallery */
        'gallery-tag': 'व्हिज्युअल कथा', 'gallery-title': 'दालन',
        'gallery-subtitle': 'आमच्या दृष्टिकोनातून ग्रामीण जीवन, प्रशासन आणि सामुदायिक विकासाची झलक.',
        /* Announcements */
        'announcements-title': 'सार्वजनिक घोषणा',
        'announcements-subtitle': 'आपल्या पंचायत मंडळाच्या अधिकृत निवेदनांसह अद्ययावत राहा.',
        'ann-1-title': 'लसीकरण शिबीर — जुलै २०२६',
        'ann-1-desc': '० ते ५ वर्षे वयोगटातील मुलांसाठी पंचायत परिसरात मोफत लसीकरण शिबीर. आरोग्य कार्ड आणा.',
        /* About */
        'about-tag': 'आमच्याबद्दल', 'about-title': 'ग्रामपंचायतबद्दल',
        'about-desc': 'ग्रामपंचायत पारदर्शक प्रशासन, सार्वजनिक सहभाग, डिजिटल सेवा आणि ग्रामीण विकासासाठी वचनबद्ध आहे. नागरिक तक्रारी नोंदवू शकतात, प्रगती ट्रॅक करू शकतात, योजना मिळवू शकतात आणि या पोर्टलद्वारे माहिती मिळवू शकतात.',
        'about-f1': 'प्रत्येक नागरिकासाठी पारदर्शक आणि जबाबदार स्थानिक प्रशासन',
        'about-f2': 'रिअल-टाइम स्थिती ट्रॅकिंगसह डिजिटल तक्रार नोंदणी',
        'about-f3': 'केंद्र आणि राज्य सरकारच्या सर्व कल्याण योजनांचा प्रवेश',
        'about-f4': 'ऑनलाइन प्रमाणपत्रे, कर भरणा आणि सार्वजनिक सूचना',
        'about-f5': 'आपत्कालीन हेल्पलाइन क्रमांकांसह २४×७ नागरिक सहाय्य',
        'about-cta-1': 'तक्रार नोंदवा', 'about-cta-2': 'योजना पाहा',
        /* Testimonials */
        'testimonials-title': 'नागरिकांचे अनुभव',
        'testimonials-subtitle': 'GPCMS पोर्टलने कसे मदत केले ते वाचा.',
        /* FAQ */
        'faq-title': 'वारंवार विचारले जाणारे प्रश्न',
        'faq-subtitle': 'तक्रारी नोंदवणे, ट्रॅक करणे आणि निकालाबाबत सामान्य प्रश्नांची उत्तरे.',
        'faq-q1': 'मी तक्रार कशी नोंदवू?',
        'faq-a1': 'मुख्य पृष्ठावरील किंवा सेवा कार्डातील "तक्रार नोंदवा" बटणावर क्लिक करा. नागरिक क्रेडेंशियल्स वापरून लॉगिन करा, विभाग निवडा, तक्रार तपशील भरा, आवश्यक असल्यास फोटो जोडा आणि सबमिट करा. तुम्हाला लगेच एसएमएसद्वारे एक युनिक तक्रार आयडी मिळाला आहे.',
        'faq-q2': 'तक्रार निवारणासाठी किती वेळ लागतो?',
        'faq-a2': 'समस्येच्या प्रकारानुसार निवारणाचा वेळ बदलतो. पथदिवे, सांडपाणी किंवा पाणीपुरवठा यांसारख्या गंभीर समस्या सहसा ३-५ कामकाजाच्या दिवसांत सोडवल्या जातात. गुंतागुंतीच्या संरचनात्मक समस्यांसाठी १५ कामकाजाचे दिवस लागू शकतात. प्रणाली प्रत्येक टप्प्यावर स्थिती अद्ययावत करते.',
        'faq-q3': 'मी तक्रारीची स्थिती कशी ट्रॅक करू?',
        'faq-a3': 'या पृष्ठावरील "तक्रार स्थिती तपासा" विभागात तुमचा तक्रार आयडी (उदा. GPCMS202600123) प्रविष्ट करा आणि ट्रॅक वर क्लिक करा. सबमिशनपासून निवारणापर्यंतचे सर्व टप्पे दर्शवणारी थेट टाइमलाइन दिसेल.',
        'faq-q4': 'कोणत्या शासकीय योजना उपलब्ध आहेत?',
        'faq-a4': 'आम्ही ३६+ योजनांची यादी देतो, ज्यामध्ये पीएम आवास योजना, जल जीवन मिशन, पीएम किसान सन्मान निधी, आयुष्मान भारत, मनरेगा (MGNREGA), उज्ज्वला योजना, स्वच्छ भारत मिशन आणि इतर बऱ्याच योजनांचा समावेश आहे. सर्व योजना पाहण्यासाठी शासकीय योजना विभागाला भेट द्या.',
        'faq-q5': 'नोंदणीसाठी आधार कार्ड आवश्यक आहे का?',
        'faq-a5': 'होय, स्पॅम रोखण्यासाठी आणि नागरिक सत्यता सुनिश्चित करण्यासाठी पहिल्यांदा नोंदणी करताना आधार-आधारित ओटीपी पडताळणी आवश्यक आहे. तथापि, लॉगिन न करता कोणत्याही अस्तित्वात असलेल्या तक्रार आयडीची स्थिती तपासणे सर्वांसाठी खुले आहे.',
        /* Contact */
        'contact-title': 'संपर्क साधा', 'contact-subtitle': 'ग्रामपंचायत कार्यालयास भेट द्या किंवा खाली दिलेल्या तपशिलाद्वारे संपर्क साधा.',
        'contact-office-title': 'कार्यालय माहिती', 'contact-office-sub': 'ग्रामपंचायत कार्यालय — सोमवार ते शनिवार उघडे',
        /* Footer */
        'footer-links': 'द्रुत लिंक्स', 'footer-help': 'मदत',
    }
};

function initLanguageSwitcher() {
    const items = document.querySelectorAll('.lang-select-item');
    const langText = document.getElementById('current-lang-text');

    // Load saved language
    const saved = localStorage.getItem('gpcms_lang') || 'en';
    if (saved !== 'en') {
        translatePage(saved);
        const match = document.querySelector(`[data-lang="${saved}"]`);
        if (match && langText) langText.innerHTML = match.innerText;
    }

    items.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const lang = item.getAttribute('data-lang');
            translatePage(lang);
            if (langText) langText.innerHTML = item.innerText;
            localStorage.setItem('gpcms_lang', lang);
        });
    });
}

function translatePage(lang) {
    const map = translations[lang];
    if (!map) return;

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (!map[key]) return;

        if (el.tagName === 'INPUT' && el.placeholder) {
            el.placeholder = map[key];
        } else {
            el.textContent = map[key];
        }
    });
}

/* ============================================================
   COMPLAINT TRACKER
   ============================================================ */
function initComplaintTracker() {
    const form = document.getElementById('tracker-form');
    const input = document.getElementById('complaint-id-input');
    const timelineCard = document.getElementById('timeline-card');
    const trackingNo = document.getElementById('tracking-no');
    const bar = document.getElementById('timeline-bar');

    if (!form) return;

    const mockData = {
        'GPCMS202600123': {
            progress: 75,
            steps: [
                { state: 'completed', date: '15 Jul 2026, 10:30 AM' },
                { state: 'completed', date: '16 Jul 2026, 02:15 PM' },
                { state: 'completed', date: '17 Jul 2026, 11:00 AM' },
                { state: 'active',    date: '19 Jul 2026, 09:30 AM' },
                { state: 'pending',   date: 'Pending Resolution' },
            ]
        },
        'GPCMS202600999': {
            progress: 100,
            steps: [
                { state: 'completed', date: '10 Jul 2026, 09:00 AM' },
                { state: 'completed', date: '11 Jul 2026, 11:45 AM' },
                { state: 'completed', date: '12 Jul 2026, 04:30 PM' },
                { state: 'completed', date: '13 Jul 2026, 10:15 AM' },
                { state: 'completed', date: '15 Jul 2026, 05:00 PM' },
            ]
        }
    };

    form.addEventListener('submit', e => {
        e.preventDefault();
        const id = (input.value || '').trim().toUpperCase();

        if (!id) {
            input.style.borderColor = '#e74c3c';
            input.focus();
            setTimeout(() => (input.style.borderColor = ''), 1500);
            return;
        }

        // Reveal card
        timelineCard.classList.add('active');
        if (trackingNo) trackingNo.textContent = id;

        // Reset steps
        for (let i = 1; i <= 5; i++) {
            const node = document.getElementById(`step-${i}`);
            const dateEl = document.getElementById(`date-${i}`);
            if (node) node.className = 'step-node';
            if (dateEl) dateEl.textContent = '—';
        }
        if (bar) bar.style.width = '0%';

        // Lookup
        const record = mockData[id] || {
            progress: 25,
            steps: [
                { state: 'completed', date: new Date().toLocaleString('en-IN') },
                { state: 'active',    date: 'Under Review' },
                { state: 'pending',   date: 'Pending' },
                { state: 'pending',   date: 'Pending' },
                { state: 'pending',   date: 'Pending' },
            ]
        };

        setTimeout(() => {
            if (bar) bar.style.width = `${record.progress}%`;

            record.steps.forEach((step, i) => {
                setTimeout(() => {
                    const node = document.getElementById(`step-${i + 1}`);
                    const dateEl = document.getElementById(`date-${i + 1}`);
                    if (node) node.classList.add(step.state);
                    if (dateEl) dateEl.textContent = step.date;
                }, i * 280);
            });
        }, 200);
    });
}

/* ============================================================
   STATS COUNTER ANIMATION
   ============================================================ */
function initStatsCounter() {
    const statsSection = document.getElementById('stats');
    const counters = document.querySelectorAll('.stat-number');
    let done = false;

    if (!statsSection || !counters.length) return;

    const run = () => {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
            const suffix = counter.getAttribute('data-suffix') || '';
            let current = 0;
            const duration = 1800;
            const step = target / (duration / 16);

            const tick = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.ceil(current).toLocaleString() + suffix;
                    requestAnimationFrame(tick);
                } else {
                    counter.textContent = target.toLocaleString() + suffix;
                }
            };
            tick();
        });
    };

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !done) {
            done = true;
            run();
            observer.disconnect();
        }
    }, { threshold: 0.3 });

    observer.observe(statsSection);
}

/* ============================================================
   SCROLL REVEAL (fallback for non-AOS elements)
   ============================================================ */
function initScrollReveal() {
    const revealEls = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(el => observer.observe(el));
}

/* ============================================================
   BUTTON RIPPLE EFFECT
   ============================================================ */
function initButtonRipple() {
    const targets = document.querySelectorAll(
        '.btn-hero-primary, .btn-primary-custom, .btn-track, .btn-submit-login'
    );

    targets.forEach(btn => {
        btn.addEventListener('click', function (e) {
            this.querySelectorAll('.ripple').forEach(r => r.remove());
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.left = `${e.clientX - rect.left}px`;
            ripple.style.top  = `${e.clientY - rect.top}px`;
            this.appendChild(ripple);
        });
    });
}

/* ============================================================
   ANNOUNCEMENT SLIDER
   ============================================================ */
function initAnnouncementSlider() {
    const slides = document.querySelectorAll('.announcement-slide');
    const dots   = document.querySelectorAll('.slider-dot');
    if (!slides.length) return;

    let current = 0;
    let timer;

    const go = (n) => {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    };

    const next = () => go(current + 1);

    const startAuto = () => { timer = setInterval(next, 4000); };
    const stopAuto  = () => clearInterval(timer);

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { stopAuto(); go(i); startAuto(); });
    });

    startAuto();
}

/* ============================================================
   SCROLL TO TOP
   ============================================================ */
function initScrollToTop() {
    const btn = document.getElementById('scroll-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
