<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Module: Field Officer Shared Header Include
 * File Ownership: Field Team (Module #6 / #7)
 * Purpose: Provides reusable HTML document head, title initialization, CSS imports, and app container wrapper.
 */

if (!isset($page_title)) {
    $page_title = "Field Officer - Gram Panchayat System";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Field Officer Shared CSS -->
  <link href="../css/field.css" rel="stylesheet">
</head>
<body>

  <div class="app-container">
