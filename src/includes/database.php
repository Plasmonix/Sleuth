<?php

require_once "config.php";
require_once "handler.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host; dbname=$database", $user, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$licenseSystem = new LicenseSystem($pdo);

if (!$licenseSystem->isLoggedIn()) {
    if (basename($_SERVER['PHP_SELF']) != 'login.php') { // Prevent infinite redirect
        header("Location: login.php");
        exit();
    }
}

