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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservasi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
  <style>
    :root {
      --primary-dark: #0b1e33;
      --primary-light: #0d2744;
      --accent-blue: #74b9ff;
      --hover-blue: rgba(0, 123, 255, 0.3);
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      --border-radius: 20px;
      --sidebar-width: 250px;
      --sidebar-collapsed: 70px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background-color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      color: #000;
      margin: 0;
      overflow-x: hidden;
      line-height: 1.6;
    }

    .sidebar {
      height: 100vh;
      background: linear-gradient(180deg, var(--primary-dark), var(--primary-light));
      color: #fff;
      padding: 20px 15px;
      position: fixed;
      width: var(--sidebar-width);
      top: 0;
      left: 0;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
      transition: all 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
    }

    .sidebar.collapsed {
      width: var(--sidebar-collapsed);
      padding: 20px 5px;
    }

    .sidebar h4 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      letter-spacing: 1px;
      color: var(--accent-blue);
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed h4 {
      opacity: 0;
      display: none;
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
      position: relative;
      overflow: hidden;
    }

    .sidebar.collapsed a {
      justify-content: center;
      padding: 10px;
    }

    .sidebar a i {
      margin-right: 10px;
      font-size: 1.2rem;
      min-width: 20px;
      text-align: center;
    }

    .sidebar.collapsed a i {
      margin-right: 0;
    }

    .sidebar a span {
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed a span {
      opacity: 0;
      display: none;
    }

    .sidebar a:hover {
      background-color: var(--hover-blue);
      color: #fff;
      transform: translateX(5px);
      text-decoration: none;
    }

    .sidebar.collapsed a:hover {
      transform: translateX(0);
    }

    .sidebar a.active {
      background-color: #007bff;
      color: #fff;
      font-weight: 600;
    }

    .mobile-toggle {
      display: none;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1001;
      background: var(--primary-dark);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 12px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .mobile-toggle:hover {
      background: var(--primary-light);
    }

    .main {
      margin-left: var(--sidebar-width);
      padding: 30px;
      transition: margin-left 0.3s ease;
      min-height: 100vh;
    }

    .main.expanded {
      margin-left: var(--sidebar-collapsed);
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .dashboard-header h2 {
      margin: 0;
      font-size: clamp(1.5rem, 4vw, 2rem);
    }

    .dashboard-header img {
      height: 70px;
      max-height: 10vh;
      width: auto;
    }

    .btn-warning {
      background: linear-gradient(135deg, #ffca2c, #ffda6a);
      border: none;
      color: #000;
      font-weight: 600;
    }

    .btn-warning:hover {
      background: linear-gradient(135deg, #ffda6a, #ffca2c);
      color: #000;
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745, #38c172);
      border: none;
    }

    .btn-success:hover {
      background: linear-gradient(135deg, #38c172, #28a745);
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #ef5753);
      border: none;
    }

    .btn-danger:hover {
      background: linear-gradient(135deg, #ef5753, #dc3545);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #6c757d, #868e96);
      border: none;
    }

    .btn-secondary:hover {
      background: linear-gradient(135deg, #868e96, #6c757d);
    }

    .btn-primary {
      background: linear-gradient(135deg, #007bff, #4dabf7);
      border: none;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #4dabf7, #007bff);
    }

    .btn-info {
      background: linear-gradient(135deg, #17a2b8, #3dd5f3);
      border: none;
    }

    .btn-info:hover {
      background: linear-gradient(135deg, #3dd5f3, #17a2b8);
    }

    .card {
      background-color: #f8f9fa;
      border-radius: var(--border-radius);
      padding: 20px;
      box-shadow: var(--shadow);
      margin-bottom: 20px;
    }

    .card-header {
      background: transparent;
      border: none;
      padding: 0 0 20px 0;
      margin-bottom: 20px;
      border-bottom: 2px solid #dee2e6;
    }

    .card-header h5 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--primary-dark);
    }

    .table-responsive {
      background-color: #f8f9fa;
      border-radius: var(--border-radius);
      padding: 20px;
      box-shadow: var(--shadow);
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      margin-bottom: 0;
      white-space: nowrap;
    }

    .table thead th {
      background: linear-gradient(180deg, var(--primary-dark), var(--primary-light));
      color: #fff;
      border: none;
      font-weight: 600;
      text-align: center;
      vertical-align: middle;
      padding: 15px 8px;
      font-size: 0.9rem;
    }

    .table tbody td {
      vertical-align: middle;
      padding: 12px 8px;
      font-size: 0.85rem;
    }

    .table tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.05);
    }

    .btn-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
      min-width: 120px;
    }

    .btn-group .btn {
      margin: 0;
      font-size: 0.8rem;
      padding: 6px 10px;
    }

    .action-controls {
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-width: 150px;
    }

    .action-controls .form-control {
      font-size: 0.8rem;
      padding: 4px 8px;
      height: auto;
    }

    .action-controls .btn {
      font-size: 0.8rem;
      padding: 6px 10px;
    }

    .alert {
      border-radius: 8px;
    }

    .loading {
      text-align: center;
      padding: 20px;
      color: #6c757d;
    }

    .loading i {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Responsive Styles */
    @media (max-width: 1200px) {
      .table thead th,
      .table tbody td {
        padding: 10px 6px;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 992px) {
      .sidebar {
        width: var(--sidebar-collapsed);
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
        margin-left: var(--sidebar-collapsed);
        padding: 20px;
      }

      .dashboard-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
      }

      .table thead th,
      .table tbody td {
        padding: 8px 4px;
        font-size: 0.75rem;
      }

      .btn-group {
        min-width: 100px;
      }

      .action-controls {
        min-width: 120px;
      }
    }

    @media (max-width: 768px) {
      .mobile-toggle {
        display: block;
      }

      .sidebar {
        transform: translateX(-100%);
        width: var(--sidebar-width);
        padding: 20px 15px;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .sidebar h4 {
        display: block;
      }

      .sidebar a {
        justify-content: flex-start;
        padding: 12px 15px;
      }

      .sidebar a i {
        margin-right: 10px;
      }

      .sidebar a span {
        display: inline;
      }

      .main {
        margin-left: 0;
        padding: 80px 15px 20px 15px;
      }

      .card {
        padding: 15px;
      }

      .table-responsive {
        padding: 15px;
      }

      .table {
        font-size: 0.7rem;
      }

      .table thead th,
      .table tbody td {
        padding: 6px 3px;
      }

      .btn-group {
        min-width: 80px;
      }

      .btn-group .btn {
        font-size: 0.7rem;
        padding: 4px 6px;
      }

      .action-controls {
        min-width: 100px;
      }

      .action-controls .form-control {
        font-size: 0.7rem;
        padding: 3px 6px;
      }

      .action-controls .btn {
        font-size: 0.7rem;
        padding: 4px 6px;
      }
    }

    @media (max-width: 576px) {
      .main {
        padding: 70px 10px 15px 10px;
      }

      .dashboard-header h2 {
        font-size: 1.25rem;
      }

      .card {
        padding: 10px;
      }

      .table-responsive {
        padding: 10px;
      }

      .table {
        font-size: 0.65rem;
      }

      .btn-group {
        min-width: 70px;
      }

      .action-controls {
        min-width: 90px;
      }
    }

    /* DataTables Responsive Overrides */
    .dataTables_wrapper .dataTables_filter {
      margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length {
      margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_info {
      margin-top: 15px;
    }

    .dataTables_wrapper .dataTables_paginate {
      margin-top: 15px;
    }

    @media (max-width: 768px) {
      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_length {
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <div class="sidebar" id="sidebar">
    <h4>Panel Admin</h4>
    <a href="../dashboard_adm.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    <a href="../reservasi/reservasilihat.php" class="active"><i class="fas fa-users"></i><span>Reservasi</span></a>
    <a href="../layanan/layananlihat.php"><i class="fas fa-box"></i><span>Layanan</span></a>
    <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
  </div>

  <div class="main" id="main">
    <div class="dashboard-header">
      <div>
        <h2 class="text-dark font-weight-bold mb-0">Data Reservasi</h2>
        <small class="text-muted"><?= date('H:i A \W\I\B, d F Y') ?></small>
      </div>
      <img src="../images/logo.png" alt="Logo" height="70">
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Reservasi</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="example" class="table table-striped table-bordered" style="width:100%; text-align: center;">
            <thead class="table-primary">
              <tr>
                <th>ID Reservasi</th>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>Nomor Telepon</th>
                <th>Tanggal Servis</th>
                <th>Waktu Servis</th>
                <th>Nomor Mesin</th>
                <th>Nomor Polisi</th>
                <th>Status Servis</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include '../koneksi.php';
              if (!$conn) {
                echo "<tr><td colspan='12'>Gagal terhubung ke database</td></tr>";
              } else {
                $query = mysqli_query($conn, "
                  SELECT *
                  FROM reservasi;
                ");
                if ($query) {
                  if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='12'>Tidak ada data Reservasi</td></tr>";
                  } else {
                    while ($data = mysqli_fetch_array($query)) {
              ?>
                      <tr>
                        <td><?php echo htmlspecialchars($data['id_reservasi']); ?></td>
                        <td style="text-align: start;"><?php echo htmlspecialchars($data['id_user']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($data['nomor_telepon']); ?></td>
                        <td><?php echo htmlspecialchars($data['tanggal_servis']); ?></td>
                        <td><?php echo htmlspecialchars($data['waktu_servis']); ?></td>
                        <td><?php echo htmlspecialchars($data['no_mesin']); ?></td>
                        <td><?php echo htmlspecialchars($data['nopolisi']); ?></td>
                        <td><?php echo htmlspecialchars($data['status']); ?></td>
                        <td><?php echo htmlspecialchars($data['status_pembayaran']); ?></td>
                        <td>
                          <div class="btn-group" role="group">
                            <a class="btn btn-sm btn-success" href="reservasiubah.php?id_reservasi=<?php echo urlencode($data['id_reservasi']); ?>" data-toggle="tooltip" title="Ubah"><i class="fa fa-pencil"></i></a>
                            <a 
                              class="btn btn-sm btn-danger btn-delete" 
                              href="reservasihapus.php?id_reservasi=<?php echo urlencode($data['id_reservasi']); ?>" 
                              data-id="<?php echo htmlspecialchars($data['id_reservasi']); ?>"
                              data-toggle="tooltip" 
                              title="Hapus">
                              <i class="fa fa-trash"></i>
                            </a>
                          </div>
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <a href="detailreservasi-lihat.php?id_reservasi=<?= urlencode($data['id_reservasi']) ?>" 
                              class="btn btn-sm btn-info" title="Lihat Detail">
                              <i class="fa fa-eye"></i>
                            </a>

                            <form action="reservasi-updatestatus.php" method="post" style="display:inline-block;">
                              <input type="hidden" name="id_reservasi" value="<?= htmlspecialchars($data['id_reservasi']) ?>">
                              
                              <select name="status" class="form-control mb-1" style="font-size: 0.85rem; padding: 2px;">
                                <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Antri" <?= $data['status'] == 'Antri' ? 'selected' : '' ?>>Antri</option>
                                <option value="Proses" <?= $data['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                              </select>

                              <select name="status_pembayaran" class="form-control mb-1" style="font-size: 0.85rem; padding: 2px;">
                                <option value="Belum Lunas" <?= $data['status_pembayaran'] == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                                <option value="Lunas" <?= $data['status_pembayaran'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                              </select>

                              <button type="submit" class="btn btn-sm btn-secondary mt-1" title="Simpan Perubahan">
                                <i class="fa fa-save"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
              <?php
                    }
                  }
                  mysqli_free_result($query);
                } else {
                  echo "<tr><td colspan='12'>Gagal menjalankan query</td></tr>";
                  error_log("Query Reservasi gagal: " . mysqli_error($conn));
                }
                mysqli_close($conn);
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.all.min.js"></script>
  <script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const sidebar = document.getElementById('sidebar');
      const toggle = document.querySelector('.mobile-toggle');
      
      if (window.innerWidth <= 768 && 
          !sidebar.contains(event.target) && 
          !toggle.contains(event.target) && 
          sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
      }
    });

    $(document).ready(function() {
      $('#example').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
          "search": "Cari:",
          "lengthMenu": "Tampilkan _MENU_ data per halaman",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
          "infoFiltered": "(difilter dari _MAX_ total data)",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          },
          "emptyTable": "Tidak ada data yang tersedia",
          "zeroRecords": "Tidak ada data yang cocok"
        }
      });

      // Enable tooltips
      $('[data-toggle="tooltip"]').tooltip();

      // Tampilkan SweetAlert2 berdasarkan parameter success
      <?php if (isset($_GET['success'])): ?>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: '<?php 
            switch ($_GET['success']) {
              case '1':
                echo 'Data Reservasi berhasil ditambahkan!';
                break;
              case 'update':
                echo 'Data Reservasi berhasil diubah!';
                break;
              case 'delete':
                echo 'Data Reservasi berhasil dihapus!';
                break;
              default:
                echo 'Data Reservasi berhasil ditambahkan!';
            }
          ?>',
          showConfirmButton: false,
          timer: 3000
        });
      <?php endif; ?>
    });

    // Konfirmasi hapus dengan SweetAlert2
    $('.btn-delete').on('click', function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      var id = $(this).data('id');

      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data reservasi ID " + id + " akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = href;
        }
      });
    });
  </script>
</body>
</html>