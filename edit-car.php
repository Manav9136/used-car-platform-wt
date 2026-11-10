<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: /used-car-platform/my-cars.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) { die("Car not found or access denied."); }
if ($car['status'] !== 'Pending') { die("Only pending listings can be edited."); }

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

    $allowed_fuels = ['Petrol', 'Diesel', 'CNG', 'LPG', 'Electric'];
    $allowed_trans = ['Manual', 'Automatic'];
    $current_year  = (int)date('Y');

    if (!$brand || !$model || !$location || !$desc || !$fuel || !$trans) {
        $error = 'All fields are required.';
    } elseif ($year < 1990 || $year > $current_year) {
        $error = 'Enter a valid manufacturing year.';
    } elseif ($price <= 0) {
        $error = 'Price must be a positive number.';
    } elseif ($km < 0) {
        $error = 'KM driven cannot be negative.';
    } elseif (!in_array($fuel, $allowed_fuels) || !in_array($trans, $allowed_trans)) {
        $error = 'Invalid fuel or transmission type.';
    } else {
        $image = $car['image'];
        if (!empty($_FILES['image']['name'])) {
            $file    = $_FILES['image'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed)) {
                $error = 'Only JPG, PNG, and WebP images are allowed.';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $error = 'Image must be under 3MB.';
            } else {
                $newname = uniqid('car_', true) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $newname)) {
                    $old = __DIR__ . '/uploads/' . $image;
                    if (file_exists($old)) unlink($old);
                    $image = $newname;
                } else {
                    $error = 'Image upload failed.';
                }
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("UPDATE cars SET brand=?, model=?, year=?, price=?, km_driven=?, fuel_type=?, transmission=?, location=?, description=?, image=?, status='Pending', updated_at=NOW() WHERE id=? AND user_id=?");
            $stmt->execute([$brand, $model, $year, $price, $km, $fuel, $trans, $location, $desc, $image, $id, $_SESSION['user_id']]);
            $success = 'Listing updated and resubmitted for admin review.';
            $car = array_merge($car, ['brand'=>$brand,'model'=>$model,'year'=>$year,'price'=>$price,'km_driven'=>$km,'fuel_type'=>$fuel,'transmission'=>$trans,'location'=>$location,'description'=>$desc,'image'=>$image]);
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h2 class="section-title">Edit Listing</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Car Brand</label>
          <input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($car['brand']); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Car Model</label>
          <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($car['model']); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Manufacturing Year</label>
          <input type="number" name="year" class="form-control" min="1990" max="<?php echo date('Y'); ?>" value="<?php echo (int)$car['year']; ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Price (&#8377;)</label>
          <input type="number" name="price" class="form-control" min="1" value="<?php echo (float)$car['price']; ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">KM Driven</label>
          <input type="number" name="km_driven" class="form-control" min="0" value="<?php echo (int)$car['km_driven']; ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Fuel Type</label>
          <select name="fuel_type" class="form-control">
            <?php foreach (['Petrol','Diesel','CNG','LPG','Electric'] as $f): ?>
              <option value="<?php echo $f; ?>" <?php echo $car['fuel_type'] === $f ? 'selected' : ''; ?>><?php echo $f; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Transmission</label>
          <select name="transmission" class="form-control">
            <option value="Manual" <?php echo $car['transmission'] === 'Manual' ? 'selected' : ''; ?>>Manual</option>
            <option value="Automatic" <?php echo $car['transmission'] === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Location</label>
          <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($car['location']); ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-bold">Description</label>
          <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($car['description']); ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label fw-bold">Car Image (leave blank to keep current)</label>
          <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
          <small class="text-muted">Current: <?php echo htmlspecialchars($car['image']); ?></small>
        </div>
        <div class="col-12">
          <button type="submit" class="btn-submit">Update Listing</button>
          <a href="/used-car-platform/my-cars.php" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
