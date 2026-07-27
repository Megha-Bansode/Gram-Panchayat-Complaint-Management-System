<?php
/**
 * FILE: my_complaints.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests for my_complaints.php to citizen/my_complaints.php
 *          Ensures links to my_complaints.php work from both root and citizen directory.
 */
header("Location: citizen/my_complaints.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
