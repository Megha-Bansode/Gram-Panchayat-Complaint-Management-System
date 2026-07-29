<?php

declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

auth_start_session();

// Preserve session role & referer before destroying session
$roleName = strtolower(trim((string) ($_SESSION['role_name'] ?? '')));
$referer = strtolower(trim((string) ($_SERVER['HTTP_REFERER'] ?? '')));
$portal = strtolower(trim((string) ($_GET['portal'] ?? '')));

auth_logout();

// Determine target login portal (Citizen vs Official)
$isCitizen = ($portal === 'citizen') || str_contains($roleName, 'citizen') || str_contains($referer, '/citizen/');
$targetLogin = $isCitizen ? 'login.php' : 'official_login.php';

auth_redirect($targetLogin, 'You have been logged out successfully.', 'success');
