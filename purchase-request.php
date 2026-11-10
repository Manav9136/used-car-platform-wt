
<?php
require_once 'config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only logged-in users can send requests
if (!isset($_SESSION['user_id'])) {
    header("Location: /used-car-platform/login.php");
    exit;
}

$car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
$buyer_id = (int) $_SESSION['user_id'];

if (!$car_id) {
    die("Invalid car ID.");
}

// Check that the car exists and is approved
$stmt = $pdo->prepare(
    "SELECT id, user_id, brand, model, status
     FROM cars
     WHERE id = ?"
);
$stmt->execute([$car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die("Car not found.");
}

if ($car['status'] !== 'approved') {
    die("This car is not available for purchase.");
}

if ((int)$car['user_id'] === $buyer_id) {
    die("You cannot request your own car.");
}

// Prevent duplicate requests
$stmt = $pdo->prepare(
    "SELECT id FROM purchase_requests
     WHERE car_id = ? AND buyer_id = ? AND status = 'pending'"
);
$stmt->execute([$car_id, $buyer_id]);

if ($stmt->fetch()) {
    header("Location: /used-car-platform/my-requests.php");
    exit;
}

// Save the request
$stmt = $pdo->prepare(
    "INSERT INTO purchase_requests (car_id, buyer_id, status)
     VALUES (?, ?, 'pending')"
);
$stmt->execute([$car_id, $buyer_id]);

echo "<h2>Purchase request sent successfully!</h2>";
echo "<p>Your request for " .
     htmlspecialchars($car['brand'] . ' ' . $car['model']) .
     " has been saved.</p>";
echo '<a href="/used-car-platform/">Return to homepage</a>';
?>
