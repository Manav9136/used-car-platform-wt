<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';
    $allowed = ['Approved', 'Rejected'];
    if ($car_id && in_array($action, $allowed)) {
        $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
        $stmt->execute([$action, $car_id]);
        $msg = "Car #$car_id has been $action.";
    }
}

$cars = $pdo->query("SELECT cars.*, users.name AS seller_name FROM cars JOIN users ON cars.user_id = users.id ORDER BY cars.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Cars - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="/used-car-platform/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  <div class="admin-sidebar" style="width:220px;min-width:220px;">
    <div class="brand">&#9679; ADMIN</div>
    <a href="/used-car-platform/admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/used-car-platform/admin/manage-cars.php" class="active"><i class="bi bi-car-front"></i> Manage Cars</a>
    <a href="/used-car-platform/admin/manage-users.php"><i class="bi bi-people"></i> Manage Users</a>
    <a href="/used-car-platform/admin/logout.php" class="mt-5 text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>
  <div class="flex-grow-1 p-4">
    <h2 class="fw-bold mb-4">Manage Car Listings</h2>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>#</th><th>Seller</th><th>Car</th><th>Price</th><th>Status</th><th>Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cars as $i => $car): ?>
          <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($car['seller_name']); ?></td>
            <td><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'); ?></td>
            <td>&#8377;<?php echo number_format($car['price']); ?></td>
            <td><span class="badge badge-status-<?php echo strtolower($car['status']); ?>"><?php echo $car['status']; ?></span></td>
            <td><?php echo date('d M Y', strtotime($car['created_at'])); ?></td>
            <td>
              <a href="/used-car-platform/admin/view-car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-outline-dark">View</a>
              <?php if ($car['status'] === 'Pending'): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                <button type="submit" name="action" value="Approved" class="btn btn-sm btn-success">Approve</button>
                <button type="submit" name="action" value="Rejected" class="btn btn-sm btn-danger">Reject</button>
              </form>
              <?php endif; ?>
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
