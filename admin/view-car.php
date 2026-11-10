<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: /used-car-platform/admin/manage-cars.php"); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'approved') {
      $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
      $stmt->execute(['approved', $id]);
      $msg = "Car has been approved.";
    }
}

$stmt = $pdo->prepare("SELECT cars.*, users.name AS seller_name, users.email AS seller_email, users.mobile AS seller_mobile FROM cars JOIN users ON cars.user_id = users.id WHERE cars.id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$car) { die("Car not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Car - Admin</title>
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
    <a href="/used-car-platform/admin/manage-cars.php" class="btn btn-outline-secondary btn-sm mb-3">&larr; Back</a>
    <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <img src="/used-car-platform/uploads/<?php echo htmlspecialchars($car['image']); ?>"
             class="car-detail-img"
             onerror="this.src='https://via.placeholder.com/600x360?text=No+Image'">
      </div>
      <div class="col-md-6">
        <table class="table table-bordered">
          <tr><th>Status</th><td><span class="badge badge-status-<?php echo strtolower($car['status']); ?>"><?php echo $car['status']; ?></span></td></tr>
          <tr><th>Brand</th><td><?php echo htmlspecialchars($car['brand']); ?></td></tr>
          <tr><th>Model</th><td><?php echo htmlspecialchars($car['model']); ?></td></tr>
          <tr><th>Year</th><td><?php echo (int)$car['year']; ?></td></tr>
          <tr><th>Price</th><td>&#8377;<?php echo number_format($car['price']); ?></td></tr>
          <tr><th>KM Driven</th><td><?php echo number_format($car['km_driven']); ?> km</td></tr>
          <tr><th>Fuel</th><td><?php echo htmlspecialchars($car['fuel_type']); ?></td></tr>
          <tr><th>Transmission</th><td><?php echo htmlspecialchars($car['transmission']); ?></td></tr>
          <tr><th>Location</th><td><?php echo htmlspecialchars($car['location']); ?></td></tr>
          <tr><th>Seller</th><td><?php echo htmlspecialchars($car['seller_name']); ?></td></tr>
          <tr><th>Email</th><td><?php echo htmlspecialchars($car['seller_email']); ?></td></tr>
          <tr><th>Mobile</th><td><?php echo htmlspecialchars($car['seller_mobile']); ?></td></tr>
        </table>
        <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
        <?php if (strtolower($car['status']) === 'pending'): ?>
        <form method="POST" class="d-flex gap-2">
          <button type="submit" name="action" value="approved"
            class="btn btn-success flex-fill">Approve</button>

      
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
