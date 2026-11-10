<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: /used-car-platform/admin/login.php");
        exit; 
    }
}
?>
