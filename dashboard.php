<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

$user = auth_require_auth();
$roleName = (string) $user['role_name'];
$roleLabel = $roleName === '' ? 'User' : $roleName;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GPCMS</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            <h1>Welcome to GPCMS</h1>
            <p class="lead">Hello, <?php echo auth_escape_output($user['full_name']); ?>.</p>
            <p class="lead">Your role: <?php echo auth_escape_output($roleLabel); ?></p>
            <p class="lead">This shared dashboard is the authenticated landing page for the current module structure.</p>
            <div class="auth-links">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
