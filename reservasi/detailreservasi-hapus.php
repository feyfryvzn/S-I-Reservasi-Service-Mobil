<?php
include '../koneksi.php';

if (!isset($_GET['id_reservasi'])) {
    header('Location: reservasilihat.php');
    exit;
}
$id_reservasi = mysqli_real_escape_string($conn, $_GET['id_reservasi']);

//Query header penjualan (penjualan JOIN customer JOIN pegawai) dengan prepared statement
$sql_header = "
    SELECT 
        p.id_reservasi,
        p.tanggal,
        p.jumlah_Rp,
        p.DP,
        p.sisa,
        c.nama_customer,
        pg.nama_pegawai
    FROM penjualan p
    JOIN customer c ON p.id_customer = c.id_customer
    JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
    WHERE p.id_reservasi = ?
";
$stmt_header = $conn->prepare($sql_header);
if ($stmt_header === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_header->bind_param("s", $id_reservasi);
$stmt_header->execute();
$result_header = $stmt_header->get_result();
if ($result_header->num_rows == 0) {
    // Jika id_reservasi tidak ditemukan, redirect kembali ke daftar
    header('Location: reservasilihat.php');
    exit;
}
$data_header = $result_header->fetch_assoc();
$stmt_header->close();

//Query detail barang (detail_penjualan JOIN barang) dengan prepared statement
$sql_detail = "
    SELECT 
        dp.id_barang,
        b.jenis_layanan,
        dp.banyaknya,
        dp.jumlah
    FROM detail_penjualan dp
    JOIN barang b ON dp.id_barang = b.id_barang
    WHERE dp.id_reservasi = ?
";
$stmt_detail = $conn->prepare($sql_detail);
if ($stmt_detail === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_detail->bind_param("s", $id_reservasi);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
?>

<!doctype html>
<html lang="en">

<head>
  <title>Detail Reservasi - Nota <?= htmlspecialchars($data_header['id_reservasi']) ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../css/sidebar.css">
</head>

<body>
  <div class="wrapper d-flex align-items-stretch">
    <nav id="sidebar">
      <div class="p-4 pt-5">
        <a href="#" class="img logo rounded-circle mb-5"
           style="background-image: url(../img/oip.jpg); display: block; width: 80px; height: 80px; background-size: cover;"></a>
        <ul class="list-unstyled components mb-5">
          <li><a href="../home.php">Home</a></li>
          <li><a href="../reservasi/reservasilihat.php">Reservasi</a></li>
          <li><a href="../layanan/layanan-lihat.php">Layanan</a></li>
          <li><a href="index.php" onclick="return confirm('Yakin keluar?')">Logout</a></li>
        </ul>
        <div class="footer">
          <p>
            Mbd ©<script>document.write(new Date().getFullYear());</script> <br>
            <i class="fa fa-heart" aria-hidden="true"></i>
          </p>
        </div>
      </div>
    </nav>

    <div id="content" class="p-4 p-md-5">
      <nav class="navbar navbar-expand-lg navbar-light bg-light top-navbar">
        <div class="container-fluid">
          <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle Menu</span>
          </button>
          <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button"
                  data-toggle="collapse" data-target="#navbarSupportedContent"
                  aria-controls="navbarSupportedContent" aria-expanded="false"
                  aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav navbar-nav ml-auto">
              <li class="nav-item"><a class="nav-link" href="../home.php">Home</a></li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>Detail Reservasi (No. Nota: <?= htmlspecialchars($data_header['id_reservasi']) ?>)</h5>
          <a href="reservasilihat.php" class="btn btn-secondary btn-sm">← Kembali ke Daftar</a>
        </div>

        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-4">
              <table class="table table-borderless table-sm">
                <tr>
                  <th style="width: 40%;">ID Reservasi</th>
                  <td><?= htmlspecialchars($data_header['id_reservasi']) ?></td>
                </tr>
                <tr>
                  <th>Jumlah</th>
                  <td><?= htmlspecialchars($data_header['jumlah']) ?></td>
                </tr>
                <tr>
                  <th>Harga</th>
                  <td><?= htmlspecialchars($data_header['harga']) ?></td>
                </tr>
                <tr>
                  <th>Subtotal</th>
                  <td><?= htmlspecialchars($data_header['subtotal']) ?></td>
                </tr>
            </div>
          </div>

          <h6>Daftar Layanan:</h6>
          <div class="table-responsive">
            <table id="detailTable" class="table table-striped table-bordered table-hover" 
                   style="width:100%; text-align: center;">
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
                while ($row = $result_detail->fetch_assoc()) {
                ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['id_layanan']) ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($row['jenis_layanan']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                    <td style="text-align: right;"><?= number_format(floatval(str_replace('.', '', $row['harga'])), 0, ',', '.') ?></td>
                    <td style="text-align: right;"><?= number_format(floatval(str_replace('.', '', $row['subtotal'])), 0, ',', '.') ?></td>
                    <td>
                      <a href="detailreservasi-ubah.php?id_reservasi=<?= urlencode($id_reservasi) ?>&id_layanan=<?= urlencode($row['id_layanan']) ?>"
                         class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit Detail">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <a href="detailreservasi-hapus.php?id_reservasi=<?= urlencode($id_reservasi) ?>&id_layanan=<?= urlencode($row['id_layanan']) ?>"
                         class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus Detail">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
                <?php if ($result_detail->num_rows == 0) : ?>
                  <tr>
                    <td colspan="6">Tidak ada detail barang untuk nota ini.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="text-right mt-3">
            <button class="btn btn-primary btn-sm" onclick="window.location.href='penjualancetak.php?id_reservasi=<?= urlencode($id_reservasi) ?>'">
              <i class="fa fa-print"></i> Cetak Nota
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
  <script src="../js/sidebar.js"></script>
  <script>
    $(document).ready(function() {
      $('#detailTable').DataTable({
        ordering: false,
        paging: false,
        searching: false,
        info: false
      });
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
</body>
</html>

<?php
$stmt_detail->close();
$conn->close();
?>