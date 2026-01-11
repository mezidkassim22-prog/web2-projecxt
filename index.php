<?php
session_start();

// If user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

// Redirect based on role
$role = $_SESSION['user']['role'];

if ($role === 'admin') {
    header("Location: admin/dashboard.php");
} else {
    header("Location: user/dashboard.php");
}

exit();
