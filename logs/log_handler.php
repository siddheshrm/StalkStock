<?php

// Log file path
define('LOG_FILE', __DIR__ . '/scraper.log');

date_default_timezone_set('Asia/Kolkata');

function write_log($message)
{
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}