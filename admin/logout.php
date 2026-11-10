<?php
require_once '../includes/admin-auth.php';
$_SESSION = [];
session_destroy();
header("Location: /used-car-platform/admin/login.php");
exit;
?>
