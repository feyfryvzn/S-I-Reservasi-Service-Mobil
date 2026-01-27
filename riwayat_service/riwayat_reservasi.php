<?php
ob_start();
session_start();

// Cek jika role bukan 'user', logout dan redirect ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Cek jika URL mengandung ?logout, maka logout
if (isset($_GET['logout']) && isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Reservasi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
  <style>
    :root {
      --primary-dark: #003366;
      --primary-light: #66ccff;
      --accent-blue: #74b9ff;
      --hover-blue: rgba(0, 123, 255, 0.3);
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      --card-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --border-radius: 15px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    .sidebar {
      width: 70px;
      background: var(--primary-dark);
      color: #ffffff;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      transition: width 0.3s ease;
      z-index: 1001;
    }

    .sidebar:hover {
      width: 200px;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 15px 0;
      text-decoration: none;
      color: #ffffff;
      font-size: 1.2rem;
      transition: background 0.3s ease, color 0.3s ease;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a span {
      display: none;
      font-weight: 500;
    }

    .sidebar:hover a span {
      display: inline;
    }

    .sidebar a:hover {
      background: var(--primary-light);
      color: var(--primary-dark);
    }

    .sidebar a.active {
      background: var(--primary-light);
      color: var(--primary-dark);
    }

    .content {
      margin-left: 70px;
      width: calc(100% - 70px);
      padding: 2rem;
      transition: margin-left 0.3s ease, width 0.3s ease;
      min-height: 100vh;
    }

    .sidebar:hover ~ .content {
      margin-left: 200px;
      width: calc(100% - 200px);
    }

    .page-header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .page-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 0.5rem;
    }

    .page-header p {
      font-size: 1.1rem;
      color: #666;
      margin: 0;
    }

    .reservasi-card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      margin-bottom: 2rem;
      overflow: hidden;
      transition: all 0.3s ease;
      border: none;
    }

    .reservasi-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .card-header {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      color: white;
      padding: 1.5rem;
      border: none;
    }

    .card-header h5 {
      margin: 0;
      font-size: 1.3rem;
      font-weight: 600;
    }

    .card-header small {
      opacity: 0.9;
      font-size: 0.9rem;
    }

    .card-body {
      padding: 2rem;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .info-item {
      display: flex;
      align-items: center;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .info-item:hover {
      background: #e9ecef;
      transform: translateY(-2px);
    }

    .info-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      font-size: 1.2rem;
    }

    .info-content h6 {
      margin: 0;
      color: #666;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .info-content p {
      margin: 0;
      color: #333;
      font-size: 1rem;
      font-weight: 600;
    }

    .status-badges {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 10px;
    }

    .badge {
      padding: 0.75rem 1.5rem;
      font-size: 0.9rem;
      font-weight: 600;
      border-radius: 25px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-pending {
      background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
      color: #2d3436;
    }

    .badge-antri {
      background: linear-gradient(135deg, #74b9ff, #0984e3);
      color: white;
    }

    .badge-proses {
      background: linear-gradient(135deg, #fd79a8, #e84393);
      color: white;
    }

    .badge-selesai {
      background: linear-gradient(135deg, #00b894, #00a085);
      color: white;
    }

    .badge-belum-lunas {
      background: linear-gradient(135deg, #e17055, #d63031);
      color: white;
    }

    .badge-lunas {
      background: linear-gradient(135deg, #00b894, #00a085);
      color: white;
    }

    .detail-btn {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      color: white;
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .detail-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 51, 102, 0.4);
      color: white;
      text-decoration: none;
    }

    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #666;
    }

    .empty-state i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 1rem;
    }

    .empty-state h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: #999;
    }

    .empty-state p {
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 51, 102, 0.4);
    }

    .filter-section {
      background: white;
      padding: 2rem;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      margin-bottom: 2rem;
    }

    .filter-section h5 {
      margin-bottom: 1rem;
      color: var(--primary-dark);
      font-weight: 600;
    }

    .form-control {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-light);
      box-shadow: 0 0 0 0.2rem rgba(102, 204, 255, 0.25);
    }
    .custom-select-fix {
  padding-top: 0.55rem;
  padding-bottom: 0.55rem;
  height: auto;
  line-height: 1.5;
  font-size: 1rem;
}


    /* Mobile Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 0;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }

      .sidebar.show {
        width: 200px;
        transform: translateX(0);
      }

      .sidebar:hover {
        width: 200px;
      }

      .content {
        margin-left: 0;
        width: 100%;
        padding: 1rem;
      }

      .sidebar:hover ~ .content {
        margin-left: 0;
        width: 100%;
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .info-item {
        padding: 0.75rem;
      }

      .info-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
      }

      .card-body {
        padding: 1.5rem;
      }

      .status-badges {
        flex-direction: column;
        gap: 0.5rem;
      }

      .badge {
        width: 100%;
        text-align: center;
      }

      .mobile-toggle {
        display: block;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1002;
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
        color: var(--primary-dark);
      }
    }

    .mobile-toggle {
      display: none;
    }

    @media (max-width: 576px) {
      .page-header h1 {
        font-size: 1.8rem;
      }

      .card-header {
        padding: 1rem;
      }

      .card-body {
        padding: 1rem;
      }

      .filter-section {
        padding: 1rem;
      }
    }

    .modal-content {
  border: none;
}

.modal-header {
  border-bottom: none;
  padding: 1.5rem;
}

.modal-title {
  font-weight: 600;
  font-size: 1.3rem;
}

.modal-body {
  padding: 2rem;
}

.modal-footer {
  border-top: none;
  padding: 1.5rem;
}

.table {
  margin-bottom: 0;
}

.table th {
  font-weight: 600;
  color: var(--primary-dark);
  border-bottom: 2px solid var(--primary-light);
}

.table td {
  vertical-align: middle;
  font-size: 0.95rem;
}

#noDetailMessage {
  color: #666;
  font-size: 1rem;
  padding: 2rem;
}

#noDetailMessage i {
  font-size: 2rem;
  color: #ddd;
  margin-bottom: 1rem;
}
  </style>
</head>

<body>
  <!-- Mobile Toggle -->
  <button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <a href="../dashboard_cust.php"><i class="fas fa-home"></i><span>Beranda</span></a>
    <a href="../dashboard_cust.php#info"><i class="fas fa-info"></i><span>Tentang</span></a>
    <a href="../dashboard_cust.php#layanan"><i class="fas fa-wrench"></i><span>Layanan</span></a>
    <a href="../dashboard_cust.php#kontak"><i class="fas fa-phone"></i><span>Kontak</span></a>
    <a href="riwayat_reservasi.php" class="active"><i class="fas fa-calendar-check"></i><span>Riwayat</span></a>
  </div>

  <!-- Content -->
  <div class="content">
    <!-- Page Header -->
    <div class="page-header">
      <h1><i class="fas fa-history"></i> Riwayat Reservasi</h1>
      <p>Lihat semua reservasi yang pernah Anda buat</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <h5><i class="fas fa-filter"></i> Filter Reservasi</h5>
      <div class="row">
       <!-- Filter Status Servis -->
<div class="col-md-4 mb-4">
  <div class="form-group">
    <label for="filterStatus" class="text-dark font-weight-bold">
      <i class="fas fa-tools mr-1"></i> Status Servis
    </label>
    <select class="form-control custom-select-fix" id="filterStatus">
      <option value="">Semua Status</option>
      <option value="Pending">Pending</option>
      <option value="Antri">Antri</option>
      <option value="Proses">Proses</option>
      <option value="Selesai">Selesai</option>
    </select>
  </div>
</div>

          <!-- Filter Status Pembayaran -->
          <div class="col-md-4 mb-4">
            <div class="form-group">
              <label for="filterPembayaran" class="text-dark font-weight-bold">
                <i class="fas fa-wallet mr-1"></i> Status Pembayaran
              </label>
              <select class="form-control custom-select-fix" id="filterPembayaran">
                <option value="">Semua Status</option>
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
              </select>
            </div>
          </div>

        <div class="col-md-4">
          <label for="searchReservasi">Cari Reservasi:</label>
          <input type="text" class="form-control" id="searchReservasi" placeholder="Cari berdasarkan ID atau nomor polisi...">
        </div>
      </div>
    </div>

    <!-- Reservasi Cards -->
    <div id="reservasiContainer">
      <?php
      include '../koneksi.php';
      
      // Simulasi data (ganti dengan query yang sesuai untuk user yang login)
      $user_id = $_SESSION['id_user'] ?? ''; // Ganti dengan ID user yang login
      
      if (!$conn) {
        echo '<div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Koneksi Database Gagal</h3>
                <p>Tidak dapat terhubung ke database</p>
              </div>';
      } else {
        $query = mysqli_query($conn, "
          SELECT * FROM reservasi 
          WHERE id_user = '$user_id' 
          ORDER BY tanggal_servis DESC, waktu_servis DESC
        ");
        
        if ($query && mysqli_num_rows($query) > 0) {
          while ($data = mysqli_fetch_array($query)) {
            // Tentukan class badge berdasarkan status
            $statusClass = '';
            switch (strtolower($data['status'])) {
              case 'pending':
                $statusClass = 'badge-pending';
                break;
              case 'antri':
                $statusClass = 'badge-antri';
                break;
              case 'proses':
                $statusClass = 'badge-proses';
                break;
              case 'selesai':
                $statusClass = 'badge-selesai';
                break;
            }
            
            $pembayaranClass = '';
            switch (strtolower($data['status_pembayaran'])) {
              case 'belum lunas':
                $pembayaranClass = 'badge-belum-lunas';
                break;
              case 'lunas':
                $pembayaranClass = 'badge-lunas';
                break;
            }
            
            // Format tanggal
            $tanggal = date('d F Y', strtotime($data['tanggal_servis']));
            $waktu = date('H:i', strtotime($data['waktu_servis']));
      ?>
      
      <div class="reservasi-card" data-status="<?= htmlspecialchars($data['status']) ?>" data-pembayaran="<?= htmlspecialchars($data['status_pembayaran']) ?>" data-search="<?= htmlspecialchars($data['id_reservasi'] . ' ' . $data['nopolisi']) ?>">
       <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5>
            <i class="fas fa-calendar-check"></i> 
            Reservasi #<?= htmlspecialchars($data['id_reservasi']) ?>
          </h5>
          <small>Dibuat pada <?= $tanggal ?></small>
        </div>
        <div>
          <a class="btn btn-sm btn-primary" href="./cetak.php?id_reservasi=<?php echo urlencode($data['id_reservasi']); ?>"  target="_blank" data-toggle="tooltip" title="Cetak"><i class="fa fa-print"></i></a>
        </div>
      </div>
        <div class="card-body">
          <div class="info-grid">
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-user"></i>
              </div>
              <div class="info-content">
                <h6>Nama Lengkap</h6>
                <p><?= htmlspecialchars($data['nama_lengkap']) ?></p>
              </div>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div class="info-content">
                <h6>Nomor Telepon</h6>
                <p><?= htmlspecialchars($data['nomor_telepon']) ?></p>
              </div>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <div class="info-content">
                <h6>Tanggal & Waktu</h6>
                <p><?= $tanggal ?> - <?= $waktu ?></p>
              </div>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-motorcycle"></i>
              </div>
              <div class="info-content">
                <h6>Nomor Polisi</h6>
                <p><?= htmlspecialchars($data['nopolisi']) ?></p>
              </div>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-cog"></i>
              </div>
              <div class="info-content">
                <h6>Nomor Mesin</h6>
                <p><?= htmlspecialchars($data['no_mesin']) ?></p>
              </div>
            </div>
          </div>
          
          <div class="status-badges">
            <div>
              <small class="text-muted">Status Servis:</small>
              <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($data['status']) ?></span>
            </div>
            <div>
              <small class="text-muted">Status Pembayaran:</small>
              <span class="badge <?= $pembayaranClass ?>"><?= htmlspecialchars($data['status_pembayaran']) ?></span>
            </div>
            <div>
              <a href="detail-reservasi.php?id=<?= urlencode($data['id_reservasi']) ?>" class="detail-btn">
                <i class="fas fa-eye"></i> Detail
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <?php
          }
        } else {
      ?>
      
      <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>Belum Ada Reservasi</h3>
        <p>Anda belum memiliki riwayat reservasi. Mulai buat reservasi pertama Anda!</p>
        <a href="reservasi.php" class="btn btn-primary">
          <i class="fas fa-plus"></i> Buat Reservasi
        </a>
      </div>
      
      <?php
        }
        mysqli_close($conn);
      }
      ?>
    </div>
  </div>
</body>

<!-- Modal for Detail Reservasi -->
  <div class="modal fade" id="detailReservasiModal" tabindex="-1" role="dialog" aria-labelledby="detailReservasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius: var(--border-radius); box-shadow: var(--card-shadow);">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white;">
          <h5 class="modal-title" id="detailReservasiModalLabel">
            <i class="fas fa-info-circle"></i> Detail Reservasi
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="color: white;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-striped">
            <thead style="background: #f8f9fa;">
              <tr>
                <th>Jenis Layanan</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody id="detailReservasiTableBody">
              <!-- Data will be populated via AJAX -->
            </tbody>
          </table>
          <div id="noDetailMessage" class="text-center" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <p>Tidak ada detail reservasi ditemukan.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 25px;">
            <i class="fas fa-times"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.all.min.js"></script>
  
  <script>
    // Mobile sidebar toggle
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

    $(document).ready(function() {
      // Filter functionality
      function filterReservasi() {
        const statusFilter = $('#filterStatus').val().toLowerCase();
        const pembayaranFilter = $('#filterPembayaran').val().toLowerCase();
        const searchFilter = $('#searchReservasi').val().toLowerCase();
        
        $('.reservasi-card').each(function() {
          const card = $(this);
          const status = card.data('status').toLowerCase();
          const pembayaran = card.data('pembayaran').toLowerCase();
          const searchText = card.data('search').toLowerCase();
          
          let showCard = true;
          
          // Filter by status
          if (statusFilter && status !== statusFilter) {
            showCard = false;
          }
          
          // Filter by pembayaran
          if (pembayaranFilter && pembayaran !== pembayaranFilter) {
            showCard = false;
          }
          
          // Filter by search
          if (searchFilter && !searchText.includes(searchFilter)) {
            showCard = false;
          }
          
          if (showCard) {
            card.show();
          } else {
            card.hide();
          }
        });
        
        // Show/hide empty state
        const visibleCards = $('.reservasi-card:visible').length;
        if (visibleCards === 0 && $('.empty-state').length === 0) {
          $('#reservasiContainer').append(`
            <div class="empty-state" id="noResults">
              <i class="fas fa-search"></i>
              <h3>Tidak Ada Hasil</h3>
              <p>Tidak ada reservasi yang sesuai dengan filter yang dipilih.</p>
            </div>
          `);
        } else if (visibleCards > 0) {
          $('#noResults').remove();
        }
      }
      
      // Bind filter events
      $('#filterStatus, #filterPembayaran').change(filterReservasi);
      $('#searchReservasi').keyup(filterReservasi);
      
      // Success message from URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success')) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Reservasi berhasil dibuat!',
          showConfirmButton: false,
          timer: 3000
        });
      }
      
      // Smooth scroll animation for cards
      $('.reservasi-card').each(function(index) {
        $(this).css('opacity', '0');
        $(this).animate({
          opacity: 1
        }, 300 + (index * 100));
      });

      // Handle Detail Button Click
$('.detail-btn').click(function(e) {
    e.preventDefault();
    const idReservasi = $(this).attr('href').split('id=')[1];
    
    // Fetch detail data via AJAX
    $.ajax({
        url: './getdetail-reservasi.php',
        type: 'GET',
        data: { id_reservasi: idReservasi },
        dataType: 'json',
        success: function(data) {
            const tableBody = $('#detailReservasiTableBody');
            tableBody.empty();
            
            if (data.length > 0) {
                $('#noDetailMessage').hide();
                data.forEach(function(detail) {
                    tableBody.append(`
                        <tr>
                            <td>${detail.jenis_layanan}</td>
                            <td>${detail.jumlah}</td>
                            <td>${detail.harga}</td>
                            <td>Rp ${parseInt(detail.subtotal).toLocaleString('id-ID')}</td>
                        </tr>
                    `);
                });
            } else {
                $('#noDetailMessage').show();
            }
            
            $('#detailReservasiModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil detail reservasi.',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
});
    });
  </script>
</html>