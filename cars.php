<?php
require_once 'config.php';
require_once 'includes/auth.php';

$search    = trim($_GET['search'] ?? '');
$fuel_filter = $_GET['fuel'] ?? '';

$sql = "SELECT * FROM cars WHERE status = 'Approved'";
$params = [];

if ($search !== '') {
    $sql .= " AND (brand LIKE ? OR model LIKE ? OR location LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like, $like];
}
if ($fuel_filter !== '') {
    $sql .= " AND fuel_type = ?";
    $params[] = $fuel_filter;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h2 class="section-title mb-1">Browse Cars</h2>
    <p class="text-muted small mb-0">Discover verified pre-owned vehicles with complete inspection details</p>
  </div>
  <div class="text-muted small">
    Showing <strong><?php echo count($cars); ?></strong> cars
  </div>
</div>

<div class="filter-card">
  <form method="GET" class="row g-3 align-items-end">
    <div class="col-lg-6 col-md-5">
      <label class="form-label small fw-bold text-muted">SEARCH VEHICLES</label>
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Brand, model, or location..." value="<?php echo htmlspecialchars($search); ?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-3">
      <label class="form-label small fw-bold text-muted">FUEL TYPE</label>
      <select name="fuel" class="form-select">
        <option value="">All Fuel Types</option>
        <?php foreach (['Petrol','Diesel','CNG','LPG','Electric'] as $f): ?>
          <option value="<?php echo $f; ?>" <?php echo $fuel_filter === $f ? 'selected' : ''; ?>><?php echo $f; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-lg-2 col-md-2">
      <button type="submit" class="btn btn-black w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
    </div>
    <?php if ($search || $fuel_filter): ?>
    <div class="col-lg-1 col-md-2">
      <a href="/used-car-platform/cars.php" class="btn btn-outline-secondary w-100">Clear</a>
    </div>
    <?php endif; ?>
  </form>
</div>

<?php if (count($cars) === 0): ?>
  <div class="empty-state-card text-center py-5">
    <div class="mb-3"><i class="bi bi-search fs-1 text-muted"></i></div>
    <h5 class="fw-bold mb-2">No cars found matching your criteria</h5>
    <p class="text-muted mb-4">Try clearing filters or searching for another brand or model.</p>
    <a href="/used-car-platform/cars.php" class="btn btn-outline-black btn-sm">Clear All Filters</a>
  </div>
<?php else: ?>
<div class="row g-4">
  <?php foreach ($cars as $car): ?>
  <div class="col-lg-3 col-md-4 col-sm-6">
    <div class="card car-card h-100">
      <div class="car-img-wrapper">
        <?php
        $imageFile = $car['image'] ?? '';
        $imagePath = __DIR__ . '/uploads/' . basename($imageFile);
        ?>

        <?php if ($imageFile !== '' && is_file($imagePath)): ?>
          <img
            src="/used-car-platform/uploads/<?php echo rawurlencode(basename($imageFile)); ?>"
            alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
            class="car-image"
          >
        <?php else: ?>
          <div class="car-image-placeholder">No Image Available</div>
        <?php endif; ?>
        <span class="car-badge-fuel"><?php echo htmlspecialchars($car['fuel_type']); ?></span>
      </div>
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
        <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
          <span><i class="bi bi-calendar3 me-1"></i><?php echo (int)$car['year']; ?></span>
          <span>&bull;</span>
          <span><i class="bi bi-speedometer2 me-1"></i><?php echo number_format((float)($car['km_driven'] ?? 0)); ?> km</span>
          <span>&bull;</span>
          <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($car['transmission'] ?? 'Not specified'); ?></span>
        </div>
        <p class="car-price">&#8377;<?php echo number_format($car['price']); ?></p>
        <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($car['location']); ?></p>
        <a href="/used-car-platform/car-details.php?id=<?php echo (int)$car['id']; ?>" class="btn btn-black btn-sm w-100 mt-auto">View Details</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
