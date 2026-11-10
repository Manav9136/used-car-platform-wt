<?php
require_once 'config.php';
require_once 'includes/auth.php';
if (isset($_SESSION['user_id'])) {
    header("Location: /used-car-platform/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (!$email || !$pass) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: /used-car-platform/index.php");
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>
<div class="form-container">
  <h2>Welcome Back</h2>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-bold">Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-bold">Password</label>
      <input type="password" name="password" class="form-control">
    </div>
    <button type="submit" class="btn-submit">Login</button>
  </form>
  <p class="text-center mt-3">New here? <a href="/used-car-platform/register.php">Create an account</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>
