<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Global Language & Session Management Component
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowed_languages = ['en', 'hi', 'mr'];

// Handle language change request via GET parameter
if (isset($_GET['lang'])) {
    $requested_lang = strtolower(trim($_GET['lang']));
    if (in_array($requested_lang, $allowed_languages)) {
        $_SESSION['lang'] = $requested_lang;
    }

    // Reconstruct current URL without 'lang' parameter for a clean redirect
    $uri = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($uri);
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : $_SERVER['PHP_SELF'];
    
    $queryParams = [];
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $queryParams);
        unset($queryParams['lang']);
    }
    
    $redirectUrl = $path . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');
    
    header('Location: ' . $redirectUrl);
    exit();
}

// Default to English if no session language exists
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
if (!in_array($current_lang, $allowed_languages)) {
    $current_lang = 'en';
    $_SESSION['lang'] = 'en';
}

// Load language file using absolute dirname path
$lang_file_path = dirname(__DIR__) . '/languages/' . $current_lang . '.php';
if (file_exists($lang_file_path)) {
    $lang = require $lang_file_path;
} else {
    $lang = require dirname(__DIR__) . '/languages/en.php';
}

/**
 * Global Translation helper function
 * @param string $key
 * @param string $default
 * @return string
 */
if (!function_exists('__')) {
    function __($key, $default = '') {
        global $lang;
        return isset($lang[$key]) ? $lang[$key] : ($default !== '' ? $default : $key);
    }
}
