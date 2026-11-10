
<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /used-car-platform/admin/manage-requests.php");
    exit;
}

$request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if (!$request_id || !in_array($status, ['approved', 'rejected'], true)) {
    die("Invalid request.");
}

$stmt = $pdo->prepare(
    "UPDATE purchase_requests
     SET status = ?
     WHERE id = ? AND status = 'pending'"
);
$stmt->execute([$status, $request_id]);

header("Location: /used-car-platform/admin/manage-requests.php");
exit;
?>
