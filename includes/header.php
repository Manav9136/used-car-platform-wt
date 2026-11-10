<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Used Car Platform - CarMarket</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="/used-car-platform/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4" href="/used-car-platform/index.php">
      <span class="brand-dot"></span> CARMARKET
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="/used-car-platform/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'cars.php' ? 'active' : ''; ?>" href="/used-car-platform/cars.php">Browse Cars</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link <?php echo $current_page === 'sell-car.php' ? 'active' : ''; ?>" href="/used-car-platform/sell-car.php">Sell Car</a></li>
          <li class="nav-item"><a class="nav-link <?php echo $current_page === 'my-cars.php' ? 'active' : ''; ?>" href="/used-car-platform/my-cars.php">My Listings</a></li>
          <li class="nav-item"><a class="nav-link <?php echo $current_page === 'my-requests.php' ? 'active' : ''; ?>" href="/used-car-platform/my-requests.php">My Requests</a></li>
          <li class="nav-item ms-lg-2"><span class="user-badge"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
          <li class="nav-item ms-lg-2"><a class="btn btn-outline-light btn-sm px-3" href="/used-car-platform/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item ms-lg-2"><a class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>" href="/used-car-platform/login.php">Login</a></li>
          <li class="nav-item ms-lg-2"><a class="btn btn-light btn-sm text-black fw-bold px-3" href="/used-car-platform/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="site-main <?php echo $current_page !== 'index.php' ? 'container py-4' : ''; ?>">
