<?php
/**
 * FILE: track_complaint.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests for track_complaint.php to citizen/track_complaint.php
 */
header("Location: citizen/track_complaint.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
