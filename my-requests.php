
<?php
require_once 'config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /used-car-platform/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];


$stmt = $pdo->prepare("
    SELECT
        pr.id AS request_id,
        pr.status AS request_status,
        pr.created_at,
        c.id AS car_id,
        c.user_id AS seller_id,
        c.brand,
        c.model,
        c.year,
        c.price,
        c.image,
        seller.name AS seller_name,
        seller.email AS seller_email,
        seller.mobile AS seller_mobile
    FROM purchase_requests pr
    JOIN cars c ON pr.car_id = c.id
    JOIN users seller ON c.user_id = seller.id
    WHERE pr.buyer_id = ?
    ORDER BY pr.created_at DESC
");

$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">My Purchase Requests</h2>

    <?php if (empty($requests)): ?>
        <div class="alert alert-info">
            You haven't submitted any purchase requests yet.
            <a href="/used-car-platform/cars.php">Browse cars</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Car</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?>
                        </td>
                        <td><?= (int)$r['year'] ?></td>
                        <td>₹<?= number_format((float)$r['price']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                        
                        <td>
                            <?php
                            $badgeClass = 'bg-warning text-dark';

                            if ($r['request_status'] === 'approved') {
                                $badgeClass = 'bg-success';
                            } elseif ($r['request_status'] === 'rejected') {
                                $badgeClass = 'bg-danger';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars(ucfirst($r['request_status'])) ?>
                            </span>
                        </td>

                        
                        <td>
                            <a class="btn btn-sm btn-outline-dark"
                            href="/used-car-platform/car-details.php?id=<?= (int)$r['car_id'] ?>">
                                View Car
                            </a>

                            <?php if ($r['request_status'] === 'approved'): ?>
                                <div class="mt-2">
                                    <strong><?= htmlspecialchars($r['seller_name']) ?></strong><br>
                                    <a href="mailto:<?= htmlspecialchars($r['seller_email']) ?>">
                                        Email Seller
                                    </a>

                                    <?php if (!empty($r['seller_mobile'])): ?>
                                        <br>
                                        <a href="tel:<?= htmlspecialchars($r['seller_mobile']) ?>">
                                            Call Seller
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
