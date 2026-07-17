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
        $currentRole = $_SESSION["role"] ?? '';
        // If superadmin, allow it to pass for both admin and superadmin roles
        if ($currentRole === 'superadmin') {
            return;
        }
        if (is_array($role)) {
            if (!in_array($currentRole, $role)) {
                header("location: ../login.php");
                exit;
            }
        } else {
            if ($currentRole !== $role) {
                header("location: ../login.php");
                exit;
            }
        }
    }
}
?>
