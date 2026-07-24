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
    <title>Citizen Registration | GPCMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            <div class="brand-block">
                <p class="eyebrow">GPCMS</p>
                <h1>Create Citizen Account</h1>
                <p class="lead">Register to submit complaints and track progress securely.</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="register_process.php" method="post" class="auth-form" data-auth-form novalidate>
                <div class="field-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required data-required>
                </div>

                <div class="field-group">
                    <label for="login_id">Login ID</label>
                    <input type="text" id="login_id" name="login_id" placeholder="Choose a login ID" required data-required>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required data-required>
                </div>

                <div class="field-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required data-required>
                </div>

                <div class="field-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile_number" name="mobile_number" placeholder="10 digit mobile number" required data-required>
                </div>

                <button type="submit" class="btn-primary">Register</button>
            </form>

            <div class="auth-links">
                <a href="login.php">Already have an account?</a>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>
