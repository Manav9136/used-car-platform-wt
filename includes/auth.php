<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /used-car-platform/login.php");
        exit;
    }
}
?>
