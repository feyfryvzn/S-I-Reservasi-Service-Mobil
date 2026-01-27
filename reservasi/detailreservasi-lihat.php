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

if (!isset($_GET['id_reservasi'])) {
    echo "<script>alert('ID Reservasi tidak ditemukan'); window.location='reservasilihat.php';</script>";
    exit;
}

$id_reservasi = mysqli_real_escape_string($conn, $_GET['id_reservasi']);

// Ambil data header reservasi
$sql_header = "SELECT * FROM reservasi WHERE id_reservasi = ?";
$stmt_header = $conn->prepare($sql_header);
$stmt_header->bind_param("s", $id_reservasi);
$stmt_header->execute();
$result_header = $stmt_header->get_result();

if ($result_header->num_rows == 0) {
    echo "<script>alert('Data reservasi tidak ditemukan'); window.location='reservasilihat.php';</script>";
    exit;
}

$data_header = $result_header->fetch_assoc();
$stmt_header->close();

// Ambil data detail layanan
$sql_detail = "
    SELECT 
        dr.id_layanan,
        l.jenis_layanan,
        dr.jumlah,
        dr.harga,
        dr.subtotal
    FROM detail_reservasi dr
    JOIN layanan l ON dr.id_layanan = l.id_layanan
    WHERE dr.id_reservasi = ?
";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("s", $id_reservasi);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Reservasi</title>
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

    .btn-primary {
      background: linear-gradient(135deg, #007bff, #339af0);
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
      <h2 class="text-dark font-weight-bold mb-0">Detail Reservasi</h2>
      <small class="text-muted"><?= date('H:i A \W\I\B, d F Y') ?></small>
    </div>
      <img src="../images/logo.png" alt="Logo" height="70">
  </div>

  <div class="card">
    <h5 class="mb-4 text-center">Data Reservasi: <?= htmlspecialchars($data_header['id_reservasi']) ?></h5>
    <table class="table table-bordered mb-4">
      <tr><th>Nama Lengkap</th><td><?= htmlspecialchars($data_header['nama_lengkap']) ?></td></tr>
      <tr><th>Nomor Polisi</th><td><?= htmlspecialchars($data_header['nopolisi']) ?></td></tr>
      <tr><th>Nomor Mesin</th><td><?= htmlspecialchars($data_header['no_mesin']) ?></td></tr>
      <tr><th>Merk</th><td><?= htmlspecialchars($data_header['merk']) ?></td></tr>
      <tr><th>Total Harga</th><td><?= number_format($data_header['total_harga'], 0, ',', '.') ?></td></tr>
    </table>

    <h6 class="mb-3">Daftar Layanan</h6>
    <div class="table-responsive">
      <table class="table table-hover table-striped table-bordered text-center">
        <thead class="thead-dark">
          <tr>
            <th>No</th>
            <th>ID Layanan</th>
            <th>Jenis Layanan</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = $result_detail->fetch_assoc()):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['id_layanan']) ?></td>
            <td class="text-left"><?= htmlspecialchars($row['jenis_layanan']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= number_format($row['subtotal'], 0, ',', '.') ?></td>
            <td>
              <a href="detailreservasi-ubah.php?id_reservasi=<?= $id_reservasi ?>&id_layanan=<?= $row['id_layanan'] ?>" class="btn btn-warning btn-sm">
                <i class="fa fa-pencil"></i>
              </a>
              <a href="detailreservasi-hapus.php?id_reservasi=<?= $id_reservasi ?>&id_layanan=<?= $row['id_layanan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">
                <i class="fa fa-trash"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if ($result_detail->num_rows == 0): ?>
            <tr><td colspan="7">Tidak ada layanan ditemukan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-right mt-3">
      <a href="reservasilihat.php" class="btn btn-danger">Kembali</a>
      <a href="reservasicetak.php?id_reservasi=<?= urlencode($id_reservasi) ?>" class="btn btn-primary">
        <i class="fa fa-print"></i> Cetak Nota
      </a>
    </div>
  </div>
</div>

<?php
$stmt_detail->close();
$conn->close();
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Script SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['success'])): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php 
      switch ($_GET['success']) {
        case '1':
          echo 'Data Reservasi berhasil ditambahkan!';
          break;
        case 'update':
          echo 'Detail reservasi berhasil diubah!';
          break;
        case 'delete':
          echo 'Data Reservasi berhasil dihapus!';
          break;
        default:
          echo 'Operasi berhasil dilakukan!';
      }
    ?>',
    showConfirmButton: false,
    timer: 3000
  });
</script>
<?php endif; ?>
</body>
</html>
