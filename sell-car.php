<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand    = trim($_POST['brand'] ?? '');
    $model    = trim($_POST['model'] ?? '');
    $year     = (int)($_POST['year'] ?? 0);
    $price    = (float)($_POST['price'] ?? 0);
    $km       = (int)($_POST['km_driven'] ?? 0);
    $fuel     = $_POST['fuel_type'] ?? '';
    $trans    = $_POST['transmission'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $desc     = trim($_POST['description'] ?? '');

    $allowed_fuels  = ['Petrol', 'Diesel', 'CNG', 'LPG', 'Electric'];
    $allowed_trans  = ['Manual', 'Automatic'];
    $current_year   = (int)date('Y');

    if (!$brand || !$model || !$location || !$desc || !$fuel || !$trans) {
        $error = 'All fields are required.';
    } elseif ($year < 1990 || $year > $current_year) {
        $error = 'Enter a valid manufacturing year.';
    } elseif ($price <= 0) {
        $error = 'Price must be a positive number.';
    } elseif ($km < 0) {
        $error = 'KM driven cannot be negative.';
    } elseif (!in_array($fuel, $allowed_fuels)) {
        $error = 'Invalid fuel type.';
    } elseif (!in_array($trans, $allowed_trans)) {
        $error = 'Invalid transmission type.';
    } elseif (empty($_FILES['image']['name'])) {
        $error = 'Car image is required.';
    } else {
        $file     = $_FILES['image'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
        $max_size = 3 * 1024 * 1024;

        if (!in_array($ext, $allowed)) {
            $error = 'Only JPG, PNG, and WebP images are allowed.';
        } elseif ($file['size'] > $max_size) {
            $error = 'Image size must be under 3MB.';
        } else {
            $filename = uniqid('car_', true) . '.' . $ext;
            $dest     = __DIR__ . '/uploads/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $stmt = $pdo->prepare("INSERT INTO cars (user_id, brand, model, year, price, km_driven, fuel_type, transmission, location, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $brand, $model, $year, $price, $km, $fuel, $trans, $location, $desc, $filename]);
                $success = 'Your car has been submitted for review. It will appear publicly once approved by the admin.';
            } else {
                $error = 'Image upload failed. Please try again.';
            }
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h2 class="section-title">Sell Your Car</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <a href="/used-car-platform/my-cars.php">View my listings</a></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data" id="sellForm">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Car Brand</label>
          <input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($_POST['brand'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Car Model</label>
          <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($_POST['model'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Manufacturing Year</label>
          <input type="number" name="year" class="form-control" min="1990" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($_POST['year'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Price (&#8377;)</label>
          <input type="number" name="price" class="form-control" min="1" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">KM Driven</label>
          <input type="number" name="km_driven" class="form-control" min="0" value="<?php echo htmlspecialchars($_POST['km_driven'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Fuel Type</label>
          <select name="fuel_type" class="form-control">
            <option value="">Select Fuel</option>
            <?php foreach (['Petrol','Diesel','CNG','LPG','Electric'] as $f): ?>
              <option value="<?php echo $f; ?>" <?php echo (($_POST['fuel_type'] ?? '') === $f) ? 'selected' : ''; ?>><?php echo $f; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Transmission</label>
          <select name="transmission" class="form-control">
            <option value="">Select</option>
            <option value="Manual" <?php echo (($_POST['transmission'] ?? '') === 'Manual') ? 'selected' : ''; ?>>Manual</option>
            <option value="Automatic" <?php echo (($_POST['transmission'] ?? '') === 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Location</label>
          <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-bold">Description</label>
          <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label fw-bold">Car Image (JPG, PNG, WebP — max 3MB)</label>
          <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div class="col-12">
          <button type="submit" class="btn-submit">Submit for Review</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
