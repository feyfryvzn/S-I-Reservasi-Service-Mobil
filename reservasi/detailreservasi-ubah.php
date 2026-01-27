<?php
include '../koneksi.php';
session_start();

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tangani logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    if (isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    } else {
        die("Token CSRF tidak valid!");
    }
}

if (!isset($_GET['id_reservasi']) || !isset($_GET['id_layanan'])) {
    header("Location: detailreservasi-lihat.php");
    exit();
}

$id_reservasi = $_GET['id_reservasi'];
$id_layanan = $_GET['id_layanan'];

// Ambil data detail reservasi
$stmt = $conn->prepare("
    SELECT dr.*, l.jenis_layanan 
    FROM detail_reservasi dr
    JOIN layanan l ON dr.id_layanan = l.id_layanan
    WHERE dr.id_reservasi = ? AND dr.id_layanan = ?
");
$stmt->bind_param("ss", $id_reservasi, $id_layanan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: detailreservasi-lihat.php");
    exit();
}

// Proses update
if (isset($_POST['proses'])) {
    $id_reservasi = $_GET['id_reservasi'];
    $id_layanan = $_GET['id_layanan'];
    $jumlah = (int) $_POST['jumlah'];
    $harga = $data['harga'];
    $subtotal = $jumlah * $harga;

    $stmt = $conn->prepare("UPDATE detail_reservasi SET jumlah=?, subtotal=? WHERE id_reservasi=? AND id_layanan=?");
    $stmt->bind_param("idss", $jumlah, $subtotal, $id_reservasi, $id_layanan);

    if ($stmt->execute()) {
    $stmt->close();
      // Hitung total_harga baru dari semua subtotal berdasarkan id_reservasi
      $stmt_total = $conn->prepare("SELECT SUM(subtotal) AS total FROM detail_reservasi WHERE id_reservasi = ?");
      $stmt_total->bind_param("s", $id_reservasi);
      $stmt_total->execute();
      $result_total = $stmt_total->get_result();
      $row_total = $result_total->fetch_assoc();
      $total_harga = $row_total['total'] ?? 0;
      $stmt_total->close();

      // Update total_harga di tabel reservasi
      $stmt_update_total = $conn->prepare("UPDATE reservasi SET total_harga = ? WHERE id_reservasi = ?");
      $stmt_update_total->bind_param("ds", $total_harga, $id_reservasi);
      $stmt_update_total->execute();
      $stmt_update_total->close();

      // Redirect kembali ke halaman lihat
      header("Location: detailreservasi-lihat.php?id_reservasi=" . urlencode($id_reservasi) . "&success=update");
      exit();
  }

    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ubah Reservasi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background-color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
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
      z-index: 1000;
    }

    .sidebar h4 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      color: #74b9ff;
    }

    .sidebar a {
      color: #fff;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      margin: 5px 0;
      text-decoration: none;
      border-radius: 8px;
      font-size: 1rem;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(0, 123, 255, 0.3);
      color: #fff;
    }

    .main {
      margin-left: 260px;
      padding: 30px;
    }

    .card {
      background-color: #f8f9fa;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745, #38c172);
      border: none;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #ef5753);
      border: none;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }

      .sidebar h4,
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
  <a href="../dashboard_adm.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
  <a href="reservasilihat.php" class="active"><i class="fas fa-users"></i><span>Reservasi</span></a>
  <a href="../layanan/layananlihat.php"><i class="fas fa-box"></i><span>Layanan</span></a>
    <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>

<div class="main">
  <div class="dashboard-header">
    <div>
      <h2 class="text-dark font-weight-bold mb-0">Ubah Reservasi</h2>
      <small class="text-muted"><?= date('H:i A \W\I\B, d F Y') ?></small>
    </div>
      <img src="../images/logo.png" alt="Logo" height="70">
  </div>

  <div class="card">
    <h5 class="mb-4 text-center">Form Ubah Reservasi</h5>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="" method="post">
      <input type="hidden" name="id_reservasi" value="<?= htmlspecialchars($id_reservasi) ?>">
      <input type="hidden" name="id_layanan" value="<?= htmlspecialchars($id_layanan) ?>">
      <div class="form-group">
        <label>ID Reservasi</label>
        <input type="text" name="id_reservasi" class="form-control" value="<?= htmlspecialchars($data['id_reservasi']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Jenis Layanan</label>
        <input type="text" name="jenis_layanan" class="form-control" value="<?= htmlspecialchars($data['jenis_layanan']) ?>" readonly>
      </div>
       <div class="form-group">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
      </div>
      <div class="form-group">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($data['harga']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Subtotal</label>
        <input type="number" name="subtotal" class="form-control" value="<?= htmlspecialchars($data['subtotal']) ?>" readonly>
      </div>
     
      <div class="text-right">
        <a href="detailreservasi-lihat.php" class="btn btn-danger">Kembali</a>
        <input type="submit" name="proses" value="Ubah" class="btn btn-success">
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
