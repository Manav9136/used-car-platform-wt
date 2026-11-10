<?php
require_once 'config.php';
require_once 'includes/auth.php';
if (isset($_SESSION['user_id'])) {
    header("Location: /used-car-platform/index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $pass   = $_POST['password'] ?? '';
    $cpass  = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$mobile || !$pass || !$cpass) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $error = 'Enter a valid 10-digit Indian mobile number.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $cpass) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $mobile, $hashed]);
            $success = 'Registration successful! You can now login.';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>
<div class="form-container">
  <h2>Create Account</h2>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <a href="/used-car-platform/login.php">Login here</a></div><?php endif; ?>
  <form method="POST" novalidate id="regForm">
    <div class="mb-3">
      <label class="form-label fw-bold">Full Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-bold">Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-bold">Mobile Number</label>
      <input type="text" name="mobile" class="form-control" maxlength="10" value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-bold">Password</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label fw-bold">Confirm Password</label>
      <input type="password" name="confirm_password" class="form-control">
    </div>
    <button type="submit" class="btn-submit">Register</button>
  </form>
  <p class="text-center mt-3">Already have an account? <a href="/used-car-platform/login.php">Login</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>
