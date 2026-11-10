<?php
require_once 'includes/auth.php';
$_SESSION = [];
session_destroy();
header("Location: /used-car-platform/index.php");
exit;
?>
