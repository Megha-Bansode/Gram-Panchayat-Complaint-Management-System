<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

auth_logout();
auth_redirect('login.php', 'You have been logged out successfully.', 'success');
