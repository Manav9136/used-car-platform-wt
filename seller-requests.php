
<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

$car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
$seller_id = (int)$_SESSION['user_id'];

if (!$car_id) {
    die("Invalid car ID.");
}

// Confirm this car belongs to the logged-in seller
$stmt = $pdo->prepare(
    "SELECT id, brand, model FROM cars WHERE id = ? AND user_id = ?"
);
$stmt->execute([$car_id, $seller_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die("Car not found or you do not have permission to view its requests.");
}

// Get requests for this seller's car
$stmt = $pdo->prepare("
    SELECT pr.id, pr.status, pr.created_at,
           buyer.name AS buyer_name, buyer.email AS buyer_email
    FROM purchase_requests pr
    JOIN users buyer ON pr.buyer_id = buyer.id
    WHERE pr.car_id = ?
    ORDER BY pr.created_at DESC
");
$stmt->execute([$car_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-3">
        Purchase Requests: <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>
    </h2>

    <?php if (!$requests): ?>
        <div class="alert alert-info">No purchase requests for this car yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Buyer</th>
                        <th>Email</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['buyer_name']) ?></td>
                        <td><?= htmlspecialchars($r['buyer_email']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($r['status'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
