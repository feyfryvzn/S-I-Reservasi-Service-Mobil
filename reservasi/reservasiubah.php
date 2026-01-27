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

    if (!isset($_GET['id_reservasi']) || empty($_GET['id_reservasi'])) {
        header("Location: reservasilihat.php");
        exit();
    }

    $id_reservasi = $_GET['id_reservasi'];
    $stmt = $conn->prepare("SELECT * FROM reservasi WHERE id_reservasi = ?");
    $stmt->bind_param("s", $id_reservasi);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if (!$data) {
        header("Location: reservasilihat.php");
        exit();
    }

    if (isset($_POST['proses'])) {
        $nama_lengkap = $_POST['nama_lengkap'];
        $nomor_telepon = $_POST['nomor_telepon'];
        $no_mesin = $_POST['no_mesin'];
        $nopolisi = $_POST['nopolisi'];
        $status = $_POST['status'];
        $status_pembayaran = $_POST['status_pembayaran'];

        $stmt = $conn->prepare("UPDATE reservasi SET nama_lengkap=?, nomor_telepon=?, no_mesin=?, nopolisi=?, status=?, status_pembayaran=? WHERE id_reservasi=?");
        $stmt->bind_param("sssssss", $nama_lengkap, $nomor_telepon, $no_mesin, $nopolisi, $status, $status_pembayaran, $id_reservasi);

        if ($stmt->execute()) {
            header("Location: reservasilihat.php?success=update");
            exit();
        } else {
            $error = "Gagal memperbarui data: " . $stmt->error;
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
      <div class="form-group">
        <label>ID Reservasi</label>
        <input type="text" name="id_reservasi" class="form-control" value="<?= htmlspecialchars($data['id_reservasi']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" reaquired>
      </div>
      <div class="form-group">
        <label>Nomor Telepon</label>
        <input type="text" name="nomor_telepon" class="form-control" value="<?= htmlspecialchars($data['nomor_telepon']) ?>" required>
      </div>
      <div class="form-group">
        <label>No Mesin</label>
        <input type="text" name="no_mesin" class="form-control" value="<?= htmlspecialchars($data['no_mesin']) ?>" required>
      </div>
      <div class="form-group">
        <label>No Polisi</label>
        <input type="text" name="nopolisi" class="form-control" value="<?= htmlspecialchars($data['nopolisi']) ?>" required>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Antri" <?= $data['status'] == 'Antri' ? 'selected' : '' ?>>Antri</option>
          <option value="Proses" <?= $data['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
          <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status Pembayaran</label>
        <select name="status_pembayaran" class="form-control">
          <option value="Belum Lunas" <?= $data['status_pembayaran'] == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
          <option value="Lunas" <?= $data['status_pembayaran'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
        </select>
      </div>
      <div class="text-right">
        <a href="reservasilihat.php" class="btn btn-danger">Kembali</a>
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
