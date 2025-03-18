<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
print_r($_SESSION);
echo "</pre>";
print_r(session_status());
