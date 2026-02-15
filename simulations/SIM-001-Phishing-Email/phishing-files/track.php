<?php
/*
SIM-001 Tracking Script
Logs click events for controlled phishing simulation
Lab use only – no credential harvesting
*/

// Log file location
$logfile = "/var/www/html/phish_log.txt";

// Collect request details
$ip = $_SERVER['REMOTE_ADDR'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "Direct";
$timestamp = date("Y-m-d H:i:s");

// Construct log entry
$logentry = "TIME: $timestamp | IP: $ip | REFERRER: $referrer | USER-AGENT: $useragent\n";

// Write to log
file_put_contents($logfile, $logentry, FILE_APPEND);

// Redirect user to legitimate site
header("Location: https://www.microsoft.com");
exit();
?>
