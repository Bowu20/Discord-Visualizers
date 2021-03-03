<?php
$file = "index.php";

// Quick check to verify that the file exists
if (!file_exists($file)) die("File not found");

// Force the download
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($file));
header("Content-Type: application/octet-stream;");
readfile($file);