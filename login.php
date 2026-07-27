<?php
/**
 * FILE: login.php
 * PURPOSE: Citizen login + self-registration.
 *          Sets all session keys per GPCMS Handbook V3.1.
 * HANDBOOK: GPCMS Engineering Handbook V3.1
 */

session_start();

// ── Already logged in → go straight to dashboard ─────────
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: citizen/citizen_dashboard.php");
    exit();
}

require_once 'config/db_connect.php';

$error_msg   = '';
$success_msg = '';
$active_tab  = 'login'; // default tab

// ════════════════════════════════════════════════════════
// HANDLE LOGIN
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {

    $login_id = trim($_POST['login_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login_id) || empty($password)) {
        $error_msg = "Please enter both Login ID and password.";
    } else {
        // Contract: users.login_id, users.password_hash, users.status
        $stmt = $conn->prepare(
            "SELECT u.user_id, u.full_name, u.password_hash, u.role_id, u.status,
                    r.role_name
             FROM users u
             INNER JOIN roles r ON u.role_id = r.role_id
             WHERE u.login_id = ?
             LIMIT 1"
        );
        $stmt->bind_param("s", $login_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $error_msg = "Your account is not active. Please contact the administrator.";
            } else {
                // Set session keys per contract
                session_regenerate_id(true);
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id']      = (int) $user['user_id'];
                $_SESSION['full_name']    = $user['full_name'];
                $_SESSION['role_id']      = (int) $user['role_id'];
                $_SESSION['role_name']    = $user['role_name'];

                // Route by role
                if ($user['role_name'] === 'Citizen') {
                    header("Location: citizen/citizen_dashboard.php");
                } else {
                    header("Location: citizen/citizen_dashboard.php");
                }
                exit();
            }
        } else {
            $error_msg = "Invalid Login ID or password. Please try again.";
        }
    }
}

// ════════════════════════════════════════════════════════
// HANDLE REGISTER
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $active_tab = 'register';

    $full_name     = trim($_POST['full_name']        ?? '');
    $reg_login_id  = trim($_POST['reg_login_id']     ?? '');
    $mobile_number = trim($_POST['mobile_number']    ?? '');
    $password      = trim($_POST['reg_password']     ?? '');
    $confirm_pw    = trim($_POST['confirm_password'] ?? '');

    $errors = [];
    if (empty($full_name))                               $errors[] = "Full name is required.";
    if (strlen($reg_login_id) < 4)                       $errors[] = "Login ID must be at least 4 characters.";
    if (!preg_match('/^[6-9][0-9]{9}$/', $mobile_number)) $errors[] = "Enter a valid 10-digit mobile number.";
    if (strlen($password) < 6)                           $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_pw)                       $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        // Check login_id uniqueness per contract
        $chk = $conn->prepare("SELECT user_id FROM users WHERE login_id = ? LIMIT 1");
        $chk->bind_param("s", $reg_login_id);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error_msg = "This Login ID is already taken. Please choose another.";
        } else {
            $hash    = password_hash($password, PASSWORD_BCRYPT);
            $role_id = 3;        // Citizen role_id
            $status  = 'active'; // default per contract
            $ins = $conn->prepare(
                "INSERT INTO users (full_name, login_id, password_hash, mobile_number, role_id, status)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param("ssssis", $full_name, $reg_login_id, $hash, $mobile_number, $role_id, $status);
            if ($ins->execute()) {
                $success_msg = "Account created! You can now log in with your Login ID.";
                $active_tab  = 'login';
            } else {
                $error_msg = "Registration failed. Please try again.";
            }
            $ins->close();
        }
        $chk->close();
    } else {
        $error_msg = implode(" ", $errors);
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to GPCMS – Gram Panchayat Complaint Management System">
    <title>Login | GPCMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:    #8A724C;
            --secondary:  #B99668;
            --accent:     #DCC9A7;
            --surface:    #EDE2CC;
            --background: #F7F3E8;
            --text-main:  #4a3e2a;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 460px;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(138,114,76,0.18);
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: #fff;
        }
        .auth-header-icon {
            width: 68px; height: 68px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        .auth-header h1 {
            font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem;
        }
        .auth-header p { font-size: 0.83rem; opacity: 0.87; margin: 0; }

        .auth-tabs {
            display: flex;
            border-bottom: 2px solid var(--accent);
            background: #faf8f3;
        }
        .auth-tab {
            flex: 1; padding: 1rem; text-align: center;
            font-weight: 600; font-size: 0.9rem; cursor: pointer;
            background: none; border: none;
            font-family: 'Poppins', sans-serif;
            color: var(--secondary);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: color 0.2s, border-color 0.2s;
        }
        .auth-tab.active { color: var(--primary); border-bottom-color: var(--primary); }

        .auth-body { padding: 2rem; }
        .form-panel { display: none; }
        .form-panel.active { display: block; }

        .auth-label {
            display: block; font-size: 0.85rem; font-weight: 600;
            color: var(--primary); margin-bottom: 0.4rem;
        }
        .auth-input {
            width: 100%; border: 1.5px solid var(--accent); border-radius: 8px;
            padding: 0.75rem 1rem; font-family: 'Poppins', sans-serif;
            font-size: 0.9rem; color: var(--text-main);
            background: var(--background);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .auth-input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(138,114,76,0.18);
        }
        .form-grp { margin-bottom: 1.2rem; }
        .pw-wrap { position: relative; }
        .pw-wrap .auth-input { padding-right: 3rem; }
        .pw-toggle {
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--secondary); font-size: 1rem;
        }
        .btn-auth {
            width: 100%; background: var(--primary); color: #fff;
            border: none; border-radius: 8px; padding: 0.85rem;
            font-family: 'Poppins', sans-serif; font-size: 0.95rem;
            font-weight: 600; cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }
        .btn-auth:hover { background: var(--secondary); transform: translateY(-1px); }
        .btn-auth:active { transform: translateY(0); }

        .auth-alert {
            border-radius: 8px; padding: 0.85rem 1rem;
            margin-bottom: 1.25rem; font-size: 0.875rem;
            display: flex; align-items: flex-start; gap: 0.6rem;
        }
        .auth-alert-error   { background:#fde8e8; color:#8b1a1a; border-left:4px solid #8b1a1a; }
        .auth-alert-success { background:#d4edda; color:#0f5132; border-left:4px solid #0f5132; }

        .demo-notice {
            background: var(--surface); border-radius: 8px;
            padding: 0.85rem 1rem; margin-bottom: 1.25rem;
            font-size: 0.8rem; color: var(--text-main);
            border-left: 4px solid var(--primary);
        }
        .demo-notice strong { color: var(--primary); }
        .demo-notice code {
            background: rgba(138,114,76,0.12); padding: 0.1rem 0.4rem;
            border-radius: 4px; font-size: 0.8rem;
        }
        .auth-footer {
            text-align: center; padding: 1rem 2rem 1.5rem;
            font-size: 0.78rem; color: #aaa;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Header -->
        <div class="auth-header">
            <div class="auth-header-icon" aria-hidden="true">
                <i class="bi bi-building"></i>
            </div>
            <h1>GPCMS</h1>
            <p>Gram Panchayat Complaint Management System</p>
        </div>

        <!-- Tabs -->
        <div class="auth-tabs" role="tablist">
            <button class="auth-tab <?php echo $active_tab === 'login'    ? 'active' : ''; ?>"
                    id="tab-login" role="tab"
                    aria-selected="<?php echo $active_tab === 'login' ? 'true' : 'false'; ?>"
                    aria-controls="panel-login"
                    onclick="switchTab('login')">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </button>
            <button class="auth-tab <?php echo $active_tab === 'register' ? 'active' : ''; ?>"
                    id="tab-register" role="tab"
                    aria-selected="<?php echo $active_tab === 'register' ? 'true' : 'false'; ?>"
                    aria-controls="panel-register"
                    onclick="switchTab('register')">
                <i class="bi bi-person-plus me-1"></i>Register
            </button>
        </div>

        <div class="auth-body">

            <!-- Alerts -->
            <?php if (!empty($error_msg)): ?>
            <div class="auth-alert auth-alert-error" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($success_msg)): ?>
            <div class="auth-alert auth-alert-success" role="alert">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span><?php echo htmlspecialchars($success_msg); ?></span>
            </div>
            <?php endif; ?>

            <!-- Demo credentials -->
            <div class="demo-notice">
                <strong>Demo Account:</strong><br>
                Login ID: <code>citizen01</code> &nbsp;
                Password: <code>Citizen@123</code>
            </div>

            <!-- ── LOGIN PANEL ── -->
            <div class="form-panel <?php echo $active_tab === 'login' ? 'active' : ''; ?>"
                 id="panel-login" role="tabpanel" aria-labelledby="tab-login">
                <form method="POST" action="login.php" id="loginForm">
                    <input type="hidden" name="action" value="login">

                    <div class="form-grp">
                        <label class="auth-label" for="login_id">Login ID</label>
                        <input type="text" class="auth-input" id="login_id" name="login_id"
                               placeholder="Your Login ID" required autocomplete="username"
                               value="<?php echo isset($_POST['login_id']) && ($_POST['action']??'') === 'login'
                                         ? htmlspecialchars($_POST['login_id']) : ''; ?>">
                    </div>

                    <div class="form-grp">
                        <label class="auth-label" for="login_password">Password</label>
                        <div class="pw-wrap">
                            <input type="password" class="auth-input" id="login_password" name="password"
                                   placeholder="Your password" required autocomplete="current-password">
                            <button type="button" class="pw-toggle"
                                    onclick="togglePw('login_password', this)"
                                    aria-label="Show or hide password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth" id="btn-login">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </button>
                </form>
            </div>

            <!-- ── REGISTER PANEL ── -->
            <div class="form-panel <?php echo $active_tab === 'register' ? 'active' : ''; ?>"
                 id="panel-register" role="tabpanel" aria-labelledby="tab-register">
                <form method="POST" action="login.php" id="registerForm">
                    <input type="hidden" name="action" value="register">

                    <div class="form-grp">
                        <label class="auth-label" for="full_name">Full Name</label>
                        <input type="text" class="auth-input" id="full_name" name="full_name"
                               placeholder="Your full name" required autocomplete="name"
                               value="<?php echo isset($_POST['full_name']) && ($_POST['action']??'') === 'register'
                                         ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>

                    <div class="form-grp">
                        <label class="auth-label" for="reg_login_id">Login ID <span style="color:#aaa;font-weight:400;">(min. 4 chars, no spaces)</span></label>
                        <input type="text" class="auth-input" id="reg_login_id" name="reg_login_id"
                               placeholder="e.g. ramesh01" required minlength="4" autocomplete="username"
                               value="<?php echo isset($_POST['reg_login_id']) && ($_POST['action']??'') === 'register'
                                         ? htmlspecialchars($_POST['reg_login_id']) : ''; ?>">
                    </div>

                    <div class="form-grp">
                        <label class="auth-label" for="mobile_number">Mobile Number</label>
                        <input type="tel" class="auth-input" id="mobile_number" name="mobile_number"
                               placeholder="10-digit mobile number" required
                               pattern="[6-9][0-9]{9}" maxlength="10"
                               value="<?php echo isset($_POST['mobile_number']) && ($_POST['action']??'') === 'register'
                                         ? htmlspecialchars($_POST['mobile_number']) : ''; ?>">
                    </div>

                    <div class="form-grp">
                        <label class="auth-label" for="reg_password">
                            Password <span style="color:#aaa;font-weight:400;">(min. 6 chars)</span>
                        </label>
                        <div class="pw-wrap">
                            <input type="password" class="auth-input" id="reg_password" name="reg_password"
                                   placeholder="Choose a strong password" required
                                   minlength="6" autocomplete="new-password">
                            <button type="button" class="pw-toggle"
                                    onclick="togglePw('reg_password', this)"
                                    aria-label="Show or hide password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-grp">
                        <label class="auth-label" for="confirm_password">Confirm Password</label>
                        <input type="password" class="auth-input" id="confirm_password"
                               name="confirm_password"
                               placeholder="Repeat your password" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-auth" id="btn-register">
                        <i class="bi bi-person-plus me-1"></i>Create Citizen Account
                    </button>
                </form>
            </div>

        </div><!-- /.auth-body -->

        <div class="auth-footer">
            © <?php echo date('Y'); ?> Gram Panchayat Complaint Management System &nbsp;·&nbsp; v3.1
        </div>

    </div><!-- /.auth-card -->
</div><!-- /.auth-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function switchTab(tab) {
    document.querySelectorAll('.auth-tab').forEach(function(t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.form-panel').forEach(function(p) {
        p.classList.remove('active');
    });
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('tab-' + tab).setAttribute('aria-selected', 'true');
    document.getElementById('panel-' + tab).classList.add('active');
}

function togglePw(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Confirm password validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var pw  = document.getElementById('reg_password').value;
    var cpw = document.getElementById('confirm_password').value;
    if (pw !== cpw) {
        e.preventDefault();
        alert('Passwords do not match. Please check and try again.');
        document.getElementById('confirm_password').focus();
    }
});
</script>
</body>
</html>
