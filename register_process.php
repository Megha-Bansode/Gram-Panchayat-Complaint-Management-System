<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth_functions.php';

auth_require_guest();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    auth_redirect('register.php', 'Invalid request method.');
}

$fullName = auth_sanitize_input($_POST['full_name'] ?? '');
$loginId = auth_sanitize_input($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$mobileNumber = auth_sanitize_input($_POST['mobile_number'] ?? '');

$errors = [];

if ($fullName === '') {
    $errors[] = 'Full name is required.';
}

if ($loginId === '') {
    $errors[] = 'Login ID is required.';
} elseif (strlen($loginId) < 4) {
    $errors[] = 'Login ID must be at least 4 characters.';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

if (!preg_match('/^[0-9]{10}$/', $mobileNumber)) {
    $errors[] = 'Mobile number must be exactly 10 digits.';
}

if ($errors !== []) {
    auth_redirect('register.php', implode(' ', $errors));
}

$conn = get_db_connection();

$existingUser = auth_get_user_by_login_id($loginId);
if ($existingUser !== null) {
    auth_redirect('register.php', 'This login ID is already taken.');
}

$citizenRoleId = auth_get_role_id_by_name('Citizen');
if ($citizenRoleId === null) {
    auth_redirect('register.php', 'Citizen role is not available.');
}

if (auth_get_user_by_login_id($loginId) !== null) {
    auth_redirect('register.php', 'This login ID is already taken.');
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare(
    'INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->bind_param('sssiss', $fullName, $loginId, $passwordHash, $citizenRoleId, $mobileNumber, $status);
$status = 'active';
$stmt->execute();
$stmt->close();

auth_redirect('login.php', 'Registration successful. Please sign in.', 'success');
