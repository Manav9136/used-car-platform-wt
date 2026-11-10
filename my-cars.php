<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($del_id) {
        $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ? AND user_id = ? AND status = 'Pending'");
        $stmt->execute([$del_id, $_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row) {
            $imgPath = __DIR__ . '/uploads/' . $row['image'];
            if (file_exists($imgPath)) unlink($imgPath);
            $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ? AND user_id = ?");
            $stmt->execute([$del_id, $_SESSION['user_id']]);
        }
    }
    header("Location: /used-car-platform/my-cars.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once 'includes/header.php'; ?>
<h2 class="section-title">My Listings</h2>
<?php if (count($cars) === 0): ?>
  <div class="alert alert-info">You haven't listed any cars yet. <a href="/used-car-platform/sell-car.php">Sell your car</a></div>
<?php else: ?>
<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Car</th>
        <th>Price</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cars as $i => $car): ?>
      <tr>
        <td><?php echo $i + 1; ?></td>
        <td><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'); ?></td>
        <td>&#8377;<?php echo number_format($car['price']); ?></td>
        <td><span class="badge badge-status-<?php echo strtolower($car['status']); ?>"><?php echo $car['status']; ?></span></td>
        <td><?php echo date('d M Y', strtotime($car['created_at'])); ?></td>
        <td>
          <?php if ($car['status'] === 'Pending'): ?>
            <a href="/used-car-platform/edit-car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-outline-dark">Edit</a>
            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this listing?');">
              <input type="hidden" name="delete_id" value="<?php echo $car['id']; ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          
          <?php else: ?>
              <a href="/used-car-platform/car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-outline-dark">View</a>
              <a href="/used-car-platform/seller-requests.php?car_id=<?php echo (int)$car['id']; ?>" class="btn btn-sm btn-outline-primary">Requests</a>
          <?php endif; ?>

        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
