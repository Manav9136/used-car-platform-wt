<?php
require_once 'config.php';
require_once 'includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Invalid car ID.");
}

$stmt = $pdo->prepare("SELECT cars.*, users.name AS seller_name, users.email AS seller_email, users.mobile AS seller_mobile FROM cars JOIN users ON cars.user_id = users.id WHERE cars.id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die("Car not found or not available.");
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="mb-4">
  <a href="/used-car-platform/cars.php" class="btn btn-sm btn-outline-secondary px-3">
    <i class="bi bi-arrow-left me-1"></i> Back to Browse
  </a>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="position-relative">
      <img src="/used-car-platform/uploads/<?php echo htmlspecialchars($car['image']); ?>"
           class="car-detail-img w-100"
           alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
           onerror="this.src='https://via.placeholder.com/800x450?text=No+Image'">
    </div>
  </div>
  <div class="col-lg-5">
    <div class="detail-card">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <h2 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
      </div>
      <p class="car-price fs-2 mb-3">&#8377;<?php echo number_format($car['price']); ?></p>

      <div class="row g-2 mb-4">
        <?php
        $specs = [
          ['label' => 'Brand', 'val' => $car['brand'], 'icon' => 'bi-award'],
          ['label' => 'Model', 'val' => $car['model'], 'icon' => 'bi-car-front'],
          ['label' => 'Year', 'val' => $car['year'], 'icon' => 'bi-calendar3'],
          ['label' => 'KM Driven', 'val' => number_format((float)($car['km_driven'] ?? 0)) . ' km', 'icon' => 'bi-speedometer2'],
          ['label' => 'Fuel Type', 'val' => $car['fuel_type'], 'icon' => 'bi-fuel-pump'],
          ['label' => 'Transmission', 'val' => htmlspecialchars($car['transmission'] ?? 'Not specified'), 'icon' => 'bi-gear'],
          ['label' => 'Location', 'val' => $car['location'], 'icon' => 'bi-geo-alt'],
          ['label' => 'Status', 'val' => $car['status'], 'icon' => 'bi-check-circle'],
        ];
        foreach ($specs as $s): ?>
        <div class="col-6">
          <div class="spec-item h-100">
            <p class="detail-label"><i class="bi <?php echo $s['icon']; ?> me-1"></i><?php echo $s['label']; ?></p>
            <p class="detail-value"><?php echo htmlspecialchars((string)$s['val']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="mb-4">
        <p class="detail-label mb-2">Vehicle Overview</p>
        <p class="text-secondary small lh-base mb-0"><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
      </div>

      <?php if (isset($_SESSION['user_id'])): ?>
      <div class="seller-card">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
            <i class="bi bi-person fs-5"></i>
          </div>
          <div>
            <p class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($car['seller_name']); ?></p>
            <span class="badge bg-success-subtle text-success border border-success-subtle small">Verified Seller</span>
          </div>
        </div>
        <hr class="my-2 text-muted opacity-25">
        <p class="mb-0 text-dark fw-semibold"><i class="bi bi-telephone-fill me-2 text-primary"></i> <?php echo htmlspecialchars($car['seller_mobile'] ?? ''); ?></p>
            </div>

      <?php
      $isOwnCar = isset($_SESSION['user_id'])
          && (int)$_SESSION['user_id'] === (int)$car['user_id'];
      ?>

      <?php if (isset($_SESSION['user_id']) && !$isOwnCar && strtolower($car['status']) === 'approved'): ?>
        <div class="mt-3">
          <a href="/used-car-platform/purchase-request.php?car_id=<?php echo (int)$car['id']; ?>"
             class="btn btn-dark w-100">
            <i class="bi bi-cart-check me-2"></i> Send Purchase Request
          </a>
        </div>
      <?php endif; ?>

      <?php else: ?>
      <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" role="alert">
        <i class="bi bi-lock-fill fs-5"></i>
        <div>
          <a href="/used-car-platform/login.php" class="fw-bold text-dark text-decoration-underline">Login</a> to view seller contact details.
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
