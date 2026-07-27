<?php
/**
 * FILE: notifications.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests to citizen/notifications.php
 */
header("Location: citizen/notifications.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
