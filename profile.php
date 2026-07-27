<?php
/**
 * FILE: profile.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests to citizen/profile.php
 */
header("Location: citizen/profile.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
