
<?php
require_once '../config.php';
require_once '../includes/admin-auth.php';
requireAdmin();

$sql = "
    SELECT
        pr.id AS request_id,
        pr.status AS request_status,
        pr.created_at,
        c.brand,
        c.model,
        c.year,
        c.price,
        buyer.name AS buyer_name,
        buyer.email AS buyer_email,
        seller.name AS seller_name
    FROM purchase_requests pr
    JOIN cars c ON pr.car_id = c.id
    JOIN users buyer ON pr.buyer_id = buyer.id
    JOIN users seller ON c.user_id = seller.id
    ORDER BY pr.created_at DESC
";

$requests = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Purchase Requests</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/used-car-platform/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <a href="/used-car-platform/admin/dashboard.php" class="btn btn-outline-secondary mb-3">
        Back to Dashboard
    </a>

    <h2 class="fw-bold mb-4">Manage Purchase Requests</h2>

    <?php if (!$requests): ?>
        <div class="alert alert-info">No purchase requests found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Car</th>
                        <th>Buyer</th>
                        <th>Seller</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= (int)$r['request_id'] ?></td>
                        <td><?= htmlspecialchars($r['brand'].' '.$r['model'].' '.$r['year']) ?></td>
                        <td>
                            <?= htmlspecialchars($r['buyer_name']) ?><br>
                            <small><?= htmlspecialchars($r['buyer_email']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($r['seller_name']) ?></td>
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
                            <?php if ($r['request_status'] === 'pending'): ?>
                                <form method="POST" action="update-request.php" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= (int)$r['request_id'] ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="POST" action="update-request.php" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= (int)$r['request_id'] ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                No action
                            <?php endif; ?>
                        </td>
                        </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
