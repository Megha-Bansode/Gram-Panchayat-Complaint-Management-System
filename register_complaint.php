<?php
/**
 * FILE: register_complaint.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests for register_complaint.php to citizen/register_complaint.php
 */
header("Location: citizen/register_complaint.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
