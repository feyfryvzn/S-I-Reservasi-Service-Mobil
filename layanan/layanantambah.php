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
        header("Location: ../index.php");
        exit;
    } else {
        die("Token CSRF tidak valid!");
    }
}
if (isset($_POST['proses'])) {
    include '../koneksi.php';
    
    $id_layanan = $_POST['id_layanan'];
    $jenis_layanan = $_POST['jenis_layanan'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    
    // Validasi input
    if (empty($id_layanan) || empty($jenis_layanan) || empty($harga)) {
        $error = "Semua kolom harus diisi!";
    } else {
        if ($conn) {
            $stmt = $conn->prepare("INSERT INTO layanan (id_layanan, jenis_layanan, harga, deskripsi) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssss", $id_layanan, $jenis_layanan, $harga, $deskripsi);
                if ($stmt->execute()) {
                    header("Location: layananlihat.php?success=1");
                    exit();
                } else {
                    $error = "Gagal menyimpan data: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Gagal menyiapkan query: " . $conn->error;
            }
            $conn->close();
        } else {
            $error = "Gagal terhubung ke database";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Layanan - Tambah</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
      transition: margin-left: 0.3s ease;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .card {
      background-color: #f8f9fa;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745, #38c172);
      border: none;
      font-weight: 600;
    }

    .btn-success:hover {
      background: linear-gradient(135deg, #38c172, #28a745);
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #ef5753);
      border: none;
      font-weight: 600;
    }

    .btn-danger:hover {
      background: linear-gradient(135deg, #ef5753, #dc3545);
    }

    .form-group label {
      font-weight: 600;
      color: #333;
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ced4da;
    }

    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .alert {
      border-radius: 8px;
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
        <a href="../dashboard_adm.php" ><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="../reservasi/reservasilihat.php" ><i class="fas fa-users"></i><span>Reservasi</span></a>
        <a href="../layanan/layananlihat.php"class="active"><i class="fas fa-box"></i><span>Layanan</span></a>
        <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

  <div class="main">
    <div class="dashboard-header">
      <div>
        <h2 class="text-dark font-weight-bold mb-0">Tambah Layanan</h2>
        <small class="text-muted"><?= date('H:i A \W\I\B, d F Y') ?></small>
      </div>
      <img src="../images/logo.png" alt="Logo" height="70">
    </div>

    <div class="card">
      <h5 class="mb-4 text-center">Form Tambah Layanan</h5>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form action="" method="post">
        <div class="form-group">
          <label for="id_layanan">ID Layanan</label>
          <input type="number" name="id_layanan" id="id_layanan" class="form-control" placeholder="Masukkan ID Layanan" value="<?php echo isset($_POST['id_layanan']) ? htmlspecialchars($_POST['id_layanan']) : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="jenis_layanan">Jenis Layanan</label>
          <input type="text" name="jenis_layanan" id="jenis_layanan" class="form-control" placeholder="Masukkan Jenis Layanan" value="<?php echo isset($_POST['jenis_layanan']) ? htmlspecialchars($_POST['jenis_layanan']) : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="harga">Harga</label>
          <input type="number" name="harga" id="harga" class="form-control" placeholder="Masukkan Harga" value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="harga">Deskripsi</label>
          <input type="text" name="deskripsi" id="deskripsi" class="form-control" placeholder="Masukkan deskripsi" value="<?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?>" required>
        </div>
        <div class="text-right">
          <a href="layananlihat.php" class="btn btn-danger">Kembali</a>
          <input type="submit" name="proses" value="Simpan" class="btn btn-success">
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>