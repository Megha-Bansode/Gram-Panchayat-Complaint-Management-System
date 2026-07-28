<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * User Registration Processor
 * handbook reference: includes/register_process.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email     = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $ward_no   = filter_input(INPUT_POST, 'ward_no', FILTER_SANITIZE_SPECIAL_CHARS);
    $password  = $_POST['password'] ?? '';

    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        header("Location: ../register.php?error=empty_fields");
        exit();
    }

    try {
        // Check existing user
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetch()) {
            header("Location: ../register.php?error=email_exists");
            exit();
        }

        // Default role is 3 ('citizen')
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $conn->prepare("
            INSERT INTO users (full_name, email, phone, password, role_id, ward_no, status)
            VALUES (:full_name, :email, :phone, :password, 3, :ward_no, 'active')
        ");
        $insertStmt->execute([
            ':full_name' => $full_name,
            ':email'     => $email,
            ':phone'     => $phone,
            ':password'  => $hashed_password,
            ':ward_no'   => $ward_no
        ]);

        header("Location: ../login.php?success=registered");
        exit();
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        header("Location: ../register.php?error=system_error");
        exit();
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>
