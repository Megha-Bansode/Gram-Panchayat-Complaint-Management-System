<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Super Admin') {
    auth_redirect('official_login.php', 'Unauthorized access.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard | GPCMS</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            <h1>Super Admin Dashboard</h1>
            <p class="lead">Welcome, <?php echo auth_escape_output($user['full_name']); ?>.</p>
            <p class="lead">You have successfully logged in to the super admin portal.</p>
            <div class="auth-links">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
