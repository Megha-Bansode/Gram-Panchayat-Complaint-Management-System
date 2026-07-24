<?php
require_once __DIR__ . '/language.php';

// Calculate base path for assets and URLs relative to project root
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$project_folder = 'Gram-Panchayat-Complaint-Management-System';
$pos = strpos($script_name, '/' . $project_folder . '/');

if ($pos !== false) {
    $sub_path = substr($script_name, $pos + strlen('/' . $project_folder . '/'));
    $depth = substr_count(trim(dirname($sub_path), '.'), '/');
    if (dirname($sub_path) === '.') { $depth = 0; }
    $base_path = ($depth > 0) ? str_repeat('../', $depth) : '';
} else {
    $base_path = file_exists('css/style.css') ? '' : '../';
}

if (!function_exists('getLangUrl')) {
    function getLangUrl($langCode) {
        $uri = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($uri);
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : $_SERVER['PHP_SELF'];
        $queryParams = [];
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $queryParams);
        }
        $queryParams['lang'] = $langCode;
        return $path . '?' . http_build_query($queryParams);
    }
}

$lang_labels = [
    'en' => '🇬🇧 English',
    'mr' => '🇮🇳 मराठी',
    'hi' => '🇮🇳 हिन्दी'
];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('gpcms_full'); ?> | Digital Governance Portal</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Official Gram Panchayat Complaint Management System. Register complaints, track status, explore government schemes, apply for certificates, and stay connected with your village administration.">
    <meta name="keywords" content="Gram Panchayat, GPCMS, Complaint Management, Grievance Portal, Rural India, Government Schemes, Digital Governance">
    <meta name="author" content="Gram Panchayat Digital Cell">
    <meta property="og:title" content="GPCMS — <?php echo __('gpcms_full'); ?>">
    <meta property="og:description" content="Empowering rural India with transparent, accountable and efficient digital governance.">
    <meta property="og:type" content="website">

    <!-- Google Fonts: Poppins & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS — Animate On Scroll CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/landing.css">
</head>

<body>

    <!-- PAGE LOADER -->
    <div id="page-loader">
        <style>
            @keyframes logoPulse {
                0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(217,164,65,0.4); }
                50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(217,164,65,0); }
                100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(217,164,65,0); }
            }
        </style>
        <img src="<?php echo $base_path ?? ''; ?>assets/images/panchayat_logo.png?v=<?php echo time(); ?>" alt="GPCMS Logo" style="width:150px; height:150px; object-fit:contain; border-radius:50%; margin-bottom:15px; animation: logoPulse 1.5s infinite;" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3209/3209994.png';">
        <span class="loader-text">GPCMS &nbsp;|&nbsp; <?php echo __('loading'); ?></span>
    </div>

    <!-- SCROLL TO TOP -->
    <button id="scroll-top" aria-label="Back to top"><i class="fa-solid fa-chevron-up"></i></button>

    <!-- STICKY HEADER -->
    <header id="main-header" class="glass-nav fixed-top py-2">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">

                <!-- Brand / Logo -->
                <a class="navbar-brand-wrap" href="<?php echo $base_path; ?>index.php#home" style="text-decoration:none;">
                    <div class="brand-logo-circle">
                        <img src="<?php echo $base_path; ?>assets/images/panchayat_logo.png?v=<?php echo time(); ?>" alt="Gram Panchayat Logo" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3209/3209994.png';">
                    </div>
                    <div class="brand-title-text">
                        <span class="brand-name"><?php echo __('brand_name'); ?></span>
                        <span class="brand-sub"><?php echo __('brand_sub'); ?></span>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false"
                    aria-label="Toggle navigation" style="background:transparent;">
                    <i class="fa-solid fa-bars" style="color:var(--primary);font-size:1.2rem;"></i>
                </button>

                <!-- Nav Menu -->
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav mx-auto align-items-center gap-1 my-3 my-lg-0">
                        <li class="nav-item"><a class="nav-link active-nav" href="<?php echo $base_path; ?>index.php#home"><?php echo __('nav_home'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#quick-services"><?php echo __('nav_services'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#notices"><?php echo __('nav_notices'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#schemes"><?php echo __('nav_schemes'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#gallery"><?php echo __('nav_gallery'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#about"><?php echo __('nav_about'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#contact"><?php echo __('nav_contact'); ?></a></li>
                    </ul>

                    <!-- Right Side Controls -->
                    <div class="d-flex align-items-center gap-2 ms-lg-3 my-2 my-lg-0">

                        <!-- Global Language Switcher -->
                        <div class="nav-item dropdown">
                            <div class="language-selector dropdown-toggle" id="langDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-globe" style="color:var(--primary);"></i>
                                <span id="current-lang-text"><?php echo $lang_labels[$current_lang] ?? '🇬🇧 English'; ?></span>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="langDropdown" style="min-width:160px; z-index:1060;">
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $current_lang === 'en' ? 'active fw-bold' : ''; ?>" href="<?php echo getLangUrl('en'); ?>">
                                        <span>🇬🇧 English</span>
                                        <?php if ($current_lang === 'en'): ?><i class="fa-solid fa-check text-success"></i><?php endif; ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $current_lang === 'mr' ? 'active fw-bold' : ''; ?>" href="<?php echo getLangUrl('mr'); ?>">
                                        <span>🇮🇳 मराठी</span>
                                        <?php if ($current_lang === 'mr'): ?><i class="fa-solid fa-check text-success"></i><?php endif; ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $current_lang === 'hi' ? 'active fw-bold' : ''; ?>" href="<?php echo getLangUrl('hi'); ?>">
                                        <span>🇮🇳 हिन्दी</span>
                                        <?php if ($current_lang === 'hi'): ?><i class="fa-solid fa-check text-success"></i><?php endif; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Login Dropdown -->
                        <div class="nav-item dropdown">
                            <button class="btn-login-main dropdown-toggle" id="loginDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                                <span><?php echo __('nav_login'); ?></span>
                            </button>
                            <ul class="dropdown-menu login-dropdown-menu dropdown-menu-end"
                                aria-labelledby="loginDropdown">
                                <!-- Citizen Login -->
                                <li>
                                    <a class="dropdown-item login-role-card d-flex align-items-start gap-3"
                                        href="<?php echo $base_path; ?>login/citizen_login.php" id="citizen-login-btn">
                                        <div class="login-role-icon">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="login-role-title"><?php echo __('citizen_login_title'); ?></div>
                                            <div class="login-role-desc"><?php echo __('citizen_login_desc'); ?></div>
                                            <div class="mt-1">
                                                <span style="font-size:0.72rem;font-weight:700;color:var(--primary);"><?php echo __('citizen_login_btn'); ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider"
                                        style="border-color:rgba(139,94,52,0.12);margin:0.4rem 1rem;">
                                </li>
                                <!-- Official Login -->
                                <li>
                                    <a class="dropdown-item login-role-card d-flex align-items-start gap-3"
                                        href="<?php echo $base_path; ?>login/official_login.php" id="official-login-btn">
                                        <div class="login-role-icon"
                                            style="background:rgba(212,175,55,0.12);color:#9a7e1a;">
                                            <i class="fa-solid fa-building-columns"></i>
                                        </div>
                                        <div>
                                            <div class="login-role-title"><?php echo __('official_login_title'); ?></div>
                                            <div class="login-role-desc"><?php echo __('official_login_desc'); ?></div>
                                            <div class="mt-1">
                                                <span style="font-size:0.72rem;font-weight:700;color:var(--primary);"><?php echo __('official_login_btn'); ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div><!-- /navbar-collapse -->
            </nav>
        </div>
    </header>
