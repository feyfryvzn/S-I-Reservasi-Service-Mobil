<?php
session_start();

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tangani logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    if (isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
        session_destroy();
        header("Location: index.php");
        exit;
    } else {
        die("Token CSRF tidak valid!");
    }
}

// Cek role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      color: #000;
      margin: 0;
      overflow-x: hidden;
    }

    .sidebar {
      height: 100vh;
      background: linear-gradient(180deg, #0b1e33, #0d2744);
      color: #fff;
      padding: 20px 15px;
      position: fixed;
      width: 250px;
      top: 0;
      left: 0;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
      transition: width 0.3s ease;
      z-index: 1000;
    }

    .sidebar h4 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      letter-spacing: 1px;
      color: #74b9ff;
    }

    .sidebar a {
      color: #fff;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      margin: 5px 0;
      text-decoration: none;
      font-size: 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .sidebar a i {
      margin-right: 10px;
      font-size: 1.2rem;
    }

    .sidebar a:hover {
      background-color: rgba(0, 123, 255, 0.3);
      color: #fff;
      transform: translateX(5px);
    }

    .sidebar a.active {
      background-color: #007bff;
      color: #fff;
      font-weight: 600;
    }

    .main {
      margin-left: 260px;
      padding: 30px;
      transition: margin-left 0.3s ease;
    }

    .card-custom {
      border-radius: 20px;
      border: none;
      padding: 20px;
      color: #fff;
    }

    .card-blue {
      background: linear-gradient(135deg, #3a8dde, #007bff);
    }

    .card-lightblue {
      background: linear-gradient(135deg, #74b9ff, #0984e3);
    }

    .card-white {
      background-color: #f8f9fa;
      color: #000;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-white h5 {
      font-weight: 600;
    }

    .badge {
      padding: 5px 10px;
      font-size: 0.75rem;
      border-radius: 10px;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
        padding: 20px 5px;
      }

      .sidebar h4 {
        display: none;
      }

      .sidebar a {
        justify-content: center;
        padding: 10px;
      }

      .sidebar a i {
        margin-right: 0;
      }

      .sidebar a span {
        display: none;
      }

      .main {
        margin-left: 80px;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h4>Panel Admin</h4>
    <a href="dashboard_adm.php" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    <a href="reservasi/reservasilihat.php"><i class="fas fa-users"></i><span>Reservasi</span></a>
    <a href="layanan/layananlihat.php"><i class="fas fa-box"></i><span>Layanan</span></a>
    <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt"></i>Logout</a>
  </div>

  <div class="main">
    <div class="dashboard-header">
      <div>
        <h2 class="text-dark font-weight-bold mb-0">Dashboard</h2>
      </div>
      <img src="./images/logo.png" alt="Logo" height="70">
    </div>
         

    <footer class="mt-5 text-center text-muted">
      <small>© 2025 MBD System. All rights reserved.</small>
    </footer>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>