<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Redirect to relevant dashboard based on role
    if ($_SESSION["role"] === 'admin') {
        header("location: admin/dashboard.php");
    } else {
        header("location: cashier/dashboard.php");
    }
    exit;
} else {
    // If not logged in, redirect to login page
    header("location: login.php");
    exit;
}
?>
