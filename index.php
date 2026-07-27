<?php
/**
 * FILE: index.php (Root Entry Point)
 * PURPOSE: Redirects user to login or citizen dashboard depending on session state.
 */
session_start();

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: citizen/citizen_dashboard.php");
} else {
    header("Location: login.php");
}
exit();
