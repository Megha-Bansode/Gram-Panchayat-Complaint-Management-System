<?php

declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

auth_logout();

$portal = strtolower(trim((string) ($_GET['portal'] ?? 'official')));
$targetLogin = $portal === 'citizen' ? 'login.php' : 'official_login.php';

auth_redirect($targetLogin, 'You have been logged out successfully.', 'success');
