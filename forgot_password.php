<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

auth_require_guest();

$message = '';
$messageType = 'error';
$showResetForm = false;
$verifiedLoginId = '';
$verifiedMobile = '';

if (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = auth_escape_output($_GET['error']);
    $messageType = 'error';
} elseif (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = auth_escape_output($_GET['success']);
    $messageType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = strtolower(trim((string) ($_POST['recovery_step'] ?? 'verify')));
    $loginId = auth_sanitize_input($_POST['login_id'] ?? '');
    $mobileNumber = auth_sanitize_input($_POST['mobile_number'] ?? '');

    if ($step === 'verify') {
        if ($loginId === '' || $mobileNumber === '') {
            $message = 'Please enter both login ID and mobile number.';
        } else {
            $user = auth_get_user_by_login_id($loginId);
            if ($user === null || (string) $user['mobile_number'] !== $mobileNumber) {
                $message = 'We could not verify that account. Please check your details.';
            } elseif ((string) $user['status'] !== 'active') {
                $message = 'This account is not active.';
            } else {
                $showResetForm = true;
                $verifiedLoginId = $loginId;
                $verifiedMobile = $mobileNumber;
                $message = 'Identity verified. Please choose a new password.';
                $messageType = 'success';
            }
        }
    } else {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword === '' || $confirmPassword === '') {
            $message = 'Please enter and confirm your new password.';
        } elseif (strlen($newPassword) < 8) {
            $message = 'Password must be at least 8 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'Passwords do not match.';
        } else {
            $user = auth_get_user_by_login_id($loginId);
            if ($user === null || (string) $user['mobile_number'] !== $mobileNumber) {
                $message = 'The verification details are no longer valid.';
            } else {
                $conn = get_db_connection();
                $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE login_id = ? AND mobile_number = ?');
                $stmt->bind_param('sss', $passwordHash, $loginId, $mobileNumber);
                $stmt->execute();
                $stmt->close();

                auth_redirect('login.php', 'Password updated successfully. Please sign in.', 'success');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | GPCMS</title>
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
                <h1>Recover Access</h1>
                <p class="lead">Verify your account details and create a fresh password securely.</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($showResetForm): ?>
                <form action="forgot_password.php" method="post" class="auth-form" data-auth-form novalidate>
                    <input type="hidden" name="recovery_step" value="reset">
                    <input type="hidden" name="login_id" value="<?php echo auth_escape_output($verifiedLoginId); ?>">
                    <input type="hidden" name="mobile_number" value="<?php echo auth_escape_output($verifiedMobile); ?>">

                    <div class="field-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter a new password" required data-required>
                    </div>

                    <div class="field-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter the password" required data-required>
                    </div>

                    <button type="submit" class="btn-primary">Reset Password</button>
                </form>
            <?php else: ?>
                <form action="forgot_password.php" method="post" class="auth-form" data-auth-form novalidate>
                    <input type="hidden" name="recovery_step" value="verify">

                    <div class="field-group">
                        <label for="login_id">Login ID</label>
                        <input type="text" id="login_id" name="login_id" placeholder="Enter your login ID" required data-required>
                    </div>

                    <div class="field-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" placeholder="Enter your registered mobile number" required data-required>
                    </div>

                    <button type="submit" class="btn-primary">Verify Account</button>
                </form>
            <?php endif; ?>

            <div class="auth-links">
                <a href="login.php">Back to Citizen Login</a>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>
