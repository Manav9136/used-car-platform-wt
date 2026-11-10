<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

$total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_cars     = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$pending_cars   = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='Pending'")->fetchColumn();
$approved_cars  = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='Approved'")->fetchColumn();
$rejected_cars  = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='Rejected'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - CarMarket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="/used-car-platform/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  <div class="admin-sidebar" style="width:220px;min-width:220px;">
    <div class="brand">&#9679; ADMIN</div>
    <a href="/used-car-platform/admin/dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/used-car-platform/admin/manage-cars.php"><i class="bi bi-car-front"></i> Manage Cars</a>
    <a href="/used-car-platform/admin/manage-users.php"><i class="bi bi-people"></i> Manage Users</a>
    <a href="/used-car-platform/admin/manage-requests.php">Manage Purchase Requests</a>
    <a href="/used-car-platform/admin/logout.php" class="mt-5 text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>
  <div class="flex-grow-1 p-4">
    <h2 class="fw-bold mb-4">Dashboard</h2>
    <p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong></p>
    <div class="row g-3 mt-2">
      <div class="col-md-2 col-sm-4">
        <div class="stat-card">
          <div class="number"><?php echo $total_users; ?></div>
          <div class="label">Total Users</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4">
        <div class="stat-card">
          <div class="number"><?php echo $total_cars; ?></div>
          <div class="label">Total Cars</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4">
        <div class="stat-card">
          <div class="number text-warning"><?php echo $pending_cars; ?></div>
          <div class="label">Pending</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4">
        <div class="stat-card">
          <div class="number text-success"><?php echo $approved_cars; ?></div>
          <div class="label">Approved</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4">
        <div class="stat-card">
          <div class="number text-danger"><?php echo $rejected_cars; ?></div>
          <div class="label">Rejected</div>
        </div>
      </div>
    </div>
    <div class="mt-4">
      <a href="/used-car-platform/admin/manage-cars.php" class="btn btn-black me-2">Manage Cars</a>
      <a href="/used-car-platform/admin/manage-users.php" class="btn btn-black">Manage Users</a>
    </div>
  </div>
</div>
</body>
</html>
