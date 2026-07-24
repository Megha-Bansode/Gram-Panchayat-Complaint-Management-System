<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

auth_require_guest();

$message = '';
$messageType = 'error';

if (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = auth_escape_output($_GET['error']);
    $messageType = 'error';
} elseif (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = auth_escape_output($_GET['success']);
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Portal Login | GPCMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-shell">
        <div class="login-split">
            <div class="login-left">
                <div class="login-left-content">
                    <div class="brand-block">
                        <p class="eyebrow">GPCMS</p>
                        <h1>Citizen Portal</h1>
                        <p class="lead">Your gateway to digital rural governance. Register grievances, track applications, and view local updates.</p>
                    </div>
                    <ul class="login-feature-list">
                        <li><span>Aadhaar verified secure access</span></li>
                        <li><span>Real-time status updates</span></li>
                        <li><span>Apply for certificates online</span></li>
                    </ul>
                </div>
            </div>

            <div class="login-right">
                <div class="auth-card">
                    <div class="brand-block">
                        <p class="eyebrow">GPCMS</p>
                        <h1>Citizen Portal Login</h1>
                        <p class="lead">Access services, track complaints, and stay connected with your panchayat.</p>
                    </div>

                    <?php if ($message !== ''): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_process.php" method="post" class="auth-form" data-auth-form novalidate>
                        <input type="hidden" name="portal" value="citizen">

                        <div class="field-group">
                            <label for="login_id">Login ID</label>
                            <input type="text" id="login_id" name="login_id" placeholder="Enter your login ID" required data-required>
                        </div>

                        <div class="field-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required data-required>
                        </div>

                        <button type="submit" class="btn-primary">Sign In</button>
                    </form>

                    <div class="auth-links">
                        <a href="register.php">Create Account</a>
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>
