<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth($role = null) {
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: ../login.php");
        exit;
    }

    if ($role) {
        if (is_array($role)) {
            if (!in_array($_SESSION["role"], $role)) {
                header("location: ../login.php");
                exit;
            }
        } else {
            if ($_SESSION["role"] !== $role) {
                header("location: ../login.php");
                exit;
            }
        }
    }
}
?>
