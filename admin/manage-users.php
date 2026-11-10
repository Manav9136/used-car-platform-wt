<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

// Lines 6–31: Safe user deletion
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if (!$del) {
        $msg = 'Invalid user ID.';
        $msgType = 'danger';
    } elseif (isset($_SESSION['user_id']) && $del == $_SESSION['user_id']) {
        $msg = 'You cannot delete your own account.';
        $msgType = 'danger';
    } else {
        $check = $pdo->prepare("SELECT id, role FROM users WHERE id = ?");
        $check->execute([$del]);
        $targetUser = $check->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            $msg = 'User not found.';
            $msgType = 'danger';
        } elseif (strtolower($targetUser['role'] ?? 'user') === 'admin') {
            $msg = 'Admin accounts cannot be deleted here.';
            $msgType = 'danger';
        } else {
            try {
                $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $delete->execute([$del]);
                $msg = 'User deleted successfully.';
            } catch (PDOException $e) {
                $msg = 'Unable to delete this user. Check related car listings.';
                $msgType = 'danger';
            }
        }
    }
}

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM users";
$params = [];
if ($search !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like];
}
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="/used-car-platform/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  <div class="admin-sidebar" style="width:220px;min-width:220px;">
    <div class="brand">&#9679; ADMIN</div>
    <a href="/used-car-platform/admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/used-car-platform/admin/manage-cars.php"><i class="bi bi-car-front"></i> Manage Cars</a>
    <a href="/used-car-platform/admin/manage-users.php" class="active"><i class="bi bi-people"></i> Manage Users</a>
    <a href="/used-car-platform/admin/manage-requests.php"><i class="bi bi-cart-check"></i> Manage Requests</a>
    <a href="/used-car-platform/admin/logout.php" class="mt-5 text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>
  <div class="flex-grow-1 p-4">
    <h2 class="fw-bold mb-4">Manage Users</h2>
    <?php if ($msg): ?>
      <div class="alert alert-<?php echo htmlspecialchars($msgType); ?>">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>
    <form method="GET" class="d-flex gap-2 mb-3">
      <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>" style="max-width:300px;">
      <button type="submit" class="btn btn-black">Search</button>
      <?php if ($search): ?><a href="/used-car-platform/admin/manage-users.php" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr><th>#</th><th>Name</th><th>Email</th><th>Mobile</th><th>Registered</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $u): ?>
          <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>Not available</td>
            <td>Not recorded</td>
            <td>
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete user and all their car listings?');">
                <input type="hidden" name="delete_id" value="<?php echo $u['id']; ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
