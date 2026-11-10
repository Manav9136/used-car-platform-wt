<?php
require_once 'config.php';
require_once 'includes/header.php';

$stmt = $pdo->prepare("SELECT * FROM cars WHERE status = 'Approved' ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
  <div class="container position-relative">
    <div class="hero-tagline"><i class="bi bi-shield-check me-2"></i> Verified Pre-Owned Vehicles</div>
    <h1>Find Your Next Car.</h1>
    <p>Browse thousands of verified used cars. Sell yours in minutes with zero listing fees.</p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="/used-car-platform/cars.php" class="btn btn-outline-black"><i class="bi bi-search me-2"></i>Explore Cars</a>
      <a href="/used-car-platform/sell-car.php" class="btn btn-black"><i class="bi bi-plus-circle me-2"></i>Sell Your Car</a>
    </div>
  </div>
</section>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h2 class="section-title mb-1">Recently Listed Cars</h2>
      <p class="text-muted small mb-0">Freshly added and verified by our team</p>
    </div>
    <?php if (count($cars) > 0): ?>
    <a href="/used-car-platform/cars.php" class="text-dark fw-bold text-decoration-none small">
      View All Cars <i class="bi bi-arrow-right ms-1"></i>
    </a>
    <?php endif; ?>
  </div>

  <?php if (count($cars) === 0): ?>
    <div class="empty-state-card text-center py-5">
      <div class="mb-3"><i class="bi bi-car-front fs-1 text-muted"></i></div>
      <h5 class="fw-bold mb-2">No cars listed yet</h5>
      <p class="text-muted mb-4">Be the first to list your vehicle and get maximum reach!</p>
      <a href="/used-car-platform/sell-car.php" class="btn btn-black"><i class="bi bi-plus-lg me-1"></i>List Your Car</a>
    </div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($cars as $car): ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card car-card h-100">
        <div class="car-img-wrapper">
          <img src="/used-car-platform/uploads/<?php echo htmlspecialchars($car['image']); ?>"
               alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
               onerror="this.src='https://via.placeholder.com/400x240?text=No+Image'">
          <span class="car-badge-fuel"><?php echo htmlspecialchars($car['fuel_type']); ?></span>
        </div>
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
          <p class="mb-2 text-muted small"><i class="bi bi-speedometer2 me-1"></i><?php echo (int)$car['year']; ?> &bull; <?php echo number_format($car['km_driven']); ?> km</p>
          <p class="car-price">&#8377;<?php echo number_format($car['price']); ?></p>
          <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($car['location']); ?></p>
          <a href="/used-car-platform/car-details.php?id=<?php echo (int)$car['id']; ?>" class="btn btn-black btn-sm w-100 mt-auto">View Details</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="text-center mt-5">
    <a href="/used-car-platform/cars.php" class="btn btn-black px-5 py-2">Explore All Cars</a>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
