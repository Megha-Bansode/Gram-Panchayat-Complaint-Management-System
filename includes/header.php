<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Shared Navigation Header
 * handbook reference: includes/header.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = $_SESSION['is_logged_in'] ?? false;
$user_name = $_SESSION['full_name'] ?? 'User';
$role_name = $_SESSION['role_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gram Panchayat Complaint Management System (GPCMS)</title>
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Global Application CSS -->
  <link rel="stylesheet" href="../css/field.css">
</head>
<body class="bg-light">
