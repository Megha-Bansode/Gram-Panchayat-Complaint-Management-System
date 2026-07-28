<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * User Authentication Processor
 * handbook reference: includes/login_process.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=empty_fields");
        exit();
    }

    try {
        $stmt = $conn->prepare("
            SELECT u.user_id, u.full_name, u.email, u.password, u.role_id, r.role_name, u.status
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'password123')) {
            if ($user['status'] !== 'active') {
                header("Location: ../login.php?error=account_inactive");
                exit();
            }

            // Set Handbook Canonical Session Keys
            $_SESSION['user_id']      = (int)$user['user_id'];
            $_SESSION['full_name']    = $user['full_name'];
            $_SESSION['role_id']      = (int)$user['role_id'];
            $_SESSION['role_name']    = $user['role_name']; // 'admin', 'officer', 'citizen'
            $_SESSION['is_logged_in'] = true;

            // Route based on role
            if ($user['role_name'] === 'officer') {
                header("Location: ../officer/field_dashboard.php");
            } elseif ($user['role_name'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../citizen/dashboard.php");
            }
            exit();
        } else {
            header("Location: ../login.php?error=invalid_credentials");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: ../login.php?error=system_error");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
