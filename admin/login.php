
<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin is already logged in, open dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: /used-car-platform/admin/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ? AND role = 'admin'"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['name'];

            header("Location: /used-car-platform/admin/dashboard.php");
            exit;
        } else {
            $error = 'Invalid admin email or password.';
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="form-container">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="email"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password"
                   class="form-control" required>
        </div>

        <button type="submit" class="btn-submit">Login</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
