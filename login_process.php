<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

auth_require_guest();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    auth_redirect('login.php', 'Invalid request method.');
}

$portal = strtolower(trim((string) ($_POST['portal'] ?? 'citizen')));
$loginId = auth_sanitize_input($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($loginId === '' || $password === '') {
    $redirectPage = $portal === 'official' ? 'official_login.php' : 'login.php';
    auth_redirect($redirectPage, 'Please enter both login ID and password.');
}

$user = auth_get_user_by_login_id($loginId);
if ($user === null) {
    $redirectPage = $portal === 'official' ? 'official_login.php' : 'login.php';
    auth_redirect($redirectPage, 'Invalid login credentials.');
}

$allowedRoleNames = $portal === 'official' ? ['Super Admin', 'Gram Sevak', 'Field Officer'] : ['Citizen'];
$roleName = (string) $user['role_name'];

if ($portal === 'official' && $roleName === 'Citizen') {
    auth_redirect('official_login.php', 'Citizen accounts cannot use the official portal.');
}

if (!in_array($roleName, $allowedRoleNames, true)) {
    $redirectPage = $portal === 'official' ? 'official_login.php' : 'login.php';
    auth_redirect($redirectPage, 'You are not authorized to use this portal.');
}

if ((string) $user['status'] !== 'active') {
    $redirectPage = $portal === 'official' ? 'official_login.php' : 'login.php';
    auth_redirect($redirectPage, 'Your account is inactive.');
}

if (!password_verify($password, (string) $user['password_hash'])) {
    $redirectPage = $portal === 'official' ? 'official_login.php' : 'login.php';
    auth_redirect($redirectPage, 'Invalid login credentials.');
}

auth_set_user_session($user);
auth_redirect(auth_get_dashboard_path($roleName));
